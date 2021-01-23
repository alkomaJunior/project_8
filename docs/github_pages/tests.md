# Tests

[Back to summary](../index.md)

## PHPUnit
>You find unit & functional tests in the folder ``./tests/PHPUnit``.

```shell
# Run all tests of the app
./bin/phpunit

# Run tests for one class (replace CLASS_NAME with the name of class you want test)
./bin/phpunit --filter CLASS_NAME

# Run all tests & generate its code-coverage
./bin/phpunit --coverage-html OUTPUT_PATH
```

## Behat
>You find BDD tests in the folder ``./features``.

```shell
# Run all tests of the app
./vendor/bin/behat

# Run only one test (replace TAGS_NAME with the name of the tag you want test)
./vendor/bin/behat.bat --tags=TAGS_NAME
```
