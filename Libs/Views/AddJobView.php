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

class AddJobView {
    function __construct() {
        // Do nothing for now.
    }

    function main() {
        PageData::pageHeader();
        echo '<div class="pageTitle">PHP Job Seeker</div>';
        echo '<div class="pageSubtitle">'
           . '<a href="jobList.php?activeOnly=1">Job Listing</a>'
           . ' &gt;&gt;&gt; Add New Job</div>';
        PageData::displayNavBar();
        $oForm = new JobFormView();
        $oForm->displayForm();
        PageData::pageFooter();
    }
}
