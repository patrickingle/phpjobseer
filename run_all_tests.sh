#!/bin/sh
# phpjobseeker
#
# Copyright (C) 2009 Kevin Benton - kbenton at bentonfam dot org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
# 
testfile="Tests/AllTests.php"
verbose="/bin/false"
verboseparam=""
if [ "--verbose" = "$1" ] ; then
    verbose="/bin/true"
    verboseparam="$1"
fi
for i in `find . -name \*.php -print`
do
    err="`php -l "$i" 2>&1 | grep -v \"^No syntax errors detected in $i\$\"`"
    if [ ! -z "$err" ] ; then
        echo "Syntax error detected in $i: $err"
        exit 1
    elif $verbose ; then
        echo "Syntax tests passed for $i" | strings
    fi
done
echo "Code appears to be free from syntax errors."

if [ -n "`php index.php 2>&1 | grep -i 'fatal '`" ] ; then
    echo "Index produces fatal error(s)"
    exit 1
fi

phpunit $verboseparam $testfile 2>&1
exit $?
