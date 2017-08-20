PHP_BIN ?= php

.SILENT:

build:
	composer install

run:
	php bin/console github:notifications:list