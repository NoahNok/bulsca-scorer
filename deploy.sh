#!/usr/bin/env bash
#
# Deploy the latest master on the VPS: ./deploy.sh
#
# Runs the whole deploy inside maintenance mode. If any step fails the site
# stays down so a half-applied deploy is never served; fix the problem and
# re-run, every step is safe to repeat.
#
# Options (environment variables):
#   DEPLOY_RELOAD_FPM=1   also reload PHP-FPM (needed if OPcache has
#                         validate_timestamps=0). Uses sudo.
#   DEPLOY_FPM_SERVICE    FPM unit to reload (default: first php*-fpm found)

set -euo pipefail

cd "$(dirname "$(readlink -f "$0")")"

BRANCH=master
BACKUP_DIR=storage/backups
BACKUPS_TO_KEEP=10

step() { CURRENT_STEP="$1"; printf '\n\033[1;34m==> %s\033[0m\n' "$1"; }
warn() { printf '\033[1;33m!! %s\033[0m\n' "$1"; }

CURRENT_STEP="pre-flight checks"
on_error() {
    printf '\n\033[1;31mDeploy failed during: %s\033[0m\n' "$CURRENT_STEP"
    if [ -f storage/framework/down ]; then
        echo "The site is still in maintenance mode. Fix the problem and re-run ./deploy.sh,"
        echo "or bring it back as-is with: php artisan up"
    fi
}
trap on_error ERR

# --- Pre-flight -------------------------------------------------------------

step "Pre-flight checks"

if [ "$(id -u)" -eq 0 ]; then
    echo "Don't run this as root: it leaves root-owned cache files the web server can't write."
    echo "Run it as the user that owns the app files."
    exit 1
fi

if [ "$(stat -c %U storage)" != "$(id -un)" ]; then
    warn "storage/ is owned by $(stat -c %U storage), not $(id -un). Caches written now may not be writable by the web server."
fi

if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
    echo "Working tree has local changes; refusing to deploy over them:"
    git status --short --untracked-files=no
    exit 1
fi

# --- Deploy -----------------------------------------------------------------

step "Entering maintenance mode"
# The page is pre-rendered so it still works while vendor/ and public/build
# are being replaced. Skip --render on a checkout that doesn't have it yet.
DOWN_ARGS=(--retry=15 --refresh=15)
if [ -f resources/views/errors/503.blade.php ]; then
    DOWN_ARGS+=(--render=errors::503)
fi
php artisan down "${DOWN_ARGS[@]}"

step "Pulling $BRANCH"
git pull --ff-only origin "$BRANCH"

step "Clearing cached config, routes and views"
php artisan optimize:clear

step "Installing PHP dependencies"
composer install --no-dev --optimize-autoloader --no-interaction

step "Building the frontend"
npm ci
npm run build

if ! php artisan migrate:status --pending=1 > /dev/null 2>&1; then
    step "Backing up the database before migrating"
    mkdir -p "$BACKUP_DIR"

    # Pass credentials through a temporary defaults file so the password
    # doesn't show up in the process list.
    MYSQL_DEFAULTS=$(mktemp)
    trap 'rm -f "$MYSQL_DEFAULTS"' EXIT
    php -r '
        require "vendor/autoload.php";
        $app = require "bootstrap/app.php";
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $db = config("database.connections." . config("database.default"));
        printf("[client]\nhost=%s\nport=%s\nuser=%s\npassword=\"%s\"\n# database=%s\n",
            $db["host"], $db["port"], $db["username"],
            addcslashes($db["password"] ?? "", "\\\""), $db["database"]);
    ' > "$MYSQL_DEFAULTS"
    DB_NAME=$(sed -n 's/^# database=//p' "$MYSQL_DEFAULTS")

    BACKUP_FILE="$BACKUP_DIR/$(date +%Y%m%d-%H%M%S)-$(git rev-parse --short HEAD).sql.gz"
    mysqldump --defaults-extra-file="$MYSQL_DEFAULTS" --single-transaction --quick --no-tablespaces "$DB_NAME" | gzip > "$BACKUP_FILE"
    rm -f "$MYSQL_DEFAULTS"
    echo "Saved $BACKUP_FILE"

    ls -1t "$BACKUP_DIR"/*.sql.gz | tail -n +$((BACKUPS_TO_KEEP + 1)) | xargs -r rm --
fi

step "Running migrations"
php artisan migrate --force

step "Caching config, routes, views and events"
php artisan optimize

step "Restarting long-running processes"
php artisan reverb:restart
php artisan queue:restart

if [ "${DEPLOY_RELOAD_FPM:-0}" = "1" ]; then
    FPM_SERVICE=${DEPLOY_FPM_SERVICE:-$(systemctl list-units --type=service --no-legend 'php*-fpm.service' | awk '{print $1}' | head -n1)}
    if [ -z "$FPM_SERVICE" ]; then
        echo "Couldn't find a php*-fpm service; set DEPLOY_FPM_SERVICE."
        false
    fi
    echo "Reloading $FPM_SERVICE"
    sudo systemctl reload "$FPM_SERVICE"
fi

step "Leaving maintenance mode"
php artisan up

printf '\n\033[1;32mDeployed %s\033[0m\n' "$(git log -1 --pretty='%h %s')"
