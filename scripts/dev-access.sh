#!/usr/bin/env bash
#
# Switches local development between this PC only (*.localhost) and the local network (*.<ip>.sslip.io),
# so a phone can open the app. It only edits APP_URL and APP_SUBDOMAIN_BASE in .env, and refuses to run
# unless APP_ENV=local. A running Vite restarts itself on the .env change, regenerating Wayfinder's urls.
#
#   npm run access         show the current mode
#   npm run access:local   back to *.localhost
#   npm run access:lan     use this PC's network IP (or: npm run access:lan -- 192.168.1.50)

set -euo pipefail

cd "$(dirname "$0")/.."

WINDOWS=/mnt/c/Windows/System32
POWERSHELL=$WINDOWS/WindowsPowerShell/v1.0/powershell.exe

env_value() {
    grep -E "^$1=" .env | cut -d= -f2-
}

if [[ "$(env_value APP_ENV)" != "local" ]]; then
    echo "APP_ENV isn't local, so this .env was left alone." >&2
    exit 1
fi

set_base() {
    sed -i -E "s|^APP_URL=.*|APP_URL=http://$1|; s|^APP_SUBDOMAIN_BASE=.*|APP_SUBDOMAIN_BASE=$1|" .env
}

# The IP of the Windows network adapter holding the best default route
windows_ip() {
    [[ -x "$POWERSHELL" ]] || return 0
    "$POWERSHELL" -NoProfile -Command "(Get-NetRoute -DestinationPrefix '0.0.0.0/0' | Sort-Object { \$_.RouteMetric + (Get-NetIPInterface -InterfaceIndex \$_.InterfaceIndex -AddressFamily IPv4).InterfaceMetric } | Select-Object -First 1 | Get-NetIPAddress -AddressFamily IPv4).IPAddress" | tr -d '\r'
}

# WSL's IP can change when it restarts, leaving Windows forwarding to the old one. Fixing that needs an admin shell.
check_forwarding() {
    [[ -x "$WINDOWS/netsh.exe" ]] || return 0

    local wsl_ip rules port stale=()
    wsl_ip=$(hostname -I | awk '{print $1}')
    rules=$("$WINDOWS/netsh.exe" interface portproxy show v4tov4 | tr -d '\r')

    for port in 80 5173; do
        if ! grep -qE "^0\.0\.0\.0 +$port +$wsl_ip +$port *$" <<<"$rules"; then
            stale+=("$port")
        fi
    done

    if ((${#stale[@]})); then
        echo
        echo "Windows isn't forwarding port(s) ${stale[*]} to WSL ($wsl_ip). Run this in an admin PowerShell:"
        for port in "${stale[@]}"; do
            echo "  netsh interface portproxy set v4tov4 listenport=$port listenaddress=0.0.0.0 connectport=$port connectaddress=$wsl_ip"
        done
    fi
}

show() {
    local base
    base=$(env_value APP_SUBDOMAIN_BASE)

    if [[ "$base" == "localhost" ]]; then
        echo "Mode: this PC only (http://localhost)"
    else
        echo "Mode: local network (http://$base)"
        check_forwarding
    fi

    if ! pgrep -f "$PWD/node_modules/.bin/vite" >/dev/null; then
        echo
        echo "Vite isn't running. Run npm run dev, which also regenerates Wayfinder's urls for this mode."
    fi
}

case "${1:-status}" in
    status)
        ;;
    local)
        set_base localhost
        ;;
    lan)
        ip=${2:-$(windows_ip)}
        if [[ ! "$ip" =~ ^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
            echo "Couldn't find this PC's network IP. Pass it in: npm run access:lan -- 192.168.1.50" >&2
            exit 1
        fi
        set_base "$ip.sslip.io"
        ;;
    *)
        echo "Usage: $0 [status|local|lan [ip]]" >&2
        exit 1
        ;;
esac

show
