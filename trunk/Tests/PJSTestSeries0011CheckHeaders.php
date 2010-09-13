<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php' ;

set_include_path( '..' . PATH_SEPARATOR . get_include_path() );

require_once 'Libs/Config.php' ;

class PJSTestSeries0011CheckHeaders extends PHPUnit_Extensions_SeleniumTestCase
{

    const maxWaitTime = 3000 ;
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

    function testHeaderLoads()
    {
        $this->open( $this->_config->values['browserDir'] ) ;
        $this->waitForPageToLoad( self::maxWaitTime ) ;
        $this->checkHeaderIsLoaded( 'index' ) ;
        $this->checkFooterIsLoaded( 'index' ) ;
    }

    function testHeaderLinksWork()
    {
        self::testHeaderLoads() ;
        foreach ( $this->_headerLabels as $label ) {
            $this->click( "link=$label" ) ;
            $this->waitForPageToLoad( self::maxWaitTime ) ;
            $this->checkHeaderIsLoaded( $label ) ;
            $this->checkFooterIsLoaded( $label ) ;
        }
    }

    private function checkHeaderIsLoaded(  $label = '' ) {
        try {
            $this->assertTextNotPresent( 'PHP Stack Trace' ) ;
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            $this->verificationErrors[] = "Checking header ($label): " . $e->toString() ;
        }
        try {
            $this->assertTextPresent( $this->_pageTitle ) ;
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            $this->verificationErrors[] = "Checking header ($label): " . $e->toString() ;
        }
        foreach ( $this->_headerLabels as $label ) {
            try {
                $this->assertTextPresent( $label ) ;
            } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
                $this->verificationErrors[] = "Checking header ($label): " . $e->toString() ;
            }
        }
        // TODO verify that the search function is available.
   }

   private function checkFooterIsLoaded( $label = '' ) {
       try {
       	   $this->assertTextPresent( 'Want your own copy of this tool?' ) ;
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            $this->verificationErrors[] = "Checking footer ($label): " . $e->toString() ;
       }
   }
}
