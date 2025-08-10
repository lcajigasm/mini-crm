#!/usr/bin/env bash
set -euo pipefail

# Staging deploy wrapper
# Usage: HEALTHCHECK_URL="https://staging.example.com/healthz" scripts/deploy-staging.sh

PROJECT_ROOT=$(cd "$(dirname "$0")/.." && pwd)
TARGET_DIR=${TARGET_DIR:-/var/www/mini-crm-staging}
PHP_BIN=${PHP_BIN:-php}

export APP_ENV=staging

"${PROJECT_ROOT}/scripts/deploy.sh" "${TARGET_DIR}" "${PHP_BIN}"

if [ -n "${HEALTHCHECK_URL:-}" ]; then
  echo "Checking health at ${HEALTHCHECK_URL}"
  curl -fsS --max-time 10 "${HEALTHCHECK_URL}" | cat
fi

echo "Staging deployment finished"


