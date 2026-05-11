#!/bin/sh
set -eu

port="${EXTERNAL_HTTPS_PORT:-8443}"
sed -i "s/__EXTERNAL_HTTPS_PORT__/${port}/g" /etc/apache2/sites-available/000-default.conf

exec "$@"
