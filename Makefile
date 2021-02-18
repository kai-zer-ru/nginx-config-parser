
.PHONY: test clean coverage

vendor: composer.json
	composer install --dev

build:
	mkdir -p tests/$@

test: vendor build
	./vendor/bin/phpunit

coverage: vendor build
	mkdir -p reports
	./vendor/bin/phpunit --coverage-html reports/

clean:
	rm -rf vendor
	rm -rf reports
	rm -rf build
