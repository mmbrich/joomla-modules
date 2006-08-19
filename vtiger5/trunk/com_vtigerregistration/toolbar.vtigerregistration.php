<?php
/**
* @version $Id: toolbar.vtigerregistration.php 85 2005-09-15 23:12:03Z eddieajau $
* @package Joomla
* @subpackage Vtiger User Registration
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
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

switch ( $task ) {
        case 'save':
        case 'apply':
                $q = "SELECT * FROM #__vtiger_portal_configuration"
                        ." WHERE name LIKE 'registration_%'";
                $fields = $database->setQuery($q);
                $current_config = $database->loadObjectList();

                foreach($current_config as $config) {
                        $q = "UPDATE #__vtiger_portal_configuration "
                                ."\n SET value='".$_POST[$config->name]."' "
                                ."\n WHERE name='".$config->name."' ";
                        $database->setQuery($q);
                        $database->query();
                }

                $tfields = mosGetParam( $_POST, 'fields', '' );
                $fields=split(',',$tfields);
                $msg = save_fields( $fields );
                //mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );

		$msg = "Successfully saved options";
                mosRedirect( 'index2.php?option='. $option.'&act=settings', $msg );
        break;
        case 'new':
        case 'edit':
        case 'editA':
                TOOLBAR_vtfields::_EDIT();
        break;
        default:
                TOOLBAR_vtfields::_DEFAULT();
        break;
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
                $size = mosGetParam( $_POST, 'size_'.$id.'' , '');

                $jname = mosGetParam( $_POST, 'jname_'.$id.'' , '');
                if($add == "on" || $columnname == "email" || $columnname == "firstname")
                        $show='1';
                else
                        $show='0';
                if($req == "on" || $columnname == "email" || $columnname == "firstname")
                        $required = '1';
                else
                        $required = '0';

                $q = "INSERT INTO #__vtiger_registration_fields VALUES ('".$fields[$i]."','".$columnname."','".$jname."','".$uitype."','".$show."','".$size."','".$required."','".$order."')";

                $database->setQuery($q);
                $database->query() or die( $database->stderr() );
        }
        return "Successfully saved fields";
}
?>
