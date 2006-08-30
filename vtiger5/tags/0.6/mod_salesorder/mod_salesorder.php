<?php
/**
* @version 1.1 $
* @package VtigerLead
* @copyright (C) 2005 Foss Labs <mmbrich@fosslabs.com>
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
 
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// check if the bot exist
if (!file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php')) {
        echo "You should install bot_vconnection if you want something to happen here ;)";
        flush();exit();
}

// Get the right language if it exists
if (file_exists($mosConfig_absolute_path.'/components/com_vtigersalesorders/languages/vtigersalesorders_'.$mosConfig_lang.'.php')) {
    include($mosConfig_absolute_path.'/components/com_vtigersalesorders/languages/vtigersalesorders_'.$mosConfig_lang.'.php');
} else {
    include($mosConfig_absolute_path.'/components/com_vtigersalesorders/languages/vtigersalesorders_english.php');
}

global $my;
require_once($mosConfig_absolute_path.'/components/com_vtigerregistration/vtiger/VTigerSalesOrder.class.php');
$SalesOrder = new VtigerSalesOrder();
$Itemid = mosGetParam($_REQUEST, "Itemid", $SalesOrder->defaultItemid);

if(!$my->id) {
	if(!isset($_COOKIE["current_salesorder"]))
		echo "<p style='text-align:center;font-weight:bold'>"._CART_NOITEMS."</p>";
	else {
		$tmp = $SalesOrder->GetCurrentSalesOrders();
		if(is_array($tmp)) {
			?>
			<div style='valign:top;text-align:center;font-weight:bold'><?php echo _CART_LOGIN;?></div>
			<br>
			<table border='0' cellspacing='0' cellpadding='0' style='text-align:center' align='center'>
				<tr>
					<td colspan='3'>
						<a href='<?php echo sefRelToAbs('index.php?option=com_vtigersalesorders&task=view&Itemid='.$Itemid.'&soid='.$tmp[0]["salesorderid"]);?>'>
							<?php echo _CART_YOUHAVE ." ".$tmp[0]["num_products"] ." ". _CART_IN_CART;?>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						$<?php echo $tmp[0]["total"];?>
					</td>
				</tr>
			</table>
			<?
		} else {
			echo "<p style='text-align:center;font-weight:bold'>"._CART_EMPTY."</p>";
			setcookie("current_salesorder", "", time()-3600, '/');
		}
	}
} else {
	if($my->id && $_COOKIE["current_salesorder"]) {
		if($SalesOrder->Checkid($my->id)) {
			$soid = $SalesOrder->AssociateToUser();
			mosRedirect('index.php');
		}
	}
	echo "<div style='valign:top;text-align:center;font-weight:bold'>"._CART_FOR." ".$my->name."</div>";

	$tmp = $SalesOrder->GetCurrentSalesOrders($my->id);
	if(is_array($tmp)) {
		echo "<br><table border='0' cellspacing='0' cellpadding='0' style='text-align:center' align='center'>";

		foreach($tmp as $order) {
		?>
			<tr>
				<td colspan='3'>
					<a href='<?php echo sefRelToAbs('index.php?option=com_vtigersalesorders&task=view&Itemid='.$Itemid.'&soid='.$order["salesorderid"]);?>'>
						<?php echo _CART_YOUHAVE." ".$order["num_products"]." "._CART_IN_CART;?>
					</a>
				</td>
			</tr>
			<tr>
				<td>
					$<?php echo $order["total"];?>
				</td>
			</tr>
		<?
		}
		echo "</table>";
	}
}
?>
