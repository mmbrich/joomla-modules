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

require_once( $mainframe->getPath( 'toolbar_html' ) );

switch ($task) {
        case 'save':
        case 'apply':
                $q = "SELECT * FROM #__vtiger_portal_configuration"
                        ." WHERE name LIKE 'helpdesk_%'";
                $fields = $database->setQuery($q);
                $current_config = $database->loadObjectList();

                foreach($current_config as $config) {
                        $q = "UPDATE #__vtiger_portal_configuration "
                                ."\n SET value='".$_POST[$config->name]."' "
                                ."\n WHERE name='".$config->name."' ";
                        $database->setQuery($q);
                        $database->query();
                }
                mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );
        break;
        case 'cancel':
                mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );
        break;
        default:
                TOOLBAR_helpdesk::_DEFAULT();
        break;
}
?>
