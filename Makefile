install: 
	composer install
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests
gendiff:
	./bin/gendiff file1.json file2.json