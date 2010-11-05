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

class CompanyDao extends DaoBase {

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('company');
        $this->populateFields(null);
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
        $info->addColumn( 'companyId'      , 'SERIAL'      , false    ) ;
        $info->addColumn( 'isAnAgency'     , 'BOOLEAN'     , false, 0 ) ;
        $info->addColumn( 'agencyCompanyId', 'INT'         , true , 1
                        , array( 'unsigned' => true )
                        ) ;
        $info->addColumn( 'companyName'    , 'VARCHAR(100)', false, '' ) ;
        $info->addColumn( 'companyAddress1', 'TINYTEXT'    , false     ) ;
        $info->addColumn( 'companyAddress2', 'TINYTEXT'    , false     ) ;
        $info->addColumn( 'companyCity'    , 'VARCHAR(60)' , false, '' ) ;
        $info->addColumn( 'companyState'   , 'CHAR(2)'     , false, '' ) ;
        $info->addColumn( 'companyZip'     , 'MEDIUMINT(5)', true      ) ;
        $info->addColumn( 'companyPhone'   , 'INT(10)'     , true      ) ;
        $info->addColumn( 'created'        , 'TIMESTAMP'   , false , '0000-00-00 00:00:00' ) ;
        $info->addColumn( 'updated'
                        , 'TIMESTAMP'
                        , false
                        , 'CURENT_TIMESTAMP'
                        , 'ON UPDATE CURRENT_TIMESTAMP'
                        ) ;
        $info->addKey( 'PRIMARY'
                     , 'companyPk'
                     , array( 'companyId' )
                     ) ;
        $info->addKey( 'FOREIGN'
                     , 'agencyCompanyFk'
                     , array( 'agencyCompanyId' )
                     , array( 'references' => 'company(companyId)'
                            , 'onDelete' => 'NO ACTION'
                            , 'onUpdate' => 'NO ACTION'
                            )
                     ) ;
        $info->addTrigger( 'companyAfterUpdateTrigger'
                         , 'AFTER'
                         , 'UPDATE'
                         , "IF OLD.companyId <> NEW.companyId\n"
                         . "THEN\n"
                         . "  UPDATE note\n"
                         . "     SET note.appliesToId = NEW.companyId\n"
                         . "   WHERE note.appliesToId = OLD.companyId\n"
                         . "     AND note.appliestoTable = 'company'\n"
                         . "       ;\n"
                         . "END IF ;\n"
                         ) ;
        $info->addTrigger( 'companyAfterDeleteTrigger'
                         , 'AFTER'
                         , 'DELETE'
                         , "DELETE\n"
                         . "  FROM note\n"
                         . " WHERE note.appliesToId = OLD.companyId\n"
                         . "   AND note.appliestoTable = 'company'\n"
                         . "     ;\n"
                         ) ;
        return $info() ;
    }

    /**
     * getDefaults acts like DaoBase::getRowById returning a hash of fields to
     * column values to be used by the insertRow routine to compare values with
     * for default values at row insertion time.
     *
     * @return array Default values for new records
     */
    public function getDefaults() {
        return array( 'companyId' => ''
                    , 'isAnAgency' => 0
                    , 'agencyCompanyId' => ''
                    , 'companyName' => ''
                    , 'companyAddress1' => ''
                    , 'companyAddress2' => ''
                    , 'companyCity' => ''
                    , 'companyState' => ''
                    , 'companyZip' => ''
                    , 'companyPhone' => ''
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
        $y = isset($fieldValues['companyId']) ? $fieldValues['companyId'] : null;
        $x->setAllFields( 'companyId'           // $fieldName
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
        $y = isset($fieldValues['isAnAgency']) ? $fieldValues['isAnAgency'] : null;
        $x->setAllFields( 'isAnAgency'          // $fieldName
                        , $y                    // $fieldValue
                        , 'BOOLEAN'             // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Is this an Agency?'  // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['agencyCompanyId'])
           ? $fieldValues['agencyCompanyId']
           : null;
        $x->setAllFields( 'agencyCompanyId'     // $fieldName
                        , $y                    // $fieldValue
                        , 'REFERENCE(Company)'  // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Agency'              // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['companyName']) ? $fieldValues['companyName'] : null;
        $x->setAllFields( 'companyName'         // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(100)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Name'                // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['companyAddress1'])
           ? $fieldValues['companyAddress1']
           : null;
        $x->setAllFields( 'companyAddress1'     // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Address 1'           // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['companyAddress2'])
           ? $fieldValues['companyAddress2']
           : null;
        $x->setAllFields( 'companyAddress2'     // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Address 2'           // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['companyCity']) ? $fieldValues['companyCity'] : null;
        $x->setAllFields( 'companyCity'         // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(60)'         // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'City'                // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['companyState']) ? $fieldValues['companyState'] : null;
        $x->setAllFields( 'companyState'        // $fieldName
                        , $y                    // $fieldValue
                        , 'CHAR(2)'             // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'State'               // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['companyZip']) ? $fieldValues['companyZip'] : null;
        $x->setAllFields( 'companyZip'          // $fieldName
                        , $y                    // $fieldValue
                        , 'INTEGER UNSIGNED'    // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Zip'                 // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['companyPhone']) ? $fieldValues['companyPhone'] : null;
        $x->setAllFields( 'companyPhone'           // $fieldName
                        , $y                    // $fieldValue
                        , 'INTEGER UNSIGNED'    // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Phone'               // $fieldLabel
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
        return ( isset($rowValues)
              && isset($rowValues['isAnAgency'])
              && ( ( 1 == $rowValues['isAnAgency'] )
                || ( 0 == $rowValues['isAnAgency'] )
                 )
              && ( ( ( $rowValues['isAnAgency'] == 0 )
                  && ( ! isset($rowValues['agencyCompanyId']) )
                   )
                  ||
                   ( ( $rowValues['isAnAgency'] == 1 )
                  && ( isset($rowValues['agencyCompanyId']) )
                   )
                 )
              && isset($rowValues['companyName'])
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
              && (!isset($rowValues['companyId']))
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
              && (isset($rowValues['companyId']))
              && self::validateRowForInsertOrUpdate($rowValues)
               );
    }

    /**
     * findSome overrides DaoBase::findSome.  The intent is to provide a
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
        $oCompany = new CompanyDao();
        while ($row = $this->_sth->fetch_assoc()) {
            $results[] = $row;
        }
        return $results;
    }

}