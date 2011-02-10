<?php
/**
 * Copy this file to Config.php but be sure to edit the file first.
 * If you don't read the entire file, you'll find out that syntax
 * errors will occur.
 */

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


/**
 * The proper way to use the config class is to first create
 * a new instance of the class, then utilize the $values
 * hash to pull out what's needed.
 *
 * $oConfig = new Config() ;
 * echo $oConfig->getValue( $key ) ;
 * 
 * The values are gathered from PJSConfig.xml
 */

require_once("Libs/autoload.php");

class Config {
    /**
     * Configuration Class
     * 
     * Requires config.xml formatted like this:
     * 
     * <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
     * <configuration>
     *     <!-- CAUTION: If test_mode is set to 1, it will be
     *                   possible to run resetDb.php and it will
     *                   wipe all existing user data from the
     *                   database. Use only in a testing
     *                   environment and then only with extreme
     *                   caution. -->
     *     <value name="db_style">MySQL</value>
     *     <value name="timezone">America/Chicago</value>
     *     <value name="db_host">CHANGEME</value>
     *     <value name="db_name">CHANGEME</value>
     *     <value name="db_user">CHANGEME</value>
     *     <value name="db_pass">CHANGEME</value>
     *     <value name="really_update_db">1</value><!-- This is the default -->
     *     <value name="debug_mode">0</value><!-- This is the default -->
     *     <value name="test_mode">0</value><!-- This is safe the default -->
     *     <value name="browserRoot">http://localhost</value>
     *     <value name="browserDir">/test_dir/</value>
     * </configuration>
     *
     */

    /** Where the configuration values are stored */
    static private $_values = null ;

    /**
     * Class Constructor
     * 
     * @throws Exception
     */
    public function __construct() {
        if ( isset( self::$_values ) ) {
            return ; // No sense in re-reading the config file many times.
        }
        $configFileName = 'PJSConfig.xml' ;
        if ( ! is_readable( $configFileName ) ) {
            throw new Exception( "Unable to load configuration from $configFileName!" ) ;
        }
        $xml = simplexml_load_file( $configFileName ) ;
        if ( ! $xml ) {
            throw new Exception( "Invalid syntax in $configFileName!" ) ;
        }
        $errors = "" ;
        $cfgValues = array() ;
        $paramList = array( 'db_style'         => 0
                          , 'timeZone'         => 0
                          , 'db_host'          => 0
                          , 'db_name'          => 0
                          , 'db_user'          => 0
                          , 'db_pass'          => 0
                          , 'really_update_db' => 0
                          , 'debug_mode'       => 0
                          , 'test_mode'        => 0
                          , 'browserRoot'      => 0
                          , 'browserDir'       => 0
                          ) ;
        // verify that all the parameters are present and just once.
        foreach ( $xml as $v ) {
            $key = $v[ 'name' ] ;
            if  ( ( ! isset( $paramList[ $key ] ) )
               || ( $paramList[ $key ] > 0 ) ) {
                $errors .= "Invalid or multiply set parameter: $key\n" ;
            }
            else {
                $paramList[ $key ]++ ;
                $cfgValues[ $key ] = $v[ 0 ] ;
            }
        }
        foreach ( $paramList as $key=>$cnt ) {
            if ( $cnt === 0 ) {
                $errors .= "Missing parameter: $key\n" ;
            }
        }
        if ( $errors !== '' ) {
            throw new Exception( "\nConfiguration problem! Check $configFileName\n\n$errors" ) ;
        }
        self::$_values = $cfgValues ;
    }

    /**
     * Get a configuration value
     * 
     * @param String $key
     * @return mixed
     */
    public function getValue( $key ) {
        return ( isset( self::$_values[ $key ] ) ? self::$_values[ $key ] : null ) ;
    }
}
