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

class JobKeywordDao extends DaoBase {

    /**
     * validateRowForInsertOrUpdate does all the "other" checks needed to verify
     * a row is valid for insert/update besides whether or not the row ID is
     * present or not.
     */
    public function validateRowForInsertOrUpdate($rowValues) {
        return ( isset($rowValues)
              && isset($rowValues['keywordId'])
              && isset($rowValues['jobId'])
              && KeywordDao::validateRowId($rowValues['keywordId'])
              && JobDao::validateRowId($rowValues['jobId'])
               );
    }

    /**
     * validateRowForInsert checks to make sure that data being inserted is valid.
     *
     * @param array $rowValues Hash of row keys / values to be checked
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForInsert($rowValues) {
        return ( self::validateRowForInsertOrUpdate($rowValues) );
    }

    /**
     * validateRowForUpdate checks to make sure that data being updated is valid.
     *
     * @param array $rowValues Hash of row keys / values to be checked
     * @return boolean True when validation passes, false otherwise.
     */
    public function validateRowForUpdate($rowValues) {
        return ( self::validateRowForInsertOrUpdate($rowValues) );
    }

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct($fieldValues = null) {
        parent::__construct('jobKeywordMap');
        $this->populateFields($fieldValues);
    }

    /**
     * static function that creates a new DDInfo record and returns it set up
     * for the concrete class.
     * @param $dbName Name of the table
     * @param $dbStyle Style of database to create
     * @return DDInfo
     */
    static public function getDDInfo($tableName, $dbStyle) {
        $info = new DDInfo($tableName, $dbStyle) ;
        $info->addColumn( 'jobId'
                        , 'INT'
                        , false
                        , null
                        , array( 'unsigned' => true )
                        ) ;
        $info->addColumn( 'kewordId'
                        , 'INT'
                        , false
                        , null
                        , array( 'unsigned' => true )
                        ) ;
        $info->addKey( 'PRIMARY'
                     , 'jobPk'
                     , array( 'jobId', 'kewordId' )
                     ) ;
        $info->addKey( 'FOREIGN'
                     , 'jobKeywordPk'
                     , array( 'jobId' )
                     , array( 'references' => 'job(jobId)'
                            , 'onDelete' => 'CASCADE'
                            , 'onUpdate' => 'CASCADE'
                            )
                     ) ;
        $info->addKey( 'FOREIGN'
                     , 'keywordFk'
                     , array( 'keywordId' )
                     , array( 'references' => 'keyword(keywordId)'
                            , 'onDelete' => 'CASCADET'
                            , 'onUpdate' => 'CASCADE'
                            )
                     ) ;
        return $info() ;
    }

    /**
     * getDefaults acts like DaoBase::getRowById returning a hash of fields to
     * column values to be used by the insertRow routine to compare values with
     * for default values at row insertion time.
     *
     * @return array Default values for new records
     */
    public function getDefaults() {
        return array( 'jobId' => ''
                    , 'keywordId' => ''
                    , 'created' => ''
                    , 'updated' => ''
                    );
    }

    /**
     * populateFields creates an array of FieldDescription's.  This function is
     * called by getRowById to fulfill data requests.
     *
     * @param array $fieldValues A hash of field values by field names.
     * @return void
     */
    public function populateFields($fieldValues) {
        $_fieldDescriptions = array();

        $x = new FieldDescription();
        $y = isset($fieldValues['keywordId']) ? $fieldValues['keywordId'] : null;
        $x->setAllFields( 'jobId'               // $fieldName
                        , $y                    // $fieldValue
                        , 'INTEGER UNSIGNED'    // $dataType
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Id'                  // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['keywordId']) ? $fieldValues['keywordId'] : null;
        $x->setAllFields( 'keywordId'           // $fieldName
                        , $y                    // $fieldValue
                        , 'INTEGER UNSIGNED'    // $dataType
                        , 1                     // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Id'                  // $fieldLabel
                        , ''                    // $quote
                        , null                  // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['created']) ? $fieldValues['created'] : null;
        $x->setAllFields( 'created'             // $fieldName
                        , $y                    // $fieldValue
                        , 'TIMESTAMP'           // $dataType
                        , 999                   // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Created'             // $fieldLabel
                        , '\''                  // $quote
                        , 'When was this record created?'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

        $x = new FieldDescription();
        $y = isset($fieldValues['updated']) ? $fieldValues['updated'] : null;
        $x->setAllFields( 'updated'             // $fieldName
                        , $y                    // $fieldValue
                        , 'TIMESTAMP'           // $dataType
                        , 999                   // $sortKey
                        , 0                     // $userCanChange
                        , 1                     // $userCanSee
                        , 'Updated'             // $fieldLabel
                        , '\''                  // $quote
                        , 'When was this record last updated?'
                                                // $fieldHelp
                        , null                  // $fieldValidator
                        );
        $_fieldDescriptions[] = $x;

    }

    /**
     * Find a listing of Job ID's by keyword ID and Job ApplicationStatusId
     *
     * @param $keywordId int
     * @param $applicationStatusList array List of Application Status ID's
     * @return array
     */
    public function findJobsIdsByKeywordIdAndApplicationStatusValue($keywordId, $applicationStatusList) {
        if (null === $keywordId) {
            return array();
        }
        $restrictions = "keywordId = $keywordId";
        $optionalJoin = '';
        if ( count($applicationStatusList)>0 ) {
            $optionalJoin .= " INNER"
                          .   " JOIN applicationStatus"
                          .     " AS ast"
                          .     " ON ast.applicationStatusId = job.applicationStatusId";
            $restrictions .= " AND statusValue IN ( '"
                          .  join("', '", $applicationStatusList)
                          .  "' )";
        }
        $query = "SELECT job.jobId"
               .  " FROM job"
               . " INNER"
               .  " JOIN jobKeywordMap"
               .    " AS jkMap"
               .    " ON jkMap.jobId = job.jobId"
               . $optionalJoin
               . " WHERE $restrictions";
        $this->_sth = $this->_oDbh->query($query);
        $results = array();
        if ( ! $this->_sth ) {
            return $results; // no data available.
        }
        while ($row = $this->_sth->fetch_assoc()) {
            $results[] = $row;
        }
        return $results;
    }

    /**
     * Find keyword values by job ID and return them as a string.
     *
     * @param $jobId
     * @return String
     * @throws Exception
     */
    public function findKeywordValuesByJobId($jobId = '') {
        if ( '' === $jobId ) {
            return '';
        }
        if ( ! preg_match('/^[0-9]+$/', $jobId) ) {
            throw new Exception('Invalid jobId');
        }
        $query = 'SELECT GROUP_CONCAT(DISTINCT keywordValue ORDER BY kewordValue SEPARATOR \', \')'
               .  ' FROM jobKeywordMap'
               .    ' AS jkmap'
               . ' INNER'
               .  ' JOIN keyword'
               .    ' AS k'
               .    ' ON k.keywordId = jkmap.keywordId'
               . ' WHERE jkmap.keywordId = ' . $jobId;
        $this->_sth = $this->_oDbh->query($query);
        $results = '';
        if ( ! $this->_sth ) {
            return $results; // no data available.
        }
        $results = $this->_sth->fetch_field();
        $this->_sth->free();
        return $results;
    }
}
