#

.PHONY: test

test:
	composer install
	./vendor/bin/phpunit tests/*
