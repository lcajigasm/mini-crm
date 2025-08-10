#!/usr/bin/env bash
set -euo pipefail

# Simple HTTP health check
# Usage: scripts/health_check.sh https://mini-crm.example.com/healthz

URL=${1:-}
if [ -z "$URL" ]; then
  echo "Usage: $0 <url>" >&2
  exit 1
fi

echo "GET $URL"
http_code=$(curl -s -o /tmp/health_body.$$ -w "%{http_code}" --max-time 10 "$URL")
cat /tmp/health_body.$$
echo
rm -f /tmp/health_body.$$

if [ "$http_code" != "200" ]; then
  echo "Health check failed with HTTP $http_code" >&2
  exit 1
fi

echo "Health check OK"


