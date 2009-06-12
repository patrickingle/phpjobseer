<?php
/**
 * Created on May 1, 2009 by kbenton
 *
 */

require_once("Libs/autoload.php");

class IndexMain {
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
            echo "<tr>";
            echo "<tr><th class=\"applicationStatus$statusLabel\">$statusLabel</th><td>$count</td></tr>\n";
        }
        $count = $oJob->countAll();
        echo "<tr><th>Total</th><td>$count</td>\n";
        echo "\n</table><p />\n";
        PageData::pageFooter();
    }
}