#!/bin/sh
testfile="Tests/AllTests.php"
/usr/local/bin/phpunit --syntax-check $testfile && phpunit $testfile
exit $?

