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

// Import array definitions
require_once('jdef.php');

require_once("jinc.php");

include_once("contact.php");
include_once("fields.php");
include_once("products.php");
include_once("salesorder.php");
include_once("jportal.php"); // Copy of customer portal file

$server->service($HTTP_RAW_POST_DATA);
exit();
?>
