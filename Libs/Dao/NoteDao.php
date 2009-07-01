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

class NoteDao extends DaoBase {

    const APPLIES_TO_TABLES = '/^(company|contact|job|search)$/';

    /**
     * validateRowForInsertOrUpdate does all the "other" checks needed to verify
     * a row is valid for insert/update besides whether or not the row ID is
     * present or not.
     */
    public function validateRowForInsertOrUpdate($rowValues) {
        return ( isset($rowValues)
              && isset($rowValues['appliesToTable'])
              && preg_match(self::APPLIES_TO_TABLES, $rowValues['appliesToTable'])
              && isset($rowValues['appliesToId'])
              && preg_match('/^[1-9][0-9]*$/', $rowValues['appliesToId'])
              && isset($rowValues['note'])
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
              && (!isset($rowValues['noteId']))
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
              && (isset($rowValues['noteId']))
              && self::validateRowForInsertOrUpdate($rowValues)
               );
    }

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('note');
        $this->populateFields(null);
    }

    /**
     * getDefaults acts like DaoBase::getRowById returning a hash of fields to
     * column values to be used by the insertRow routine to compare values with
     * for default values at row insertion time.
     *
     * @return array Default values for new records
     */
    public function getDefaults() {
        return array( 'noteId' => ''
                    , 'appliesToTable' => ''
                    , 'appliesToId' => ''
                    , 'created' => ''
                    , 'updated' => ''
                    , 'note' => ''
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
        $y = isset($fieldValues['noteId']) ? $fieldValues['noteId'] : null;
        $x->setAllFields( 'noteId'              // $fieldName
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
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['appliesToTable'])
           ? $fieldValues['appliesToTable']
           : null;
        $x->setAllFields( 'appliesToTable'      // $fieldName
                        , $y                    // $fieldValue
                        , 'ENUM(\'job\''
                             . ', \'company\''
                             . ', \'contact\''
                             . ', \'keyword\''
                             . ', \'search\')'
                                                // $dataType
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 0                     // $userCanSee
                        , 'Applies To Table'    // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['appliesToId'])
           ? $fieldValues['appliesToId']
           : null;
        $x->setAllFields( 'appliesToId'         // $fieldName
                        , $y                    // $fieldValue
                        , 'INTEGER UNSIGNED'    // $dataType
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 0                     // $userCanSee
                        , 'Applies To ID'       // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['note']) ? $fieldValues['note'] : null;
        $x->setAllFields( 'note'                // $fieldName
                        , $y                    // $fieldValue
                        , 'TEXT'                // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Note'                // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;


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
        $this->_fields[$x->getFieldName()] = $x;

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
        $this->_fields[$x->getFieldName()] = $x;

    }

}