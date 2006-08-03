<?php
/**
* @version $Id: toolbar.vtigerregistration.html.php 85 2006-07-10 23:12:03Z mmbrich $
* @package Joomla
* @subpackage Vtiger User Registration
* @copyright Copyright (C) 2006 FOSS Labs. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ensure user has access to this function
if (!($acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'all' ) | $acl->acl_check( 'administration', 'edit', 'users', $my->usertype, 'components', 'com_contact' ))) {
        mosRedirect( 'index2.php', _NOT_AUTH );
}

global $mosConfig_useractivation, $mosConfig_allowUserRegistration;
if($mosConfig_allowUserRegistration != "1") {
	mosRedirect( 'index2.php', "You must enable user registration" ); 
}

require_once( $mainframe->getPath( 'admin_html' ) );
define('_MYNAMEIS', 'com_vtigerregistration');
$basePath = $mainframe->getCfg('absolute_path') . "/components/" . _MYNAMEIS . "/";


switch ($act) {
        case "about":
                about();
        break;
        case "settings":
                settings( $option );
        break;
}

switch($task) {
        case 'save':
        case 'apply':
		$tfields = mosGetParam( $_POST, 'fields', '' );
                $fields=split(',',$tfields);
                $msg = save_fields( $fields );
                mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );
        break;
        case 'syncContacts':
                syncContacts( $option );
                //$msg = "Syncronization Successful";
                //mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );
        break;
        case 'cancel':
                cancel( $option );
        break;
}

function about() {
        HTML_vtigerregistration::about();
}
function syncContacts($option) {
	cleanRelationships($option);
	syncVtiger($option);
}
function syncVtiger($option) {
	global $database,$basePath;

	// get the current list of users that aren't mapped to the relationships table
	$q = "SELECT #__users.id,#__users.email, #__users.name, #__users.username "
		." FROM #__users "
		." LEFT JOIN #__vtiger_portal_contacts "
		." ON #__vtiger_portal_contacts.contactid=#__users.id "
		." WHERE #__vtiger_portal_contacts.contactid IS NULL";
        $database->setQuery($q);
	$syncable_users = $database->loadObjectList();

	require_once( $basePath . "vtiger/VTigerContact.class.php" );
	$vtContact = new VtigerContact();

	foreach($syncable_users as $user) {
		$entityid = $vtContact->CheckAndCreate($user->email);
		// if there is no entity in the CRM, lets create one.
		if($entityid == 0) {
			$entityid = $vtContact->RegisterUser($user->email,$user->username,$user->name,'NA',$user->id);
		}
		echo $entityid." == ".$user->email."<br>";
	}

}
function cleanRelationships($option) {
	global $database;

	// get the relationship list
	$q = "SELECT #__vtiger_portal_contacts.contactid FROM "
		." #__vtiger_portal_contacts";
        $database->setQuery($q);
	$current_mapping = $database->loadObjectList();

	// loop and clean any that have been deleted from joomla
	foreach($current_mapping as $map) {
		$q = "SELECT count(*) FROM #__users where #__users.id='".$map->contactid."'";
		$database->setQuery($q);
		$cnt = $database->loadResult();
		if($cnt <= 0) {
			$q = "DELETE FROM #__vtiger_portal_contacts WHERE "
				." #__vtiger_portal_contacts.contactid='".$map->contactid."'";
			$database->setQuery($q);
        		$database->query() or die( $database->stderr() );
		}
	}

}
function settings($option) {
        global $database,$basePath;

        $query = "SELECT #__vtiger_registration_fields.* FROM "
			." #__vtiger_registration_fields "
			." ORDER BY `order`"
	;
        $database->setQuery( $query );
	$current_orders = $database->loadObjectList();

	require_once( $basePath . "vtiger/VTigerField.class.php" );
	$vtField = new VtigerField();
	$current_rows = $vtField->listAllowedFields("Contacts");

	$rows = array();
	for($i=0;$i<count($current_rows);$i++) {
	    // Populate variables from vtiger
	    if(isset($current_rows[$i]["id"])) {
	    	$rows[$current_rows[$i]["id"]] = array();
	    	$rows[$current_rows[$i]["id"]]["id"] = $current_rows[$i]["id"];
	    	$rows[$current_rows[$i]["id"]]["name"] = $current_rows[$i]["name"];
	    	$rows[$current_rows[$i]["id"]]["type"] = $current_rows[$i]["type"];
	    	$rows[$current_rows[$i]["id"]]["size"] = $current_rows[$i]["size"];
	    	$rows[$current_rows[$i]["id"]]["field"] = $current_rows[$i]["field"];
	    }
	    foreach($current_orders as $key=>$ord) {
		if($ord->id != "" && isset($ord->id)) {
	    	  if(isset($current_rows[$i]["id"])) {
		    if($ord->id == $current_rows[$i]["id"]) {
			// if there is a valid DB record, overwrite and populate variables from that
			$rows[$current_rows[$i]["id"]]["field"] = $ord->field;
			$rows[$current_rows[$i]["id"]]["name"] = $ord->name;
			$rows[$current_rows[$i]["id"]]["order"] = $ord->order;
			$rows[$current_rows[$i]["id"]]["show"] = $ord->show;
			$rows[$current_rows[$i]["id"]]["size"] = $ord->size;
			$rows[$current_rows[$i]["id"]]["required"] = $ord->required;
	    		$rows[$current_rows[$i]["id"]]["type"] = $ord->type;
		    }
		  }
		}
	    }
	}

	$row = arrayColumnSort("order", SORT_ASC, SORT_NUMERIC, $rows);
        HTML_vtigerregistration::settings($option,$row);
}
function arrayColumnSort() {
   $n = func_num_args();
   $ar = func_get_arg($n-1);
   if(!is_array($ar))
     return false;

   for($i = 0; $i < $n-1; $i++)
     $col[$i] = func_get_arg($i);

   foreach($ar as $key => $val)
     foreach($col as $kkey => $vval)
       if(is_string($vval))
         ${"subar$kkey"}[$key] = $val[$vval];

   $arv = array();
   foreach($col as $key => $val)
     $arv[] = (is_string($val) ? ${"subar$key"} : $val);
   $arv[] = $ar;

   call_user_func_array("array_multisort", $arv);
   return $ar;
}
function save_fields($fields) {
        global $database;
        $q = "DELETE FROM #__vtiger_registration_fields";
        $database->setQuery($q);
        $database->query() or die( $database->stderr() );
        for($i=0,$num=count($fields);$i<$num;$i++) {
                $id = $fields[$i];
		if($id == "") {break;}
                $add = mosGetParam( $_POST, 'add_'.$id.'' , '');

                $fieldlabel = mosGetParam( $_POST, 'fieldlabel_'.$id.'' , '');
                $columnname = mosGetParam( $_POST, 'columnname_'.$id.'' , '');
                $uitype = mosGetParam( $_POST, 'uitype_'.$id.'' , '');
                $req = mosGetParam( $_POST, 'require_'.$id.'' , '');
                $order = mosGetParam( $_POST, 'order_'.$id.'' , '');

                $jname = mosGetParam( $_POST, 'jname_'.$id.'' , '');
                if($add == "on" || $columnname == "email" || $columnname == "firstname")
                        $show='1';
                else
                        $show='0';
                if($req == "on" || $columnname == "email" || $columnname == "firstname")
                        $required = '1';
                else
                        $required = '0';

                $q = "INSERT INTO #__vtiger_registration_fields VALUES ('".$fields[$i]."','".$columnname."','".$jname."','".$uitype."','".$show."','20','".$required."','".$order."')";

                $database->setQuery($q);
                $database->query() or die( $database->stderr() );
        }
        return "Successfully saved fields";
}
?>
