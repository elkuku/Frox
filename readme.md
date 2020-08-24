# FroX

Maxfield helper.

## Install

* clone `https://github.com/tvwenger/maxfield` and run the setup. Supported are V2 and V3+ of MaxField
* Setup env vars in `.env.local`<br>
Set path to maxfield as `MAXFIELDS_EXEC` use the full path for maxfield V 2 and `maxfield-plan` for V 3+
* `composer install`
* `yarn install`
* `yarn dev`
* `bin/start`

This requires Docker, Docker compose and the Symfony binary installed.

### Docker MySQL Restore

`cat backup.sql | docker exec -i CONTAINER /usr/bin/mysql -u main --password=main main`
