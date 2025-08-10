#!/usr/bin/env bash
set -euo pipefail

# Production deploy wrapper
# Usage: HEALTHCHECK_URL="https://mini-crm.example.com/healthz" scripts/deploy-prod.sh

PROJECT_ROOT=$(cd "$(dirname "$0")/.." && pwd)
TARGET_DIR=${TARGET_DIR:-/var/www/mini-crm}
PHP_BIN=${PHP_BIN:-php}

export APP_ENV=production

"${PROJECT_ROOT}/scripts/deploy.sh" "${TARGET_DIR}" "${PHP_BIN}"

if [ -n "${HEALTHCHECK_URL:-}" ]; then
  echo "Checking health at ${HEALTHCHECK_URL}"
  curl -fsS --max-time 10 "${HEALTHCHECK_URL}" | cat
fi

echo "Production deployment finished"


