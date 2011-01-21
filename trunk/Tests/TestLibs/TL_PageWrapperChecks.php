<?php

class TL_PageWrapperChecks {

	/** @var TL_Config */
	private static $_TLC ;
	/** @var PHPUnit_Extensions_SeleniumTestCase */
	private $_selTC ;

	/**
	 * 
	 * Class constructor
	 * @param PHPUnit_Extensions_SeleniumTestCase $selTC
	 * @throws Exception
	 */
	public function __construct( $selTC = null ) {
		if  ( ( ! isset( $selTC ) )
		   || ( ! $selTC instanceof PHPUnit_Extensions_SeleniumTestCase )
		    ) {
			throw new Exception( "Improper construct." ) ;
		}
		$this->_selTC = $selTC ;
		// Use cached TL_Config since it doesn't change.
		if ( ! isset( $this->_TLC ) ) {
			$this->_TLC = new TL_Config() ;
		}
	}

	/**
	 *
	 * Check that the page footer is loaded.
	 * @param PHPUnit_Extensions_SeleniumTestCase $selTC
	 * @param String $label
	 */
	public function checkFooterIsLoaded( $label = '' ) {
		try {
			$this->_selTC->assertTextPresent( 'Want your own copy of this tool?' ) ;
		} catch ( PHPUnit_Framework_AssertionFailedError $e ) {
			$this->_selTC->verificationErrors[] = "Checking footer ($label): " . $e->toString() ;
		}
	}

	/**
	 *
	 * Check that the page header is loaded.
	 * @param PHPUnit_Extensions_SeleniumTestCase $selTC
	 * @param String $label
	 * @todo  Verify that the search function is available.
	 */
	public function checkHeaderIsLoaded( $label = '' ) {
		try {
			$this->_selTC->assertTextNotPresent( 'PHP Stack Trace' ) ;
		} catch ( PHPUnit_Framework_AssertionFailedError $e ) {
			$this->_selTC->verificationErrors[] = "Checking header ($label): " . $e->toString() ;
		}
		try {
			$this->_selTC->assertTextPresent( $this->_TLC->pageTitle ) ;
		} catch ( PHPUnit_Framework_AssertionFailedError $e ) {
			$this->_selTC->verificationErrors[] = "Checking header ($label): " . $e->toString() ;
		}
		foreach ( $this->_TLC->headerLabels as $label ) {
			try {
				$this->_selTC->assertTextPresent( $label ) ;
			} catch ( PHPUnit_Framework_AssertionFailedError $e ) {
				$this->_selTC->verificationErrors[] = "Checking header ($label): " . $e->toString() ;
			}
		}
                $buttons = $this->_selTC->getAllButtons() ;
                var_dump( $buttons ) ;
	}
}
