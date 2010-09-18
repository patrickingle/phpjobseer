<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php' ;
require_once 'Libs/autoload.php' ;

class Framework_PJSTestSeries0000Init extends PHPUnit_Extensions_SeleniumTestCase
{


    /** @var TL_PageWrapperChecks */
	private $_PWC ;
	/** @var TL_Config */
	private $_TLC ;

    function setUp()
    {
    	$this->_PWC = new TL_PageWrapperChecks( $this ) ;
    	$this->_TLC = new TL_Config() ;
        $this->setBrowser( $this->_TLC->browser ) ;
        $this->setBrowserUrl( $this->_TLC->browserRoot ) ;
    }

    function testDatabaseResetOutputOk()
    {
        $this->open( $this->_TLC->browserDir . 'resetDb.php' ) ;
        $this->waitForPageToLoad( $this->_TLC->maxWaitTime ) ;
    	try {
            $this->assertTextNotPresent( 'PHP Stack Trace' ) ;
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            $this->verificationErrors[] = "Checking body ($label): " . $e->toString() ;
        }
        try {
        	$needle = "<div>Resetting database to known state.</div>"
        	        . "<div>A clean database should be loaded.</div>"
        	        ;
            $this->assertTrue( strpos( $this->getHtmlSource(), $needle ) > 0 ) ;
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            $this->verificationErrors[] = "Checking body : " . $e->toString() ;
        }
    }

}
