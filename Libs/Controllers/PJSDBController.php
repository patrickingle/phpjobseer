<?php

/**
 *
 * phpjobseeker
 *
 * Copyright (C) 2010 Kevin Benton - kbenton at bentonfam dot org
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

class PJSDBController {

    /** @var PJSDbModel */
    private $_fullModel ;

    /**
     *
     * Check a table for the appropriate configuration
     * @todo FINISH THIS
     */
    public function checkTable() {
        // Do nothing for now.
    }

    /**
     *
     * Check a database for the appropriate configuration
     * @todo FINISH THIS
     */
    function checkDb() {
        echo "Made it here.\n" ;
        exit ;
        foreach ( $this->_fullModel as $model ) {
            Tools::dump_var( 'model', $model ) ;
        }
    }

    /**
     * Class Constructor
     * @todo FINISH THIS
     */
    public function __construct() {
        $this->_fullModel = new PJSDBModel() ;
    }

    /**
     *
     * Return string representation of this class
     * @return String
     * @todo FINISH THIS
     */
    public function __toString() {
        // Do nothing meaningful for now
        return __CLASS__ . '.__toString() not implemented.' ;
    }

    /**
     * Class Destructor
     *
     */
    public function __destruct() {
        // Do nothing for now
    }
}