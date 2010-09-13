<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php' ;

set_include_path( '..' . PATH_SEPARATOR . get_include_path() );

require_once 'Libs/Config.php' ;

class VeryBasicFirstTest extends PHPUnit_Extensions_SeleniumTestCase
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
        $this->checkHeaderIsLoaded() ;
    }

    function testAddNewJobLinkFromHeaderWorks()
    {
        self::testHeaderLoads() ;
        foreach ( $this->_headerLabels as $label ) {
            $this->click( "link=$label" ) ;
            $this->waitForPageToLoad( self::maxWaitTime ) ;
            $this->checkHeaderIsLoaded() ;
            $this->checkFooterIsLoaded() ;
        }
    }

    private function checkHeaderIsLoaded() {
        try {
            $this->assertFalse( $this->isTextPresent( 'PHP Stack Trace' ) ) ;
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            $this->verificationErrors[] = $e->toString() ;
        }
        try {
            $this->assertTrue( $this->isTextPresent( $this->_pageTitle ) ) ;
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            $this->verificationErrors[] = $e->toString() ;
        }
        foreach ( $this->_headerLabels as $label ) {
            try {
                $this->assertTrue( $this->isTextPresent( $label ) ) ;
            } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
                $this->verificationErrors[] = $e->toString() ;
            }
        }
        // TODO verify that the search function is available.
   }

   private function checkFooterIsLoaded() {
       try {
       	   $this->assertTrue( $this->isTextPresent( 'Want your own copy of this tool?' ) ) ;
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            $this->verificationErrors[] = $e->toString() ;
       }
   }
}
