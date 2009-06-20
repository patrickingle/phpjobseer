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

// @todo Write test to verify that ajaxCheckUrlForDuplicate.php works as expected.

require_once('Libs/autoload.php');

// Don't say anything if either of these fields is not set to something other than an empty string.
if ( !isset($_GET['url'])
  || (''===$_GET['url'])
   ) {
    exit();
}

$url = $_GET['url'];
$jobIdLimit = '';
if ( isset($_GET['jobId']) && (''!==$_GET['jobId']) ) {
    $jobIdLimit = " and jobId <> '"
                . mysql_escape_string($_GET['jobId'])
                . "'";
}

// @todo Make this show what jobId has this URL as a duplicate as a hyperlink
$oJob = new JobDao();
$queryString = "url = '" . mysql_escape_string($url) . "' $jobIdLimit";
$cnt = $oJob->countSome( $queryString );
if ( $cnt > 0 ) {
    echo "Note: Duplicate URL found in system";
}
