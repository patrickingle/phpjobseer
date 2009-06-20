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

class FieldDescription {
    /**
     * Typical usage:
     *
     * $oField = new FieldDescription();
     * $oField->setAllFields( $fieldName
     *                      , $fieldValue
     *                      , $dataType
     *                      , $sortKey
     *                      , $userCanChange
     *                      , $userCanSee
     *                      , $fieldLabel
     *                      , $quote
     *                      , $fieldHelp
     *                      , $fieldValidator
     *                      );
     */

	/**
     * @var String Name of this field
     */
    private $_fieldName;

    /**
     * @var unkown Value of data in this field
     */
    private $_fieldValue;

    /**
     * @var String data type of this field
     */
    private $_dataType;

    /**
     * @var int Sort Key
     */
    private $_sortKey;

    /**
     * @var boolean User is allowed to change this field
     */
    private $_userCanChange;

    /**
     * @var boolean can the user see this field?
     */
    private $_userCanSee;

    /**
     * @var String Label to be displayed to user
     */
    private $_fieldLabel;

    /**
     * @var String Help for this field
     */
    private $_fieldHelp;

    /**
     * @var unknown reference to field validator function
     */
    private $_fieldValidator;

    /**
     * @var String Field quote character
     */
    private $_quote;

    public function __construct() {
        // do nothing at the moment.
    }

    /**
     * Sets all fields
     *
     * @param  String $fieldName
     * @param  String $fieldValue
     * @param  String $dataType
     * @param  String $sortKey
     * @param  String $userCanChange
     * @param  String $userCanSee
     * @param  String $fieldLabel
     * @param  String $fieldHelp (optional)
     * @param  String $fieldValidator (optional)
     * @throws Exception
     * @return void
     */
    public function setAllFields( $fieldName
                                , $fieldValue
                                , $dataType
                                , $sortKey
                                , $userCanChange
                                , $userCanSee
                                , $fieldLabel
                                , $quote
                                , $fieldHelp = null
                                , $fieldValidator = null
                                ) {
        $this->setFieldName($fieldName);
        $this->setFieldValue($fieldValue);
        $this->setDataType($dataType);
        $this->setSortKey($sortKey);
        $this->setUserCanChange($userCanChange);
        $this->setUserCanSee($userCanSee);
        $this->setFieldLabel($fieldLabel);
        $this->setQuote($quote);
        $this->setFieldHelp($fieldHelp);
        $this->setFieldValidator($fieldValidator);
    }

    /**
     * @return String Name of this field
     */
    public function getFieldName() {
    	return $this->_fieldName;
    }

    /**
     * @param String $fieldName Name of this field
     */
    public function setFieldName($fieldName) {
        if ( (!isset($fieldName)) || ('' === $fieldName) ) {
        	throw Exception("Invalid value");
        }
    	$this->_fieldName = $fieldName;
    }

    /**
     * @return unkown Value of data in this field
     */
    public function getFieldValue() {
    	return $this->_fieldValue;
    }

    /**
     * @param unkown $fieldValue Value of data in this field
     */
    public function setFieldValue($fieldValue) {
//        if ( (!isset($fieldValue)) || ('' === $fieldValue) ) {
//            throw new Exception("Invalid value");
//        }
    	$this->_fieldValue = $fieldValue;
    }

    /**
     * @return String data type of this field
     */
    public function getDataType() {
    	return $this->_dataType;
    }

    /**
     * @param String $dataType data type of this field
     */
    public function setDataType($dataType) {
        if ( (!isset($dataType)) || ('' === $dataType) ) {
            throw new Exception("Invalid value");
        }
    	$this->_dataType = $dataType;
    }

    /**
     * @return int Sort Key
     */
    public function getSortKey() {
        return $this->_sortKey;
    }

    /**
     * @param  int $sortKey Sort Key
     * @throws Exception
     * @return void
     */
    public function setSortKey($sortKey) {
        if ( (!isset($sortKey)) || (''===$sortKey) ) {
            throw new Exception("Invalid value");
        }
        $this->_sortKey = $sortKey;
    }

    /**
     * @return boolean User is allowed to change this field
     */
    public function getUserCanChange() {
        return ( (!isset($this->_userCanChange)) || ($this->_userCanChange) );
    }

    /**
     * @param boolean $userCanChange User is allowed to change this field
     */
    public function setUserCanChange($userCanChange) {
        if ( (!isset($userCanChange)) || ('' === $userCanChange) ) {
            throw new Exception("Invalid value");
        }
    	$this->_userCanChange = $userCanChange;
    }

    /**
     * @return boolean can the user see this field?
     */
    public function getUserCanSee() {
    	return ( (!isset($this->_userCanSee)) || ($this->_userCanSee) );
    }

    /**
     * @param boolean $userCanSee can the user see this field?
     */
    public function setUserCanSee($userCanSee) {
        if ( (!isset($userCanSee)) || ('' === $userCanSee) ) {
            throw new Exception("Invalid value");
        }
    	$this->_userCanSee = $userCanSee;
    }

    /**
     * @return String Label to be displayed to user
     */
    public function getFieldLabel() {
    	return $this->_fieldLabel;
    }

    /**
     * @param String $fieldLabel Label to be displayed to user
     */
    public function setFieldLabel($fieldLabel) {
        if ( (!isset($fieldLabel)) || ('' === $fieldLabel) ) {
            throw new Exception("Invalid value");
        }
    	$this->_fieldLabel = $fieldLabel;
    }

    /**
     * @return String Help for this field
     */
    public function getFieldHelp() {
    	return $this->_fieldHelp;
    }

    /**
     * @param String $fieldHelp Help for this field
     */
    public function setFieldHelp($fieldHelp) {
//        if ( (!isset($fieldHelp)) || ('' === $fieldHelp) ) {
//            throw new Exception("Invalid value");
//        }
    	$this->_fieldHelp = $fieldHelp;
    }

    /**
     * @return unknown reference to field validator function
     */
    public function getFieldValidator() {
    	return $this->_fieldValidator;
    }

    /**
     * @param unknown $fieldValidator reference to field validator function
     */
    public function setFieldValidator($fieldValidator) {
//        if ( (!isset($fieldValidator)) || ('' === $fieldValidator) ) {
//            throw new Exception("Invalid value");
//        }
    	$this->_fieldValidator = $fieldValidator;
    }

    /**
     * Call the field validator which should return true on validation or
     * false on failure.  If there is no validator, returns true.
     */
    public function validateField() {
        $funcName = $this->getFieldValidator();
    	return ( (!isset($funcName)) && $funcName() );
    }

    /**
     * @return String Field quote character
     */
    public function getQuote() {
        return $this->_quote;
    }

    /**
     * @param  String $quote Field quote character
     * @throws Exception
     * @return void
     */
    public function setQuote($quote) {
        if ( ( null !== $quote )
          && ( ''  !== $quote )
          && ( '"' !== $quote )
          && ( "'" !== $quote )
           ) {
            throw new Exception("Invalid value");
        }
        $this->_quote = $quote;
    }

}
