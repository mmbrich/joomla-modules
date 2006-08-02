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

require_once( $mainframe->getPath( 'front_html' ) );
define('_MYNAMEIS', 'com_vtigerregistration');
$basePath = $mainframe->getCfg('absolute_path') . "/components/" . _MYNAMEIS . "/";

switch($task) {
	case 'saveVtigerRegistration';
		saveRegistration();
	break;
	case 'activate':
		activate(mosGetParam( $_REQUEST, 'activation', '' ));
	break;
        case 'register':
                $fields = get_fields();
                HTML_vtigerregistration::register($fields,$mainframe);
        break;
}
function activate( $option ) {
        global $database, $my;
        global $mosConfig_useractivation, $mosConfig_allowUserRegistration;

        if($my->id) {
                // They're already logged in, so redirect them to the home page
                mosRedirect( 'index.php' );
        }

        if ($mosConfig_allowUserRegistration == '0' || $mosConfig_useractivation == '0') {
                mosNotAuth();
                return;
        }

        $activation = mosGetParam( $_REQUEST, 'activation', '' );
        $activation = $database->getEscaped( $activation );

        if (empty( $activation )) {
                echo _REG_ACTIVATE_NOT_FOUND;
                return;
        }

        $query = "SELECT id"
        . "\n FROM #__users"
        . "\n WHERE activation = '$activation'"
        . "\n AND block = 1"
        ;
        $database->setQuery( $query );
        $result = $database->loadResult();

        if ($result) {
                $query = "UPDATE #__users"
                . "\n SET block = 0, activation = ''"
                . "\n WHERE activation = '$activation'"
                . "\n AND block = 1"
                ;
                $database->setQuery( $query );
                if (!$database->query()) {
                        echo "SQL error" . $database->stderr(true);
                }
                echo _REG_ACTIVATE_COMPLETE;
        } else {
                echo _REG_ACTIVATE_NOT_FOUND;
        }
}
function get_fields() {
	global $database,$basePath;
        $query = "SELECT * FROM "
                        ." #__vtiger_registration_fields "
			." ORDER BY #__vtiger_registration_fields.order"
        ;
        $database->setQuery( $query );
        $current_rows = $database->loadObjectList();

	require_once($basePath."/vtiger/VTigerField.class.php");
	$vtigerField = new VtigerField();
	for($i=0;$i<count($current_rows);$i++) {
		if($current_rows[$i]->type == "33" || $current_rows[$i]->type == "15") {
			$vals = split(',',$vtigerField->GetPicklistValues($current_rows[$i]->id));
			for($j=0,$num=count($vals);$j<$num;$j++) {
				$current_rows[$i]->values[] = $vals[$j];
			}
		}
	}

	return $current_rows;
}
function saveRegistration() {
        global $database, $acl, $basePath;
        global $mosConfig_sitename, $mosConfig_live_site, $mosConfig_useractivation, $mosConfig_allowUserRegistration;
        global $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailfrom, $mosConfig_fromname;

        if ( $mosConfig_allowUserRegistration == 0 ) {
                mosNotAuth();
                return;
        }
        $row = new mosUser( $database );

        if (!$row->bind( $_POST, 'usertype' )) {
                mosErrorAlert( $row->getError() );
        }

        mosMakeHtmlSafe($row);
        $row->id                = 0;
        $row->usertype  = '';
        $row->gid               = $acl->get_group_id( 'Registered', 'ARO' );

        if ( $mosConfig_useractivation == 1 ) {
                $row->activation = md5( mosMakePassword() );
                $row->block = '1';
        }

	$row->username = mosGetParam( $_POST, 'username', '' );
	$row->email = mosGetParam( $_POST, 'email', '' );
	$row->name = mosGetParam( $_POST, 'firstname', '' )." ".mosGetParam( $_POST, 'lastname', '' );

        if (!$row->check()) {
                echo "<script> alert('".html_entity_decode($row->getError())."'); window.history.go(-1); </script>\n";
                exit();
        }
        $pwd                    = $row->password;
        $row->password          = md5( $row->password );
        $row->registerDate      = date( 'Y-m-d H:i:s' );

        if (!$row->store()) {
                echo "<script> alert('".html_entity_decode($row->getError())."'); window.history.go(-1); </script>\n";
                exit();
        }

        $row->checkin();
	require_once($basePath."/vtiger/VTigerContact.class.php");
	$vtigerContact = new VtigerContact();
	$userid = $vtigerContact->RegisterUser(
		$row->email,
		mosGetParam( $_POST, 'firstname', '' ),
		mosGetParam( $_POST, 'lastname', '' )
	);

	if($userid <= 0) {
                echo "<script> alert('CRM REGISTRATION ERROR'); window.history.go(-1); </script>\n";
                exit();
	}

	$q = "INSERT INTO #__vtiger_portal_contacts (contactid,entityid) VALUES ('".$row->id."','".$userid."')";
        $database->setQuery( $q );
	$database->query() or die( $database->stderr() );

	$q = "SELECT id,field FROM #__vtiger_registration_fields "
		." WHERE #__vtiger_registration_fields.show='1'
	";
        $database->setQuery( $q );
        $fields = $database->loadObjectList();

	for($i=0,$cnt=count($fields);$i<$cnt;$i++) {
		if( $fields[$i]->field  != "firstname" 
		&& $fields[$i]->field  != "lastname" 
		&& $fields[$i]->field  != "email" 
		&& $fields[$i]->field  != ""
		&& mosGetParam( $_POST, $fields[$i]->field, '' ) != "") {
			if(is_array(mosGetParam( $_POST, $fields[$i]->field, '' ))) {
				$tval = mosGetParam( $_POST, $fields[$i]->field, '' );
				$value ='';
				for($j=0;$j<count($tval);$j++) {
					$value .= $tval[$j].",";
				}
			} else 
				$value = mosGetParam( $_POST, $fields[$i]->field, '' );

			$vtigerContact->SetField(
				$fields[$i]->id,
				$value
			);
		}
	}

        $name           = $row->name;
        $email          = $row->email;
        $username       = $row->username;

        $subject        = sprintf (_SEND_SUB, $name, $mosConfig_sitename);
        $subject        = html_entity_decode($subject, ENT_QUOTES);

        if ($mosConfig_useractivation == 1){
                $message = sprintf (_USEND_MSG_ACTIVATE, $name, $mosConfig_sitename, $mosConfig_live_site."/index.php?option=com_vtigerregistration&task=activate&activation=".$row->activation, $mosConfig_live_site, $username, $pwd);
        } else {
                $message = sprintf (_USEND_MSG, $name, $mosConfig_sitename, $mosConfig_live_site);
        }

        $message = html_entity_decode($message, ENT_QUOTES);

        // check if Global Config `mailfrom` and `fromname` values exist
        if ($mosConfig_mailfrom != '' && $mosConfig_fromname != '') {
                $adminName2     = $mosConfig_fromname;
                $adminEmail2    = $mosConfig_mailfrom;
        } else {
        // use email address and name of first superadmin for use in email sent to user
                $query = "SELECT name, email"
                . "\n FROM #__users"
                . "\n WHERE LOWER( usertype ) = 'superadministrator'"
                . "\n OR LOWER( usertype ) = 'super administrator'"
                ;
                $database->setQuery( $query );
                $rows = $database->loadObjectList();
                $row2                   = $rows[0];

                $adminName2     = $row2->name;
                $adminEmail2    = $row2->email;
        }

        // Send email to user
        mosMail($adminEmail2, $adminName2, $email, $subject, $message);

        // Send notification to all administrators
        $subject2 = sprintf (_SEND_SUB, $name, $mosConfig_sitename);
        $message2 = sprintf (_ASEND_MSG, $adminName2, $mosConfig_sitename, $row->name, $email, $username);
        $subject2 = html_entity_decode($subject2, ENT_QUOTES);
        $message2 = html_entity_decode($message2, ENT_QUOTES);

        // get email addresses of all admins and superadmins set to recieve system emails
        $query = "SELECT email, sendEmail"
        . "\n FROM #__users"
        . "\n WHERE ( gid = 24 OR gid = 25 )"
        . "\n AND sendEmail = 1"
        . "\n AND block = 0"
        ;
        $database->setQuery( $query );
        $admins = $database->loadObjectList();

        foreach ( $admins as $admin ) {
                // send email to admin & super admin set to recieve system emails
                mosMail($adminEmail2, $adminName2, $admin->email, $subject2, $message2);
        }
        if ( $mosConfig_useractivation == 1 ){
                echo _REG_COMPLETE_ACTIVATE;
        } else {
                echo _REG_COMPLETE;
        }
}
?>
