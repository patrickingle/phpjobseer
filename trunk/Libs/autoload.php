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

require_once( 'Libs/Config.php' ) ;

function __autoload( $class_name ) {
    error_reporting(-1);
    ini_set('display_errors', 'on');
    $oConfig = new Config() ;
    date_default_timezone_set( $oConfig->getValue( 'timeZone' ) ) ;
    $script_tz = date_default_timezone_get();
    if ( strcmp( $script_tz, ini_get( 'date.timezone' ) ) ) {
        $errMsg = "Timezone Difference: $script_tz != " . ini_get( 'date.timezone' ) ;
        echo "$errMsg\n" ;
        throw new Exception ( $errMsg ) ;
//    } else {
//        echo 'Script timezone and ini-set timezone match.';
    }
    switch (true) {
        case ( preg_match( '/Controller(Base|)$/', $class_name ) ) :
            $reqFile = 'Libs/Controllers/' . $class_name . '.php' ;
            break ;
        case ( preg_match( '/Dao(Base|)$/', $class_name ) ) :
            $reqFile = 'Libs/Dao/' . $class_name . '.php' ;
            break ;
        case ( preg_match( '/View(Base|)$/', $class_name ) ) :
            $reqFile = 'Libs/Views/' . $class_name . '.php' ;
            break ;
        case ( preg_match( '/^TL_/', $class_name ) ) :
        	$reqFile = 'Tests/TestLibs/' . $class_name . '.php' ;
            break ;
        case ( preg_match( '/^Framework/', $class_name ) ) :
        	$class_name = str_replace( '_', '/', $class_name . '.php' ) ;
        	$reqFile = 'Tests/' . $class_name ;
            break ;
        default :
            $reqFile = 'Libs/' . $class_name . '.php' ;
            break ;
    } // END OF switch (true)
    if ( ! is_file( $reqFile ) ) {
    	echo "Class file $reqFile does not exist." ;
    	exit(1) ;
    }
    try {
        require_once $reqFile ;
    }
    catch ( Exception $e ) {
    	echo $e->getMessage(), "\n";
    	exit(1) ;
    }
}
