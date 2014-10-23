
test:
	@"./vendor/bin/phpunit" -c phpunit.xml tests

test-cov-cli:
	@"./vendor/bin/phpunit" -c phpunit.xml  --coverage-text tests

test-cov-html:
	@"./vendor/bin/phpunit" -c phpunit.xml  --coverage-html reports tests
