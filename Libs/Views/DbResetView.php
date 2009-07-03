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

require_once("Libs/autoload.php");

class DbResetView {

    /**
     * @var mixed Configuration values from class Config
     */
    private $_configValues;

    /**
     * @var mixed Database Handle
     */
    private $_dbh;

    /**
     * Class constructor
     * 
     */
    public function __construct() {
        $oConfig = new Config();
        $this->_configValues = $oConfig->values;
        $this->_dbh = mysql_connect( $this->_configValues['db_host']
                                   . ':'
                                   . $this->_configValues['db_port']
                                   , $this->_configValues['db_user']
                                   , $this->_configValues['db_pass']
                                   );
        mysql_select_db($this->_configValues['db_name'], $this->_dbh);
        $this->_sth = null;
    }

    /**
     * Execute statements within an SQL file.  Rudimentary ability to find ;'s - could be imporved.
     * 
     * @param $filename String Name of file to be processed
     * @return void
     */
    private function processSqlFile($filename) {
        $filehandle = fopen($filename, 'r');
        $currentLine = '';
        echo '<pre>';
        while ( $thisLine = fgets($filehandle) ) {
            $thisLine     = str_replace("\n", '', $thisLine);
            $thisLine     = preg_replace('/[-][-][\s].*$/', '', $thisLine);
            if ( ";" === substr($thisLine, strlen($thisLine) - 2, 1) ) {
                $thisLine = preg_replace('/;$/', '', $thisLine);
                $currentLine .= $thisLine;
                if ( !mysql_query($currentLine, $this->_dbh) ) {
                    fclose($filehandle); // Oh, if only PHP had try/catch/FINALLY
                    throw new Exception( 'Invalid query: '
                                       . $currentLine
                                       . ' - '
                                       . mysql_error($this->_dbh)
                                       );
                }
                $currentLine = '';
            }
            else {
                $currentLine .= $thisLine;
            }
        }
        echo '</pre>';
        fclose($filehandle);
    }

    /**
     * Reset the database to a sane pre-loaded state.
     * 
     * @return void
     */
    public function main() {
        PageData::pageHeader();
        echo '<div class="pageTitle">PHP Job Seeker</div>';
        PageData::displayNavBar();
        echo '<div>Resetting database to known state.</div>';
        mysql_query('DROP DATABASE IF EXISTS ' . $this->_configValues['db_name'], $this->_dbh);
        mysql_query('CREATE DATABASE ' . $this->_configValues['db_name'], $this->_dbh);
        mysql_select_db($this->_configValues['db_name'], $this->_dbh);
        $this->processSqlFile('create_pjs_db.sql');
        echo '<div>A clean database should be loaded.</div>';
        PageData::pageFooter();
    }
}
