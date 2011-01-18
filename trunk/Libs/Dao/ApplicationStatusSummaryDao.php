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

class ApplicationStatusSummaryDao extends DaoBase {

    /**
     * static function that creates a new DDInfo record and returns it set up
     * for the concrete class.
     * @param $dbName Name of the table
     * @param $dbStyle Style of database to create
     * @return DDInfo
     */
    static public function getDDInfo( $tableName, $dbStyle ) {
        $info = new DDInfo( $tableName, $dbStyle ) ;
        $info->addColumn( 'applicationStatusId'
                        , 'INT'
                        , false
                        , null
                        , array( 'unsigned' => 1 )
                        ) ;
        $info->addColumn( 'statusCount'
                        , 'INT'
                        , false, 0
                        , array( 'unsigned' => 1 )
                        ) ;
        $info->addColumn( 'created'
                        , 'TIMESTAMP'
                        , false
                        , '0000-00-00 00:00:00'
                        ) ;
        $info->addColumn( 'updated'
                        , 'TIMESTAMP'
                        , false
                        , 'CURENT_TIMESTAMP'
                        , 'ON UPDATE CURRENT_TIMESTAMP'
                        ) ;
        $info->addKey( 'PRIMARY'
                     , 'applicationStatusSummaryPk'
                     , array( 'applicationStatusId' )
                     ) ;
        $info->addKey( 'FOREIGN'
                     , 'applicationStatusFk'
                     , array( 'applicationStatusId' )
                     , array( 'references' => 'applicationStatus(applicationStatusId)'
                            , 'onDelete' => 'CASCADE'
                            , 'onUpdate' => 'CASCADE'
                            )
                     ) ;

        return $info ;
    }

    /**
     * getDefaults acts like DaoBase::getRowById returning a hash of fields to
     * column values to be used by the insertRow routine to compare values with
     * for default values at row insertion time.
     *
     * @return array Default values for new records
     */
    public function getDefaults() {
        return array( 'applicationStatusId' => ''
                    , 'statusCount' => 0
                    , 'created' => ''
                    , 'updated' => ''
                    );
    }

    /**
     * populateFields
     *
     * @return void
     */
    public function populateFields($fieldValues) {
        $_fieldDescriptions = array();

        $x = new FieldDescription();
        $y = isset($fieldValues['applicationStatusId']) ? $fieldValues['applicationStatusId'] : null;
        $x->setAllFields( 'applicationStatusId' // $fieldName
                        , $y                    // $fieldValue
                        , 'INTEGER UNSIGNED'    // $dataType
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Id'                  // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['statusCount']) ? $fieldValues['statusCount'] : null;
        $x->setAllFields( 'statusCount'         // $fieldName
                        , $y                    // $fieldValue
                        , 'INTEGER UNSIGNED'    // $dataType
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Count'               // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['created']) ? $fieldValues['created'] : null;
        $x->setAllFields( 'created'             // $fieldName
                        , $y                    // $fieldValue
                        , 'TIMESTAMP'           // $dataType
                        , 999                   // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Created'             // $fieldLabel
                        , '\''                  // $quote
                        , 'When was this record created?'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['updated']) ? $fieldValues['updated'] : null;
        $x->setAllFields( 'updated'             // $fieldName
                        , $y                    // $fieldValue
                        , 'TIMESTAMP'           // $dataType
                        , 999                   // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Updated'             // $fieldLabel
                        , '\''                  // $quote
                        , 'When was this record last updated?'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

    }

    /**
     * validateRowForInsertOrUpdate does all the "other" checks needed to verify
     * a row is valid for insert/update besides whether or not the row ID is
     * present or not.
     */
    public function validateRowForInsertOrUpdate($rowValues) {
        return ( false );
    }

    /**
     * validateRowForInsert checks to make sure that data being inserted is valid.
     *
     * @param array $rowValues Hash of row keys / values to be checked
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForInsert($rowValues) {
        return ( false );
    }

    /**
     * validateRowForUpdate checks to make sure that data being updated is valid.
     *
     * @param array $rowValues Hash of row keys / values to be checked
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForUpdate($rowValues) {
        return ( false );
    }

    /**
     * Provide the application status counts for a particular status value
     * @param String $statusValue
     * @return int Count
     */
    function getApplicationStatusCount( $statusId ) {
        if ( self::validateRowId( $statusId ) ) {
            $rows = findSome(" applicationStatusId = $statusId" ) ;
            return  ( isset( $rows[0] )
                   && isset( $rows[0][0] )
                    ) ? $rows[0][0] : 0 ;
        }
        else {
            throw new Exception( "Invalid Application Status ID" ) ;
        }
    }

    /**
     * Class Constructor
     *
     */
    public function __construct() {
        parent::__construct() ;
        // Do nothing for now
    }

    /**
     *
     * Return string representation of this class
     * @return String
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

    public function insertRow( $rowValues = null ) {
        throw new Exception( "Invalid insert operation. Use the trigger." ) ;
    }

    public function updateRowById( $id = null, $rowValues = null ) {
        throw new Exception( "Invalide update operation. Use the trigger." ) ;
    }

    public function deleteRowById( $id = null ) {
        throw new Exception( "Invalid delete operation. Use the trigger." ) ;
    }
}
