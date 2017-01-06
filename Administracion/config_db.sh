#!/bin/bash
set -e
version="3.8.4"
psql -c "CREATE ROLE orfeo_user LOGIN CREATEDB NOSUPERUSER PASSWORD 'wrksplf'"
psql -c "CREATE TABLESPACE orfeo_tablespace owner orfeo_user location '/var/db'"
psql -c "CREATE DATABASE orfeo_db OWNER orfeo_user TABLESPACE orfeo_tablespace"
psql orfeo_db < /var/www/orfeo-${version}/instalacion/postgres_2012_06_19.sql
psql orfeo_db -c "ALTER SCHEMA public OWNER TO orfeo_user"
#Change owner of tables
for tbl in `psql -qAt -c "select tablename from pg_tables where schemaname = 'public';" orfeo_db`; do  psql -c "alter table $tbl owner to orfeo_user"  orfeo_db;  done
#Change owner of sequences
for tbl in `psql -qAt -c "SELECT c.relname FROM pg_class c WHERE c.relkind = 'S';" orfeo_db`; do  psql -c "alter sequence $tbl owner to orfeo_user"  orfeo_db; done
