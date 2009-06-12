<?php
/**
 * Created on May 11, 2009 by kbenton
 *
 */

require_once("Libs/autoload.php");

class AddJobMain {
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
        $oForm = new JobDisplayForm();
        $oForm->displayForm();
        PageData::pageFooter();
    }
}
