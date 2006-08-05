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

$log = &LoggerManager::getLogger('webforms');
$NAMESPACE = 'http://www.vtiger.com/vtigercrm/';
$server = new soap_server;
$server->configureWSDL('vtigersoap');


/* START ARRAY DECLARATIONS */
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
        'multi_field_return_array',
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
                'values' => array('name'=>'values','type'=>'xsd:string'),
                'module' => array('name'=>'module','type'=>'xsd:string'),
                'viewtype' => array('name'=>'viewtype','type'=>'xsd:string'),
                'showlabel' => array('name'=>'showlabel','type'=>'xsd:string'),
                'entityid' => array('name'=>'entityid','type'=>'xsd:string'),
                'picnum' => array('name'=>'picnum','type'=>'xsd:string')
        )
);

$server->wsdl->addComplexType(
        'multi_field_type_array',
        'complexType',
        'array',
        '',
        array(
                'module' => array('name'=>'module','type'=>'xsd:string'),
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'viewtype' => array('name'=>'viewtype','type'=>'xsd:string'),
                'showlabel' => array('name'=>'showlabel','type'=>'xsd:string'),
                'entityid' => array('name'=>'entityid','type'=>'xsd:string'),
                'picnum' => array('name'=>'picnum','type'=>'xsd:string')
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

$server->wsdl->addComplexType(
        'mod_fields',
        'complexType',
        'array',
        '',
        array(
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'fieldlabel' => array('name'=>'fieldlabel','type'=>'xsd:string')
        )
);

$server->wsdl->addComplexType(
        'mods',
        'complexType',
        'array',
        '',
        array(
                'module' => array('name'=>'module','type'=>'xsd:string')
        )
);
/* END ARRAY DECLARATIONS */

/* SAVE_FORM_FIELDS  START */
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

	$focus = create_entity($module,$entityid);

	for($j=0;$j<count($fields);$j++) {
		if(($fields[$j]["columnname"] == "accountid" || $fields[$j]["columnname"] == "account_id") && $module == "Contacts") {
			$adb->query("UPDATE vtiger_account set accountname='".$fields[$j]["value"]."' WHERE accountid='".$focus->column_fields["account_id"]."'");
		} else {
			$focus->column_fields[$fields[$j]["columnname"]] = $fields[$j]["value"];
		}
	}
	$focus->save($module);

	return $focus->id;
}
/* SAVE_FORM_FIELDS  START */

/* GET_MULTIPLE_FIELD_DETAILS  START */
$server->register(
	'get_multiple_field_details',
	array(
		'fields'=>'tns:multi_field_type_array'
	),
	array(
		'return'=>'tns:multi_field_return_array'
	),
	$NAMESPACE
);

function get_multiple_field_details($fields) {
	global $adb;
	$adb->println("Enter into the function get_multi_field_details($fields)");

	$lastid = '';
	usort($fields,"entityid_sort");
	foreach($fields as $num=>$field) {
		if($lastid != $field["entityid"])
			$focus = create_entity($field["module"],$field["entityid"]);
		$lastid = $field["entityid"];

		$tabid=getTabid($field["module"]);
		$q = "SELECT fieldid,columnname,uitype,fieldname,fieldlabel,maximumlength FROM vtiger_field ";
		if($field["columnname"] != "") {
			$q .= " WHERE columnname='".$field["columnname"]."' ";
			$q .= " AND tabid='".$tabid."'";
		} else {
			$q .= " WHERE tabid='".$tabid."'";
		}
		$rs = $adb->query($q);

        	$tfield[$num]["fieldid"] = $adb->query_result($rs,0,'fieldid');
        	$tfield[$num]["columnname"] = $field["columnname"];
        	$tfield[$num]["uitype"] = $adb->query_result($rs,0,'uitype');
        	$tfield[$num]["fieldname"] = $adb->query_result($rs,0,'fieldname');
        	$tfield[$num]["fieldlabel"] = $adb->query_result($rs,0,'fieldlabel');
        	$tfield[$num]["maximumlength"] = $adb->query_result($rs,0,'maximumlength');

		// Extra variables passed in
        	$tfield[$num]["module"] = $field["module"];
        	$tfield[$num]["viewtype"] = $field["viewtype"];
        	$tfield[$num]["showlabel"] = $field["showlabel"];
        	$tfield[$num]["entityid"] = $field["entityid"];
        	$tfield[$num]["picnum"] = $field["picnum"];

		// Populate the picklist values and field values where needed
		if($field["entityid"] != "") {
			if($tfield[$num]["uitype"] == "15" || $tfield[$num]["uitype"] == "33") {
        			$tfield[$num]["values"] = get_picklist_values($tfield[$num]["fieldid"]);
        			$tfield[$num]["value"] = get_field_values($focus,$field["columnname"]);
			} elseif ($tfield[$num]["columnname"] == "accountid" && $tfield[$num]["module"] == "Contacts") {
        			$tfield[$num]["value"] = $adb->query_result($adb->query("SELECT accountname FROM vtiger_account WHERE accountid='".$focus->column_fields["account_id"]."'"),'0','accountname');
        			$tfield[$num]["values"] = "";
			} else {
        			$tfield[$num]["value"] = get_field_values($focus,$field["columnname"]);
        			$tfield[$num]["values"] = "";
			}
		} else {
        		$tfield[$num]["value"] = "";
        		$tfield[$num]["values"] = "";
			if($tfield[$num]["uitype"] == "15" || $tfield[$num]["uitype"] == "33")
        			$tfield[$num]["values"] = get_picklist_values($tfield[$num]["fieldid"]);
		}
	}
	return $tfield;
}
/* GET_MULTIPLE_FIELD_DETAILS  END */

/* GET_FIELD_DETAILS  START */
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
function get_field_details($module,$columnname,$entityid) {
        global $adb;
        $adb->println("Enter into the function get_field_details($module,$columnname)");

	$focus = create_entity($module,$entityid);

        $tabid=GetTabid($module);
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
                                $field[$i]["value"] = get_field_values($focus,$columnname);
                        } else {
                                $field[$i]["value"] = get_field_values($focus,$columnname);
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
/* GET_FIELD_DETAILS  END */

/* GET_PORTAL_REGISTER_FIELDS  START */
$server->register(
	'get_portal_register_fields',
	array('module_name'=>'xsd:string'),
	array('return'=>'tns:field_array'),
	$NAMESPACE
);

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
/* GET_PORTAL_REGISTER_FIELDS  END */


/* GET_PICKLIST_VALUES START */
$server->register(
	'get_picklist_values',
	array('fieldid'=>'xsd:string'),
	array('return'=>'xsd:string'),
	$NAMESPACE
);

function get_picklist_values($fieldid) {
	global $adb;
	$adb->println("Enter into the function get_picklist_values($fieldid)");
	
	$rs = $adb->query("SELECT fieldname FROM vtiger_field WHERE fieldid='".$fieldid."'");
	$fieldname = $adb->query_result($rs,'0','fieldname');
	$rs2 = $adb->query("SELECT * FROM vtiger_".$fieldname." WHERE presence='1' ORDER BY sortorderid");

	$values="";
	while($row = $adb->fetch_array($rs2)) {
		$values .= $row[$fieldname].",";
	}
	return $values;
}
/* GET_PICKLIST_VALUES END */


/* GET_MODULE_FIELDS START */
$server->register(
	'get_module_fields',
	array('module'=>'xsd:string'),
	array('return'=>'tns:mod_fields'),
	$NAMESPACE
);

function get_module_fields($module='') {
	global $adb;
	$adb->println("Enter into the function get_module_fields($module)");
	
	if($module != "")
		$q = "SELECT columnname,fieldlabel FROM vtiger_field WHERE tabid='".getTabid($module)."'";
	else
		$q = "SELECT columnname,fieldlabel,vtiger_tab.name FROM vtiger_field INNER JOIN vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid ORDER BY vtiger_tab.name";

	$rs = $adb->query($q);
	for($i=0,$num=$adb->num_rows($rs);$i<$num;$i++) {
		$fields[$i]["columnname"] = $adb->query_result($rs,$i,'columnname');
		$fields[$i]["fieldlabel"] = $adb->query_result($rs,$i,'fieldlabel');
		$fields[$i]["module_name"] = $adb->query_result($rs,$i,'name');
	}
	return $fields;
}
/* GET_MODULE_FIELDS END */

/* GET_MODULES START */
$server->register(
	'get_modules',
	array('module'=>'xsd:string'),
	array('return'=>'tns:mods'),
	$NAMESPACE
);

function get_modules($module='') {
	global $adb;
	$adb->println("Enter into the function get_module_fields($module)");
	
	$q = "SELECT name FROM vtiger_tab "
		." WHERE name <> 'Users' AND name <> 'Reports' AND name <> 'Rss' "
		." AND name <> 'Dashboard' AND name <> 'Emails' AND name <> 'Home' "
		." AND name <> 'Notes' AND name <> 'Portal' AND name <> 'PriceBooks' "
		." AND name <> 'PurchaseOrder' AND name <> 'Vendors' AND name <> 'Webmails'"
		." AND name <> 'Calendar'";
	$rs = $adb->query($q);

	for($i=0,$num=$adb->num_rows($rs);$i<$num;$i++) {
		$modules[$i]["module"] = $adb->query_result($rs,$i,'name');
	}
	return $modules;
}
/* GET_MODULES END */

/****** END OF PUBLIC FUNCTIONS *********/



/* START INTERNAL FUNCTIONS */
function create_entity($module,$entityid='') {
	global $adb;
	$adb->println("Enter into the function create_entity($module,$entityid)");

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
		$focus->id = $entityid;
		$focus->mode = "edit";
		$focus->retrieve_entity_info($entityid,$module);
	}
	return $focus;
}

function get_field_values($focus,$columnname) {
	if($focus->id == "" || !isset($focus->id))
		return '';

	global $adb;
	$adb->println("Enter into the function get_field_values($focus,$columnname)");
	return $focus->column_fields[$columnname];
}

function entityid_sort($a,$b) {
	return strcmp($a["entityid"], $b["entityid"]);
}
/* END INTERNAL FUNCTIONS */

/* Begin the HTTP listener service and exit. */ 
$server->service($HTTP_RAW_POST_DATA); 
exit(); 
?>
