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

class ApplicationStatusDao extends DaoBase {

    /**
     * validateRowForInsertOrUpdate does all the "other" checks needed to verify
     * a row is valid for insert/update besides whether or not the row ID is
     * present or not.
     */
    public function validateRowForInsertOrUpdate($rowValues) {
        return ( isset($rowValues)
              && isset($rowValues['statusValue'])
              && isset($rowValues['isActive'])
              && isset($rowValues['sortKey'])
              && ( ( 1 == $rowValues['isActive'] )
                || ( 0 == $rowValues['isActive'] )
                 )
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
              && (!isset($rowValues['applicationStatusId']))
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
              && (isset($rowValues['applicationStatusId']))
              && self::validateRowForInsertOrUpdate($rowValues)
               );
    }

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('applicationStatus');
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
        return array( 'applicationStatusId' => ''
                    , 'statusValue' => ''
                    , 'isActive' => 1
                    , 'sortKey' => 100
                    , 'style' => ''
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
        $y = isset($fieldValues['statusValue']) ? $fieldValues['statusValue'] : null;
        $x->setAllFields( 'statusValue'         // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(50)'         // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Value'               // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['isActive']) ? $fieldValues['isActive'] : null;
        $x->setAllFields( 'isActive'            // $fieldName
                        , $y                    // $fieldValue
                        , 'BOOLEAN'             // $dataType
                        , 2                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Active?'             // $fieldLabel
                        , ''                    // $quote
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
        $y = isset($fieldValues['style']) ? $fieldValues['style'] : null;
        $x->setAllFields( 'style'               // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(4096)'       // $dataType
                        , 4                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Style'               // $fieldLabel
                        , '\''                  // $quote
                        , 'CSS Style Info'      // $fieldHelp
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