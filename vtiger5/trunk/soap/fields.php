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

$server->wsdl->addComplexType(
        'field_type_array',
        'complexType',
        'array',
        '',
        array(
                'fieldid' => array('name'=>'fieldid','type'=>'xsd:string'),
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'uitype' => array('name'=>'uitype','type'=>'xsd:string'),
                'fieldname' => array('name'=>'fieldname','type'=>'xsd:string'),
                'fieldlabel' => array('name'=>'fieldlabel','type'=>'xsd:string'),
                'maximumlength' => array('name'=>'maximumlength','type'=>'xsd:string'),
                'value' => array('name'=>'value','type'=>'xsd:string'),
                'values' => array('name'=>'values','type'=>'xsd:string')
        )
);

$server->wsdl->addComplexType(
        'save_field_type',
        'complexType',
        'array',
        '',
        array(
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'value' => array('name'=>'value','type'=>'xsd:string')
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

$server->register(
	'get_field_details',
	array(
		'module'=>'xsd:string',
		'columnname'=>'xsd:string',
		'entityid'=>'xsd:string'
	),
	array(
		'return'=>'tns:field_type_array'
	),
	$NAMESPACE
);

$server->register(
	'save_form_fields',
	array(
		'entityid'=>'xsd:string',
		'module'=>'xsd:string',
		'fields'=>'tns:save_field_type'
	),
	array(
		'return'=>'xsd:string'
	),
	$NAMESPACE
);

function save_form_fields($entityid,$module,$fields) {
	global $adb,$current_user;
	$adb->println("Enter into the function save_form_fields($entityid,$module,$fields)");

	if($entityid != "") {
		$q = "SELECT smownerid FROM vtiger_crmentity WHERE crmid='".$entityid."'";
		$rs = $adb->query($q);
		$current_owner = $adb->query_result($rs,'0','smownerid');
		if($current_owner == "" || !isset($current_owner)) $current_owner=1;

        	require_once('modules/Users/User.php');
        	$current_user = new User();
        	$current_user->retrieve_entity_info($current_owner,"Users");
	}

	if($module == "Products") {
		require_once('modules/Products/Product.php');
		$focus = new Product();
	} else if($module == "Contacts") {
		require_once('modules/Contacts/Contact.php');
		$focus = new Contact();
	} else if($module == "Accounts") {
		require_once('modules/Accounts/Account.php');
		$focus = new Account();
	} else if($module == "Leads") {
		require_once('modules/Leads/Lead.php');
		$focus = new Lead();
	} else if($module == "Activities") {
		require_once('modules/Activities/Activity.php');
		$focus = new Activity();
	} else if($module == "Campaigns") {
		require_once('modules/Campaigns/Campaign.php');
		$focus = new Campaign();
	} else if($module == "HelpDesk") {
		require_once('modules/HelpDesk/HelpDesk.php');
		$focus = new HelpDesk();
	} else if($module == "Invoice") {
		require_once('modules/Invoice/Invoice.php');
		$focus = new Invoice();
	} else if($module == "Potentials") {
		require_once('modules/Potentials/Potential.php');
		$focus = new Potential();
	} else if($module == "Quotes") {
		require_once('modules/Quotes/Quote.php');
		$focus = new Quote();
	} else if($module == "SalesOrder") {
		require_once('modules/SalesOrder/SalesOrder.php');
		$focus = new SalesOrder();
	}
	if($entityid != "" && $entityid != 0) {
		$focus->retrieve_entity_info($entityid,$module);
		$focus->id = $entityid;
		$focus->mode = "edit";
	}

	for($j=0;$j<count($fields);$j++) {
		$adb->println("\n\rSaving\n\r");
		$focus->column_fields[$fields[$j]["columnname"]] = $fields[$j]["value"];
	}
	$focus->save($module);

	return $focus->id;
}

function get_field_details($module,$columnname,$entityid) {
	global $adb;
	$adb->println("Enter into the function get_field_details($module,$columnname)");

	$tabid=getTabid($module);
	$q = "SELECT fieldid,columnname,uitype,fieldname,fieldlabel,maximumlength FROM vtiger_field ";
	if($columnname != "") {
		$q .= " WHERE columnname='".$columnname."' ";
		$q .= " AND tabid='".$tabid."'";
	} else {
		$q .= " WHERE tabid='".$tabid."'";
	}
	$rs = $adb->query($q);

	for($i=0,$num=$adb->num_rows($rs);$i<$num;$i++) {
        	$field[$i]["fieldid"] = $adb->query_result($rs,$i,'fieldid');
        	$field[$i]["columnname"] = $adb->query_result($rs,$i,'columnname');
        	$field[$i]["uitype"] = $adb->query_result($rs,$i,'uitype');
        	$field[$i]["fieldname"] = $adb->query_result($rs,$i,'fieldname');
        	$field[$i]["fieldlabel"] = $adb->query_result($rs,$i,'fieldlabel');
        	$field[$i]["maximumlength"] = $adb->query_result($rs,$i,'maximumlength');

		// Populate the picklist values and field values where needed
		if($entityid != "") {
			if($field[$i]["uitype"] == "15" || $field[$i]["uitype"] == "33") {
        			$field[$i]["values"] = get_picklist_values($field[$i]["fieldid"]);
        			$field[$i]["value"] = get_field_values($columnname,$entityid,$module);
			} else {
        			$field[$i]["value"] = get_field_values($columnname,$entityid,$module);
        			$field[$i]["values"] = "";
			}
		} else {
        		$field[$i]["value"] = "";
        		$field[$i]["values"] = "";
			if($field[$i]["uitype"] == "15" || $field[$i]["uitype"] == "33")
        			$field[$i]["values"] = get_picklist_values($field[$i]["fieldid"]);
		}
	}
	return $field;
}

function get_field_values($columnname,$entityid,$module) {
	if($entityid == "" || !isset($entityid))
		return '';
	global $adb;
	$adb->println("Enter into the function get_field_values($columnname,$entityid,$module)");

	if($module == "Products") {
		require_once('modules/Products/Product.php');
		$focus = new Product();
	} else if($module == "Contacts") {
		require_once('modules/Contacts/Contact.php');
		$focus = new Contact();
	} else if($module == "Accounts") {
		require_once('modules/Accounts/Account.php');
		$focus = new Account();
	} else if($module == "Leads") {
		require_once('modules/Leads/Lead.php');
		$focus = new Lead();
	} else if($module == "Activities") {
		require_once('modules/Activities/Activity.php');
		$focus = new Activity();
	} else if($module == "Campaigns") {
		require_once('modules/Campaigns/Campaign.php');
		$focus = new Campaign();
	} else if($module == "HelpDesk") {
		require_once('modules/HelpDesk/HelpDesk.php');
		$focus = new HelpDesk();
	} else if($module == "Invoice") {
		require_once('modules/Invoice/Invoice.php');
		$focus = new Invoice();
	} else if($module == "Potentials") {
		require_once('modules/Potentials/Potential.php');
		$focus = new Potential();
	} else if($module == "Quotes") {
		require_once('modules/Quotes/Quote.php');
		$focus = new Quote();
	} else if($module == "SalesOrder") {
		require_once('modules/SalesOrder/SalesOrder.php');
		$focus = new SalesOrder();
	}
	$focus->retrieve_entity_info($entityid,$module);
	return $focus->column_fields[$columnname];
}

function get_portal_register_fields($module_name)
{
	global $adb;
	$adb->println("Enter into the function get_portal_register_fields($module_name)");
	
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

	$adb->println("Exit from the function get_portal_register_fields");
	return $row;
}

function get_picklist_values($fieldid) {
	global $adb;
	$adb->println("Enter into the function get_picklist_values($fieldid)");
	
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
