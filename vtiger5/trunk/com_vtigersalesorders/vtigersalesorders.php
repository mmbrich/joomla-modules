<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

// check if the bot exist
if(!file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php')) {
	echo "You should install bot_vconnection if you want something to happen here ;)";
	flush();exit();
}

require_once( $mainframe->getPath( 'front_html' ) );
require_once('components/com_vtigerregistration/vtiger/VTigerSalesOrder.class.php');
$SalesOrder = new VtigerSalesOrder();

switch($task) {
	case 'view':
		global $my;
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$order = $SalesOrder->GetSalesOrderDetails($soid);
		$ret = $SalesOrder->Checkid($my->id);
		if($ret) {
			$SalesOrder->soid=$soid;
			HTML_vtigersalesorders::view($order[0]);
		} else
			echo "You must log-in or create an account to view sales orders";
	break;
	case 'updateQuantity':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$productid = mosGetParam( $_REQUEST, 'product_id', '0' );
		$quantity = mosGetParam( $_REQUEST, 'quantity', '0' );
		if($productid == 0 || $quantity == 0) {
			$msg = "Failed to update";
			mosRedirect('index.php?option=com_vtigersalesorders&task=view&soid='.$soid.'&msg='.$msg);
		} else {
			$ret = $SalesOrder->Checkid($my->id);
			if(!$ret)
				echo "You must log-in or create an account to view sales orders";
			else {
				$SalesOrder->soid=$soid;
				$SalesOrder->UpdateProductQuantity($productid,$quantity);
				$msg = "Updated Product";
				mosRedirect('index.php?option=com_vtigersalesorders&task=view&soid='.$soid.'&msg='.$msg);
			}
		}
	break;
	case 'addProduct':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$ret = $SalesOrder->Checkid($my->id);
		if(!$ret)
			echo "You must log-in or create an account to view sales orders";
		else {
			$SalesOrder->soid=$soid;
			$SalesOrder->AddToSalesOrder($soid,$product,$qty);
			$msg = "Added Product";
			mosRedirect('index.php?option=com_vtigersalesorders&task=view&soid='.$SalesOrder->soid.'&msg='.$msg);
		}
	break;
	case 'removeProduct':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$productid = mosGetParam( $_REQUEST, 'productid', '0' );
		$ret = $SalesOrder->Checkid($my->id);
		if(!$ret)
			echo "You must log-in or create an account to view sales orders";
		else {
			$SalesOrder->soid=$soid;
			$SalesOrder->RemoveFromSalesOrder($productid);
			$msg = "Removed Product";
			mosRedirect('index.php?option=com_vtigersalesorders&task=view&soid='.$SalesOrder->soid.'&msg='.$msg);
		}
	break;
	default:
		echo "Not Authorized";
	break;
}
?>
