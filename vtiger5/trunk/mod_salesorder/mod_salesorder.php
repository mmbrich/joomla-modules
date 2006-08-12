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

// check if the bot exist
if (!file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php')) {
        echo "You should install bot_vconnection if you want something to happen here ;)";
        flush();exit();
}

global $my;
require_once($mosConfig_absolute_path.'/components/com_vtigerregistration/vtiger/VTigerSalesOrder.class.php');
$SalesOrder = new VtigerSalesOrder();

if(!$my->id) {
	if(!isset($_COOKIE["current_salesorder"]))
		echo "<p style='text-align:center;font-weight:bold'>You have no items in your cart</p>";
	else
		echo "<p style='text-align:center;font-weight:bold'>Please log in to complete your transaction</p>";
} else {
	if($my->id && $_COOKIE["current_salesorder"]) {
		if($SalesOrder->Checkid($my->id)) {
			$soid = $SalesOrder->AssociateToUser();
		}
	}
	echo "<div style='valign:top;text-align:center;font-weight:bold'>Cart for ".$my->name."</div>";

	$tmp = $SalesOrder->GetCurrentSalesOrders($my->id);
	if(is_array($tmp)) {
		echo "<br><table border='0' cellspacing='0' cellpadding='0' style='text-align:center' align='center'>";
		echo "<tr><th>Name</th><th>&nbsp;</th><th>Total</th></tr>";
		foreach($tmp as $order) {
			echo "<tr><td><a href='index.php?option=com_vtigersalesorders&task=view&soid=".$order["salesorderid"]."'>".$order["subject"]."</a></td><td>&nbsp;</td><td>$".$order["total"]."</td></tr>";
		}
		echo "</table>";
	}
}
?>
