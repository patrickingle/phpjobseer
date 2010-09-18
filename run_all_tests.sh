#!/bin/sh

testfile="Tests/AllTests.php"
for i in `find . -name \*.php -print`
do
    err="`php -l "$i" | grep -v \"^No syntax errors detected in $i\$\"`"
    if [ ! -z "$err" ] ; then
        echo "Syntax error detected in $i: $err"
        exit 1
    fi
done
echo "Code appears to be free from syntax errors."

/usr/local/bin/phpunit $testfile
exit $?
