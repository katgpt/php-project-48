install: 
	composer install
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests
test:
	composer exec --verbose phpunit tests
gendiff-stylish:
	./bin/gendiff --format stylish tests/fixtures/file1.json tests/fixtures/file2.json

gendiff-plain:
	./bin/gendiff --format plain tests/fixtures/file1.json tests/fixtures/file2.json

gendiff-json:
	./bin/gendiff --format json tests/fixtures/file1.json tests/fixtures/file2.json