#!/bin/sh
set -eu

if [ -z "${POSTGRES_TEST_DATABASE:-}" ]; then
    exit 0
fi

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname postgres <<-EOSQL
SELECT 'CREATE DATABASE "' || :'POSTGRES_TEST_DATABASE' || '"'
WHERE NOT EXISTS (
    SELECT 1
    FROM pg_database
    WHERE datname = :'POSTGRES_TEST_DATABASE'
)\gexec
EOSQL
