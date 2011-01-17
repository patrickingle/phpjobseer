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

class ContactListView {
    function __construct() {
        // Do nothing for now.
    }

    function main() {
        $headerRepeatCount = 20 ;

        PageData :: pageHeader() ;
        echo '<div class="pageTitle">PHP Job Seeker</div>' ;
        PageData :: displayNavBar() ;
        
        echo '<table border="1" cellspacing="0" cellpadding="1">' ;
        $headerRow="<tr class=\"ContactListHeader\">
          <th>Edit</th>
          <th>Works For</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Created</th>
          <th>Updated</th>
        </tr>" ;
        $cells = array( 'contactId'
                      , 'contactCompanyId'
                      , 'contactName'
                      , 'contactPhone'
                      , 'contactEmail'
                      , 'created'
                      , 'updated'
                      ) ;
        $oContact = new ContactDao() ;
        if ( isset( $_POST[ 'action' ] )
          && ( "Search" === $_POST[ 'action' ] )
          && isset( $_POST[ 'search' ] )
          && ( '' !== $_POST[ 'search' ] )
           ) {
            $searchStr = $oContact->escape_string($_POST['search']) ;
            $contactResults = $oContact->findSome( "contactId = '$searchStr'"
                                         . " OR contactName LIKE '%$searchStr%'"
                                         . " ORDER BY contactName"
                                         ) ;
        }
        else {
            $contactResults = $oContact->findSome("1 = 1 ORDER BY contactName") ;
        }
       
        $count = 1 ;
        foreach ( $contactResults as $contact ) {
            if ( 1 == $count % $headerRepeatCount ) {
                print $headerRow;
            }
            $count++ ;
            $nextActionStyle = 'nextActionDue' ;
            $rowId = $contact[ 'contactId' ] ;
            print "<tr id=\"Row$rowId\">\n" ;

            foreach ( $cells as $cell ) {
                if ( isset( $contact[ $cell ] ) && ("" !== $contact[ $cell ] ) ) {
                    switch ( $cell ) {
                        case 'contactId':
                            Tools::printCell( "<a href=\"editContact.php?contactId={$contact[ $cell ]}\">{$contact[ $cell ]}</a>" );
                            break;
                        case 'contactCompanyId':
                            if ( $contact[ 'contactCompanyId' ] ) {
                                $oWorksFor = new CompanyDao() ;
                                $searchStr = "companyId = " . $contact[ 'contactCompanyId' ] ;
                                $companyResults = $oWorksFor->findSome( $searchStr ) ;
                                Tools::printCell( $companyResults[ 0 ][ 'companyName' ] );
                            }
                            else {
                                Tools::printCell( "None" );
                            }
                            break;
                        default:
                            Tools::printCell( $contact[ $cell ] );
                    }
                }
                else {
                    Tools::printCell("&nbsp;");
                }
            }
            print "</tr>\n";
        }
        print "</table>\n";
        PageData :: pageFooter();
    }
}
