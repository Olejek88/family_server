local redis = require "nginx.redis"
local red = redis:new()

--noinspection UnusedDef
local ok, err = red:connect("127.0.0.1", 6379)
if not ok then
    ngx.exit(ngx.HTTP_INTERNAL_SERVER_ERROR)
end

red:select(ngx.var.redisDb)

--noinspection UnusedDef
local res, err = red:get(ngx.var.userName)
if res == ngx.null then
    ngx.exit(ngx.HTTP_NOT_FOUND)
end

local result = '/files/' .. res .. '/' .. ngx.var.filePath
ngx.req.set_uri(result, true);
