#!/bin/bash

POSTGIS_SQL=postgis.sql
POSTGIS_SQL_PATH=/usr/share/postgresql/9.4/contrib/postgis-2.1

createdb -E UTF8 template_postgis && \
( createlang -d template_postgis -l | grep plpgsql || createlang -d template_postgis plpgsql ) && \
psql -d postgres -c "UPDATE pg_database SET datistemplate='true' WHERE datname='template_postgis';" && \
psql -d template_postgis -f $POSTGIS_SQL_PATH/$POSTGIS_SQL && \
psql -d template_postgis -f $POSTGIS_SQL_PATH/spatial_ref_sys.sql && \
psql -d template_postgis -c "GRANT ALL ON geometry_columns TO PUBLIC;" && \
psql -d template_postgis -c "GRANT ALL ON spatial_ref_sys TO PUBLIC;" && \
psql -d template_postgis -c "GRANT ALL ON geography_columns TO PUBLIC;"