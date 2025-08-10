#!/usr/bin/env bash
set -euo pipefail

# Database backup script (SQLite/MySQL/PostgreSQL)
# Usage: scripts/backup_db.sh

PROJECT_ROOT=$(cd "$(dirname "$0")/.." && pwd)
cd "$PROJECT_ROOT"

BACKUP_DIR=${BACKUP_DIR:-storage/backups}
RETENTION=${RETENTION:-7}

mkdir -p "$BACKUP_DIR"

get_env() {
  local key="$1"
  local val
  val=$(grep -E "^${key}=" .env 2>/dev/null | tail -n1 | sed -E "s/^${key}=//" | tr -d '"' | tr -d "'" || true)
  echo "$val"
}

DB_CONNECTION=${DB_CONNECTION:-$(get_env DB_CONNECTION)}
DB_HOST=${DB_HOST:-$(get_env DB_HOST)}
DB_PORT=${DB_PORT:-$(get_env DB_PORT)}
DB_DATABASE=${DB_DATABASE:-$(get_env DB_DATABASE)}
DB_USERNAME=${DB_USERNAME:-$(get_env DB_USERNAME)}
DB_PASSWORD=${DB_PASSWORD:-$(get_env DB_PASSWORD)}

timestamp=$(date +%Y%m%d-%H%M%S)

case "$DB_CONNECTION" in
  sqlite|"")
    DB_FILE=${DB_DATABASE:-database/database.sqlite}
    if [ -f "$DB_FILE" ]; then
      echo "Backing up SQLite database: $DB_FILE"
      sqlite3 "$DB_FILE" ".backup '| gzip > ${BACKUP_DIR}/sqlite-${timestamp}.db.gz'" || {
        echo "Falling back to file copy" >&2
        gzip -c "$DB_FILE" > "${BACKUP_DIR}/sqlite-${timestamp}.db.gz"
      }
    else
      echo "SQLite file not found: $DB_FILE" >&2
      exit 1
    fi
    ;;
  mysql|mariadb)
    echo "Backing up MySQL/MariaDB database: $DB_DATABASE"
    mysqldump -h"${DB_HOST:-127.0.0.1}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --single-transaction --quick --routines --events "$DB_DATABASE" | gzip > "${BACKUP_DIR}/mysql-${DB_DATABASE}-${timestamp}.sql.gz"
    ;;
  pgsql|postgres|postgresql)
    echo "Backing up PostgreSQL database: $DB_DATABASE"
    PGPASSWORD="${DB_PASSWORD}" pg_dump -h "${DB_HOST:-127.0.0.1}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME}" -Fc "$DB_DATABASE" | gzip > "${BACKUP_DIR}/pgsql-${DB_DATABASE}-${timestamp}.dump.gz"
    ;;
  *)
    echo "Unsupported DB_CONNECTION: $DB_CONNECTION" >&2
    exit 1
    ;;
esac

# Retention policy
echo "Applying retention: keep last ${RETENTION} backups"
ls -1t "$BACKUP_DIR" | tail -n +$((RETENTION+1)) | while read -r f; do
  [ -n "$f" ] && rm -f "$BACKUP_DIR/$f"
done

echo "Backup completed in $BACKUP_DIR"


