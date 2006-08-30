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
	default:
                settings( $option );
        break;
}

function about() {
	HTML_helpdesk::about();
}
function settings($option) {
	global $database;
        $q = "SELECT * FROM #__vtiger_portal_configuration "
                ." WHERE name LIKE 'helpdesk_%'";
        $database->setQuery($q);
        $current_config = $database->loadObjectList();
	HTML_helpdesk::settings($option,$current_config);
}
?>
