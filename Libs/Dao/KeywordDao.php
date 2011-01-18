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

class KeywordDao extends DaoBase {

    /**
     * validateRowForInsertOrUpdate does all the "other" checks needed to verify
     * a row is valid for insert/update besides whether or not the row ID is
     * present or not.
     */
    public function validateRowForInsertOrUpdate($rowValues) {
        return ( isset($rowValues)
              && isset($rowValues['keywordValue'])
              && isset($rowValues['sortKey'])
              && is_numeric($rowValues['sortKey'])
              && ( $rowValues['sortKey'] >= 0 )
              && ( $rowValues['sortKey'] < 1000 )
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
              && (!isset($rowValues['keywordId']))
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
              && (isset($rowValues['keywordId']))
              && self::validateRowForInsertOrUpdate($rowValues)
               );
    }

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct($fieldValues = null) {
        parent::__construct('keyword');
        $this->populateFields($fieldValues);
    }

    /**
     * static function that creates a new DDInfo record and returns it set up
     * for the concrete class.
     * @param $dbName Name of the table
     * @param $dbStyle Style of database to create
     * @return DDInfo
     */
    static public function getDDInfo($tableName, $dbStyle) {
        $info = new DDInfo($tableName, $dbStyle) ;
        $info->addColumn( 'keywordId'
                        , 'SERIAL'
                        , false
                        ) ;
        $info->addColumn( 'keywordValue'
                        , 'VARCHAR(255)'
                        , false
                        , null
                        ) ;
        $info->addColumn( 'sortKey'
                        , 'SMALLINT(3)'
                        , false
                        , 0
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
                     , 'keywordPk'
                     , array( 'keywordId' )
                     ) ;
        $info->addKey( 'UNIQUE'
                     , 'valueUx'
                     , array( 'keywordValue' )
                     ) ;
        $info->addTrigger( 'keywordAfterUpdateTrigger'
                         , 'AFTER'
                         , 'UPDATE'
                         . "  IF OLD.keywordId <> NEW.keywordId\n"
                         . "THEN\n"
                         . "     UPDATE note\n"
                         . "        SET note.appliesToId = NEW.keywordId\n"
                         . "      WHERE note.appliesToId = OLD.keywordId\n"
                         . "        AND note.appliestoTable = 'keyword'\n"
                         . "          ;\n"
                         . " END IF ;\n"
                         ) ;
        $info->addTrigger( 'keywordAfterDeleteTrigger'
                         , 'AFTER'
                         , 'DELETE'
                         . "DELETE\n"
                         . "  FROM note\n"
                         . " WHERE note.appliesToId = OLD.keywordId\n"
                         . "   AND note.appliestoTable = 'keyword'\n"
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
        return array( 'keywordId' => ''
                    , 'keywordValue' => ''
                    , 'sortKey' => 100
                    , 'created' => ''
                    , 'updated' => ''
                    );
    }

    /**
     * populateFields creates an array of FieldDescription's.  This function is
     * called by getRowById to fulfill data requests.
     *
     * @param array $fieldValues A hash of field values by field names.
     * @return void
     */
    public function populateFields($fieldValues) {
        $_fieldDescriptions = array();

        $x = new FieldDescription();
        $y = isset($fieldValues['keywordId']) ? $fieldValues['keywordId'] : null;
        $x->setAllFields( 'keywordId'           // $fieldName
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
        $y = isset($fieldValues['keywordValue']) ? $fieldValues['keywordValue'] : null;
        $x->setAllFields( 'keywordValue'        // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Keyword'             // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['sortKey']) ? $fieldValues['sortKey'] : null;
        $x->setAllFields( 'sortKey'             // $fieldName
                        , $y                    // $fieldValue
                        , 'SMALLINT(3)'         // $dataType
                        , 3                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Sort Key'            // $fieldLabel
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

}