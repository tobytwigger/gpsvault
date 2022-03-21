SELECT 'CREATE DATABASE cycle_store_testing'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'cycle_store_testing')

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA cycle_store_testing TO sail;

CREATE EXTENSION postgis;
-- enable raster support (for 3+)
CREATE EXTENSION postgis_raster;
-- Enable Topology
CREATE EXTENSION postgis_topology;
-- Enable PostGIS Advanced 3D
-- and other geoprocessing algorithms
-- sfcgal not available with all distributions
CREATE EXTENSION postgis_sfcgal;
