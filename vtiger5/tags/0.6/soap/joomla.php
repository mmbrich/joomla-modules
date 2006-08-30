<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once("config.php");
require_once('include/logging.php');
require_once('include/nusoap/nusoap.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');

$log = &LoggerManager::getLogger('JOOMLA_SOAP');
$NAMESPACE = 'http://www.vtiger.com/vtigercrm/';
$server = new soap_server;
$server->configureWSDL('vtigersoap');

// array definitions
require_once("soap/jdef.php");
// functions
require_once("soap/jinc.php");

/* Quick availability check function */
$server->register(
        'check_connection',
        array('alive'=>'xsd:string'),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function check_connection($alive) {
	return 'hello';
}

$server->register(
	'download_image',
	array('image'=>'xsd:string'),
	array('return'=>'xsd:string'),
	$NAMESPACE);
function download_image($image) {
	global $adb,$root_directory;

	$filename = $root_directory.$image;

        // Try to open our storage path
        if (!$handle = fopen($filename, 'r')) {
        	$adb->println("Cannot open file");
		return 'failed';
        } else {
       	// send the file
		$content='';
		while(!feof($handle)) {
			$content .= fgets($handle);
		}
		fclose($handle);
		return base64_encode($content);
	}
}

include_once("soap/contact.php");
include_once("soap/fields.php");
include_once("soap/products.php");
include_once("soap/salesorder.php");
include_once("soap/jportal.php"); // Copy of customer portal file with server calls stripped

$server->service($HTTP_RAW_POST_DATA);
exit();
?>
