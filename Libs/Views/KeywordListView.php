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

class KeywordListView {
    function __construct() {
        // Do nothing for now.
    }

    function main() {
        PageData::pageHeader();
        echo '<div class="pageTitle">PHP Job Seeker</div>';
        PageData::displayNavBar();
        $oKeywords = new KeywordDao();
        $aKeywords = $oKeywords->findSome("1 = 1 ORDER BY sortKey");
        echo "<a href=\"addKeyword.php\">Add Keyword</a><br />\n";
        echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"1\">\n";
        $headerRow = 0;
        echo "<tr><th rowspan=2>Keyword</th><th colspan=2>Jobs</th></tr>\n";
        echo "<tr><th>Active</th><th>Inactive</th></tr>\n";
        $oJobKeyword = new JobKeywordDao(null);
        $oApplicationStatus = new ApplicationStatusDao(null);
        $activeApplicationStatusList = array();
        $activeApplicationStatusFields = $oApplicationStatus->findSome("isActive = 1");
        foreach ( $activeApplicationStatusFields as $activeRow ) {
            $activeApplicationStatusList[$activeRow['applicationStatusId']] = $activeRow['statusValue'];
        }
        $inactiveApplicationStatusList = array();
        $inactiveApplicationStatusFields = $oApplicationStatus->findSome("isActive = 0");
        foreach ( $inactiveApplicationStatusFields as $inactiveRow ) {
            $inactiveApplicationStatusList[$inactiveRow['applicationStatusId']] = $inactiveRow['statusValue'];
        }
        foreach ( $aKeywords as $row ) {
            $keywordId       = $row['keywordId'];
            $keywordValue    = $row['keywordValue'];
            $sortKey         = $row['sortKey'];
            $keywordCreated  = $row['created'];
            $keywordUpdated  = $row['updated'];
            $activeJobList   = array();
            $inactiveJobList = array();
            $keywordJobs     = $oJobKeyword->findJobsIdsByKeywordIdAndApplicationStatusValue($keywordId, $activeApplicationStatusList);
            foreach ( $keywordJobs as $jobRow ) {
                $activeJobList[] = "<a"
                                 . " class=\"activeJobLink\""
                                 . " href=\"editJob.php?jobId={$jobRow['jobId']}\">"
                                 . "{$jobRow['jobId']}"
                                 . "</a>";
            }
            $keywordJobs     = $oJobKeyword->findJobsIdsByKeywordIdAndApplicationStatusValue($keywordId, $inactiveApplicationStatusList);
            foreach ( $keywordJobs as $jobRow ) {
                $inactiveJobList[] = "<a"
                                 . " class=\"inactiveJobLink\""
                                 . " href=\"editJob.php?jobId={$jobRow['jobId']}\">"
                                 . "{$jobRow['jobId']}"
                                 . "</a>";
            }
            $activeJobs = (count($activeJobList)>0) ? join(", ", $activeJobList) : 'None found';
            $inactiveJobs = (count($inactiveJobList)>0) ? join(", ", $inactiveJobList) : 'None found';
            echo "  <tr>\n"
               . "    <td><a href=\"editKeyword.php?keywordId=$keywordId\">$keywordValue</a></td>\n"
               . "    <td>$activeJobs</td>\n"
               . "    <td>$inactiveJobs</td>\n"
               . "  </tr>\n";
        }
        echo "\n</table><p />\n";
        PageData::pageFooter();
    }

}