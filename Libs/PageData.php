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

class PageData {

    /**
     * Load the Javascript files
     *
     * @return void
     */
    public static function jsLoader() {
        echo '<script type="text/javascript" src="testAjax.js"></script>' . "\n";
    }

    /**
     * Load the style sheet
     *
     * @return void
     */
    public static function styleSheet() {
        $dirName = isset( $_SERVER{ 'SCRIPT_NAME' } ) ? dirname( $_SERVER{ 'SCRIPT_NAME' } ) : '.' ;
        echo "                <style type=\"text/css\" media=\"all\">
                  @import url(\"$dirName/styles.css\");
                </style>
";
    }

    /**
     * Display the page header
     *
     * @return void
     */
    public static function pageHeader() {
        if ( isset( $_SERVER{'nowrapper'} ) ) {
            return ;
        }
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

    /**
     * Display the page footer
     *
     * @return void
     */
	public static function pageFooter() {
        if ( isset( $_SERVER{'nowrapper'} ) ) {
            return ;
        }
        ?>
<div class="pageFooter">
  Want your own copy of this tool?  PHP Job Seeker is available at
  <a href="http://phpjobseeker.sourceforge.net/">
    http://phpjobseeker.sourceforge.net/
  </a>
</div>
        <!-- pageFooter -->
        <?php
    }

    /**
     * Display the navigation bar
     *
     * @return void
     */
    public static function displayNavBar() {
        if ( isset( $_SERVER{'nowrapper'} ) ) {
            return ;
        }
        ?>
<p />
<form method="POST" action="jobList.php">
<table width="100%" cellpadding="2" cellspacing="0" class="navBar">
  <tr class="navBar">
    <td class="navBar">
        <a href="index.php">Summary</a>
      | <a href="addJob.php">Add New Job</a>
      | <a href="jobList.php">All Jobs</a>
      | <a href="jobList.php?activeOnly=1">Active Jobs</a>
      | <a href="contactList.php">Contacts</a>
      | <a href="companyList.php">Companies</a>
      | <a href="keywordList.php">Keywords</a>
      | <a href="help.php">Help/Documentation</a>
    </td>
    <td class="navBarSearch">
        <nobr>
            <input type="text" name="search" value="" class="searchBox" />
            <input type="submit" name="action" value="Search" />
        </nobr>
    </td>
  </tr>
</table>
</form>
<p />
        <?php
    }
}
