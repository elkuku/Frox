#!/usr/bin/env bash
#
# Frox startup script
#

docker-compose up -d

symfony server:start -d --no-tls

symfony open:local
