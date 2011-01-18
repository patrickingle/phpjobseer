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

/**
 *
 * DAO Base Class
 *
 * In order to use this base class, you must implement the following methods:
 *
 *     abstract public function getDefaults() ;
 *     abstract static public function getDDInfo( $tableName, $dbStyle ) ;
 *     abstract public function populateFields( $fieldValues ) ;
 *     abstract public function validateRowForInsert( $rowValues ) ;
 *     abstract public function validateRowForUpdate( $rowValues ) ;
 *
 * @author kbenton
 *
 */
abstract class DaoBase {
    /**
     * @var Object Database reference pointer
     */
    protected $_oDbh = null;

    /**
     * @var String Name of table this DAO serves
     */
    protected $_tableName = null;

    /**
     * @var array Hash of FieldDescription objects
     */
    protected $_fields = null;

    /**
     * @var array Hash of configuration keys to values
     */
    protected $_configValues = null;

    /**
     * @var unknown Session DB handle
     */
    private $_sth = null;

    /**
     * getDefaults acts like DaoBase::getRowById returning a hash of fields to
     * column values to be used by the insertRow routine to compare values with
     * for default values at row insertion time.
     *
     * @return array Default values for new records
     */
    abstract public function getDefaults();

    /**
     * static function that creates a new DDInfo record and returns it set up
     * for the concrete class.
     * @param $dbName Name of the table
     * @param $dbStyle Style of database to create
     * @return DDInfo
     */
    abstract static public function getDDInfo( $tableName, $dbStyle ) ;

    /**
     * populateFields creates an array of FieldDescription's.  This function is
     * called by getRowById to fulfill data requests.
     *
     * @param array $fieldValues A hash of field values by field names.
     * @return void
     */
    abstract public function populateFields($fieldValues);

    /**
     * validateRowForInsert checks to make sure that data being inserted is valid.
     *
     * @return boolean True when validation passes, false otherwise.
     */
    abstract public function validateRowForInsert($rowValues);

    /**
     * validateRowForUpdate checks to make sure that data being updated is valid.
     *
     * @return boolean True when validation passes, false otherwise.
     */
    abstract public function validateRowForUpdate($rowValues);

    /**
     * Class constructor - do not set $reuse except from within this class.
     *
     * @param String  $tableName Name of the table covered
     * @return void
     */
    public function __construct( $tableName ) {
        $oConfig = new Config();
        $this->_configValues = $oConfig->values;
        $this->_oDbh = new mysqli( $this->_configValues['db_host']
                                 , $this->_configValues['db_user']
                                 , $this->_configValues['db_pass']
                                 , $this->_configValues['db_name']
                                 , $this->_configValues['db_port']
                                 );
        $this->_sth = null;
        $this->_tableName = $tableName;
    }

    /**
     * Class destructor
     */
    public function __destruct() {
        // do nothing for now
    }

    /**
     * Execute commit on DB
     *
     * @return boolean Was the commit successful?
     */
    public function commit() {
        if ( ! $runQuery
          || ! $this->_configValues['really_update_db'] ) {
              return true;
        }
        return $this->_oDbh->commit();
    }

    /**
     * Execute rollback on DB
     *
     * @param $savepoint string Optional name of savepoint
     * @return boolean Was the rollback successful?
     */
    public function rollback($savePoint = null) {
        if ( ! $runQuery
          || ! $this->_configValues['really_update_db'] ) {
              return true;
        }
        return $this->_oDbh->rollback();
    }

    /**
     * @return array Hash of FieldDescription objects
     */
    public function getFields() {
        return $this->_fields;
    }


    /**
     * Return an escaped string compatible for this database.
     * @param String Value to escape
     */
    public function escape_string( $str ) {
        return $this->_oDbh->real_escape_string( $str ) ;
    }

    /**
     * insertRow should probably be overridden. The intent of this method is to
     * provide a generalized method of inserting a row into a "generic" table.
     *
     * @param array $rowValues A hash of column names as keys with the
     * corresponding values mapped back to those keys.
     * @return int Last Insert ID on success - throws on failure
     */
    public function insertRow($rowValues) {
        $insertId = null ;
        if ( $this->validateRowForInsert( $rowValues ) ) {
            $defaults = $this->getDefaults() ;
            $query = "INSERT {$this->_tableName} SET " ;
            $runQuery = 0 ;
            $changes = array() ;
            $fieldsByFieldName = array() ;
            $this->populateFields( $rowValues ) ;
            foreach ( $this->_fields as $field ) {
                $fieldsByFieldName[$field->getFieldName()]=$field ;
            }
            foreach ( $rowValues as $key => $value ) {
                if ( ( "created" === $key ) || ( "updated" === $key ) ) {
                    continue ;
                }
                $defaultValue = ( null === $defaults[$key] ? '' : $defaults[$key] ) ;
                if ( $value === $defaultValue ) {
                    continue ;
                }
                $quot = $fieldsByFieldName[ $key ]->getQuote() ;
                $changes[] = "$key = $quot" . $this->escape_string( $value ) . "$quot" ;
                $runQuery = 1 ;
            }
            if ( $runQuery ) {
                $changes[] = 'created = NOW()' ;
                $query .= implode( ', ', $changes ) ;
                if ( $this->_configValues[ 'really_update_db' ] ) {
                    if ( TRUE !== $this->_oDbh->query( $query ) ) {
                        throw new Exception( "Query failed: $query" ) ;
                    }
                    $insertId = $this->_oDbh->insert_id ;
                }
            }
        }
        else {
            Tools::dump_var( 'rowValues', $rowValues ) ;
            Tools::dump_var( 'this', $this ) ;
            echo "<pre>" ;
            throw new Exception( "Row failed validation for insert!" ) ;
        }
        return $insertId ;
    }

    /**
     * updateRowById should probably be overridden.  Like getRowById, the intent
     * is to provide a helper method that will update a row primarily by the ID
     * value in the table.
     *
     * @param int $id The row ID to be updated
     * @param array $rowValues A hash of column names as columns to values.
     * @return boolean Failure on success, false otherwise
     */
    public function updateRowById($id, $rowValues) {
        if ($this->validateRowForUpdate($rowValues)) {
            $oldValues = $this->getRowById($id);
            $query = "UPDATE {$this->_tableName} SET ";
            $runQuery = 0;
            $changes = array();
            $fieldsByFieldName = array();
            foreach ( $this->_fields as $field ) {
                $fieldsByFieldName[$field->getFieldName()]=$field;
                $fieldTypesByFieldName[$field->getFieldName()]=$field->getDataType();
            }
            foreach ( $rowValues as $key => $value ) {
                // Always skip created and updated because these are maintained by the DB
                if ( ("created" === $key) || ("updated" === $key) ) {
                    continue;
                }
                $oldValue = ( null === $oldValues[$key] ) ? '' : $oldValues[$key];
                if ( preg_match( '/^REFERENCE\(.+\)$/'
                               , $fieldTypesByFieldName[$key]
                               )
                   ) {
                    if ( '' === $oldValues[$key] ) {
                        $oldValue = '0';
                    }
                    if ( '' === $value ) {
                        $value = '0';
                    }
                }
                if ( $value === $oldValue ) {
                    continue;
                }
                $quot = $fieldsByFieldName[$key]->getQuote();
                $changes[] = "$key = $quot" . $this->escape_string($value) . "$quot";
                $runQuery = 1;
            }
            if ($runQuery) {
                $query .= implode(', ', $changes);
                $query .= " WHERE {$this->_tableName}Id = " . $this->escape_string($id);
                return $this->_oDbh->query($query);
            }
            else {
                echo "No changes found.<br />\n";
                return true;
            }
        }
        return false;
    }

    /**
     * deleteRowById should probably be overridden. Like getRowById, the intent
     * is to provide a helper method that will remove a row by an ID value in
     * the table.
     *
     * @param int $id The row ID to be deleted
     * @return void
     */
    public function deleteRowById($id) {
        if (self::validateRowId($id)) {
            $query = "SELECT *" .
                      " FROM {$this->_tableName}" .
                     " WHERE {$this->_tableName}Id = " . $this->escape_string($id);
            $result = $this->_oDbh->query($query);
            return;
        }
    }

    /**
     * getRowById should probably be overridden. The intent is to provide a
     * method that will return a row by the ID (primary key) of this table.
     * There should also be other ways to limit returned results through other
     * functions in concrete classes.
     *
     * @param int $id The row ID to be located
     * @return array The hash of column names to corresponding values.
     */
    public function getRowById($id) {
        if (self::validateRowId($id)) {
            $results = $this->findSome("{$this->_tableName}Id = " . $this->escape_string($id));
            return $results[0];
        }
    }


    /**
     * findAll should probably be overridden.  The intent is to provide a method
     * that will initiate a search of all rows for the given table.  There
     * should also be a way to get rows back using other search criteria to
     * limit the amount of data flowing from the server to the client.
     *
     * @return array|null
     */
    public function findAll() {
        return $this->findSome("1 = 1");
    }

    /**
     * findSome should probably be overridden.  The intent is to provide a
     * method that will return the a pointer that will allow getFirstRow(),
     * getNextRow(), and hasMoreData() to function.
     *
     * @param String $restrictions will be used to create a WHERE clause. This
     * string may not be empty.
     * @return array Pointer that will be used by getFirstRow(), getNextRow()
     * and hasMoreData().
     */
    public function findSome($restrictions) {
        $query = "SELECT * FROM {$this->_tableName} WHERE $restrictions";
        $this->_sth = $this->_oDbh->query($query);
        $results = array();
        if ( ! $this->_sth ) {
            return $results; // no data available.
        }
        while ($row = $this->_sth->fetch_assoc()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * countAll should probably be overridden.  This function returns the count
     * of rows in the table.
     *
     * @return integer
     */
    public function countAll() {
        return $this->countSome("1 = 1");
    }

    /**
     * countSome should probably be overridden.  This function returns the count
     * of rows in the table.
     *
     * @param String $restrictions
     * @return integer
     */
    public function countSome($restrictions = "1 = 1") {
        $count = 0 ;
        $query = "SELECT COUNT(1) AS cnt FROM {$this->_tableName}"
               . " WHERE $restrictions" ;
        if ( 1 === $this->_configValues['debug_mode'] ) {
        	Tools::dump_var("query", $query) ;
        }
        try {
            $this->_sth = $this->_oDbh->query($query) ;
            $result = $this->_sth->fetch_row() ;
        }
        catch ( Exception $e ) {
            Tools::quickBackTrace() ;
            print "Uncaught exception: $e\n" ;
            throw $e ;
        }
        $cnt = $result[0] ;
        $this->_sth->free() ;
        return $cnt ;
    }

    /**
     * validateRowId should probably be overridden. The intent is to provide a
     * method that validates a row ID against the expected format for the row of
     * "this" table. ID 0 is valid as a replacement for null.
     *
     * @param int $id The row ID to be validated
     * @return boolean
     */
    static public function validateRowId($id) {
        return preg_match('/^[0-9]+$/', $id);
    }

    /**
     * Returns true if the format of a DateTime variable looks good, false
     * otherwise.
     *
     * @todo Fix for limitations imposed by MySQL
     * @param String $dt
     * @return boolean
     */
    static public function validateDateTime($dt) {
        $haystack = '/[12][0-9][0-9][0-9]-[01][0-9]-[0-3][0-9] [012][0-9]:[0-5][0-9]:[0-5][0-9]/';
        return preg_match($haystack, $dt);
    }

    /**
     * Returns true if the format of a Timestamp variable looks good, false
     * otherwise.
     *
     * @todo Fix for limitations imposed by MySQL
     * @param String $dt
     * @return boolean
     */
    static public function validateTimestamp($dt) {
        return self::validateDateTime($dt); // The two look the same for now
    }


}
