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

function printcell($value, $style = null) {
    if (isset($style)) {
        print "  <td class=\"$style\">";
    }
    else {
        print "  <td>";
    }
    if (isset($value)) {
        print $value;
    }
    print "</td>\n";
}

$headerRepeatCount = 20;

$dateNow = date('Y-m-d H:i:s');
$dateNowPlus3Days = date('Y-m-d H:i:s', time() + (86400 * 3) ); // 3 days from now
PageData :: pageHeader();

$displayActiveOnly = ( isset($_GET['activeOnly']) && (1 == $_GET['activeOnly']) );
echo '<div class="pageTitle">PHP Job Seeker</div>';
PageData :: displayNavBar();

echo '<table border="1" cellspacing="0" cellpadding="1">';
$headerRow="<tr class=\"JobListHeader\">
  <th>Edit</th>
  <th>URL</th>
  <th>Urgency</th>
  <th>Next Action</th>
  <th>Due</th>
  <th>Contact</th>
  <th>Application Status</th>
  <th>Position Title</th>
  <th>Location</th>
  <th>Last Stat Chg</th>
  <th>Created</th>
  <th>Updated</th>
</tr>";
$cells = array( 'jobId'
              , 'url'
              , 'urgency'
              , 'nextAction'
              , 'nextActionDue'
              , 'contactId'
              , 'applicationStatusId'
              , 'positionTitle'
              , 'location'
              , 'lastStatusChange'
              , 'created'
              , 'updated'
              );
$oJob = new JobDao();
$oAppStatus = new ApplicationStatusDao();
$jobResults = $oJob->findSome("1 = 1 ORDER BY nextActionDue, urgency, applicationStatusId, location, jobId");

$count=1;
foreach ( $jobResults as $job ) {
    $applicationStatus = $oAppStatus->getRowById($job['applicationStatusId']);
    if ( ($displayActiveOnly) && (!$applicationStatus['isActive']) ) {
        continue;
    }
    if (1 == $count % $headerRepeatCount) {
        print $headerRow;
    }
    $count++;
    $nextActionStyle = 'nextActionDue';
    if ($job['nextActionDue'] < $dateNow) {
        $nextActionStyle .= 'OVERDUE';
    } else if ($job['nextActionDue'] < $dateNowPlus3Days) {
        $nextActionStyle .= 'SOON';
    }
    else {
        $nextActionStyle .= 'FUTURE';
    }
    print "<tr>\n";
    foreach ( $cells as $cell ) {
        if (isset($job[$cell]) && ("" !== $job[$cell])) {
            switch ($cell) {
                case 'jobId':
                    printcell( "<a href=\"editJob.php?jobId={$job[$cell]}\">{$job[$cell]}</a>" );
                    break;
                case 'url':
                    $link = $job[$cell];
                    printcell( "<a href=\"$link\">Here</a> "
                             . "<a href=\"$link\" target=\"_blank\">New</a>" );
                    break;
                case 'urgency':
                    printcell($job[$cell], "urgency" . strtoupper($job[$cell]) );
                    break;
                case 'applicationStatusId':
                    $sval = $applicationStatus['statusValue'];
                    printcell($sval, "applicationStatus$sval");
                    break;
                case 'nextActionDue':
                    printcell($job[$cell], $nextActionStyle);
                    break;
                default:
                    printcell($job[$cell]);
            }
        }
        else {
            printcell("&nbsp;");
        }
    }
    print "</tr>\n";
}
print "</table>\n";
PageData :: pageFooter();
