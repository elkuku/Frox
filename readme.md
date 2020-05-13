# FroX

Maxfield helper.

## Install

* clone `https://github.com/tvwenger/maxfield`
* Setup env vars in `.env.local`
* `composer install`
* `npm install`
* `npm run dev`
* `bin/go.sh`

### Docker MySQL Restore

`cat backup.sql | docker exec -i CONTAINER /usr/bin/mysql -u main --password=main main`
