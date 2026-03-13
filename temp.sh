#!/bin/bash
# Temporary script: Apache vhost alexis.loc:90 + /etc/hosts (IPv4 + IPv6)
# Run with: sudo ./temp.sh

set -e

VHOST_SRC="/var/www/site-alexis/apache-vhost-alexis.loc.conf"
VHOST_DST="/etc/apache2/sites-available/alexis.loc.conf"
HOSTS_FILE="/etc/hosts"
HOSTNAME="alexis.loc"

echo "=== Copying vhost to Apache ==="
cp "$VHOST_SRC" "$VHOST_DST"

echo "=== Enabling site ==="
a2ensite alexis.loc.conf

echo "=== Testing Apache config ==="
apache2ctl configtest

echo "=== Updating /etc/hosts (IPv4 + IPv6) ==="
if grep -q "$HOSTNAME" "$HOSTS_FILE"; then
    echo "Entry for $HOSTNAME already present in $HOSTS_FILE, skipping."
else
    echo "" >> "$HOSTS_FILE"
    echo "# alexis.loc (vhost site-alexis)" >> "$HOSTS_FILE"
    echo "127.0.0.1   $HOSTNAME" >> "$HOSTS_FILE"
    echo "::1         $HOSTNAME" >> "$HOSTS_FILE"
    echo "Added $HOSTNAME for 127.0.0.1 and ::1"
fi

echo "=== Reloading Apache ==="
systemctl reload apache2

echo "=== Done. Open http://${HOSTNAME}:90 in your browser. ==="
