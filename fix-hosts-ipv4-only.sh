#!/bin/bash
# Force alexis.loc to resolve to 127.0.0.1 only (remove ::1) so Firefox connects via IPv4.
# Run with: sudo ./fix-hosts-ipv4-only.sh

set -e
HOSTS_FILE="/etc/hosts"
HOSTNAME="alexis.loc"

if ! grep -q "127.0.0.1.*$HOSTNAME" "$HOSTS_FILE"; then
  echo "No 127.0.0.1 entry for $HOSTNAME found in $HOSTS_FILE. Nothing to fix."
  exit 0
fi

# Remove the ::1 line for alexis.loc (force IPv4-only resolution)
if grep -q "::1.*$HOSTNAME" "$HOSTS_FILE"; then
  sed -i "/^::1[[:space:]]*$HOSTNAME/d" "$HOSTS_FILE"
  echo "Removed ::1 entry for $HOSTNAME. It now resolves to 127.0.0.1 only."
else
  echo "No ::1 entry for $HOSTNAME. Hosts already IPv4-only for this host."
fi

echo "Done. Try http://${HOSTNAME}:90 again in Firefox."
