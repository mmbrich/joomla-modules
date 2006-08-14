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

require_once($mainframe->getCfg('absolute_path').'/components/com_vtigerregistration/vtiger/VTigerForm.class.php');
$vtigerForm = new VtigerForm();

$q = "SELECT name,value FROM #__vtiger_portal_configuration "
	." WHERE name LIKE 'salesorder_%'";
$database->setQuery($q);
$configs = $database->loadObjectList();
foreach($configs as $config) {
	$conf[$config->name] = $config->value;
}

if(isset($_POST["update_address"])) {
	$soid = mosGetParam( $_REQUEST, 'soid', '0' );
	$entityid = mosGetParam( $_REQUEST, 'vt_entityid', '0' );
	$task = mosGetParam( $_REQUEST, 'task', 'checkout' );
	$ret = $vtigerForm->SaveVtigerForm("Contacts",$entityid);
	mosRedirect('index.php?option=com_vtigersalesorders&task='.$task.'&soid='.$soid);
}

switch($task) {
	case 'view':
		global $my;
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$order = $SalesOrder->GetSalesOrderDetails($soid);
		$ret = $SalesOrder->Checkid($my->id);
		if(!$ret && !$soid)
			echo "You must log-in or create an account to view sales orders";
		else {
			$SalesOrder->soid=$soid;
			HTML_vtigersalesorders::view($order[0]);
			HTML_vtigersalesorders::shopping_footer($soid);
		}
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
			if(!$ret && !$soid)
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
		$productid = mosGetParam( $_REQUEST, 'productid', '' );
		$qty = mosGetParam( $_REQUEST, 'qty', '1' );

		$ret = $SalesOrder->Checkid($my->id);
		if(!$ret && !$soid)
			echo "You must log-in or create an account to view sales orders";
		else {
			$SalesOrder->soid=$soid;
			$SalesOrder->AddToSalesOrder($productid,$qty);
			$msg = "Added Product";
			mosRedirect('index.php?option=com_vtigersalesorders&task=view&soid='.$soid.'&msg='.$msg);
		}
	break;
	case 'removeProduct':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$productid = mosGetParam( $_REQUEST, 'productid', '0' );
		$ret = $SalesOrder->Checkid($my->id);
		if(!$ret && !$soid)
			echo "You must log-in or create an account to view sales orders";
		else {
			$SalesOrder->soid=$soid;
			$SalesOrder->RemoveFromSalesOrder($productid);
			$msg = "Removed Product";
			mosRedirect('index.php?option=com_vtigersalesorders&task=view&soid='.$SalesOrder->soid.'&msg='.$msg);
		}
	break;
	case 'makePayment':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		/* if(!$ret && !$soid)
			echo "You must log-in or create an account to view sales orders";

		$SalesOrder->soid=$soid;
		$invoiceid = $SalesOrder->ConvertToInvoice();
		echo $invoiceid;
		*/
		$SalesOrder->soid=$soid;
		$order = $SalesOrder->GetSalesOrderDetails($soid);
		HTML_vtigersalesorders::view($order[0]);
		HTML_vtigersalesorders::do_payment();
	break;
	case 'checkout':
		if(!$my->id) {
                	$fields = get_fields();

			$field_array = array();
			$i=0;
			foreach($fields as $field) {
				$vars = get_object_vars($field);
				foreach ($vars as $name=>$val) {
					if($name == "type")
						$field_array[$i]["uitype"] = $val;
					else
						$field_array[$i][$name] = $val;
				} 
				$i++;
			}
			$soid = mosGetParam( $_REQUEST, 'soid', '0' );
			$order = $SalesOrder->GetSalesOrderDetails($soid);
			$SalesOrder->soid=$soid;
			HTML_vtigersalesorders::view($order[0]);
			HTML_vtigersalesorders::gather_info($field_array,$vtigerForm,$soid);
		} else {

			$user_fields = create_fields(array('firstname','lastname','accountid'));
			$mailing_fields = create_fields(array('mailingstreet','mailingcity','mailingstate','mailingzip','mailingcountry'));
			$shipping_fields = create_fields(array('otherstreet','othercity','otherstate','otherzip','othercountry'));
			$soid = mosGetParam( $_REQUEST, 'soid', '0' );
			$order = $SalesOrder->GetSalesOrderDetails($soid);
			$SalesOrder->soid=$soid;
			HTML_vtigersalesorders::view($order[0]);
			HTML_vtigersalesorders::shipping_info($user_fields,$mailing_fields,$shipping_fields,$soid);
		}
	break;
	case 'updateAddress':
		$addy_type = mosGetParam( $_REQUEST, 'type', '0' );
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );

		if($type == "mailing")
			$fields = create_fields(array('mailingstreet','mailingcity','mailingstate','mailingzip','mailingcountry'));
		else
			$fields = create_fields(array('otherstreet','othercity','otherstate','otherzip','othercountry'));

		$SalesOrder->soid=$soid;
		$order = $SalesOrder->GetSalesOrderDetails($soid);
		HTML_vtigersalesorders::view($order[0]);
		HTML_vtigersalesorders::update_addy($fields,$type);
	break;
	default:
		echo "Not Authorized";
	break;
}
function create_fields($types) {
	global $my,$mainframe,$vtigerForm;
        require_once($mainframe->getCfg('absolute_path').'/components/com_vtigerregistration/vtiger/VTigerContact.class.php');
        $Contact = new VtigerContact($my->id);
	foreach($types as $type) {
		$fields[] = array('module'=>'Contacts','columnname'=>$type,'viewtype'=>'detail','showlabel'=>'','entityid'=>$Contact->id,'picnum'=>'');
	}
	$field_array = $vtigerForm->GetMultipleFieldDetails($fields);
	asort($field_array);
	return $field_array;
}
function get_fields() {
        global $database,$basePath,$mainframe,$vtigerForm;
        $query = "SELECT * FROM "
                        ." #__vtiger_registration_fields "
                        ." ORDER BY #__vtiger_registration_fields.order"
        ;
        $database->setQuery( $query );
        $current_rows = $database->loadObjectList();

        for($i=0;$i<count($current_rows);$i++) {
                if($current_rows[$i]->type == "33" || $current_rows[$i]->type == "15") {
                        $vals = split(',',$vtigerForm->GetPicklistValues($current_rows[$i]->id));
                        for($j=0,$num=count($vals);$j<$num;$j++) {
                                $current_rows[$i]->values[] = $vals[$j];
                        }
                }
        }
        return $current_rows;
}
?>
