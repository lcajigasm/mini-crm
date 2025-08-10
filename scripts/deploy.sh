#!/usr/bin/env bash
set -euo pipefail

# Minimal zero-downtime-ish deploy script example
# Usage: scripts/deploy.sh /var/www/mini-crm /usr/bin/php

TARGET_DIR=${1:-/var/www/mini-crm}
PHP_BIN=${2:-php}

echo "Deploying to ${TARGET_DIR}"

if [ ! -d "$TARGET_DIR" ]; then
  mkdir -p "$TARGET_DIR"
  echo "Created target directory"
fi

rsync -a --delete --exclude storage --exclude vendor --exclude node_modules ./ "$TARGET_DIR"/

pushd "$TARGET_DIR" >/dev/null

composer install --no-dev --prefer-dist --no-interaction --no-progress
npm ci --no-audit --no-fund || true
npm run build || true

$PHP_BIN artisan key:generate --force || true
$PHP_BIN artisan migrate --force
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan optimize

echo "Deploy completed"

popd >/dev/null


