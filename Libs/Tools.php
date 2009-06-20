<?php
/**
 * phpjobseeker
 *
 * Copyright (C) 2009 Kevin Benton - kbenton at bentonfam dot org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
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