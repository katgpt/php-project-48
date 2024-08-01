install: 
	composer install

validate:
	composer validate

dump:
	composer dump-autoload

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 --colors src bin

test:
	composer exec --verbose phpunit tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

gendiff-stylish:
	./bin/gendiff --format stylish tests/fixtures/file1.json tests/fixtures/file2.json

gendiff-plain:
	./bin/gendiff --format plain tests/fixtures/file1.json tests/fixtures/file2.json

gendiff-json:
	./bin/gendiff --format json tests/fixtures/file1.json tests/fixtures/file2.json