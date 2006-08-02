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
require_once('modules/Contacts/Contact.php');
require_once('include/utils/utils.php');

$log = &LoggerManager::getLogger('webforms');

//$serializer = new XML_Serializer();
$NAMESPACE = 'http://www.vtiger.com/vtigercrm/';
$server = new soap_server;

$server->configureWSDL('vtigersoap');

$server->wsdl->addComplexType(
        'field_array',
        'complexType',
        'array',
        '',
        array(
                'id' => array('name'=>'id','type'=>'xsd:string'),
                'field' => array('name'=>'field','type'=>'xsd:string'),
                'name' => array('name'=>'name','type'=>'xsd:string'),
                'type' => array('name'=>'type','type'=>'xsd:string'),
                'order' => array('name'=>'order','type'=>'xsd:string')
             )
);

$server->register(
	'get_portal_register_fields',
	array('module_name'=>'xsd:string'),
	array('return'=>'tns:field_array'),
	$NAMESPACE
);

$server->register(
	'get_picklist_values',
	array('fieldid'=>'xsd:string'),
	array('return'=>'xsd:string'),
	$NAMESPACE
);

/**	function to list available fields
 *	@param string $emailid - email address to unsubscribe
 *	return message about the success or failure status about the unsubscribe
 */
function get_portal_register_fields($module_name)
{
	global $adb;
	$adb->println("Enter into the function unsubscribe_email($emailid)");
	
	$rs = $adb->query("SELECT fieldid as id,columnname as field,fieldlabel as name,uitype as type FROM vtiger_field WHERE tabid='".getTabid($module_name)."' "
			." AND presence='0' "
			." AND columnname <> 'leadsource' AND columnname <> 'reportsto' "
			." AND columnname <> 'assistant' AND columnname <> 'assistantphone' AND columnname <> 'donotcall' "
			." AND columnname <> 'emailoptout' AND columnname <> 'smownerid' AND columnname <> 'reference' "
			." AND columnname <> 'notify_owner' AND columnname <> 'createdtime' AND columnname <> 'modifiedtime' "
			." AND columnname <> 'portal' AND columnname <> 'support_start_date' AND columnname <> 'support_end_date' "
			." ORDER BY sequence ");

	//$tmp = array("fieldid"=>"10","columnname"=>"first_name","fieldlabel"=>"First Name");
	while($tmp = $adb->fetch_array($rs)) {
		$row[] = $tmp;
	}

	$adb->println("Exit from the function ");
	return $row;
}

function get_picklist_values($fieldid) {
	global $adb;
	$adb->println("Enter into the function unsubscribe_email($emailid)");
	
	$rs = $adb->query("SELECT fieldname FROM vtiger_field WHERE fieldid='".$fieldid."'");
	$fieldname = $adb->query_result($rs,'0','fieldname');
	$rs2 = $adb->query("SELECT * FROM vtiger_".$fieldname." WHERE presence='1' ORDER BY sortorderid");

	$values='';
	while($row = $adb->fetch_array($rs2)) {
		$values .= $row[$fieldname].",";
	}
	return $values;
}


/* Begin the HTTP listener service and exit. */ 
$server->service($HTTP_RAW_POST_DATA); 
exit(); 
?>
