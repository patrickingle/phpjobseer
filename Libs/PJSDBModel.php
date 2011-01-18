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

class PJSDBModel {

    /** @var array Hash of configuration keys to values */
    private $_configValues = null;

    /** @var DDModel */
    private $_model = null ;

    /** @var String */
    private $_dbName = null ;

    /** @var String */
    private $_dbStyle = null ;

    /**
     * Class Constructor
     *
     */
    public function __construct() {
        $oConfig = new Config() ;
        $this->_configValues = $oConfig->values ;
        $this->_dbName = $this->_configValues[ 'db_name'  ] ;
        $this->_dbStyle = $this->_configValues[ 'db_style' ] ;
        $this->_model = new DDModel( $this->_dbName
                                   , $this->_dbStyle
                                   ) ;
        $this->_model->addInfo( ApplicationStatusDao::getDDInfo( 'applicationStatus'
                                               , $this->_dbStyle
                                               )
                              ) ;
        $this->_model->addInfo(
                ApplicationStatusSummaryDao::getDDInfo( 'applicationStatusSummary'
                                                      , $this->_dbStyle
                                                      )
                              ) ;
        $this->_model->addInfo(
                CompanyDao::getDDInfo( 'company'
                                     , $this->_dbStyle
                                     )
                              ) ;
        $this->_model->addInfo(
                ContactDao::getDDInfo( 'contact'
                                     , $this->_dbStyle
                                     )
                              ) ;
        $this->_model->addInfo(
                JobDao::getDDInfo( 'job'
                                 , $this->_dbStyle
                                 )
                              ) ;

//        var_dump( 'info', $this->_model ) ;
        echo "Hey me!\n" ; exit ;

        $this->_model->addInfo(
                JobKeywordDao::getDDInfo( 'jobKeyword'
                                        , $this->_dbStyle
                                        )
                              ) ;
        $this->_model->addInfo(
                KeywordDao::getDDInfo( 'keyword'
                                     , $this->_dbStyle
                                     )
                              ) ;
        $this->_model->addInfo(
                NoteDao::getDDInfo( 'note'
                                  , $this->_dbStyle
                                  )
                              ) ;
        $this->_model->addInfo(
                SearchDao::getDDInfo( 'search'
                                    , $this->_dbStyle
                                    )
                              ) ;

    }

    /**
     *
     * List of DDModel structures
     */
    public function getModels() {
        return $this->_model ;
    }

    /**
     *
     * Return string representation of this class
     * @return String
     */
    public function __toString() {
        // Do nothing meaningful for now
        return __CLASS__ . '.__toString() not implemented.' ;
    }

    /**
     * Class Destructor
     *
     */
    public function __destruct() {
        // Do nothing for now
    }
}