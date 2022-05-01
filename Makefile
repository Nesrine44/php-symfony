.PHONY: run stop stop-dev start down db

run:
	docker-compose up -d
	docker-compose exec php7 chown -R www-data:www-data app/cache && rm -rf app/cache/*
	#docker-compose exec php7 chown -R www-data:www-data app/logs
	docker-compose exec php7 php bin/console doctrine:schema:update --force 2>/dev/null; true
	docker-compose exec php7 php bin/console cache:clear 2>/dev/null; true
	docker-compose exec php7 php bin/console doctrine:fixture:load
stop:
	docker-compose stop
stop-dev:
	docker-compose -f docker-compose.dev.yml stop
start:
	docker-compose up -d
down:
	docker-compose down

db:
	docker-compose exec db mysql -upernod_u -p"1pipo2" pernod_db
