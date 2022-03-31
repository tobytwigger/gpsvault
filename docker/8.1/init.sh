PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
CREATE DATABASE cycle_store_testing;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing TO sail;
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL
