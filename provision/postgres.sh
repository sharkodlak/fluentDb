#!/usr/bin/env bash
DBNAME=pagila
USER=test
PASSWORD=test

sudo mkdir -m 770 /etc/dbal
sudo chown www-data:adm /etc/dbal
echo "pgsql:host=localhost;dbname=$DBNAME;user=$USER;password=$PASSWORD" > /etc/dbal/.dbconnect
psql postgres postgres -c "CREATE USER $USER WITH PASSWORD '$PASSWORD'"
psql postgres postgres -c "CREATE DATABASE $DBNAME"
bunzip2 pagila.tar.bz2 --stdout | pg_restore --user=postgres --dbname=$DBNAME --exit-on-error
psql $DBNAME postgres -c "GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO $USER"
