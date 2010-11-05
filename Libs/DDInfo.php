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

class DDInfo {

    /** @var mixed */
    private $_details ;

    /** @var mixed */
    private $_supportedTypes ;

    /** @var string */
    private $_dbStyle ;

    /**
     *
     * Class Constructor
     *
     * @param String $tableName
     * @param String $dbStyle
     * For now, just support MySQL and only some of it.
     * @todo to make this handle all MySQL ADTs
     * @wish to make this handle all DB2 ADTs
     * @wish to make this handle all Oracle ADTs
     * @wish to make this handle all Postgres ADTs
     */
    public function __construct( $tableName, $dbStyle = 'MySQL' ) {
        $this->_dbStyle        = $dbStyle ;
        $this->_details        = array( 'columns'  => null
                                      , 'keys'     => null
                                      , 'triggers' => null
                                      ) ;
        $this->_supportedTypes = array() ;
        switch ( $dbStyle ) {
            case 'MySQL' :
                $intExtras             = array( 'allowUnsigned'      => true ) ;
                $enumSetExtras         = array( 'listRequired'       => true ) ;
                $varCharBinaryExtras   = array( 'sizeRequired'       => true ) ;
                $textBlobExtras        = array( 'defaultAllowed'     => false
                                              , 'sizeAllowed'        => false ) ;
                $DecimalExtras         = array( 'sizeRequired'       => true ) ;
                $this->_addSupportedType( 'TINYINT'      , 'TINYINT'    , $intExtras ) ;
                $this->_addSupportedType( 'SMALLINT'     , 'SMALLINT'   , $intExtras ) ;
                $this->_addSupportedType( 'MEDIUMINT'    , 'MEDIUMINT'  , $intExtras ) ;
                $this->_addSupportedType( 'INT'          , 'INT'        , $intExtras ) ;
                $this->_addSupportedType( 'BIGINT'       , 'BIGINT'     , $intExtras ) ;
                $this->_addSupportedType( 'TINYSERIAL'   , 'TINYINT'    , $serialExtras ) ;
                $this->_addSupportedType( 'SMALLSERIAL'  , 'SMALLINT'   , $serialExtras ) ;
                $this->_addSupportedType( 'MEDIUMSERIAL' , 'MEDIUMINT'  , $serialExtras ) ;
                $this->_addSupportedType( 'SERIAL'       , 'INT'        , $serialExtras ) ;
                $this->_addSupportedType( 'BIGSERIAL'    , 'BIGINT'     , $serialExtras ) ;
                $this->_addSupportedType( 'BOOLEAN'      , 'BOOLEAN'    , array() ) ;
                $this->_addSupportedType( 'BITMAP'       , 'BITMAP'     , array() ) ;
                $this->_addSupportedType( 'ENUM'         , 'ENUM'       , $enumSetExtras ) ;
                $this->_addSupportedType( 'SET'          , 'SET'        , $enumSetExtras ) ;
                $this->_addSupportedType( 'BINARY'       , 'BINARY'     , $varCharBinaryExtras ) ;
                $this->_addSupportedType( 'VARBINARY'    , 'VARBINARY'  , $varCharBinaryExtras ) ;
                $this->_addSupportedType( 'CHAR'         , 'CHAR'       , $varCharBinaryExtras ) ;
                $this->_addSupportedType( 'VARCHAR'      , 'VARCHAR'    , $varCharBinaryExtras ) ;
                $this->_addSupportedType( 'TINYBLOB'     , 'TINYBLOB'   , $textBlobExtras ) ;
                $this->_addSupportedType( 'TINYTEXT'     , 'TINYTEXT'   , $textBlobExtras ) ;
                $this->_addSupportedType( 'SMALLBLOB'    , 'SMALLBLOB'  , $textBlobExtras ) ;
                $this->_addSupportedType( 'SMALLTEXT'    , 'SMALLTEXT'  , $textBlobExtras ) ;
                $this->_addSupportedType( 'MEDIUMBLOB'   , 'MEDIUMBLOB' , $textBlobExtras ) ;
                $this->_addSupportedType( 'MEDIUMTEXT'   , 'MEDIUMTEXT' , $textBlobExtras ) ;
                $this->_addSupportedType( 'BLOB'         , 'BLOB'       , $textBlobExtras ) ;
                $this->_addSupportedType( 'TEXT'         , 'TEXT'       , $textBlobExtras ) ;
                $this->_addSupportedType( 'LONGBLOB'     , 'LONGBLOB'   , $textBlobExtras ) ;
                $this->_addSupportedType( 'LONGTEXT'     , 'LONGTEXT'   , $textBlobExtras ) ;
                $this->_addSupportedType( 'REAL'         , 'REAL'       , array() ) ;
                $this->_addSupportedType( 'DOUBLE'       , 'DOUBLE'     , array() ) ;
                $this->_addSupportedType( 'FLOAT'        , 'FLOAT'      , array() ) ;
                $this->_addSupportedType( 'DECIMAL'      , 'DECIMAL'    , $DecimalExtras ) ;
                break ; // END OF case 'MySQL'
            case 'DB2' :      // NO BREAK HERE UNTIL IMPLEMENTED
            case 'Oracle' :   // NO BREAK HERE UNTIL IMPLEMENTED
            case 'Postgres' : // NO BREAK HERE UNTIL IMPLEMENTED
            case 'SQLite' :   // NO BREAK HERE UNTIL IMPLEMENTED
            default :
                throw new Exception( 'Invalid dbStyle' ) ;
                break ;
        } // END OF switch ( $dbStyle )
    }

    /**
     *
     * Return string representation of this class
     * @return String
     * @wish Make this __toString useful
     */
    public function __toString() {
        // Do nothing meaningful for now
        return __CLASS__ . '.__toString() not implemented.' ;
    }

    /**
     * Add a column of DDInfo
     *
     * @param String $name
     * @param String $dataType
     * @param bool $isNullable
     * @param mixed $defaultValue
     * @param mixed $extra
     */
    public function addColumn ( $name
                              , $dataType
                              , $isNullable
                              , $defaultValue = null
                              , $extra = null
                              ) {
        $this->_details[ 'columns' ][] = array( 'name'         => $name
                                              , 'dataType'     => $dataType
                                              , 'isNullable'   => $isNullable
                                              , 'defaultValue' => $defaultValue
                                              , 'extra'        => $extra
                                              ) ;
    }

    /**
     * Add an index to the model
     * @param String $keyType
     * @param String $keyName
     * @param mixed $columns
     * @param mixed $extra
     */
    public function addKey( $keyType
                          , $keyName
                          , $columns
                          , $extra = null
                          ) {
        $this->_details[ 'keys' ][] = array( 'type'    => $keyType
                                           , 'name'    => $keyName
                                           , 'columns' => $columns
                                           , 'extra'   => $extra
                                           ) ;
    }

    /**
     * Add a trigger to the model
     * @param String $triggerName
     * @param String $when
     * @param String $event
     * @param String $definition
     * @param mixed $extra
     */
    public function addTrigger( $triggerName
                              , $when
                              , $event
                              , $definition
                              , $extra = null
                              ) {
        $this->_details[ 'triggers' ][] = array( 'name'       => $triggerName
                                               , 'when'       => $when
                                               , 'event'      => $event
                                               , 'definition' => $definition
                                               , 'extra'      => $extra
                                               ) ;
    }

    /**
     * Getter for $_details
     *
     * @return mixed
     */
    public function getDetails() {
        return ( $this->_details ) ;
    }

    /**
     * Setter for $_details
     *
     * @param mixed
     * @return void
     */
    protected function setDetails( $value ) {
        $this->_details = $value ;
    }

    /**
     * Getter for $_supportedTypes
     *
     * @return mixed
     */
    public function getSupportedTypes() {
        return ( $this->_supportedTypes ) ;
    }

    /**
     * Setter for $_supportedTypes
     *
     * @param mixed
     * @return void
     */
    protected function setSupportedTypes( $value ) {
        $this->_supportedTypes = $value ;
    }

    /**
     * Getter for $_dbStyle
     *
     * @return string
     */
    public function getDbStyle() {
        return ( $this->_dbStyle ) ;
    }

    /**
     * Setter for $_dbStyle
     *
     * @param string
     * @return void
     */
    protected function setDbStyle( $value ) {
        $this->_dbStyle = $value ;
    }

    private function _addSupportedType( $label, $baseType, $extraSupports ) {
        $this->_supportedTypes[ $label ]
          = array( 'baseType'      => $baseType
                 , 'extraSupports' => $extraSupports
                 ) ;

    }

} // END OF DDInfo
