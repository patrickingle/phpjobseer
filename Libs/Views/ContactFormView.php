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

require_once('HTML/QuickForm.php') ;

class ContactFormView extends FormViewBase {

    private $_oContact = null ;
    private $_contactId = null ;
    private $_fields = null ;
    private $_formValues = null ;

    /**
     * Constructor
     */
    public function __construct( $contactId = null ) {
        parent::__construct( 'ContactChangeForm'
                           , 'post'
                           , 'saveContactChanges.php'
                           ) ;
        $this->loadFormValues( $contactId ) ;
    }

    /**
     * Destructor
     */
    public function __destruct() {
//        echo '</td><td id="ajaxBox" valign="top">' ;
//        echo '</td></tr></table>' ;
    }

    /**
     * Get the form values for display by contact ID
     *
     * @param  int $contactId
     * @return boolean True when values loaded successfully, false otherwise
     */
    public function loadFormValues( $contactId ) {
    	$this->_contactId = null ;
    	$oContact = new ContactDao() ;
        if ( null === $contactId ) {
        	$this->_contactId = $contactId ;
            $this->_formValues = $oContact->getDefaults() ;
        }
        else {
            if ( ! $oContact->validateRowId( $contactId ) ) {
                echo "<p class=\"error\">Invalid Contact ID</p>" ;
                return false ;
            }
            $this->_contactId = $contactId ;
            $this->_formValues = $oContact->getRowById( $contactId ) ;
        }
        $oContact->populateFields( $this->_formValues ) ;
        $this->_fields = $oContact->getFields() ;
        $this->_oContact = $oContact ;
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
        if ( !isset( $this->_fields ) ) {
            return ;
        }
        usort($sortedFields, 'ContactFormView::cmpFields') ;
        $constants=array() ;
        $defaults=array() ;

        $contactId = $this->_formValues[ 'contactId' ] ;
        $this->_form->addElement( 'hidden', 'contactId', $contactId ) ;
        $constants[ 'contactId' ] = $contactId ;
        $contactIdText = isset( $contactId ) && ( '' <> $contactId ) ? $contactId : "0" ;
        
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
                case 'REFERENCE(Company)' :
                    // @todo AJAX Companies - have the client load values.
                    $oCompany = new CompanyDao() ;
                    $results = $oCompany->findSome( "1 = 1 order by companyName" ) ;
                    $contacts = array( '0' => ''
                                     , 'Add new company'=> 'Add new company'
                                     ) ;
                    foreach ( $results as $result ) {
                        if ( $result[ 'companyId' ] > 0 ) {
                            $name = $result[ 'companyName' ] ;
                            $contacts[ $result[ 'companyId' ] ] = $name ;
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
                                            , $contacts
                                            , array( 'alt' => $field->getFieldHelp()
                                                   , 'onchange' => "checkForAddNewContact($contactIdText, this.value)"
                                                   )
                                            ) ;
                    break ;
                default:
                    echo "<td bgcolor=\"cyan\">" . $field->getFieldValue() . " / " . $field->getDataType() . " / " . $field->getFieldHelp() . "</td>" ;
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

            if ($field->getUserCanChange()) {
                $defaults[$field->getFieldName()]=$value ;
            }
            else {
                $constants[$field->getFieldName()]=$value ;
            }
        }
        $options = array( "rows"=>"5"
                        , "cols"=>"60"
                        ) ;
        $this->_form->addElement( 'textarea'
                                , 'newNote'
                                , 'Note'
                                , $options
                                ) ;

        $this->_form->addElement('submit', null, 'Save Changes') ;
        $this->_form->setConstants($constants) ;
        $this->_form->setDefaults($defaults) ;
        $this->_form->display() ;
        if ( null !== $contactId ) {
            $oNote = new NoteDao() ;
            $results = $oNote->findSome(      "appliesToTable = 'contact'"
                                       . " AND appliesToId = $contactId"
                                       . " ORDER BY created DESC"
                                       ) ;
            foreach ($results as $result) {
                echo "<p /><hr />"
                   . $result['updated']
                   . "<br /><pre>"
                   . $result['note']
                   . "</pre>" ;
            }
        }
    }
}
