<?php
/**
* @version $Id: toolbar.helpdesk.html.php 85 2006-07-10 23:12:03Z mmbrich $
* @package Joomla
* @subpackage Vtiger Help Desk
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

require_once( $mainframe->getPath( 'admin_html' ) );

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
                save( $option );
        break;
        case 'cancel':
                cancel( $option );
        break;

}

function about() {
	HTML_helpdesk::about();
}
function settings($option) {
	global $database;

	$query = "SELECT a.id"
        . "\n FROM #__components AS a"
        . "\n WHERE ( a.admin_menu_link = 'option=com_helpdesk' OR a.admin_menu_link = 'option=com_helpdesk&hidemainmenu=1' )"
        . "\n AND a.option = 'com_helpdesk'";

        $database->setQuery( $query );
        $id = $database->loadResult();

	$query = "SELECT params FROM #__components "
                ."\n WHERE id='".$id."'";

        $database->setQuery( $query );
	$ret = $database->loadResult();
	$opts = explode(",",$ret);

	if(sizeof($opts) == 1) {
		$opts[0]='';
		$opts[1]='';
	}

	for($i=0,$cnt=sizeof($opts);$i<$cnt;$i++) {
		if($opts[$i] == 'on')
			$opts[$i] = "CHECKED";
		else
			$opts[$i] = "";
	}

	HTML_helpdesk::settings($option,$opts[0],$opts[1]);
}
function save($option) {
	global $database;

	$livechat = mosGetParam( $_POST, 'livechat', '' );
	$invoices = mosGetParam( $_POST, 'invoices', '' );
	if($livechat == "")
		$livechat='off';
	if($invoices == "")
		$invoices='off';

        $query = "SELECT a.id"
        . "\n FROM #__components AS a"
        . "\n WHERE ( a.admin_menu_link = 'option=com_helpdesk' OR a.admin_menu_link = 'option=com_helpdesk&hidemainmenu=1' )"
        . "\n AND a.option = 'com_helpdesk'";

        $database->setQuery( $query );
        $id = $database->loadResult();

	$query = "UPDATE #__components "
		."\n SET params='".$invoices.",".$livechat."' "
		."\n WHERE id='".$id."'";

	$database->setQuery( $query );
	$ret = $database->loadResult();

	$msg = 'Settings successfully Saved';
        mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );
}
function cancel($option) {
        mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );
}
?>
