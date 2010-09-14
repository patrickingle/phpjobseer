<?php

require_once 'PHPUnit/Framework.php' ;
require_once 'Libs/autoload.php' ;

class Framework_AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite( 'PHPUnit Framework' ) ;
        $suite->addTestSuite( 'Framework_PJSTestSeries0000Init' ) ;
        $suite->addTestSuite( 'Framework_PJSTestSeries0011CheckHeaders' ) ;
        return $suite ;
    }
}

