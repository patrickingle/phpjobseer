<?php
/**
 * Created on May 20, 2009 by kbenton
 *
 */

class SaveJobMain {
    public function __construct() {
        // do nothing for now
    }

    public function main() {
        PageData::pageHeader();
        PageData::displayNavBar();
        $changeLog = '';
        $newNote = $_POST['newNote'];
        if ( isset($_POST['primaryContactId'])
          && ('Add new contact' === $_POST['primaryContactId'])
           ) {
            // FIXME Deal with adding new contact
            echo 'Do not know how to add new contact yet.';
        }
        else if ( isset($_POST['companyId'])
          && ('Add new company' === $_POST['companyId'])
           ) {
            // FIXME Deal with adding new company
            echo 'Do not know how to add new company yet.';
        }
        else {
            echo 'Adding/updating Job<br />';
            $oJob = new JobDao();
            $rowValues = array();
            $jobId = null;
            $result = null;
            // First, identify changes that have been made to fields and save
            // that in the $changeLog variable.  Then,
            if ( ($_POST['jobId'] !== NULL) && ($_POST['jobId'] !== '') ) {
                $jobId = $_POST['jobId'];
                $rowValues['jobId'] = $jobId;
                $oldRow = $oJob->getRowById($jobId);
                if ( !isset($oldRow['jobId'])
                  || ($oldRow['jobId'] !== $jobId)
                   ) {
                    throw( new Exception('Can\'t update that way!') );
                }
                $rowValues = array(
                    'jobId'               => $_POST['jobId']
                  , 'primaryContactId'    => $_POST['primaryContactId']
                  , 'companyId'           => $_POST['companyId']
                  , 'urgency'             => $_POST['urgency']
                  , 'nextActionDue'       => Tools::getDateFromPost('nextActionDue')
                  , 'lastStatusChange'    => Tools::getDateFromPost('lastStatusChange')
                  , 'positionTitle'       => $_POST['positionTitle']
                  , 'applicationStatusId' => $_POST['applicationStatusId']
                  , 'nextAction'          => $_POST['nextAction']
                  , 'location'            => $_POST['location']
                  , 'url'                 => $_POST['url']
                );
                foreach ( $rowValues as $key => $value ) {
                    ///////////////////////////////////////////////////////////
                    // Special case - when new data is empty or null and old
                    // value was also empty or null, there was no change. For
                    // the purpose of this code, empty and null are identical
                    ///////////////////////////////////////////////////////////
                    if ( (
                           !isset($rowValues[$key]) || ( $rowValues[$key]==='')
                         )
                        &&
                         (
                           ($oldRow[$key]===null) || ($oldRow[$key]==='')
                         )
                       ) {
                        continue;
                    }
                    if ($oldRow[$key] !== $rowValues[$key] ) {
                        if ( $changeLog === "" ) {
                            $changeLog = "<table border=\"1\""
                                       .       " cellspacing=\"0\""
                                       .       " cellpadding=\"1\""
                                       .       " style=\"changeLog\">\n"
                                       . "<tr>\n"
                                       . "  <th>Changes</th>\n"
                                       . "  <th>Old Value</th>\n"
                                       . "  <th>New Value</th>\n"
                                       . "</tr>\n";
                        }
                        $changeLog .= "<tr>\n"
                                   .  "  <td>$key</td>\n"
                                   .  "  <td>$oldRow[$key]</td>\n"
                                   .  "  <td>$rowValues[$key]</td>\n"
                                   .  "</tr>\n";
                    }
                }
                if ( $changeLog !== "" ) {
                    $changeLog .= "</table>\n";
                    $newNote .= $changeLog;
                }
                $oJob->populateFields($rowValues);
                $result = $oJob->updateRowById($jobId, $rowValues);
                if (!$result) {
                    echo "Update failed for some reason (1)<br />";
                    return;
                }
            }
            else {
                $oJob->populateFields($rowValues);
                $jobId = $oJob->insertRow($rowValues);
            }
            if (! $jobId) {
                echo "<div class='error'>Unable to add/update job!</div>";
                return;
            }
            echo "Job #{$jobId} added/updated.<br />";
            if ( ($newNote !== null) && ($newNote !== '') ) {
                $oNote = new NoteDao();
                $rowValues = array();
                $rowValues['appliesToTable'] = 'job';
                $rowValues['appliesToId'] = $jobId;
                $rowValues['note'] = $newNote;
                $result = $oNote->insertRow($rowValues);
                if (!$result) {
                    echo "<div class='error'>Unable to add note to this job!</div>";
                }
                else {
                    echo "Note #{$result} added to this job.<br />";
                }
            }
            echo "Click <a href=\"editJob.php?jobId=$jobId\">here</a> to view it"
               . " again.<br />";
        }
        PageData::pageFooter();
    }
}