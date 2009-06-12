<?php
/**
 * Created on May 11, 2009 by kbenton
 *
 */

require_once('HTML/QuickForm.php');

class JobDisplayForm {

    private $_formValues = null;
    private $_fields = null;
    private $_form = null;
    private $_oJob = null;
    private $_jobId = null;

    /**
     * Constructor
     */
    public function __construct($jobId = null) {
        $this->_form = new HTML_QuickForm( 'JobChangeForm'
                                         , 'post'
                                         , 'saveJobChanges.php'
                                         );
        $this->loadFormValues($jobId);
    }

    /**
     * Get the form values for display by job ID
     *
     * @param  int $jobId
     * @return boolean True when values loaded successfully, false otherwise
     */
    public function loadFormValues($jobId) {
        $this->_jobId = null;
    	$oJob = new JobDao();
        if ( null === $jobId ) {
            $this->_jobId = $jobId;
            $this->_formValues = $oJob->getDefaults();
        }
        else {
            if ( ! $oJob->validateRowId($jobId) ) {
                echo "<p class=\"error\">Invalid Job ID</p>";
                return false;
            }
            $this->_jobId = $jobId;
            $this->_formValues = $oJob->getRowById($jobId);
        }
        $oJob->populateFields($this->_formValues);
        $this->_fields = $oJob->getFields();
        $this->_oJob = $oJob;
    }

    /**
     * Compare fields function for sorting purposes
     */
    public static function cmpFields($a, $b) {
        if ($a->getSortKey() === $b->getSortKey()) {
            return 0;
        }
        return  ($a->getSortKey() < $b->getSortKey()) ? -1 : 1;
    }

    /**
     * Display the add/edit form based on $_formValues
     *
     * @return void
     */
    public function displayForm() {
        $maxFieldLength = 80;
        $dateOptions = array( 'language' => 'en', 'format'   => 'YMdHi' );
        $sortedFields = $this->_fields;
        if ( !isset($this->_fields)) {
            return;
        }
        usort($sortedFields, 'JobDisplayForm::cmpFields');
        $constants=array();
        $defaults=array();

        $jobId = $this->_formValues['jobId'];
        $this->_form->addElement('hidden', 'jobId', $jobId);
        $constants['jobId'] = $jobId;

        foreach ( $this->_fields as $field ) {
            if (! $field->getUserCanSee()) {
                continue;
            }
            $value = $this->_formValues[$field->getFieldName()];
            if (! $field->getUserCanChange()) {
                $this->_form->addElement( 'static'
                                        , $field->getFieldName()
                                        , $field->getFieldLabel()
                                        , $value
                                        );
                continue;
            }
            $dataType = $field->getDataType();

            switch ($dataType) {
                case 'DATETIME':
                case 'TIMESTAMP':
                    if ($field->getUserCanChange()) {
                        $this->_form->addElement( 'date'
                                                , $field->getFieldName()
                                                , $field->getFieldLabel()
                                                , $dateOptions
                                                );
                        $y=substr($value, 0, 4);
                        $m=substr($value, 5, 2);
                        $d=substr($value, 8, 2);
                        $h=substr($value, 11, 2);
                        $i=substr($value, 14, 2);
                        $value = array( 'Y' => $y
                                      , 'M' => $m
                                      , 'd' => $d
                                      , 'H' => $h
                                      , 'i' => $i
                                      );
                    }
                    break;
                case ( preg_match('/^VARCHAR\(([1-9][0-9]+)\)$/', $dataType, $matches)? $dataType: ! $dataType ):
                    $length = ($matches[1] < $maxFieldLength) ? $matches[1] : $maxFieldLength;
                    $this->_form->addElement( 'text'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , array( 'size' => $length
                                                   , 'maxlength' => $matches[1]
                                                   , 'alt' => $field->getFieldHelp()
                                                   )
                                            );
                    break;
                case ( preg_match( '/^ENUM\((\'[A-Za-z0-9]+\'(, |,|))+\)$/'
                                 , $dataType)
                       ? $dataType
                       : !$dataType
                     ) :
                    $listString = preg_replace( '/^ENUM\(/', '', $dataType );
                    $listString = preg_replace( '/\)$/', '', $listString );
                    $listString = preg_replace( '/\',\\s+\'/', '\',\'', $listString );
                    $listString = preg_replace( '/^\'/', '', $listString );
                    $listString = preg_replace( '/\'$/', '', $listString );
                    $items = split('\',\'', $listString);
                    foreach ($items as $k) { $list[$k] = $k; }
                    $this->_form->addElement( 'select'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , $list
                                            , array( 'alt' => $field->getFieldHelp() )
                                            );
                    break;
                case 'REFERENCE(Contact)':
                    // @todo AJAX this - have the client load values.
                    $oContact = new ContactDao();
                    $results = $oContact->findSome("1 = 1 order by contactName");
                    $contacts = array( '' => ''
                                     , 'Add new contact'=> 'Add new contact'
                                     );
                    foreach ( $results as $result ) {
                        if ( $result['contactId'] > 0 ) {
                            $name = $result['contactName'];
                            $contacts[ $result['contactId'] ] = $name;
                            if ( $this->_formValues['primaryContactId']
                                 === $result['contactId']
                               ) {
                                $value = $name;
                            }
                        }
                    }
                    $this->_form->addElement( 'select'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , $contacts
                                            , array( 'alt' => $field->getFieldHelp() )
                                            );
                    break;
                case 'REFERENCE(ApplicationStatus)':
                    $oApplicationStatus = new ApplicationStatusDao();
                    $results = $oApplicationStatus->findAll();
                    $statuses = array();
                    foreach ( $results as $result ) {
                        if ( $result['applicationStatusId'] > 0 ) {
                            $name = $result['statusValue'];
                            $statuses[ $result['applicationStatusId'] ] = $name;
                            if ( $this->_formValues['applicationStatusId']
                                 === $result['applicationStatusId']
                               ) {
                                $value = $result['applicationStatusId'];
                            }
                        }
                    }
                    $this->_form->addElement( 'select'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , $statuses
                                            , array( 'alt' => $field->getFieldHelp() )
                                            );
                    break;
                case 'REFERENCE(Company)':
                    // @todo AJAX this - have the client load values.
                    $oCompany = new CompanyDao();
                    $results = $oCompany->findSome("1 = 1 order by companyName");
                    $companies = array( '' => ''
                                     , 'Add new company'=> 'Add new company'
                                     );
                    foreach ( $results as $result ) {
                        if ( $result['companyId'] > 0 ) {
                            $name = $result['companyName'];
                            $companies[ $result['companyId'] ] = $name;
                            if ( $this->_formValues['companyId']
                                 === $result['companyId']
                               ) {
                                $value = $name;
                            }
                        }
                    }
                    $this->_form->addElement( 'select'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , $companies
                                            , array( 'alt' => $field->getFieldHelp() )
                                            );
                    break;
                default:
                    echo "<td bgcolor=\"cyan\">" . $field->getFieldValue() . " / " . $field->getDataType() . " / " . $field->getFieldHelp() . "</td>";
                    break;
            } // END OF switch ($dataType)

            if ($field->getUserCanChange()) {
                $defaults[$field->getFieldName()]=$value;
            }
            else {
                $constants[$field->getFieldName()]=$value;
            }
        }
        $options = array( "rows"=>"5"
                        , "cols"=>"60"
                        );
        $this->_form->addElement( 'textarea'
                                , 'newNote'
                                , 'Note'
                                , $options
                                );

        $this->_form->addElement('submit', null, 'Save Changes');
        $this->_form->setConstants($constants);
        $this->_form->setDefaults($defaults);
        $this->_form->display();
        if ( null !== $jobId ) {
            $oNote = new NoteDao();
            $results = $oNote->findSome(      "appliesToTable = 'job'"
                                       . " AND appliesToId = $jobId"
                                       );
            foreach ($results as $result) {
                echo "<p /><hr />"
                   . $result['updated']
                   . "<br /><pre>"
                   . $result['note']
                   . "</pre>";
            }
        }
    }
}