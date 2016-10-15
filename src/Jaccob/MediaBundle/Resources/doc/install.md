# Install

postgresql/redis
  debian:
    apt-get install postgresql
    apt-get install redis-server php5-redis

external tools
  debian
    apt-get install imagemagick ffmpeg

ngxin/lua
  debian:
    apt-get install nginx-extras lua5.1 lua-sql-postgres lua-redis
    (lua5.2 should also work)
  missing sample configuration file

mkdir -p web/media/th
mkdir -p app/logs

composer
cache clear/warmup
assetic asset warmup
