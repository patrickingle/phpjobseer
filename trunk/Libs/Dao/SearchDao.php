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

class SearchDao extends DaoBase {
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

    /**
     * validateRowForInsertOrUpdate does all the "other" checks needed to verify
     * a row is valid for insert/update besides whether or not the row ID is
     * present or not.
     */
    public function validateRowForInsertOrUpdate($rowValues) {
        return ( isset($rowValues)
              && isset($rowValues['engineName'])
              && isset($rowValues['searchName'])
              && isset($rowValues['url'])
               );
    }

    /**
     * validateRowForInsert checks to make sure that data being inserted is valid.
     *
     * @param array $rowValues Hash of row keys / values to be checked
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForInsert($rowValues) {
        return ( (isset($rowValues))
              && (!isset($rowValues['searchId']))
              && self::validateRowForInsertOrUpdate($rowValues)
               );
    }

    /**
     * validateRowForUpdate checks to make sure that data being updated is valid.
     *
     * @param array $rowValues Hash of row keys / values to be checked
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForUpdate($rowValues) {
        return ( (isset($rowValues))
              && (isset($rowValues['searchId']))
              && self::validateRowForInsertOrUpdate($rowValues)
               );
    }

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct() {
        parent::__construct( 'search' );
        $this->populateFields();
    }

    /**
     * static function that creates a new DDInfo record and returns it set up
     * for the concrete class.
     * @param $dbName Name of the table
     * @param $dbStyle Style of database to create
     * @return DDInfo
     */
    static public function getDDInfo( $tableName, $dbStyle ) {
        $info = new DDInfo( $tableName, $dbStyle ) ;
        $info->addColumn( 'searchId'
                        , 'SERIAL'
                        , false
                        ) ;
        $info->addColumn( 'engineName'
                        , 'VARCHAR(255)'
                        , false
                        , ''
                        ) ;
        $info->addColumn( 'searchName'
                        , 'VARCHAR(255)'
                        , false
                        , ''
                        ) ;
        $info->addColumn( 'url'
                        , 'SMALLTEXT'
                        , false
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
                     , 'searchPk'
                     , array( 'searchId' )
                     ) ;
        $info->addTrigger( 'searchAfterUpdateTrigger'
                         , 'AFTER'
                         , 'UPDATE'
                         . "  IF OLD.searchId <> NEW.searchId\n"
                         . "THEN\n"
                         . "     UPDATE note\n"
                         . "        SET note.appliesToId = NEW.searchId\n"
                         . "      WHERE note.appliesToId = OLD.searchId\n"
                         . "        AND note.appliestoTable = 'search'\n"
                         . "          ;\n"
                         . " END IF ;\n"
                         ) ;
        $info->addTrigger( 'searchAfterDeleteTrigger'
                         , 'AFTER'
                         , 'DELETE'
                         . "DELETE\n"
                         . "  FROM note\n"
                         . " WHERE note.appliesToId = OLD.searchId\n"
                         . "   AND note.appliestoTable = 'search'\n"
                         . "     ;\n"
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
        return array( 'searchId' => ''
                    , 'engineName' => ''
                    , 'searchName' => ''
                    , 'url' => ''
                    , 'created' => ''
                    , 'updated' => ''
                    );
    }

    /**
     * populateFields
     *
     * @return void
     */
    public function populateFields() {
        $_fieldDescriptions = array();

        $x = new FieldDescription();
        $y = isset($fieldValues['searchId']) ? $fieldValues['searchId'] : null;
        $x->setAllFields( 'searchId'            // $fieldName
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
        $y = isset($fieldValues['engineName']) ? $fieldValues['engineName'] : null;
        $x->setAllFields( 'engineName'          // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Engine Name'         // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['searchName']) ? $fieldValues['searchName'] : null;
        $x->setAllFields( 'searchName'            // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Search Name'         // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['url']) ? $fieldValues['url'] : null;
        $x->setAllFields( 'url'            // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(4096)'       // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'url'                 // $fieldLabel
                        , '\''                  // $quote
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

}