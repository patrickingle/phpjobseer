<?php
/**
 * Created on Apr 29, 2009 by kbenton
 *
 */

require_once("Libs/autoload.php");

class PageData {

    public static function styleSheet() {
        $dirName = dirname($_SERVER{'SCRIPT_NAME'});
        ?>
                <style type="text/css" media="all">
                  @import url("<?php print $dirName; ?>/styles.css");
                </style>
        <?php
    }

	public static function pageHeader() {
		?>
            <html>
        <!-- pageHeader -->
              <head>
                <title>PHP Job Seeker</title>
                <script type="text/javascript" src="PHPJobSeekerAjax.js"></script>
        <?php
        self::styleSheet();
        ?>
              </head>
              <body>
        <?php
	}

    public static function pageFooter() {
        ?>
        <!-- pageFooter -->
<div class="pageFooter">
  Want your own copy of this tool?  PHP Job Seeker is available at
  <a href="http://phpjobseeker.sourceforge.net/">
    http://phpjobseeker.sourceforge.net/
  </a>
</div>
        <?php
    }

    public static function displayNavBar() {
        ?>
<p />
<div>
  <a href="addJob.php">Add New Job</a>
| <a href="jobList.php">All Jobs</a>
| <a href="jobList.php?activeOnly=1">Active Jobs</a>
| <a href="contactList.php">Contacts</a>
| <a href="companyList.php">Companies</a>
| <a href="keywordList.php">Keywords</a>
| <a href="help.php">Help/Documentation</a>
</div>
<p />
        <?php
    }
}