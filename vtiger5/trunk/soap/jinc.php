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
require_once('soap/jdef.php');

// Set the default CRM owner for all new entities from joomla.
$default_ownerid='1';

global $default_ownerid;

function inherit_user($entityid='') {
	global $default_ownerid,$adb;
	$adb->println("Enter into the function inherit_user($entityid)");
        if($entityid != "") {
                $q = "SELECT smownerid FROM vtiger_crmentity WHERE crmid='".$entityid."' AND smownerid IS NOT NULL";
                $rs = $adb->query($q);
                $current_owner = $adb->query_result($rs,'0','smownerid');
        }

        if($current_owner == "" || !isset($current_owner) || !$current_owner) {
        	$current_owner=$default_ownerid;
		$adb->println("No owner found, set to : $current_owner");
	} else
		$adb->println("The current owner of this entity is : $current_owner");

        require_once('modules/Users/User.php');
        $current_user = new User();
        $current_user->retrieve_entity_info($current_owner,"Users");
	$adb->println("The current owner of this entity is : $current_user->id");
	return $current_user;
}

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
        } else if($module == "Activities" || $module == "Events") {
                require_once('modules/Calendar/Activity.php');
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
                require_once('modules/Potentials/Opportunity.php');
                $focus = new Potential();
        } else if($module == "Quotes") {
                require_once('modules/Quotes/Quote.php');
                $focus = new Quote();
        } else if($module == "SalesOrder") {
                require_once('modules/SalesOrder/SalesOrder.php');
                $focus = new SalesOrder();
        } else if($module == "Vendors") {
                require_once('modules/Vendors/Vendor.php');
                $focus = new Vendor();
        } else if($module == "Users") {
                require_once('modules/Users/User.php');
                $focus = new User();
        }

        if($entityid != "" && $entityid != 0) {
                $focus->id = $entityid;
                $focus->mode = "edit";
                $focus->retrieve_entity_info($entityid,$module);
        }
        return $focus;
}

function entityid_sort($a,$b) {
        return strcmp($a["entityid"], $b["entityid"]);
}

function get_field_values($focus,$columnname,$field='') {
        if($focus->id == "" || !isset($focus->id))
                return '';

        global $adb;
        $adb->println("Enter into the function get_field_values($focus,$columnname,".$field["viewtype"].")");

        $adb->println("Will bet getting field value for COLUMNNAME: $columnname IN MODULE: ".$focus->column_fields["record_module"]." THE CURRENT VALUE IS: ".$focus->column_fields[$columnname]." WITH VIEW TYPE: ".$field["viewtype"].")");

	// format comments for trouble tickets
        if($columnname == "comments"
			&& $focus->column_fields["record_module"] == "HelpDesk") {

                $q = "SELECT comments "
			." FROM vtiger_ticketcomments "
			." WHERE ticketid='".$focus->id."'";
                $rs = $adb->query($q);
                $adb->println($q);
                $ret = '';
                while($row = $adb->fetch_array($rs)) {
                        $ret .= $row["comments"]."<br>";
                }
                return $ret;

	// Populate Vendor Name
        } else if(($columnname == "vendorid" || $columnname == "vendor_id") 
			&& $focus->column_fields["record_module"] == "Products") {

		if($field["viewtype"] != "data") {
                	$q = "SELECT vendorname "
				." FROM vtiger_vendor "
				." WHERE vendorid='".$focus->column_fields["vendor_id"]."'";
			$vname = $adb->query_result($adb->query($q),'0','vendorname');
        		$adb->println("GOT VENDOR NAME -- $vname");
			return $vname;
		} else
			return $focus->column_fields["vendor_id"];
	// Populate Account Name
	} elseif (($columnname == "account_id" || $columnname == "accountid") 
			&& $focus->column_fields["record_module"] == "Contacts") {

		if($field["viewtype"] != "data") {
			return $adb->query_result($adb->query("SELECT accountname "
				." FROM vtiger_account "
				." WHERE accountid='".$focus->column_fields["account_id"]."'"),
				'0','accountname');
		} else
			return $focus->column_fields["account_id"];
	// Get all the images and paths for each image.
        } elseif ($field["uitype"] == "69") {
		$q = "SELECT path,name,vtiger_attachments.attachmentsid "
			." FROM vtiger_attachments "
			." INNER JOIN vtiger_seattachmentsrel "
			." ON vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid "
			." WHERE vtiger_seattachmentsrel.crmid='".$focus->id."' AND type LIKE '%image%'";


                $rs = $adb->query($q);
                $adb->println($q);
                $ret = '';
                while($row = $adb->fetch_array($rs)) {
                        $ret .= $row["path"].$row["attachmentsid"]."_".$row["name"]."|";
                }
                $adb->println("GOT IMAGE ".$ret);
                return $ret;

	// All the normal data fields
        } else
                return $focus->column_fields[$columnname];
}
?>
