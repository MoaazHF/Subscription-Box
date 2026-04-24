FROM postgres:17-alpine

COPY docker/postgres/init/ /docker-entrypoint-initdb.d/

FROM dpage/pgadmin4:latest

# Optional: Add a pre-configured servers.json file
# This allows pgAdmin to automatically connect to your databases on startup
COPY servers.json /pgadmin4/servers.json

# Environment variables can also be set here
ENV PGADMIN_DEFAULT_EMAIL=admin@example.com
ENV PGADMIN_DEFAULT_PASSWORD=admin_password
