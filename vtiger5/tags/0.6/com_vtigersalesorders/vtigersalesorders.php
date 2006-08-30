<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

// check if the bot exist
if(!file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php')) {
	echo "You should install bot_vconnection if you want something to happen here ;)";
	flush();exit();
}

// Get the right language if it exists
if (file_exists($mosConfig_absolute_path.'/components/com_vtigersalesorders/languages/vtigersalesorders_'.$mosConfig_lang.'.php')) {
    include($mosConfig_absolute_path.'/components/com_vtigersalesorders/languages/vtigersalesorders_'.$mosConfig_lang.'.php');
} else {
    include($mosConfig_absolute_path.'/components/com_vtigersalesorders/languages/vtigersalesorders_english.php');
}

global $my,$database;
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

// Check for correct ownership
$soid = mosGetParam( $_REQUEST, 'soid', '0' );
$Itemid = mosGetParam( $_REQUEST, 'Itemid', $vtigerForm->defaultItemid );

$SalesOrder->id=$soid;
if($soid == 0) {
	echo "Please add an item to your cart";
	return;

}  else {
	if($my->id) {
		$SalesOrder->contact->jid=$my->id;
		if(!$SalesOrder->IsOwner($soid)) {
			echo "No Access Allowed";
			return;
		}
	} else {
		$SalesOrder->contact->jid=0;
		if(!$SalesOrder->IsOwner($soid)) {
			echo "No Access Allowed";
			return;
		}
	}
}

if(isset($_POST["update_address"])) {
	$soid = mosGetParam( $_REQUEST, 'soid', '0' );
	$entityid = mosGetParam( $_REQUEST, 'vt_entityid', '0' );
	$task = mosGetParam( $_REQUEST, 'task', 'checkout' );
	$ret = $vtigerForm->SaveVtigerForm("Contacts",$entityid);
	$SalesOrder->id=$soid;
	$addy = $SalesOrder->UpdateAddresses($entityid,$_POST["update_address"]);
	mosRedirect(sefRelToAbs('index.php?option=com_vtigersalesorders&task='.$task.'&soid='.$soid.'&Itemid='.$Itemid));
}

switch($task) {
	case 'view':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$order = $SalesOrder->GetSalesOrderDetails($soid);
		$ret = $SalesOrder->Checkid($my->id);
		if(!$ret && !$soid)
			echo "You must log-in or create an account to view sales orders";
		else {
			$SalesOrder->soid=$soid;
			HTML_vtigersalesorders::view($order[0],$Itemid);
			HTML_vtigersalesorders::shopping_footer($soid,$Itemid);
		}
	break;
	case 'updateQuantity':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$productid = mosGetParam( $_REQUEST, 'product_id', '0' );
		$quantity = mosGetParam( $_REQUEST, 'quantity', '0' );
		if($productid == 0 || $quantity == 0) {
			$msg = "Failed to update";
			mosRedirect(sefRelToAbs('index.php?option=com_vtigersalesorders&task=view&Itemid='.$Itemid.'&soid='.$soid.'&msg='.$msg));
		} else {
			$ret = $SalesOrder->Checkid($my->id);
			if(!$ret && !$soid)
				echo "You must log-in or create an account to view sales orders";
			else {
				$SalesOrder->soid=$soid;
				$SalesOrder->UpdateProductQuantity($productid,$quantity);
				$msg = "Updated Product";
				mosRedirect(sefRelToAbs('index.php?option=com_vtigersalesorders&task=view&Itemid='.$Itemid.'&soid='.$soid.'&msg='.$msg));
			}
		}
	break;
	case 'addProduct':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		$productid = mosGetParam( $_REQUEST, 'productid', '' );
		$qty = mosGetParam( $_REQUEST, 'prd_qty', '1' );

		$ret = $SalesOrder->Checkid($my->id);
		if(!$ret && !$soid)
			echo "You must log-in or create an account to view sales orders";
		else {
			$SalesOrder->soid=$soid;
			$SalesOrder->AddToSalesOrder($productid,$qty);
			$msg = "Added Product";
			if(!isset($_COOKIE["current_salesorder"]) || $_COOKIE["current_salesorder"] == 0 || $_COOKIE["current_salesorder"] != $soid) {
				setcookie("current_salesorder", "", time()-3600, '/');
				setcookie("current_salesorder", $soid, time()+3600, '/');
			}
			mosRedirect(sefRelToAbs('index.php?option=com_vtigersalesorders&task=view&Itemid='.$Itemid.'&soid='.$soid.'&msg='.$msg));
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
			mosRedirect(sefRelToAbs('index.php?option=com_vtigersalesorders&task=view&Itemid='.$Itemid.'&soid='.$SalesOrder->soid.'&msg='.$msg));
		}
	break;
	case 'getPaymentInfo':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		if(!$ret && !$soid)
			echo "You must log-in or create an account to view sales orders";

		$order = $SalesOrder->GetSalesOrderDetails($soid);
		$cc_fields = create_fields(array('credit_card_name','credit_card_type','credit_card_num','cc_exp_date','cc_code'));
		$ec_fields = create_fields(array('bank_account_holder','bank_account_num','bank_sorting_number','bank_name','bank_account_type','bank_iban'));
		HTML_vtigersalesorders::view($order[0],$Itemid);
		HTML_vtigersalesorders::get_paymentinfo($cc_fields,$ec_fields,$Itemid);
	break;
	case 'makePayment':
		$soid = mosGetParam( $_REQUEST, 'soid', '0' );
		if(!$my->id && !$soid)
			echo "You must log-in or create an account to go further";

		$soid = mosGetParam( $_POST, 'soid', '0' );
		$SalesOrder->soid=$soid;
		$SalesOrder->contact->jid = $my->id;
		$SalesOrder->contact->LoadUser();
		$entityid = $SalesOrder->contact->id;

		$ret = $vtigerForm->SaveVtigerForm("Contacts",$entityid);
		$invoiceid = $SalesOrder->ConvertToInvoice();
		$payment_type = mosGetParam( $_POST, 'payment_method_id', '0' );

		echo "<strong>Please wait while we process your transaction.</strong>";
		if($SalesOrder->MakePayment($invoiceid,$payment_type) == true)
			echo "<br><h2>Transaction Completed</h2>";
		else
			echo "<br><h2>Transaction Failed</h2>";

		//mosRedirect(sefRelToAbs('index.php?option=com_vtigersalesorders&task='.$task.'&soid='.$soid.'&Itemid='.$Itemid));
	break;
	case 'checkout':
		if(!$my->id) {
                	require_once($mainframe->getCfg('absolute_path').'/components/com_vtigerregistration/vtiger/VTigerField.class.php');
                	require_once($mainframe->getCfg('absolute_path').'/components/com_vtigerregistration/vtigerregistration.html.php');

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

                	$registration_enabled = $mainframe->getCfg( 'allowUserRegistration' );
                	$message_login = $params->def( 'login_message',        0 );
                	$message_logout = $params->def( 'logout_message',       0 );
                	$login = $params->def( 'login', $return );
                	$logout = $params->def( 'logout', $return );
                	$name = $params->def( 'name',                         1 );
                	$greeting = $params->def( 'greeting',             1 );
                	$pretext = $params->get( 'pretext' );
                	$posttext = $params->get( 'posttext' );

                	$vtigerField = new VtigerField();
                	$fields = get_fields();

                	if(empty($my->id)){
                        	if(!isset($_SESSION)){
                                	session_start();
                        	}
                        	unset($_SESSION['vtiger_session']);
                	}
                	HTML_vtigerregistration::login($pretext,$posttext,$login,$soid,$Itemid);
                	HTML_vtigerregistration::register($fields,$vtigerField,$soid,$Itemid);
		} else {

			$user_fields = create_fields(array('firstname','lastname','accountid'));
			$mailing_fields = create_fields(array('mailingstreet','mailingcity','mailingstate','mailingzip','mailingcountry'));
			$shipping_fields = create_fields(array('otherstreet','othercity','otherstate','otherzip','othercountry'));
			$soid = mosGetParam( $_REQUEST, 'soid', '0' );
			$order = $SalesOrder->GetSalesOrderDetails($soid);
			$SalesOrder->soid=$soid;
			HTML_vtigersalesorders::view($order[0],$Itemid);
			HTML_vtigersalesorders::shipping_info($user_fields,$mailing_fields,$shipping_fields,$soid,$Itemid);
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
		HTML_vtigersalesorders::view($order[0],$Itemid);
		HTML_vtigersalesorders::update_addy($fields,$type,$Itemid);
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
