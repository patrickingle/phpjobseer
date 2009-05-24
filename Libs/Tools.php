<?php
/**
 * Created on May 17, 2009 by kbenton
 *
 */

class Tools {

    /**
     * dump_var is like var_dump, but it adds a wrapper and a label
     *
     * @param String $label Label to display before var_dump();
     * @param mixed $var Variable to pass to var_dump
     * @return void
     */
    static public function dump_var($label, $var) {
        echo "<hr />"
           . "Start of $label dump"
           . "<br />"
           . "<pre>";
        var_dump($var);
        echo "</pre>"
           . "End of $label dump<br />"
           . "<hr />"
           . "Start of $label backtrace"
           . "<br /><pre>";
        debug_print_backtrace();
        echo "</pre>"
           . "End of $label backtrace"
           . "<br />";
    }

    /**
     * getDateFromPost re-assembles a MySQL compatible date from a POST
     * parameter as assembled by QuickForm.
     *
     * @param String parameterName
     */
    static public function getDateFromPost($parameterName) {
        return sprintf( "%04d-%02d-%02d %02d:%02d:00"
                      , $_POST[$parameterName]['Y']
                      , $_POST[$parameterName]['M']
                      , $_POST[$parameterName]['d']
                      , $_POST[$parameterName]['H']
                      , $_POST[$parameterName]['i']
                      );
    }

}