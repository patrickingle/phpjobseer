<?php

require_once('HTML/QuickForm.php');

abstract class FormViewBase {

	protected $_form = null;

	/**
	 * 
	 * Class constructor
	 * @param String $formname
	 * @param String $method
	 * @param String $action
	 * @param String $target
	 * @param mixed $attributes
	 * @param bool $trackSubmit
	 */
	public function __construct( $formname
	                           , $method = 'post'
	                           , $action = ''
	                           , $target = ''
	                           , $attributes = null
	                           , $trackSubmit = false
	                           ) {
		$this->_form = new HTML_QuickForm( $formname
		                                 , $method
		                                 , $action
		                                 , $target
		                                 , $attributes
		                                 , $trackSubmit
		                                 ) ;
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
                $form->addelement( 'text', $fieldName, $fieldLabel, $options ) ;
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

}
