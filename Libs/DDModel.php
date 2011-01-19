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

require_once("Libs/autoload.php");

class DDModel {

	/** @var mixed DDInfo List */
	private $_ddInfoList ;

    /** @var String Name of database installed */
    private $_dbName ;

	/** @var string DB Style */
	private $_dbStyle ;

	/**
	 *
	 * Class Constructor
	 *
	 * @param String $dbName
	 * @param String $dbStyle
     * @wish to make this handle DB2 but requires DDInfo.php support first
     * @wish to make this handle Oracle but requires DDInfo.php support first
     * @wish to make this handle Postgres but requires DDInfo.php support first
     * @wish to make this handle SQLite but requires DDInfo.php support first
	 */
    public function __construct( $dbName, $dbStyle = 'MySQL' ) {
        switch ( $dbStyle ) {
            case 'MySQL'    : break ;
            case 'DB2'      : // NO BREAK HERE UNTIL IMPLEMENTED
            case 'Postgres' : // NO BREAK HERE UNTIL IMPLEMENTED
            case 'Oracle'   : // NO BREAK HERE UNTIL IMPLEMENTED
            case 'SQLite'   : // NO BREAK HERE UNTIL IMPLEMENTED
            default         : throw new Exception( 'Invalid dbStyle' ) ;
                              break ;
        }
        $this->_dbStyle    = $dbStyle ;
		$this->_ddInfoList = array() ;
	}

    /**
     *
     * Return string representation of this class
     * @return String
     * @wish Make this __toString useful
     */
    public function __toString() {
        // Do nothing meaningful for now
        return __CLASS__ . '.__toString() not implemented.' ;
    }

    /**
     * Add a DDInfo table to this model
     * @param DDInfo $info
     */
	public function addInfo( $info ) {
	    if ( $info instanceof DDInfo ) {
			$this->_ddInfoList[] = $info ;
		}
	}

    /**
     * Getter for $_dbName
     *
     * @return String
     */
    public function getDbName() {
        return ( $this->_dbName ) ;
    }

    /**
     * Setter for $_dbName
     *
     * @param String
     * @return void
     */
    public function setDbName( $value ) {
        $this->_dbName = $value ;
    }

} // END OF DDModel
