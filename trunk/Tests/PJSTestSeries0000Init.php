<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php' ;

set_include_path( '..' . PATH_SEPARATOR . get_include_path() );

require_once 'Libs/Config.php' ;

class PJSTestSeries0000Init extends PHPUnit_Extensions_SeleniumTestCase
{

    const maxWaitTime = 6000 ;
    private $_pageTitle = "PHP Job Seeker" ;
    private $_headerLabels = array( "Summary"
                                  , "Add New Job"
                                  , "All Jobs"
                                  , "Active Jobs"
                                  , "Contacts"
                                  , "Companies"
                                  , "Keywords"
                                  , "Help/Documentation"
                                  ) ;
    private $_config ;

    function setUp()
    {
        $this->_config = new Config() ;
        $this->setBrowser( "*firefox" ) ;
        $this->setBrowserUrl( $this->_config->values['browserRoot'] ) ;
    }

    function testDatabaseResetOutputOk()
    {
        $this->open( $this->_config->values['browserDir'] . 'resetDb.php' ) ;
        $this->waitForPageToLoad( self::maxWaitTime ) ;
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
