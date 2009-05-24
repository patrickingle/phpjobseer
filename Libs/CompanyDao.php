<?php
/**
 * Created on May 10, 2009 by kbenton
 *
 */

require_once("Libs/autoload.php");

class CompanyDao extends DaoBase {

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
     * Class constructor
     *
     * @return void
     */
    public function __construct() {
        parent::__construct('company');
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

}