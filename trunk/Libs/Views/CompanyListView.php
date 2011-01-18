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

class CompanyListView {
    function __construct() {
        // Do nothing for now.
    }

    function main() {
        $headerRepeatCount = 20 ;

        PageData :: pageHeader() ;
        echo '<div class="pageTitle">PHP Job Seeker</div>' ;
        PageData :: displayNavBar() ;
        
        echo '<table border="1" cellspacing="0" cellpadding="1">' ;
        $headerRow="<tr class=\"CompanyListHeader\">
          <th>Edit</th>
          <th>Name</th>
          <th>Agency Of</th>
          <th>Address</th>
          <th>Phone</th>
          <th>Created</th>
          <th>Updated</th>
        </tr>" ;
        $cells = array( 'companyId'
                      , 'companyName'
                      , 'agencyOf'
                      , 'address'
                      , 'phone'
                      , 'created'
                      , 'updated'
                      ) ;
        $cols = count( $cells ) ;
        $oCompany = new CompanyDao() ;
        if ( isset( $_POST[ 'action' ] )
          && ( "Search" === $_POST[ 'action' ] )
          && isset( $_POST[ 'search' ] )
          && ( '' !== $_POST[ 'search' ] )
           ) {
            $searchStr = $oCompany->escape_string($_POST['search']) ;
            $companyResults = $oCompany->findSome( "companyId = '$searchStr'"
                                         . " OR companyName LIKE '%$searchStr%'"
                                         . " ORDER BY companyName"
                                         ) ;
        }
        else {
            $companyResults = $oCompany->findSome("1 = 1 ORDER BY companyName") ;
        }
       
        $count = 1 ;
        print $headerRow ;
        foreach ( $companyResults as $company ) {
            if ( ( 1 == $count % $headerRepeatCount ) && ( $count > 1 ) ) {
                print $headerRow ;
            }
            $count++ ;
            $nextActionStyle = 'nextActionDue' ;
            $rowId = $company[ 'companyId' ] ;
            print "<tr id=\"Row$rowId\">\n" ;

            foreach ( $cells as $cell ) {
                if ( isset( $company[ $cell ] ) && ("" !== $company[ $cell ] ) ) {
                    switch ( $cell ) {
                        case 'companyId':
                            Tools::printCell( "<a href=\"editCompany.php?companyId={$company[ $cell ]}\">{$company[ $cell ]}</a>" );
                            break;
                        case 'agencyOf':
                            if ( $company['isAnAgency'] ) {
                                $oAgency = new Company() ;
                                $searchStr = "companyId = " . $company[ 'companyId' ] ;
                                $agencyResults = $oCompany->findSome( $searchStr ) ;
                                Tools::printCell( $agencyResults[ 0 ][ 'companyName' ] );
                            }
                            else {
                                Tools::printCell( "None" );
                            }
                            break;
                        case 'address':
                            $addr = $company[ 'companyAddress1' ] . '<br />' ;
                            if ( $company[ 'companyAddress2' ] !== '' ) {
                                $addr .= $company[ 'companyAddress1' ] . '<br />' ;
                            }
                            $addr .= $company[ 'companyCity' ] . ", "
                                  .  $company[ 'companyState' ] . " "
                                  .  $company[ 'companyZip' ] ;
                            Tools::printCell( $addr );
                            break;
                        default:
                            Tools::printCell( $company[ $cell ] );
                    }
                }
                else {
                    Tools::printCell("&nbsp;");
                }
            }
            print "</tr>\n";
        }
        print "<tr>"
            . "<td colspan=$cols>"
            . "<form action=\"addCompany.php\">"
            . "<center>"
            . "<input type=submit value=\"Add Company\" />"
            . "</center>"
            . "</form>"
            . "</td>"
            . "</tr>\n" ;
        print "</table>\n";
        PageData :: pageFooter();
    }
}

