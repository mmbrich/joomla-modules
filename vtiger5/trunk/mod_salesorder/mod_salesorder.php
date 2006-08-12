<?php
/**
* @version 1.1 $
* @package VtigerLead
* @copyright (C) 2005 Foss Labs <mmbrich@fosslabs.com>
*                2006 Pierre-Andr?ullioud www.paimages.ch
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
 
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
 
#check if the bot exist
if (!file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php'))
	echo "Install BOT_VCONNECTION";exit();

global $my;

//require_once($mosConfig_absolute_path.'/components/com_vtigerregistration/vtiger/VtigerSalesorder.class.php');

if(!$my->id)
	echo "Welcome to sales order";
else
	echo "Hello ".$my->name

?>
