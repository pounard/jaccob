--
-- Add to your nginx configuration:
--
--     location ~ ^/media/th/.* {
--         access_by_lua_file /DOCROOT/src/Jaccob/MediaBundle/Resources/nginx/auth-psql.lua
--     }
--
-- This requires that:
--   * the jaccob_media.security.external service is set to use the
--     Jaccob\MediaBundle\Security\External\PommSessionAclManager class.
--   * the jaccob_media.path_builder service is set to use the
--     Jaccob\MediaBundle\Util\SimplePathBuilder
--

-- Fake infos from nginx for testing purpose, comment those two then uncomment
-- the next two for the real environement
-- local cookie = "9qubasgs56uj9oirioap7vieq5"
-- local path = "/media/th/w1440/2015/11/29/IMG_1176.JPG";
local path = nginx_uri
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
local driver = require "luasql.postgres"
local env = driver.postgres()
local conn = env:connect("jaccob", "jaccob", "jaccob")
local cur = conn:execute(
    string.format(
        "select 1 as win from external_session_acl where id_album = %d and id_session = '%s'",
        conn:escape(albumid),
        conn:escape(cookie)
    )
)

local row = cur:fetch({}, "a")

if row and row.win then
    return
else
    ngx.exit(ngx.HTTP_FORBIDDEN)
end
