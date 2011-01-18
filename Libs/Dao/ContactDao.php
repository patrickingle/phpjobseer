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

class ContactDao extends DaoBase {

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct() {
    	parent::__construct('contact');
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
        $info->addColumn( 'contactId'            , 'SERIAL'      , false       ) ;
        $info->addColumn( 'contactCompanyId'     , 'INT'         , false, 1
                        , array( 'unsigned' => true )
                        ) ;
        $info->addColumn( 'contactName'          , 'VARCHAR(255)', false, ''   ) ;
        $info->addColumn( 'contactEmail'         , 'TINYTEXT'    , false       ) ;
        $info->addColumn( 'contactPhone'         , 'INT'         , false, null
                        , array( 'unsigned' => true )
                        ) ;
        $info->addColumn( 'contactAlternatePhone', 'INT'         , false, null
                        , array( 'unsigned' => true )
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
                     , 'contactPk'
                     , array( 'contactId' )
                     ) ;
        $info->addKey( 'FOREIGN'
                     , 'contactCompanyFk'
                     , array( 'contactCompanyId' )
                     , array( 'references' => 'company(companyId)'
                            , 'onDelete' => 'CASCADE'
                            , 'onUpdate' => 'CASCADE'
                            )
                     ) ;
        $info->addTrigger( 'contactAfterUpdateTrigger'
                         , 'AFTER'
                         , 'UPDATE'
                         , "IF OLD.contactId <> NEW.contactId\n"
                         . "THEN\n"
                         . "  UPDATE note\n"
                         . "     SET note.appliesToId = NEW.contactId\n"
                         . "   WHERE note.appliesToId = OLD.contactId\n"
                         . "     AND note.appliestoTable = 'contact'\n"
                         . "       ;\n"
                         . "END IF ;\n"
                         ) ;
        $info->addTrigger( 'contactAfterDeleteTrigger'
                         , 'AFTER'
                         , 'DELETE'
                         , "DELETE\n"
                         . "  FROM note\n"
                         . " WHERE note.appliesToId = OLD.contactId\n"
                         . "   AND note.appliestoTable = 'contact'\n"
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
    	return array( 'contactId' => ''
                    , 'contactCompanyId' => ''
                    , 'contactName' => ''
                    , 'contactEmail' => ''
                    , 'contactPhone' => ''
                    , 'contactAlternatePhone' => ''
                    , 'created' => ''
                    , 'updated' => ''
                    );
    }

    /**
     * validateRowForInsertOrUpdate does all the "other" checks needed to verify
     * a row is valid for insert/update besides whether or not the row ID is
     * present or not.
     */
    public function validateRowForInsertOrUpdate($rowValues) {
        return ( true );
    }

    /**
     * validateRowForInsert checks to make sure that data being inserted is valid.
     *
     * @param array $rowValues Hash of row keys / values to be checked
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForInsert($rowValues) {
        return ( (isset($rowValues))
              && (!isset($rowValues['contactId']))
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
              && (isset($rowValues['contactId']))
              && self::validateRowForInsertOrUpdate($rowValues)
               );
    }

    /**
     * populateFields
     *
     * @return void
     */
    public function populateFields($fieldValues) {
    	$this->_fields = array();

        $x = new FieldDescription();
        $y = isset($fieldValues['contactId']) ? $fieldValues['contactId'] : null;
        $x->setAllFields( 'contactId'           // $fieldName
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
        $y = isset($fieldValues['contactCompanyId']) ? $fieldValues['contactCompanyId'] : null;
        $x->setAllFields( 'contactCompanyId'    // $fieldName
                        , $y                    // $fieldValue
                        , 'REFERENCE(Company)'  // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Works For'           // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;
                        
        $x = new FieldDescription();
        $y = isset($fieldValues['contactName']) ? $fieldValues['contactName'] : null;
        $x->setAllFields( 'contactName'         // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Name'                // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;
                        
        $x = new FieldDescription();
        $y = isset($fieldValues['contactEmail']) ? $fieldValues['contactEmail'] : null;
        $x->setAllFields( 'contactEmail'        // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Email'               // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;
                        
        $x = new FieldDescription();
        $y = isset($fieldValues['contactPhone']) ? $fieldValues['contactPhone'] : null;
        $x->setAllFields( 'contactPhone'        // $fieldName
                        , $y                    // $fieldValue
                        , 'PHONE NUMBER'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Phone Number'        // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;
                        
        $x = new FieldDescription();
        $y = isset($fieldValues['contactAlternatePhone']) ? $fieldValues['contactAlternatePhone'] : null;
        $x->setAllFields( 'contactAlternatePhone' // $fieldName
                        , $y                      // $fieldValue
                        , 'PHONE NUMBER'          // $dataType
                        , 1                       // $sortKey
                        , 1                       // $userCanChange
                        , 1                       // $userCanSee
                        , 'Other Phone'           // $fieldLabel
                        , ''                      // $quote
                        , null                    // $fieldHelp
                        , null                    // $fieldValidator
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
                        , ''                    // $quote
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
                        , ''                    // $quote
                        , 'When was this record last updated?'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;
                        
    }

}