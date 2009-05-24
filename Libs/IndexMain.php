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
        PageData::pageFooter();
    }
}