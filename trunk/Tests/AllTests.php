<?php

require_once 'PHPUnit/Framework.php' ;
require_once 'Libs/autoload.php' ;

class AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite( 'PHPUnit' ) ;
        $suite->addTest( Framework_AllTests::suite() ) ;
        return $suite ;
    }
}

