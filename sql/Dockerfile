FROM mariadb:12

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY theschool.sql /docker-entrypoint-initdb.d/init.sql

HEALTHCHECK --interval=10s --timeout=5s --retries=5 --start-period=20s \
    CMD mariadb-admin ping -h localhost -u root -p"$MARIADB_ROOT_PASSWORD" || exit 1

EXPOSE 3306
