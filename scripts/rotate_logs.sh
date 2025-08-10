#!/usr/bin/env bash
set -euo pipefail

# Simple log rotation for Laravel logs
# Usage: scripts/rotate_logs.sh

PROJECT_ROOT=$(cd "$(dirname "$0")/.." && pwd)
cd "$PROJECT_ROOT"

LOG_DIR=${LOG_DIR:-storage/logs}
BASE=${BASE:-laravel}
RETENTION=${RETENTION:-14}

mkdir -p "$LOG_DIR"

LOG_FILE="$LOG_DIR/${BASE}.log"

if [ ! -f "$LOG_FILE" ]; then
  echo "No log file at $LOG_FILE"
  exit 0
fi

timestamp=$(date +%Y%m%d-%H%M%S)
ARCHIVE_FILE="$LOG_DIR/${BASE}-${timestamp}.log.gz"

echo "Rotating $LOG_FILE -> $ARCHIVE_FILE"
gzip -c "$LOG_FILE" > "$ARCHIVE_FILE"
: > "$LOG_FILE"

echo "Applying retention: keep last ${RETENTION} archives"
ls -1t "$LOG_DIR"/${BASE}-*.log.gz 2>/dev/null | tail -n +$((RETENTION+1)) | xargs -r rm -f

echo "Rotation done"


