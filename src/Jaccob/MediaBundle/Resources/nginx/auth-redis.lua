--
-- Add to your nginx configuration:
--
--     location ~ ^/media/th/.* {
--         access_by_lua_file /DOCROOT/src/Jaccob/MediaBundle/Resources/nginx/auth-redis.lua
--     }
--
-- This requires that:
--
--   * the jaccob_media.security.external service is set to use the
--     Jaccob\MediaBundle\Security\External\RedisSessionAclManager class.
--
--   * the jaccob_media.path_builder service is set to use the
--     Jaccob\MediaBundle\Util\SimplePathBuilder
--

-- Fake infos from nginx for testing purpose, comment those two then uncomment
-- the next two for the real environement
-- local cookie = "vuldhtq1tc7802gvvpcjveb565"
-- local path = "/media/th/w1440/2015/11/1/IMG_1176.JPG";
local path = ngx.var.uri
local cookie = ngx.var.cookie_PHPSESSID

-- Derivate album from the URI
-- God LUA seems not really helpful with strings
local args = string.gmatch(path, "[^/]+")
local index = 0
local albumid
for arg in args do
    if index == 5 then
        albumid = arg
        break
    end
    index = index + 1
end

-- Connect to postgres and fetch album status for session
local redis = require 'redis'
-- @todo Variabilise this
local client = redis.connect('127.0.0.1', 6379)
client:select(7)
local result = client:get(cookie .. ":" .. albumid)

if result then
    return
else
    ngx.exit(ngx.HTTP_FORBIDDEN)
end
