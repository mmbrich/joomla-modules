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

/************************ SAVE_FORM_FIELDS  START ****************************/
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
	$current_user = inherit_user($entityid);

	$focus = create_entity($module,$entityid);

	$focus->column_fields['assigned_user_id'] = $current_user->id;

	for($j=0;$j<count($fields);$j++) {
		$tabid = getTabId($module);
		$q = "SELECT uitype FROM vtiger_field "
			." WHERE columnname='".$fields[$j]["columnname"]."'"
			." AND tabid='".$tabid."'";
		$uitype = $adb->query_result($adb->query($q),'0','uitype');
		$adb->println("FIELD UITYPE == $uitype");

		if(($fields[$j]["columnname"] == "accountid" || $fields[$j]["columnname"] == "account_id") && $module == "Contacts") {
			if($focus->mode == 'edit') {
				$account = create_entity("Accounts",$focus->column_fields["account_id"]);
				$account->column_fields["accountname"] = $fields[$j]["value"];
				$account->save("Accounts");
			 } else {
				$account = create_entity("Accounts",'');
				$account->column_fields["accountname"] = $fields[$j]["value"];
        			$account->column_fields["assigned_user_id"] = '1';
        			$account->column_fields["description"] = 'Created by joomla on ';
        			$account->save("Accounts");
        			$focus->column_fields["account_id"] = $account->id;
			}
		} else if(preg_match("/imagename/",$fields[$j]["columnname"])) {

			$adb->println("ATTEMPTING TO UPLOAD FILE ");

			$tmp = explode("|",$fields[$j]["columnname"]);
			$new_name = $tmp[1];
			$filetype = $tmp[3];
			$filepath = decideFilePath();
			$current_id = $adb->getUniqueID("vtiger_crmentity");
			$filename = $filepath.$current_id."_".$new_name;

			$adb->println("UPLOAD FILE PATH ".$filename);
			$adb->println("UPLOAD FILE NAME ".$new_name);
			$adb->println("UPLOAD FILE TYPE ".$filetype);
			$adb->println("FILE CURRENT ID ".$current_id);
			$adb->println("FILE ENTITY ID ".$entityid);

			// Try to open our storage path
			if (!$handle = fopen($filename, 'w')) {
				$adb->println("Cannot open file for writing");
   			} else {
				// Attempt to write the file
   				if (fwrite($handle, base64_decode($fields[$j]["value"])) === FALSE) {
					$adb->println("Cannot write to file");
				}	

				// We only support one picture for uploads
				$q = "select * from vtiger_seattachmentsrel where crmid='".$entityid."'";
				$rs = $adb->query($q);
				while($tmprow = $adb->fetch_array($rs)) {
					$delquery = 'delete from vtiger_seattachmentsrel where crmid='.$tmprow['crmid'];
                                	$adb->query($delquery);

					$delquery = 'delete from vtiger_crmentity where crmid='.$tmprow['attachmentsid'];
                                	$adb->query($delquery);

					$delquery = 'delete from vtiger_attachments where attachmentsid='.$tmprow["attachmentsid"];
                                	$adb->query($delquery);
				}

				$date_var = date('YmdHis');

				$sql1 = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(".$current_id.",'1','1','".$module." Attachment','Image attachment from Joomla',".$adb->formatString("vtiger_crmentity","createdtime",$date_var).",".$adb->formatString("vtiger_crmentity","modifiedtime",$date_var).")";
				$tmp = $adb->query($sql1);

				$sql2="insert into vtiger_attachments (attachmentsid, name, description, type, path) values('".$current_id."','".$new_name."','Image upload from Joomla','".$filetype."','".$filepath."')";
				$result=$adb->query($sql2);

				$sql3="insert into vtiger_seattachmentsrel values('".$entityid."','".$current_id."')";
                        	$ret = $adb->query($sql3);

				fclose($handle);
			}
		} else if($uitype == "17") { // URL UIs
			$search = array(
				'/http:\/\//i',
				'/https:\/\//i'
			);
			$replace = array('','');
			$adb->println("CLEANING URL");
			$focus->column_fields[$fields[$j]["columnname"]] = preg_replace($search,$replace,$fields[$j]["value"]);
		} else {
			$focus->column_fields[$fields[$j]["columnname"]] = $fields[$j]["value"];
		}
	}
	$focus->save($module);

	return $focus->id;
}
/************************ SAVE_FORM_FIELDS  END ****************************/

/************************ GET_MULTIPLE_FIELD_DETAILS  START ****************************/
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
	$adb->println("Enter into the function get_multi_field_details(".$fields.")");

	$lastid = '';
	usort($fields,"entityid_sort");
	foreach($fields as $num=>$field) {

		$adb->println("MODULE IS ".$field["module"]." ENTITY IS: ".$field["entityid"]);
		if($field["module"] == "")
			$field["module"] = "Contacts";

		// Forcing the account to only allow related accounts
		if($field["module"] == "Accounts") {
			$q = "SELECT accountid FROM vtiger_contactdetails"
				." WHERE contactid='".$field["entityid"]."'";
			$entityid = $adb->query_result($adb->query($q),'0','accountid');
		} else
			$entityid = $field["entityid"];

		if($lastid != $entityid)
			$focus = create_entity($field["module"],$entityid);
		$lastid = $entityid;

		$tabid=getTabid($field["module"]);
		$q = "SELECT fieldid,columnname,uitype,fieldname,fieldlabel,maximumlength FROM vtiger_field ";
		if($field["columnname"] != "") {
			$q .= " WHERE columnname='".$field["columnname"]."' ";
			$q .= " AND tabid='".$tabid."'";
		} else {
			$q .= " WHERE tabid='".$tabid."'";
		}
		$rs = $adb->query($q);
		$adb->println("$q");

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
        	$tfield[$num]["required"] = $field["required"];

		// Populate the picklist values and field values where needed
		if($field["entityid"] != "") {
			if($tfield[$num]["uitype"] == "15" || $tfield[$num]["uitype"] == "33" || $tfield[$num]["uitype"] == "111") {
        			$tfield[$num]["values"] = get_picklist_values($tfield[$num]["fieldid"]);
        			$tfield[$num]["value"] = get_field_values($focus,$tfield[$num]["fieldname"],$tfield[$num]);

			} else {
        			$tfield[$num]["value"] = get_field_values($focus,$tfield[$num]["fieldname"],$tfield[$num]);
        			$tfield[$num]["values"] = "";
			}

		} else {
        		$tfield[$num]["value"] = "";
        		$tfield[$num]["values"] = "";
			if($tfield[$num]["uitype"] == "15" || $tfield[$num]["uitype"] == "33" || $tfield[$num]["uitype"] == "111")
        			$tfield[$num]["values"] = get_picklist_values($tfield[$num]["fieldid"]);
		}
	}
	return $tfield;
}
/************************ GET_MULTIPLE_FIELD_DETAILS  END ****************************/

/************************ GET_FIELD_DETAILS  START ****************************/
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
                        if($field[$i]["uitype"] == "15" || $field[$i]["uitype"] == "33" || $field[$num]["uitype"] == "111") {
                                $field[$i]["values"] = get_picklist_values($field[$i]["fieldid"]);
                                $field[$i]["value"] = get_field_values($focus,$field[$i]["fieldname"],$field[$i]);
                        } else {
                                $field[$i]["value"] = get_field_values($focus,$field[$i]["fieldname"],$field[$i]);
                                $field[$i]["values"] = "";
                        }
                } else {
                        $field[$i]["value"] = "";
                        $field[$i]["values"] = "";
                        if($field[$i]["uitype"] == "15" || $field[$i]["uitype"] == "33" || $tfield[$num]["uitype"] == "111")
                                $field[$i]["values"] = get_picklist_values($field[$i]["fieldid"]);
                }
        }
        return $field;
}
/************************ GET_FIELD_DETAILS  END ****************************/

/************************ GET_NEW_REGISTER_FIELDS  START ****************************/
$server->register(
	'get_new_register_fields',
	array('fields'=>'tns:current_register_fields'),
	array('return'=>'tns:field_array'),
	$NAMESPACE
);

function get_new_register_fields($fields) {
	global $adb;
	$adb->println("Enter into the function get_new_register_fields($fields)");

	$q = "SELECT fieldid as id,columnname as field,fieldlabel as name,uitype as type, sequence as ord, maximumlength "
		." FROM vtiger_field "
		." WHERE tabid='".getTabid("Contacts")."' "
		." AND presence='0' "
		." AND columnname <> 'leadsource' AND columnname <> 'reportsto' "
		." AND columnname <> 'assistant' AND columnname <> 'assistantphone' AND columnname <> 'donotcall' "
		." AND columnname <> 'emailoptout' AND columnname <> 'smownerid' AND columnname <> 'reference' "
		." AND columnname <> 'notify_owner' AND columnname <> 'createdtime' AND columnname <> 'modifiedtime' "
		." AND columnname <> 'portal' AND columnname <> 'support_start_date' AND columnname <> 'support_end_date'";
		if(is_array($fields)) {
		    foreach($fields as $field) {
			$q .= " AND columnname <> '".$field["columnname"]."' ";
		    }
		}
		$q .= " ORDER BY sequence ";
	$rs = $adb->query($q);

	$i=0;
	$row = array();
	while($tmp = $adb->fetch_array($rs)) {
		$row[] = $tmp;
	}
	return $row;
}
/************************ GET_NEW_REGISTER_FIELDS  END ****************************/


/************************ GET_PICKLIST_VALUES START ****************************/
$server->register(
	'get_picklist_values',
	array('fieldid'=>'xsd:string'),
	array('return'=>'xsd:string'),
	$NAMESPACE
);

function get_picklist_values($fieldid) {
	global $adb;
	$adb->println("Enter into the function get_picklist_values($fieldid)");
	
	$rs = $adb->query("SELECT fieldname,tablename FROM vtiger_field WHERE fieldid='".$fieldid."'");
	$fieldname = $adb->query_result($rs,'0','fieldname');
	$tablename = $adb->query_result($rs,'0','tablename');

	//$rs2 = $adb->query("SELECT * FROM ".$tablename." WHERE presence='1' ORDER BY sortorderid");
	$rs2 = $adb->query("SELECT * FROM vtiger_".$fieldname." WHERE presence='1' ORDER BY sortorderid");

	$values="";
	while($row = $adb->fetch_array($rs2)) {
		$values .= $row[$fieldname].",";
	}
	return $values;
}
/************************ GET_PICKLIST_VALUES END ****************************/


/************************ GET_MODULE_FIELDS START ****************************/
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
/************************ GET_MODULE_FIELDS END ****************************/

/************************ GET_MODULES START ****************************/
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
		." AND name <> 'PurchaseOrder' AND name <> 'Webmails'"
		." AND name <> 'Calendar'";
	$rs = $adb->query($q);

	for($i=0,$num=$adb->num_rows($rs);$i<$num;$i++) {
		$modules[$i]["module"] = $adb->query_result($rs,$i,'name');
	}
	return $modules;
}
/************************ GET_MODULES END ****************************/
?>
