CONSOLE := $(PHP) bin/console

docker-up:
	docker-compose up -d --build

docker-down:
	docker-compose down

docker-exec-php:
	docker exec -it todo-list-php-1 bash

migrate:
	$(CONSOLE) doctrine:migrations:migrate latest --no-interaction

migrations-generate:
	$(CONSOLE) doctrine:migrations:generate

migrations-status:
	$(CONSOLE) doctrine:migrations:list  --no-interaction

set-permissions-wo-sudo:
	chmod -R ug+rw .
	chmod -R a+rws var/cache var/log public/uploads

cache-prod:
	rm -rf .env.*php
	$(CONSOLE) cache:clear --env=prod

keypair:
	php bin/console lexik:jwt:generate-keypair