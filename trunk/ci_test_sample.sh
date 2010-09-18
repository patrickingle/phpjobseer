#!/bin/bash

build_pjs() {
    export out="`mktemp`"
    trap "rm -f \"$out\" ; exit" 1 2 15

    echo "New version of PJS detected." >> "$out"
    echo >> "$out"
    echo "SVN Log" >> "$out"
    svn log -r HEAD >> "$out"
    echo >> "$out"
    echo "SVN Changes" >> "$out"
    svn up -r HEAD >> "$out"
    echo >> "$out"
    if ./run_all_tests.sh --verbose >> $out ; then
        mail -s "PJS Build Passed" $NOTIFY < "$out"
    else
        mail -s "PJS Build FAILED" $NOTIFY < "$out"
    fi
    rm -f "$out"
}

export NOTIFY="ME"
export PATH="/home/ME/bin:/usr/local/bin:/usr/bin:/bin"
# verify that this is a new check-in
currentrev="`svn st -u | head -1 | grep '^Status against revision:'`"
if [ -z "$currentrev" ] ; then
    build_pjs "$NOTIFY"
elif [ -f .force_build_once ] ; then
    rm .force_build_once
    biuld_pjs "$NOTIFY"
fi
