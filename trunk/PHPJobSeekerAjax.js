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

var XMLHttp;
var stateStatusElement = '';
var stateStatusMessage = '';
var stateResultElement = '';

function checkForAddNewContact(jobId, checkValue) {
    XMLHttp = GetXmlHttpObject();
    if (XMLHttp == null) {
        alert("Browser does not support Javascript XMLHttpRequest (AJAX)");
        return;
    }
    var url = "ajax/addNewContact.php";
    url = url + "&sid=" + Math.random();
    contactMessage = 'Add new contact';
    XMLHttp.onreadystatechange = displayAjaxAddContactForm;
    XMLHttp.open("GET", url, true);
    XMLHttp.send(null);
}

/**
 * @todo TODO Preliminary only - this needs to be fixed.
 */
function displayAjaxAddContactForm() {
    if (XMLHttp.readyState == 4) {
        document.getElementById(ajaxContactFormBox).innerHTML = XMLHttp.responseText;
        document.getElementById(ajaxContactResultBox).innerHTML = "";
    }
    else {
        document.getElementById(ajaxContactResultBox).innerHTML = contactMessage ;
    }
}

function checkForDuplicateUrl(jobId, checkUrl) {
    XMLHttp = GetXmlHttpObject();
    if (XMLHttp == null) {
        alert("Browser does not support Javascript XMLHttpRequest (AJAX)");
        return;
    }
    var url = "ajax/checkUrlForDuplicate.php";
    if ( ! ( null == jobId ) ) {
        url = url + "?jobId=" + jobId;
    }
    url = url + "&url=" + checkUrl;
    url = url + "&sid=" + Math.random();
    stateStatusElement = "urlDuplicateStatusBox";
    stateStatusMessage = "Looking for duplicates";
    stateResultElement = "urlDuplicateResultBox";
    XMLHttp.onreadystatechange = stateCheck;
    XMLHttp.open("GET", url, true);
    XMLHttp.send(null);
}

function stateCheck() {
    if (XMLHttp.readyState == 4) {
        document.getElementById(stateResultElement).innerHTML = XMLHttp.responseText;
        document.getElementById(stateStatusElement).innerHTML = "";
    }
    else {
        document.getElementById(stateStatusElement).innerHTML = stateStatusMessage;
    }
}

/**
 * fillJobList is the AJAX interface to listJobs.php
 * 
 * @param searchTerms
 * @param sortBy
 * @return true
 * @todo TODO Make new listJobs.php and change UI to it.
 */
function fillJobList(searchTerms, sortBy) {
    XMLHttp = GetXmlHttpObject();
    if (XMLHttp == null) {
        alert("Browser does not support Javascript XMLHttpRequest (AJAX)");
        return;
    }
    var url = "ajax/listJobs.php";
    if ( ! ( null == jobId ) ) {
        url = url + "?searchTerms=" + searchTerms;
    }
    url = url + "&sortBy=" + sortBy;
    url = url + "&sid=" + Math.random();
    stateStatusElement = "jobListingStatusBox";
    stateStatusMessage = "Filling Job Listings";
    stateResultElement = "jobListingResultBox";
    XMLHttp.onreadystatechange = jobListingStateCheck;
    XMLHttp.open("GET", url, true);
    XMLHttp.send(null);
    return true;
}

/**
 * jobListingStateCheck
 * @todo TODO Is this function needed?  Can I just reuse stateCheck()?
 * 
 * @return true
 */
function jobListingStateCheck() {
    if (XMLHttp.readyState == 4) {
        document.getElementById(stateResultElement).innerHTML = XMLHttp.responseText;
        document.getElementById(stateStatusElement).innerHTML = "";
    }
    else {
        document.getElementById(stateStatusElement).innerHTML = stateStatusMessage;
    }
    return true;
}

/**
 * GetXmlHttpObject creates a new XML/HTTP Object for an AJAX request.
 *
 * @return
 */
function GetXmlHttpObject() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject) {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}
