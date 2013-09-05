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

require_once( 'HTML/QuickForm.php' );

class JobFormView extends FormViewBase {

    private $_oJob = null ;
    private $_jobId = null ;

    /**
     * Constructor
     */
    public function __construct( $jobId = null ) {
        parent::__construct( 'JobChangeForm'
                           , 'post'
                           , 'saveJobChanges.php'
                           ) ;
        $this->loadFormValues( $jobId ) ;
    }

    /**
     * Destructor
     */
    public function __destruct() {
//        echo '</td><td id="ajaxBox" valign="top">';
//        echo '</td></tr></table>';
    }

    /**
     * Get the form values for display by job ID
     *
     * @param  int $jobId
     * @return boolean True when values loaded successfully, false otherwise
     */
    public function loadFormValues( $jobId ) {
        $this->_jobId = null ;
        $oJob = new JobDao() ;
        if ( null === $jobId ) {
            $this->_jobId = $jobId ;
            $this->_formValues = $oJob->getDefaults() ;
        }
        else {
            if ( ! $oJob->validateRowId( $jobId ) ) {
                echo "<p class=\"error\">Invalid Job ID</p>" ;
                return false ;
            }
            $this->_jobId = $jobId ;
            $this->_formValues = $oJob->getRowById( $jobId ) ;
        }
        $oJob->populateFields( $this->_formValues ) ;
        $this->_fields = $oJob->getFields() ;
        $this->_oJob = $oJob ;
    }

    /**
     * Display the add/edit form based on $_formValues
     *
     * @return void
     */
    public function displayForm() {
        $maxFieldLength = 80 ;
        $dateOptions = array( 'language' => 'en', 'format'   => 'YMdHi' ) ;
        $sortedFields = $this->_fields ;
        if ( ! isset( $this->_fields ) ) {
            return ;
        }
        usort( $sortedFields, 'JobFormView::cmpFields' ) ;
        $constants = array() ;
        $defaults = array() ;

        $jobId = $this->_formValues[ 'jobId' ] ;
        var_dump( $this->_form ) ;
        $this->_form->addElement( 'hidden', 'jobId', $jobId ) ;
        $constants[ 'jobId' ] = $jobId ;
        $jobIdText = isset( $jobId ) && ( '' <> $jobId ) ? $jobId : "0" ;
        
        foreach ( $this->_fields as $field ) {
            if ( ! $field->getUserCanSee() ) {
                continue ;
            }
            $value = $this->_formValues[ $field->getFieldName() ] ;
            if ( ! $field->getUserCanChange() ) {
                $this->_form->addElement( 'static'
                                        , $field->getFieldName()
                                        , $field->getFieldLabel()
                                        , $value
                                        ) ;
                continue ;
            }
            $dataType = $field->getDataType() ;

            switch ($dataType) {
                case ( $this->prepFormElement( $dataType
                                             , $this->_form
                                             , $value
                                             , $field->getFieldName()
                                             , $field->getFieldLabel()
                                             , $field->getFieldHelp()
                                             , $field->getUserCanChange()
                                             , $maxFieldLength
                                             , $dateOptions
                                             )
                     ? $dataType : ! $dataType ) :
                    // Do nothing here because prepFormElement did it for me.
                    break ;
                case 'REFERENCE(Contact)' :
                    // @todo AJAX Contacts - have the client load values.
                    // @todo AJAX Display new Z-Window on Add New Contact
                    $oContact = new ContactDao() ;
                    $results = $oContact->findSome( "1 = 1 order by contactName" ) ;
                    $contacts = array( '0' => ''
                                     , 'Add new contact' => 'Add new contact'
                                     ) ;
                    foreach ( $results as $result ) {
                        if ( $result['contactId'] > 0 ) {
                            $name = $result[ 'contactName' ] ;
                            $contacts[ $result[ 'contactId' ] ] = $name ;
                            if ( $this->_formValues[ 'primaryContactId' ]
                                 === $result[ 'contactId' ]
                               ) {
                                $value = $name ;
                            }
                        }
                    }
                    $this->_form->addElement( 'select'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , $contacts
                                            , array( 'alt' => $field->getFieldHelp()
                                                   , 'onchange' => "checkForAddNewContact($jobIdText, this.value)"
                                                   )
                                            ) ;
                    break ;
                case 'REFERENCE(ApplicationStatus)' :
                    $oApplicationStatus = new ApplicationStatusDao() ;
                    $results = $oApplicationStatus->findAll() ;
                    $statuses = array() ;
                    foreach ( $results as $result ) {
                        if ( $result[ 'applicationStatusId' ] > 0 ) {
                            $name = $result[ 'statusValue' ] ;
                            $statuses[ $result[ 'applicationStatusId' ] ] = $name ;
                            if ( $this->_formValues[ 'applicationStatusId' ]
                                 === $result[ 'applicationStatusId' ]
                               ) {
                                $value = $result[ 'applicationStatusId' ] ;
                            }
                        }
                    }
                    $this->_form->addElement( 'select'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , $statuses
                                            , array( 'alt' => $field->getFieldHelp() )
                                            ) ;
                    break ;
                case 'REFERENCE(Company)' :
                    // @todo AJAX Companies - have the client load values.
                    // @todo AJAX Display new Z-Window on Add New Company
                    $oCompany = new CompanyDao() ;
                    $results = $oCompany->findSome( "1 = 1 order by companyName" ) ;
                    $companies = array( '0' => ''
                                      , 'Add new company' => 'Add new company'
                                      ) ;
                    foreach ( $results as $result ) {
                        if ( $result[ 'companyId' ] > 0 ) {
                            $name = $result[ 'companyName' ] ;
                            $companies[ $result[ 'companyId' ] ] = $name;
                            if ( $this->_formValues[ 'companyId' ]
                                 === $result[ 'companyId' ]
                               ) {
                                $value = $name ;
                            }
                        }
                    }
                    $this->_form->addElement( 'select'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , $companies
                                            , array( 'alt' => $field->getFieldHelp() )
                                            ) ;
                    break ;
                case 'REFERENCE(jobKeyword)' :
                    // @todo Make this work because R/O keyword is nasty. Does not work after db reset.
                    $oJobKeyword = new JobKeywordDao( );
                    $value = $oJobKeyword->findKeywordValuesByJobId( $jobId ) ;
                    $this->_form->addElement( 'text'
                                            , $field->getFieldName()
                                            , $field->getFieldLabel()
                                            , array( 'size' => $maxFieldLength
                                                   , 'maxlength' => $maxFieldLength
                                                   , 'alt' => $field->getFieldHelp()
                                                   , 'READONLY' => 'READONLY'
                                                   )
                                            ) ; 
                    break ;
                default:
                    echo "<td bgcolor=\"cyan\">"
                       . $field->getFieldValue()
                       . " / "
                       . $field->getDataType()
                       . " / "
                       . $field->getFieldHelp()
                       . "</td>" ;
                    break ;
            } // END OF switch ($dataType)

            if ( 'url' === $field->getFieldName() ) {
                $this->_form->addElement( 'html'
                                        , '<tr>'
                                        . '<td colspan="2" align="right">'
                                        . '<div id="urlDuplicateStatusBox"></div>'
                                        . '</td>'
                                        . '</tr>'
                                        ) ;
                $this->_form->addElement( 'html'
                                        , '<tr>'
                                        . '<td colspan="2" align="right">'
                                        . '<div id="urlDuplicateResultBox"></div>'
                                        . '</td>'
                                        . '</tr>'
                                        ) ;
            }
            if ( 'contact' === $field->getFieldName() ) {
                $this->_form->addElement( 'html'
                                        , '<tr>'
                                        . '<div id="ajaxContactFormBox"></div>'
                                        . '</td>'
                                        . '</tr>'
                                        ) ;
                $this->_form->addElement( 'html'
                                        , '<tr>'
                                        . '<div id="ajaxContactResultBox"></div>'
                                        . '</td>'
                                        . '</tr>'
                                        ) ;
            }

            if ( $field->getUserCanChange() ) {
                $defaults[ $field->getFieldName() ] = $value ;
            }
            else {
                $constants[ $field->getFieldName() ] = $value ;
            }
        }
        $options = array( "rows"=>"5", "cols"=>"60" ) ;
        $this->_form->addElement( 'textarea'
                                , 'newNote'
                                , 'Note'
                                , $options
                                ) ;

        $this->_form->addElement( 'submit', null, 'Save Changes' ) ;
        $this->_form->setConstants( $constants ) ;
        $this->_form->setDefaults( $defaults ) ;
        $this->_form->display() ;
        if ( null !== $jobId ) {
            $oNote = new NoteDao() ;
            $results = $oNote->findSome(      "appliesToTable = 'job'"
                                       . " AND appliesToId = $jobId"
                                       . " ORDER BY created DESC"
                                       ) ;
            foreach ( $results as $result ) {
                echo "<p /><hr />"
                   . $result[ 'updated' ]
                   . "<br /><pre>"
                   . $result[ 'note' ]
                   . "</pre>"
                   ;
            }
        }
    }
}
