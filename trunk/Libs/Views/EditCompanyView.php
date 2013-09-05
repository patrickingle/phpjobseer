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

class EditCompanyView {
    function __construct() {
        // Do nothing for now.
    }

    function main() {
        PageData::pageHeader();
        echo '<div class="pageTitle">PHP Job Seeker</div>';
        echo '<div class="pageSubtitle"><a href="companyList.php">Company Listing</a> &gt;&gt;&gt; Edit Company</div>';
        PageData::displayNavBar();
        $oCompany = new CompanyDao();
        if ( (!isset($_GET['companyId']))
          || (!$oCompany->validateRowId($_GET['companyId']))
           ) {
            echo "<div class=\"error\">Invalid Company ID</div>";
            exit;
        }
        $oCompanyForm = new CompanyFormView($_GET['companyId']);
        $oCompanyForm->displayForm();
        $results=$oCompany->getRowById($_GET['companyId']);
        echo "<div class=\"IHEADER\">"
           . "<a href=\"{$results['url']}\" target=\"_blank\">"
           . "Open frame in new window</a>"
           . "</div>"
           . "<iframe src=\"{$results['url']}\" height=\"50%\" width=\"100%\">"
           . "If you can see this, your browser doesn't understand IFRAMEs."
           . " However, I'll still show you the"
           . " <a href=\"{$results['url']}\">link</a>."
           . "</iframe>"
           ;
        PageData::pageFooter();
    }
}
