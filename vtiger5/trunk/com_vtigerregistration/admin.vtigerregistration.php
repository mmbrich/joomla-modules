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

global $mosConfig_useractivation, $mosConfig_allowUserRegistration,$database;
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
	default:
                settings( $option );
        break;
}

switch($task) {
        case 'syncContacts':
                syncContacts( $option );
                $msg = "Syncronization Successful";
                mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );
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
		$entityid = $vtContact->CheckUser($user->email);
		// if there is no entity in the CRM, lets create one.
		if($entityid == 0) {
			$vtContact->jid=$user->id;
			$entityid = $vtContact->RegisterUser($user->email,$user->username,$user->name,'NA');
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
			." ORDER BY #__vtiger_registration_fields.`order`"
	;
        $database->setQuery( $query );
	$current_fields = $database->loadObjectList();

	$current = array();
	$rows = array();
	$i=0;
	foreach($current_fields as $field) {
		$current[$i] = array(
			"fieldid"=>$field->id,
			"columnname"=>$field->field
		);

		$rows[$field->id]["id"] = $field->id;
		$rows[$field->id]["field"] = $field->field;
		$rows[$field->id]["name"] = $field->name;
		$rows[$field->id]["order"] = $field->order;
		$rows[$field->id]["show"] = $field->show;
		$rows[$field->id]["size"] = $field->size;
		$rows[$field->id]["required"] = $field->required;
	    	$rows[$field->id]["type"] = $field->type;
		$i++;
	}

	require_once( $basePath . "vtiger/VTigerField.class.php" );
	$vtField = new VtigerField();
	$new_fields = $vtField->getNewRegisterFields($current);

	if(is_array($new_fields)) {
		foreach($new_fields as $field) {
			$rows[$field[__numeric_0]]["id"] = $field[__numeric_0];
			$rows[$field[__numeric_0]]["field"] = $field[__numeric_1];
			$rows[$field[__numeric_0]]["name"] = $field[__numeric_2];
			$rows[$field[__numeric_0]]["size"] = $field[__numeric_5];
	    		$rows[$field[__numeric_0]]["type"] = $field[__numeric_3];
			$rows[$field[__numeric_0]]["order"] = $field[__numeric_4];
			$rows[$field[__numeric_0]]["show"] = 0;
			$rows[$field[__numeric_0]]["required"] = 0;
		}
	}
        $q = "SELECT * FROM #__vtiger_portal_configuration "
                ." WHERE name LIKE 'registration_%'";
        $database->setQuery($q);
        $current_config = $database->loadObjectList();

	$row = arrayColumnSort("order", SORT_ASC, SORT_NUMERIC, $rows);
        HTML_vtigerregistration::settings($option,$row,$current_config);
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
?>
