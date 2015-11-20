# Fake infos from nginx
local cookie = "9qubasgs56uj9oirioap7vieq5"
local albumid = 28

local driver = require "luasql.postgres"
local env = driver.postgres()
local conn = env:connect("jaccob", "jaccob", "jaccob")

local cur = conn:execute(string.format("select 1 as win from external_session_acl where id_album = %d and id_session = '%s'", conn:escape(albumid), conn:escape(cookie)))

local row = cur:fetch({}, "a")
while row do
    print(string.format("%s", row.win))
    row = cur:fetch(row, "a")
end

