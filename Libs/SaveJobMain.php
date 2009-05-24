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
            $rowValues['primaryContactId'] = $_POST['primaryContactId'];
            $rowValues['companyId'] = $_POST['companyId'];
            $rowValues['urgency'] = $_POST['urgency'];
            $rowValues['nextActionDue'] = Tools::getDateFromPost('nextActionDue');
            $rowValues['lastStatusChange'] = Tools::getDateFromPost('lastStatusChange');
            $rowValues['positionTitle'] = $_POST['positionTitle'];
            $rowValues['applicationStatusId'] = $_POST['applicationStatusId'];
            $rowValues['nextAction'] = $_POST['nextAction'];
            $rowValues['location'] = $_POST['location'];
            $rowValues['url'] = $_POST['url'];
            $jobId = null;
            $result = null;
            if ( ($_POST['jobId'] !== NULL) && ($_POST['jobId'] !== '') ) {
                $jobId = $_POST['jobId'];
                $rowValues['jobId'] = $jobId;
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
            $newNote = $_POST['newNote'];
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
            echo "Click <a href=\"javascript:back(-1);\">here</a> to view it"
               . " again. Note - you may need to hit refresh afterward.<br />";
        }
        PageData::pageFooter();
    }
}