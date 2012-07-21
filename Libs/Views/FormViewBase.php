<?php

require_once('HTML/QuickForm.php');

abstract class FormViewBase {

    /**
     * @var mixed
     */
    protected $_form ;

	/**
	 * 
	 * Class constructor
	 * @param String $formname
	 * @param String $method
	 * @param String $action
	 * @param String $target
	 * @param mixed $attributes
	 */
	public function __construct( $formname
	                           , $method = 'post'
	                           , $action = ''
	                           , $target = ''
	                           , $id = ''
	                           , $attributes = null
	                           ) {
		$this->_form = array( 'method'     => $method
		                    , 'action'     => $action
		                    , 'target'     => $target
		                    , 'id'         => $id
		                    , 'attributes' => null
		                    , 'pageData'   => ''
		                    ) ;
	}

	/**
	 * 
	 * Render the form to a string
	 * @return string
	 */
	public function renderForm() {
	    $method   = $this->getPropertyValueSetString('method', $this->_form[ 'method' ] ) ;
	    $action   = $this->getPropertyValueSetString('action', $this->_form[ 'action' ] ) ;
	    $target   = $this->getPropertyValueSetString('target', $this->_form[ 'target' ] ) ;
	    $id       = $this->getPropertyValueSetString('id', $this->_form[ 'id' ] ) ;
	    $pageData = $this->_form[ 'pageData' ] ;
	    foreach ( $this->_form[ 'attributes' ] as $key => $value ) {
	        $attributes .= " $key=\"$value\"" ;
	    }
	    return "<form $id $method $action $target>$pageData</form>" ;
	}

    /**
     * Compare fields function for sorting purposes
     */
    public static function cmpFields( $a, $b ) {
        if ( $a->getSortKey() === $b->getSortKey() ) {
            return 0 ;
        }
        return  ( $a->getSortKey() < $b->getSortKey() ) ? -1 : 1 ;
    }

    /**
     * Add data to the form for rendering
     * 
     * @param string $data
     */
    private function addFormData( $data = '' ) {
        $this->_form .= $data ;
    }

    /**
     * 
     * Add HTML Form Element
     * 
     * @param string $elementType
     * @param string $name
     * @param mixed $value string except when a select or multiselect then array of value-label pairs
     * @param mixed $attributes
     * @throws Exception
     */
    public function addElement($elementType, $name, $label, $attributes = array() ) {
        if ( !isset($name) || ( $name === '' ) ) {
            throw new Exception( 'addElement: $name is required' ) ;
        }
        $attrString = $this->getPropertyValueSetString( 'name', $name ) . ' ';
        if ( isset( $attributes[ 'value' ] ) && ( $attributes[ 'value' ] !== '' ) ) {
            $value = $attributes[ 'value' ] ;
            $xValue = $attrString = $this->getPropertyValueSetString( 'value', $value ) . ' ';
            $rValue = isset( $value ) ? htmlspecialchars( $value ) : '' ;
        }
        else {
            $value = null ;
            $xValue = '' ;
            $rValue = '' ;
        }
        if ( isset( $attributes ) && is_array( $attributes ) ) {
            $setAttrs = array() ;
            $attrs = array( 'accept', 'accesskey', 'alt', 'checked', 'class', 'dir'
                          , 'disabled', 'id', 'lang', 'maxlength', 'multiple', 'onblur'
                          , 'onchange', 'onclick', 'ondblclick', 'onfocus', 'onmousedown'
                          , 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup'
                          , 'onkeydown', 'onkeyup', 'onselect', 'readonly', 'size'
                          , 'style', 'tabindex', 'title', 'xml:lang'
                          ) ;
            foreach ( $attrs as $attr ) {
                $result = $this->getPropertyValueSetString( $attr, $attriutes ) ;
                if ( $result ) {
                    $setAttrs[ $attr ] = $result ;
                }
            }
            $attrString .= implode( ' ', $setAttrs ) ;
        }
        switch ( $elementType ) {
            case 'reset'    : // NO BREAK HERE
            case 'submit'   : // NO BREAK HERE
            case 'string'   : // NO BREAK HERE
            case 'hidden'   : // NO BREAK HERE
            case 'button'   : // NO BREAK HERE
            case 'password' : // NO BREAK HERE
            case 'checkbox' : // NO BREAK HERE
            case 'radio'    : // NO BREAK HERE
            case 'file'     :
                $this->addFormData("<input type=\"$elementType\" $attrString $xValue />") ;
                break ;
            case 'date'     :
                $this->addFormData("<input type=\"text\" $attrString $xValue />") ;
                break ;
            case 'datetime' :
                $this->addFormData("<input type=\"text\" $attrString $xValue/>") ;
                break ;
            case 'textbox'  :
                $this->addFormData("<textarea $attrString>$rValue</textarea>") ;
                break ;
            case 'select'   :
                $this->addFormData( "<select $attrString>" ) ;
                foreach ( $value as $val=>$lbl ) {
                    $xVal = $this->getPropertyValueSetString( 'value', $val ) ;
                    $this->addFormData( "<option $xVal>$lbl</option>" ) ;
                }
                $this->addFormData( "</select>" ) ;
                break ;
            default         :
                throw new Exception( 'addElement: Unknown element type: ' . $elementType ) ;
        }
    }

    /**
     * Prepare a form element if this process knows how.  Returns 1 when found,
     * 0 otherwise.
     * 
     * @param  string  $dataType
     * @param  mixed   $form
     * @param  string  $value
     * @param  string  $fieldName
     * @param  string  $fieldLabel
     * @param  string  $fieldHelp
     * @param  boolean $userCanChange
     * @param  integer $maxFieldLength
     * @param  array   $options
     * @return integer
     */
    public function prepFormElement( $dataType
                                   , &$form
                                   , &$value
                                   , $fieldName
                                   , $fieldLabel
                                   , $fieldHelp
                                   , $userCanChange
                                   , $maxFieldLength
                                   , $options = null
                                   ) {
        $foundMatch = 1 ;
    
        switch ( $dataType ) {

            case 'DATETIME' :
            case 'TIMESTAMP' :
                if ( $userCanChange ) {
                    $form->addelement( 'date'
                                     , $fieldName
                                     , $fieldLabel
                                     , $options
                                     ) ;
                    $y=substr( $value, 0, 4 ) ;
                    $m=substr( $value, 5, 2 ) ;
                    $d=substr( $value, 8, 2 ) ;
                    $h=substr( $value, 11, 2 ) ;
                    $i=substr( $value, 14, 2 ) ;
                    $value = array( 'Y' => $y
                                  , 'M' => $m
                                  , 'd' => $d
                                  , 'H' => $h
                                  , 'i' => $i
                                  ) ;
                }
                break ;

            case 'PHONE NUMBER' :
                $this->addElemen( 'text', $fieldName, $fieldLabel, $options ) ;
                break ;

            case ( preg_match( '/^VARCHAR\(([1-9][0-9]+)\)$/'
                             , $dataType
                             , $matches
                             )
                 ? $dataType
                 : ! $dataType
                 ) :
                $length = ( $matches[ 1 ] < $maxFieldLength )
                          ? $matches[ 1 ]
                          : $maxFieldLength
                          ;
                if ( 'url' === $fieldName ) {
                    $this->_form->addElement( 'text'
                                            , $fieldName
                                            , $fieldLabel
                                            , $options
                                            ) ;
                }
                else {
                    $this->_form->addElement( 'text'
                                            , $fieldName
                                            , $fieldLabel
                                            , array( 'size' => $length
                                                   , 'maxlength' => $matches[ 1 ]
                                                   , 'alt' => $fieldHelp
                                                   )
                                            ) ;
                }
                break ;

            case ( preg_match( '/^ENUM\((\'[A-Za-z0-9]+\'(, |,|))+\)$/'
                             , $dataType
                             )
                   ? $dataType
                   : !$dataType
                 ) :
                $listString = preg_replace( '/^ENUM\(/', '', $dataType ) ;
                $listString = preg_replace( '/\)$/', '', $listString ) ;
                $listString = preg_replace( '/\',\\s+\'/', '\',\'', $listString ) ;
                $listString = preg_replace( '/^\'/', '', $listString ) ;
                $listString = preg_replace( '/\'$/', '', $listString ) ;
                $items = split('\',\'', $listString) ;
                foreach ( $items as $k ) {
                    $list[$k] = $k;
                }
                $this->_form->addElement( 'select'
                                        , $fieldName
                                        , $fieldLabel
                                        , $list
                                        , array( 'alt' => $fieldHelp )
                                        ) ;
                break ;

            default :
                $foundMatch = 0 ;
                break ;

        } // END OF switch ( $dataType )
        return $foundMatch ;
    }

	/**
	 * 
	 * Return HTML string property="value" if the value is not empty or null
	 * 
	 * examples:
	 *   $this->getPropertyValueSetString( 'class', '' ) returns an empty string
	 *   $this->getPropertyValueSetString( 'class', 'show' ) returns the string: 'class="show"'
	 * 
	 * @param string $property
	 * @param mixed $value may be string or array of string values indexed by $property
	 * @return string
	 */
	public function getPropertyValueSetString( $property, $value = null ) {
	    $cmpTo = is_array( $value ) ? $value[ $property ] : $value ;
	    if ( isset( $cmpTo ) && ( $cmpTo !== '' ) ) {
	        return "$property=\"" . htmlspecialchars($value) . "\"" ;
	    }
	    return "" ;
    }

}
