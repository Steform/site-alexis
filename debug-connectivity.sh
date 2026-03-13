#!/bin/bash
# Writes NDJSON diagnostics to .cursor/debug-f469c3.log for session f469c3
LOG="/var/www/site-alexis/.cursor/debug-f469c3.log"
TS=$(date +%s)000

append() {
  echo "$1" >> "$LOG"
}

# Hypothesis A: Apache not running or port 90 not listening
apache_status=$(systemctl is-active apache2 2>/dev/null || echo "unknown")
port_listen=$(ss -tlnp 2>/dev/null | grep -c ':90' || echo "0")
append "{\"sessionId\":\"f469c3\",\"timestamp\":$TS,\"hypothesisId\":\"A\",\"message\":\"Apache and port 90\",\"data\":{\"apache\":\"$apache_status\",\"port90_listen_count\":\"$port_listen\"},\"location\":\"debug-connectivity.sh\"}"

# Hypothesis B: alexis.loc resolution (hosts/DNS)
resolved=$(getent hosts alexis.loc 2>/dev/null | awk '{print $1}' | tr '\n' ' ')
append "{\"sessionId\":\"f469c3\",\"timestamp\":$TS,\"hypothesisId\":\"B\",\"message\":\"alexis.loc resolution\",\"data\":{\"resolved_ips\":\"$resolved\"},\"location\":\"debug-connectivity.sh\"}"

# Hypothesis C: IPv4 connection to port 90
curl4=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 2 http://127.0.0.1:90/ 2>/dev/null || echo "CONN_FAIL")
append "{\"sessionId\":\"f469c3\",\"timestamp\":$TS,\"hypothesisId\":\"C\",\"message\":\"curl IPv4 127.0.0.1:90\",\"data\":{\"http_code_or_error\":\"$curl4\"},\"location\":\"debug-connectivity.sh\"}"

# Hypothesis D: IPv6 connection to port 90
curl6=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 2 http://[::1]:90/ 2>/dev/null || echo "CONN_FAIL")
append "{\"sessionId\":\"f469c3\",\"timestamp\":$TS,\"hypothesisId\":\"D\",\"message\":\"curl IPv6 [::1]:90\",\"data\":{\"http_code_or_error\":\"$curl6\"},\"location\":\"debug-connectivity.sh\"}"

# Hypothesis E: curl using hostname alexis.loc (resolution + connect)
curl_host=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 2 http://alexis.loc:90/ 2>/dev/null || echo "CONN_FAIL")
append "{\"sessionId\":\"f469c3\",\"timestamp\":$TS,\"hypothesisId\":\"E\",\"message\":\"curl alexis.loc:90\",\"data\":{\"http_code_or_error\":\"$curl_host\"},\"location\":\"debug-connectivity.sh\"}"

echo "Diagnostics written to $LOG"
