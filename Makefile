all: clean test coverage

clean:
	rm -rf build/artifacts/*

test:
	phpunit --testsuite=lock $(TEST)

coverage:
	phpunit --testsuite=lock --coverage-html=build/artifacts/coverage $(TEST)

coverage-show:
	open build/artifacts/coverage/index.html
