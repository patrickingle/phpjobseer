<?php
/**
 * Created on May 11, 2009 by kbenton
 *
 */

require_once("Libs/autoload.php");

class EditJobMain {
    function __construct() {
        // Do nothing for now.
    }

    function main() {
        PageData::pageHeader();
        echo '<div class="pageTitle">PHP Job Seeker</div>';
        echo '<div class="pageSubtitle"><a href="jobList.php?activeOnly=1">Job Listing</a> &gt;&gt;&gt; Edit Job</div>';
        PageData::displayNavBar();
        $oJob = new JobDao();
        if ( (!isset($_GET['jobId']))
          || (!$oJob->validateRowId($_GET['jobId']))
           ) {
        	echo "<div class=\"error\">Invalid Job ID</div>";
            exit;
        }
        $oJobForm = new JobDisplayForm($_GET['jobId']);
        $oJobForm->displayForm();
        $results=$oJob->getRowById($_GET['jobId']);
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
