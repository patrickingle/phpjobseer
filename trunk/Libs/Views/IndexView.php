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

class IndexView {
    function __construct() {
        // Do nothing for now.
    }

    function main() {
        PageData::pageHeader();
        echo '<div class="pageTitle">PHP Job Seeker</div>';
        PageData::displayNavBar();
        $oApplicationStatus = new ApplicationStatusDao();
        $aStatuses = $oApplicationStatus->findSome("1 = 1 ORDER BY sortKey");
        echo "<table cellspacing=\"0\" cellpadding=\"1\">\n";
        $headerRow = 0;
        echo "<tr><th>Status</th><th>Jobs</th></tr>\n";
        $oJob = new JobDao();
        foreach ( $aStatuses as $row ) {
            $statusId    = $row['applicationStatusId'];
            $statusLabel = $row['statusValue'];
            $style       = $row['style'];
            $count       = $oJob->countSome("applicationStatusId = $statusId");
            echo "</tr>";
            echo "<tr><th class=\"applicationStatus"
               . "$statusLabel\">"
               . "$statusLabel</th>"
               . "<td><a href=\"jobList.php?action=statusList&status=$statusId\">$count<a></td></tr>\n";
        }
        $count = $oJob->countAll();
        echo "<tr><th>Total</th><td>$count</td>\n";
        echo "\n</table><p />\n";
        PageData::pageFooter();
    }
}