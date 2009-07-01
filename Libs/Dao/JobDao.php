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

class JobDao extends DaoBase {

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('job');
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
        return array( 'jobId' => ''
                    , 'primaryContactId' => ''
                    , 'companyId' => ''
                    , 'urgency' => 'medium'
                    , 'nextActionDue' => date("Y-m-d H:i", time() + 86400)
                    , 'lastStatusChange' => date("Y-m-d H:i")
                    , 'created' => ''
                    , 'updated' => ''
                    , 'positionTitle' => ''
                    , 'applicationStatusId' => 1
                    , 'nextAction' => ''
                    , 'location' => 'CO-Denver'
                    , 'url' => ''
                    );
    }

    /**
     * populateFields
     *
     * @param array $fieldValues
     * @return void
     */
    public function populateFields($fieldValues) {
    	$this->_fields = array();

        $x = new FieldDescription();
        $y = isset($fieldValues['jobId']) ? $fieldValues['jobId'] : null;
        $x->setAllFields( 'jobId'               // $fieldName
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
        $y = isset($fieldValues['primaryContactId']) ? $fieldValues['primaryContactId'] : null;
        $x->setAllFields( 'primaryContactId'    // $fieldName
                        , $y                    // $fieldValue
                        , 'REFERENCE(Contact)'  // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Contact'             // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['companyId']) ? $fieldValues['companyId'] : null;
        $x->setAllFields( 'companyId'           // $fieldName
                        , $y                    // $fieldValue
                        , 'REFERENCE(Company)'  // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Company'             // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['urgency']) ? $fieldValues['urgency'] : null;
        $x->setAllFields( 'urgency'             // $fieldName
                        , $y                    // $fieldValue
                        , 'ENUM(\'low\', \'medium\', \'high\')'
                                                // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Urgency'             // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['nextActionDue']) ? $fieldValues['nextActionDue'] : null;
        $x->setAllFields( 'nextActionDue'       // $fieldName
                        , $y                    // $fieldValue
                        , 'DATETIME'            // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Due Date'            // $fieldLabel
                        , '\''                  // $quote
                        , 'When is the next action due?'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['lastStatusChange']) ? $fieldValues['lastStatusChange'] : null;
        $x->setAllFields( 'lastStatusChange'    // $fieldName
                        , $y                    // $fieldValue
                        , 'DATETIME'            // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Last Status Change'  // $fieldLabel
                        , '\''                  // $quote
                        , 'When was the last status change?  Can be set maually' .
                          ', but usually set automatically by this system'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['created']) ? $fieldValues['created'] : null;
        $x->setAllFields( 'created'             // $fieldName
                        , $y                    // $fieldValue
                        , 'TIMESTAMP'           // $dataType
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Created On'          // $fieldLabel
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
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Updated On'          // $fieldLabel
                        , '\''                  // $quote
                        , 'When was this record last changed?'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['positionTitle']) ? $fieldValues['positionTitle'] : null;
        $x->setAllFields( 'positionTitle'       // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Position Title'      // $fieldLabel
                        , '\''                  // $quote
                        , 'What will the title be at this position?'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['applicationStatusId']) ? $fieldValues['applicationStatusId'] : null;
        $x->setAllFields( 'applicationStatusId' // $fieldName
                        , $y                    // $fieldValue
                        , 'REFERENCE(ApplicationStatus)'
                                                // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Application Status'  // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['nextAction']) ? $fieldValues['nextAction'] : null;
        $x->setAllFields( 'nextAction'          // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Next Action'         // $fieldLabel
                        , '\''                  // $quote
                        , 'What\'s next?'       // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['location']) ? $fieldValues['location'] : null;
        $x->setAllFields( 'location'            // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(255)'        // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Location'            // $fieldLabel
                        , '\''                  // $quote
                        , 'Where is the work'   // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['url']) ? $fieldValues['url'] : null;
        $x->setAllFields( 'url'                 // $fieldName
                        , $y                    // $fieldValue
                        , 'VARCHAR(4096)'       // $dataType
                        , 1                     // $sortKey
                        , 1                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'URL'                 // $fieldLabel
                        , '\''                  // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $this->_fields[$x->getFieldName()] = $x;

    }

    /**
     * validateRowForInsert checks to make sure that data being inserted is valid.
     *
     * @todo TODO Auto-detect duplicate URL's and mark accordingly.
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForInsert($rowValues) {
        if ( !isset($rowValues) ) {
            return false;
        }
        if ( isset($rowValues['jobId'])
          && ( '' !== $rowValues['jobId'] )
          && !self::validateRowId($rowValues['jobId'])
           ) {
            return false;
        }
        if ( isset($rowValues['applicationStatusId'])
          && ( '' !== $rowValues['applicationStatusId'] )
          && !ApplicationStatusDao::validateRowId($rowValues['applicationStatusId'])
           ) {
            return false;
        }
        if ( isset($rowValues['primaryContactId'])
          && ( '' !== $rowValues['primaryContactId'] )
          && !ContactDao::validateRowId($rowValues['primaryContactId'])
           ) {
            return false;
        }
        if ( isset($rowValues['sortKey'])
          && !is_int($rowValues['sortKey'])
           ) {
            return false;
        }
        return true;
    }

    /**
     * validateRowForUpdate checks to make sure that data being updated is valid.
     *
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForUpdate($rowValues) {
        if ( (!isset($rowValues))
          || (!isset($rowValues['jobId']))
          || (!$this->validateRowId($rowValues['jobId']))
          || (!ApplicationStatusDao::validateRowId($rowValues['applicationStatusId']))
          || ( isset($rowValues['primaryContactId'])
               && "" !== $rowValues['primaryContactId']
               && !ContactDao::validateRowId($rowValues['primaryContactId']) )
          || ( isset($rowValues['sortKey'])
               && !is_int($rowValues['sortKey']) )
          || ( isset($rowValues['nextActionDue'])
               && !self::validateDateTime($rowValues['nextActionDue']) )
          || ( isset($rowValues['lastStatusChange'])
               && !self::validateDateTime($rowValues['lastStatusChange']) )
           ) {
            return false;
        }
        else
        {
            return true;
        }
    }

}