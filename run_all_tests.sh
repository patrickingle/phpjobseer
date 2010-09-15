#!/bin/sh
testfile="Tests/AllTests.php"
phpunit --syntax-check $testfile && phpunit $testfile
exit $?

