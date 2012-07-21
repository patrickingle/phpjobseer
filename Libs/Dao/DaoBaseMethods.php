<?php

/**
  * Data Access Objects Base Methods Interface
  *
  */

require_once "Libs/autoload.php" ;

interface DaoBaseMethods {
	/**
	* getDefaults acts like DaoBase::getRowById returning a hash of fields to
	* column values to be used by the insertRow routine to compare values with
	* for default values at row insertion time.
	*
	* @return array Default values for new records
	*/
	public function getDefaults() ;

	/**
	* static function that creates a new DDInfo record and returns it set up
	* for the concrete class.
	* @param $dbName Name of the table
	* @param $dbStyle Style of database to create
	* @return DDInfo
	*/
	static public function getDDInfo( $tableName, $dbStyle ) ;


    /**
     * populateFields creates an array of FieldDescription's.  This function is
     * called by getRowById to fulfill data requests.
     *
     * @param array $fieldValues A hash of field values by field names.
     * @return void
     */
    public function populateFields($fieldValues) ;

    /**
     * validateRowForInsert checks to make sure that data being inserted is valid.
     *
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForInsert($rowValues) ;

    /**
     * validateRowForUpdate checks to make sure that data being updated is valid.
     *
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForUpdate($rowValues) ;


    /**
     * Execute commit on DB
     *
     * @return boolean Was the commit successful?
     */
    public function commit() ;


    /**
     * Execute rollback on DB
     *
     * @param $savepoint string Optional name of savepoint
     * @return boolean Was the rollback successful?
     */
    public function rollback( $savePoint = null ) ;

}
