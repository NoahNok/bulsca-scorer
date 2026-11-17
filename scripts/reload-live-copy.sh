#!/usr/bin/env bash
#
# Reloads the copy of the live database into `scoring_test` and, if asked, compares the engine's
# output for every competition against a recording (docs/scoring-engine.md, "Whole-database
# comparison").
#
#   ./scripts/reload-live-copy.sh                                         # reload only
#   ./scripts/reload-live-copy.sh --compare=storage/app/scoring-before    # reload, then compare
#   ./scripts/reload-live-copy.sh --compare-only=storage/app/scoring-before  # compare, no reload
#   ./scripts/reload-live-copy.sh --out=storage/app/scoring-after         # reload, then record
#
# It only ever touches `scoring_test` (fixed below, not an argument), so it can't drop your dev
# database. The test suite rebuilds `scoring_test` too, so reload after running the tests.
# `scoring.sql` holds competitors' real names: keep it, and any recording, out of git.

set -euo pipefail

cd "$(dirname "$0")/.."

readonly DATABASE=scoring_test
readonly DUMP=scoring.sql

reload=true
snapshot_args=()

for arg in "$@"; do
    case "$arg" in
        --compare=*) snapshot_args=("$arg") ;;
        --out=*) snapshot_args=("$arg") ;;
        --compare-only=*) reload=false; snapshot_args=("--compare=${arg#--compare-only=}") ;;
        *) echo "Unknown option: $arg" >&2; exit 2 ;;
    esac
done

env_value() {
    # The value of KEY in .env, without surrounding quotes
    local value
    value=$(grep -E "^$1=" .env | tail -n 1 | cut -d= -f2-)
    value="${value%\"}"; value="${value#\"}"
    value="${value%\'}"; value="${value#\'}"
    printf '%s' "$value"
}

# The array cache, so nothing your dev database cached in Redis under the same ids is read back
export DB_DATABASE="$DATABASE" CACHE_DRIVER=array CACHE_STORE=array

if $reload; then
    if [[ ! -f "$DUMP" ]]; then
        echo "No $DUMP in the repo root: put the copy of the live database there first." >&2
        exit 1
    fi

    export MYSQL_PWD
    MYSQL_PWD=$(env_value DB_PASSWORD)
    mysql_args=(-h "$(env_value DB_HOST)" -P "$(env_value DB_PORT)" -u "$(env_value DB_USERNAME)")

    echo "Recreating $DATABASE..."
    mysql "${mysql_args[@]}" -e "DROP DATABASE IF EXISTS \`$DATABASE\`; CREATE DATABASE \`$DATABASE\`"

    echo "Loading $DUMP ($(du -h "$DUMP" | cut -f1))..."
    mysql "${mysql_args[@]}" "$DATABASE" < "$DUMP"

    echo "Migrating..."
    php artisan migrate --force

    php artisan tinker --execute 'echo App\Models\Competition::count() . " competitions loaded" . PHP_EOL;'
fi

if [[ ${#snapshot_args[@]} -gt 0 ]]; then
    php artisan scoring:snapshot "${snapshot_args[@]}"
fi
