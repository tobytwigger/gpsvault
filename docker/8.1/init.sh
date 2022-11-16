PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
DO \$\$
BEGIN
CREATE ROLE sail;
EXCEPTION WHEN duplicate_object THEN RAISE NOTICE '%, skipping', SQLERRM USING ERRCODE = SQLSTATE;
END
\$\$;

CREATE DATABASE gpsvault_testing;
CREATE DATABASE gpsvault_testing_test_1;
CREATE DATABASE gpsvault_testing_test_2;
CREATE DATABASE gpsvault_testing_test_3;
CREATE DATABASE gpsvault_testing_test_4;
CREATE DATABASE gpsvault_testing_test_5;
CREATE DATABASE gpsvault_testing_test_6;
CREATE DATABASE gpsvault_testing_test_7;
CREATE DATABASE gpsvault_testing_test_8;
CREATE DATABASE gpsvault_testing_test_9;
CREATE DATABASE gpsvault_testing_test_10;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_1 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_2 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_3 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_4 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_5 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_6 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_7 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_8 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_9 TO sail;
GRANT ALL PRIVILEGES ON DATABASE gpsvault_testing_test_10 TO sail;
EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_1" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_2" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_3" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_4" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_5" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_6" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_7" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_8" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_9" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
#
#PGPASSWORD="${PGPASSWORD}" psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "gpsvault_testing_test_10" <<-EOSQL
#CREATE EXTENSION postgis;
#CREATE EXTENSION postgis_raster;
#CREATE EXTENSION postgis_topology;
#CREATE EXTENSION postgis_sfcgal;
#EOSQL
