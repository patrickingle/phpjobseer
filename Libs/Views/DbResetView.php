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
     * @var Config Configuration values from class Config
     */
    private $_oConfig ;

    /**
     * @var mixed Database Handle
     */
    private $_oDbh ;

    /**
     * @var boolean Debug mode
     */
    private $_debugMode ;

    /**
     * @var boolean Test Mode
     */
    private $_testMode ;

    /**
     * @var String Database Name
     */
    private $_dbName ;
    
    /**
     * Class constructor
     *
     */

    public function __construct() {
        $oConfig = new Config() ;
        $this->_debugMode = $oConfig->getValue( 'debug_mode' ) ;
        $this->_testMode = $oConfig->getValue( 'test_mode' ) ;
        $this->_dbName = $oConfig->getValue( 'db_name' ) ;
        $this->_oConfig = $oConfig ;
        $this->_oDbh = new mysqli( $oConfig->getValue( 'db_host' )
                                 , $oConfig->getValue( 'db_user' )
                                 , $oConfig->getValue( 'db_pass' )
                                 , $this->_dbName
                                 , $oConfig->getValue( 'db_port' )
                                 ) ;
        $this->_sth = null;
    }

    /**
     * Strip comments from the string
     *
     * @param String $fileContents
     * @return String
     */
    public function stripComments( $fileContents ) {
        $contentLen = strlen( $fileContents ) ;
        $outputString = '' ;
        $quote = '' ;
        for ( $i = 0 ; $i < $contentLen ; $i++ ) {
            $char = $fileContents[ $i ] ;
            if ( '' === $quote ) {
                // double-dash comment
                if (    ( $i + 2 < $contentLen )
                     && ( '-' === $char )
                     && ( '-' === $fileContents[ $i + 1 ] )
                     && ( ' ' === $fileContents[ $i + 2 ] )
                   ) {
                    $i += 2 ;
                    // Look for the EOL
                    while ( ( $i < $contentLen ) && ! Tools::isEol( $fileContents[ $i ] ) ) {
                        $i++ ;
                    }
                    if ( $i < $contentLen ) { $i-- ; }
                    continue ;
                }
                // slash-star comment
                if (    ( $i + 1 < $contentLen )
                     && ( '/' === $char )
                     && ( '*' === $fileContents[ $i + 1 ] )
                   ) {
                    $i += 2 ;
                    while (    ( $i + 1 < $contentLen )
                            && ( "*" !== $fileContents[ $i ] )
                            && ( "/" !== $fileContents[ $i + 1 ] )
                          ) {
                        $i++ ;
                    }
                    if (    ( '*' === $fileContents[ $i ] )
                         && ( '/' === $fileContents[ $i + 1 ] )
                       ) {
                           $i++ ;
                           continue ;
                    }
                    else {
                        throw new Exception ( "Un-terminated /* comment!" ) ;
                    }
                }
            } // END OF if ( '' === $quote )
            // This is not part of a comment (continues didn't fire from above)
            $outputString .= $fileContents[ $i ] ;
            // MySQL completely ignores the \ char inside a comment so don't process them.
            if ( '"' === $char || "'" === $char || '`' === $char ) {
                if ( '' === $quote ) {
                    // Start of a new quoted string; ends with same quote
                    $quote = $char ;
                }
                else if ( $char === $quote ) {
                    // Current char matches quote char; quoted string ends
                    $quote = '' ;
                }
            }
        }
        return ( $outputString ) ;
    }

    /**
     * This is a delimiter?
     * 
     * @param String $str
     * @return boolean
     */
    private function matchesDelimiter( $str ) {
       if ( preg_match( '/^delimiter\s+(.+)$/i'
                       , trim( $str )
                       , $matches
                       ) ) {
            return $matches[1] ;
        }
        else {
            return "" ;
        }
        
    }
    /**
     * Execute statements within an SQL file.  Rudimentary ability to find ;'s - could be improved.
     *
     * @param $filename String Name of file to be processed
     * @return void
     * @throw Exception
     */
    private function processSqlFile( $fileName ) {
        if ( ! file_exists( $fileName ) ) {
            throw new Exception( 'Unable to locate file: ' . $fileName ) ;
        }
        $fileContents = file_get_contents( $fileName ) ;
        if ( ! $fileContents ) {
            throw new Exception( 'Unable to read file: ' . $fileName ) ;
        }

        $fileContents = self::stripComments( $fileContents ) ;

        $currentLine    = '' ;
        $delim          = ';' ;
        $stdDelim       = ';' ;
        $delimLen       = 1 ;
        $quote          = '' ;
        $ignoreNextChar = 0 ;
        $sqlToExecute   = array() ;
        $delimLongLine  = '' ;
        $contentLen     = strlen( $fileContents ) ;
        for ( $i = 0 ; $i < $contentLen ; $i++ ) {
            $char = $fileContents[ $i ] ;
            if ( 1 === $this->_debugMode ) {
                echo "Processing \"$char\"\n" ;
            }
            if ( ( '' === $quote ) && ! $ignoreNextChar ) {
                if ( '\\' === $char ) {
                    $ignoreNextChar = TRUE ;
                }
                else {
                    $ignoreNextChar = FALSE ;
                }
            }
            // IGNORE DELIMITER CHANGE LINES
            $match = self::matchesDelimiter( $currentLine ) ;
            if (    Tools::isEol($char)
                 && ( ! $ignoreNextChar )
                 && ( '' === $quote )
                 && ( "" != $match )
               ) {
                $delim = $match ;
                $delimLen = strlen( $delim ) ;
                if ( 1 === $this->_debugMode ) {
                    echo "Changed delimiter to \"$delim\"\n" ;
                }
                $currentLine = '' ;
                if ( $stdDelim === $delim ) {
                    // Don't add delimeter changes to outgoing SQL
                    // $sqlToExecute[] = $delimLongLine ;
                    $delimLongLine = '' ;
                }
                while ( ! Tools::isEol( $fileContents[ $i ] ) ) {
                    $i++ ;
                }
                while ( Tools::isEol( $fileContents[ $i ] ) ) {
                    $i++ ;
                }
                $i-- ;
                continue ;
            }
            else {
                $currentLine .= $char ;
                if ( ! $ignoreNextChar ) {
                    $delimLongLine .= $char ;
                    if ( ( strlen( $currentLine ) > $delimLen )
                      && ( $delim === substr( $currentLine, 0 - $delimLen ) )
                      && ( '' === $quote )
                      && ( ! self::matchesDelimiter( $currentLine ) ) 
                       ) {
                        $currentLine = trim( $currentLine ) ;
                        $toPush = substr( $currentLine
                                        , 0
                                        , strlen( $currentLine ) - $delimLen
                                        ) ;
                        if ( 1 === $this->_debugMode ) {
                            echo "Pushing $toPush\n" ;
                        }
                        $sqlToExecute[] = $toPush ;
                        $currentLine = '' ;
                    } else if ( '\\' === $char ) {
                        // Escape char; ignore the next char in the string
                        $ignoreNextChar = TRUE ;
                    } else if ( '"' === $char || "'" === $char || '`' === $char ) {
                        if ( '' === $quote ) {
                            // Start of a new quoted string; ends with same quote
                            $quote = $char ;
                        }
                        else if ( $char === $quote ) {
                            // Current char matches quote char; quoted string ends
                            $quote = '' ;
                        }
                    }
                }
                else {
                    $ignoreNextChar = FALSE ;
                }
            }
        } // END OF for ( $i = 0 ; $i < $contentLen ; $i++ )

        // Extra check to handle EOF without newline before it.
        /* @todo Put this into a function and strip trailing \r, \n */
        /* FIXME - This should be against currentLine, not dll */
//        if ( ( $stdDelim !== $delim ) && ( $delimLongLine !== '' ) ) {
//            $sqlToExecute[] = $delimLongLine ;
//            $delimLongLine = '' ;
//        }

        if ( '' !== $quote ) {
            echo "<h2>Last quoted string began with $quote</h2><br />" ;
            throw new Exception( "Quoted string was incomplete!\n" ) ;
        }

        foreach ( $sqlToExecute as $query ) {
            $query = trim( $query ) ;
            if ( ! empty( $query ) ) {
                if ( 1 === $this->_debugMode ) {
                    print "Executing *" . htmlentities($query) . "*\n" ;
                }
                $result = $this->_oDbh->query($query);
                if ( ! $result ) {
                    throw new Exception( "Unable to execute query due to "
                                       . $this->_oDbh->error
                                       . "\nQuery was: "
                                       . $query
                                       . "\n"
                                       ) ;
                }
            }
        }
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
        if ( $this->_testMode === 1 ) {
            $dbName = $this->_dbName ;
            echo '<div>Resetting database to known state.</div>';
            $this->_oDbh->query( "DROP DATABASE IF EXISTS $dbName" ) ;
            $this->_oDbh->query( "CREATE DATABASE $dbName" ) ;
            $this->_oDbh->select_db( $dbName ) ;
            $this->processSqlFile( 'create_pjs_db.sql' ) ;
            echo '<div>A clean database should be loaded.</div>' ;
        }
        else {
            echo "Not authorized. Check config.\n" ;
        }
        PageData::pageFooter() ;
    }
}
