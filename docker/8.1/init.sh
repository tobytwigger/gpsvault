CREATE USER sail;

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
CREATE DATABASE cycle_store_testing;
CREATE DATABASE cycle_store_testing_test_1;
CREATE DATABASE cycle_store_testing_test_2;
CREATE DATABASE cycle_store_testing_test_3;
CREATE DATABASE cycle_store_testing_test_4;
CREATE DATABASE cycle_store_testing_test_5;
CREATE DATABASE cycle_store_testing_test_6;
CREATE DATABASE cycle_store_testing_test_7;
CREATE DATABASE cycle_store_testing_test_8;
CREATE DATABASE cycle_store_testing_test_9;
CREATE DATABASE cycle_store_testing_test_10;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_1 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_2 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_3 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_4 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_5 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_6 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_7 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_8 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_9 TO sail;
GRANT ALL PRIVILEGES ON DATABASE cycle_store_testing_test_10 TO sail;
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

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_1" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_2" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_3" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_4" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_5" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_6" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_7" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_8" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_9" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL

PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "cycle_store_testing_test_10" <<-EOSQL
CREATE EXTENSION postgis;
CREATE EXTENSION postgis_raster;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_sfcgal;
EOSQL
