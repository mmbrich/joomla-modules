<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

/************************ CHECK_SO_OWNER  START ****************************/
$server->register(
        'check_so_owner',
        array(
		'soid'=>'xsd:string',
		'contactid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function check_so_owner($soid,$contactid) {
	global $adb;
        $adb->println("Enter into the function check_so_owner($soid,$contactid)");
	$q = "SELECT contactid FROM vtiger_salesorder "
		." WHERE salesorderid='".$soid."'";

	if($contactid == 0)
		$q .= " AND contactid IS NULL";
	else
		$q .= " AND contactid='".$contactid."'";

        $adb->println("$q");
	if($adb->num_rows($adb->query($q)) == 1)
		return true;
	else
		return false;
}
/************************ CHECK_SO_OWNER  END ****************************/


/************************ GET_CURRENT_SALESORDERS  START ****************************/
$server->register(
        'get_current_salesorders',
        array(
		'entityid'=>'xsd:string',
		'soid'=>'xsd:string'
	),
        //array('return'=>'xsd:string'),
        array('return'=>'tns:so_return_multi'),
        $NAMESPACE);

$server->wsdl->addComplexType(
        'so_return_multi',
        'complexType',
        'array',
        '',
        array(
                'salesorderid' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'subject' => array('name'=>'subject','type'=>'xsd:string'),
                'carrier' => array('name'=>'carrier','type'=>'xsd:string'),
                'pending' => array('name'=>'pending','type'=>'xsd:string'),
                'type' => array('name'=>'type','type'=>'xsd:string'),
                'salestax' => array('name'=>'salestax','type'=>'xsd:string'),
                'adjustment' => array('name'=>'adjustment','type'=>'xsd:string'),
                'total' => array('name'=>'total','type'=>'xsd:string'),
                'subtotal' => array('name'=>'subtotal','type'=>'xsd:string'),
                'taxtype' => array('name'=>'taxtype','type'=>'xsd:string'),
                'discount_percent' => array('discount_percent'=>'value','type'=>'xsd:string'),
                'discount_amount' => array('discount_amount'=>'value','type'=>'xsd:string'),
                's_h_amount' => array('name'=>'s_h_amount','type'=>'xsd:string'),
                'terms_conditions' => array('name'=>'terms_conditions','type'=>'xsd:string'),
                'num_products' => array('name'=>'num_products','type'=>'xsd:string'),
                'sostatus' => array('name'=>'sostatus','type'=>'xsd:string')
        )
);

function get_current_salesorders($entityid,$soid='') {
	global $adb;
        $adb->println("Enter into the function get_current_salesorders(".$entityid.")");

	if($entityid != "") {
		$q = "SELECT salesorderid,subject,carrier,pending,type,adjustment,total,subtotal, "
			." taxtype,discount_percent,discount_amount,s_h_amount,terms_conditions,sostatus "
			." FROM vtiger_salesorder "
			." INNER JOIN vtiger_crmentity "
			." ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid "
			." WHERE contactid='".$entityid."' "
			." AND vtiger_crmentity.deleted='0'"
			." AND (vtiger_salesorder.sostatus = 'Created' OR vtiger_salesorder.sostatus = 'Approved')";
		$rs = $adb->query($q);
	} else {
		$q = "SELECT salesorderid,subject,carrier,pending,type,adjustment,total,subtotal, "
			." taxtype,discount_percent,discount_amount,s_h_amount,terms_conditions,sostatus "
			." FROM vtiger_salesorder "
			." INNER JOIN vtiger_crmentity "
			." ON vtiger_crmentity.crmid=vtiger_salesorder.salesorderid "
			." WHERE crmid='".$soid."' "
			." AND vtiger_crmentity.deleted='0'"
			." AND (vtiger_salesorder.sostatus = 'Created' OR vtiger_salesorder.sostatus = 'Approved')";
		$rs = $adb->query($q);
	}

	//$sos = array();
	$i=0;
	while($tmp = $adb->fetch_array($rs)) {
		$sos[$i] = $tmp;
		$sos[$i]["num_products"] = $adb->num_rows($adb->query("SELECT id FROM vtiger_inventoryproductrel WHERE id='".$tmp["salesorderid"]."'"));
		$i++;
	}
	if(is_array($sos))
		return $sos;
	else
		return 0;
}
/************************ GET_CURRENT_SALESORDERS  END ****************************/


/************************ GET_CURRENT_SALESORDERS  START ****************************/
$server->register(
        'get_salesorder',
        array('soid'=>'xsd:string'),
        array('return'=>'tns:salesorder_details'),
        $NAMESPACE);

function get_salesorder($soid) {
	global $adb;
        $adb->println("Enter into the function get_salesorder(".$soid.")");
	$focus = create_entity("SalesOrder",$soid);

	$q = "SELECT * FROM vtiger_inventoryproductrel "
		." INNER JOIN vtiger_products "
		." ON vtiger_products.productid=vtiger_inventoryproductrel.productid "
		." WHERE id='".$focus->column_fields["record_id"]."'";

	$rs = $adb->query($q);
	$order = array();
	$c=0;
	while($row = $adb->fetch_array($rs)) {
		$prods[$c]["productid"] = $row["productid"];
		$prods[$c]["name"] = $row["productname"];
		$prods[$c]["code"] = $row["productcode"];
		$prods[$c]["serialno"] = $row["serialno"];
		$prods[$c]["website"] = $row["website"];
		$prods[$c]["quantity"] = $row["quantity"];
		$prods[$c]["tax1"] = $row["tax1"];
		$prods[$c]["tax2"] = $row["tax2"];
		$prods[$c]["tax3"] = $row["tax3"];
		$prods[$c]["discount_amount"] = $row["discount_amount"];
		$prods[$c]["discount_percent"] = $row["discount_percent"];
		$prods[$c]["total"] = $row["listprice"];
		$c++;
	}

	$order[0]["salesorderid"] = $focus->column_fields["record_id"];
	$order[0]["subject"] = $focus->column_fields["subject"];
	$order[0]["potentialid"] = $focus->column_fields["potentialid"];
	$order[0]["customerno"] = $focus->column_fields["customerno"];
	$order[0]["account_id"] = $focus->column_fields["account_id"];
	$order[0]["quote_id"] = $focus->column_fields["quote_id"];
	$order[0]["vtiger_purchaseorder"] = $focus->column_fields["vtiger_purchaseorder"];

	$order[0]["duedate"] = $focus->column_fields["duedate"];
	$order[0]["txtTax"] = $focus->column_fields["txtTax"];
	$order[0]["txtAdjustment"] = $focus->column_fields["txtAdjustment"];
	$order[0]["exciseduty"] = $focus->column_fields["exciseduty"];

	$order[0]["grandtotal"] = $focus->column_fields["hdnGrandTotal"];
	$order[0]["subtotal"] = $focus->column_fields["hdnSubTotal"];
	$order[0]["taxtype"] = $focus->column_fields["hdnTaxType"];
	$order[0]["discount_percent"] = $focus->column_fields["hdnDiscountPercent"];
	$order[0]["discount_amount"] = $focus->column_fields["hdnDiscountAmount"];
	$order[0]["s_h_amount"] = $focus->column_fields["hdnS_H_Amount"];

	$q1 = "SELECT * FROM vtiger_inventoryshippingrel "
		." WHERE id='".$focus->column_fields["record_id"]."'";
	$rs1 = $adb->query($q1);
	$order[0]["sh_tax1"] = $adb->query_result($rs1,'0','shtax1');
	$order[0]["sh_tax2"] = $adb->query_result($rs1,'0','shtax2');
	$order[0]["sh_tax3"] = $adb->query_result($rs1,'0','shtax3');


	$order[0]["bill_street"] = $focus->column_fields["bill_street"];
	$order[0]["bill_pobox"] = $focus->column_fields["bill_pobox"];
	$order[0]["bill_city"] = $focus->column_fields["bill_city"];
	$order[0]["bill_code"] = $focus->column_fields["bill_code"];
	$order[0]["bill_state"] = $focus->column_fields["bill_state"];
	$order[0]["bill_country"] = $focus->column_fields["bill_country"];

	$order[0]["ship_street"] = $focus->column_fields["ship_street"];
	$order[0]["ship_pobox"] = $focus->column_fields["ship_pobox"];
	$order[0]["ship_city"] = $focus->column_fields["ship_city"];
	$order[0]["ship_code"] = $focus->column_fields["ship_code"];
	$order[0]["ship_state"] = $focus->column_fields["ship_state"];
	$order[0]["ship_country"] = $focus->column_fields["ship_country"];

	$order[0]["description"] = $focus->column_fields["description"];
	$order[0]["terms_conditions"] = $focus->column_fields["terms_conditions"];
	$order[0]["pending"] = $focus->column_fields["pending"];
	$order[0]["carrier"] = $focus->column_fields["carrier"];
	$order[0]["sostatus"] = $focus->column_fields["sostatus"];

	$order[0]["products"] = $prods;

        $adb->println("Exiting the function get_salesorder($soid)");

	return $order;
}
/************************ GET_CURRENT_SALESORDERS  END ****************************/

/************************ UPDATE_PRODUCT_QUANTITY START ****************************/
$server->register(
        'update_product_quantity',
        array(	'soid'=>'xsd:string',
		'productid'=>'xsd:string',
		'quantity'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function update_product_quantity($soid,$productid,$quantity) {
	global $adb;
        $adb->println("Enter into the function update_product_quantity($soid,$productid,$quantity)");

	$q = "SELECT quantity FROM vtiger_inventoryproductrel "
		." WHERE id='".$soid."' "
		." AND productid='".$productid."' ";
	$cur_qty = $adb->query_result($adb->query($q),'0','quantity');

	$q = "UPDATE vtiger_inventoryproductrel "
		." SET quantity='".$quantity."' "
		." WHERE id='".$soid."' "
		." AND productid='".$productid."'";
	$rs = $adb->query($q);

	// TODO Re-calculate SO
	$prod_info = get_taxinfo($productid);
        $adb->println("PRODUCT INFO ".$prod_info["taxid"]);

	$q = "SELECT total, subtotal "
		." FROM vtiger_salesorder "
		." WHERE salesorderid='".$soid."' ";
	$rs = $adb->query($q);
	$gtotal = $adb->query_result($rs,'0','total');
	$stotal = $adb->query_result($rs,'0','subtotal');
        $adb->println("CURRENT SO GTOTAL ".$gtotal);
        $adb->println("CURRENT SO STOTAL ".$stotal);

	$newgtotal = calc_totals($gtotal, ($quantity-$cur_qty), $prod_info);
	$newstotal = calc_totals($stotal, ($quantity-$cur_qty), $prod_info);
        $adb->println("NEW SO GTOTAL ".$newgtotal);
        $adb->println("NEW SO STOTAL ".$newstotal);

	$q = "UPDATE vtiger_salesorder "
		." SET total='".$newgtotal."', subtotal='".$newstotal."' "
		." ,taxtype='individual' "
		." WHERE salesorderid='".$soid."'";
	$rs = $adb->query($q);

	return true;
}
/************************ UPDATE_PRODUCT_QUANTITY END ****************************/

/************************ REMOVE_PRODUCT START ****************************/
$server->register(
        'remove_product',
        array(	'soid'=>'xsd:string',
		'productid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function remove_product($soid,$productid) {
	global $adb;
        $adb->println("Enter into the function remove_product($soid,$productid)");

	$q = "SELECT * FROM vtiger_inventoryproductrel "
		." LEFT JOIN vtiger_producttaxrel "
		." ON vtiger_producttaxrel.productid=vtiger_inventoryproductrel.productid "
		." WHERE vtiger_inventoryproductrel.id='".$soid."' "
		." AND vtiger_inventoryproductrel.productid='".$productid."'";
	$rs = $adb->query($q);
	$list_price = $adb->query_result($rs,'0','listprice');
	$quantity = $adb->query_result($rs,'0','quantity');
        $adb->println("\n\rLIST PRICE: ".$list_price);
        $adb->println("\n\rQUANTITY: ".$quantity);

	$taxid = $adb->query_result($rs,'0','taxid');
	$tax = $adb->query_result($rs,'0','tax'.$taxid);
        $adb->println("TAX AMOUNT: ".$tax);

	$q = "SELECT total,subtotal "
		." FROM vtiger_salesorder "
		." WHERE salesorderid='".$soid."'";
	$rs = $adb->query($q);
	$gtotal = $adb->query_result($rs,'0','total');
	$stotal = $adb->query_result($rs,'0','subtotal');

	$total = ( $list_price * $quantity );
        $adb->println("\n\rTOTAL PRICE: ".$total);

	$this_total = ( ( ( $total * $tax ) / 100 ) + $total);

        $adb->println("\n\rTOTAL TO REMOVE: ".$this_total);

	$newgtotal = ($gtotal - $this_total);
	$newstotal = ($stotal - $this_total);
        $adb->println("\n\rNEW TOTAL : ".$newgtotal);

	$q = "DELETE FROM vtiger_inventoryproductrel "
		." WHERE id='".$soid."' "
		." AND productid='".$productid."'";
	$rs = $adb->query($q);

	$q = "SELECT count(*) FROM vtiger_inventoryproductrel "
		." WHERE id='".$soid."'";
	$num_products = $adb->query_result($adb->query($q),'0','count(*)');

	// Check if its the last product
	if($num_products >= 1) {
       		$adb->println("UPDATING SO ".$soid);
		$q = "UPDATE vtiger_salesorder "
			." SET total='".$newgtotal."', subtotal='".$newstotal."' "
			." ,taxtype='individual' "
			." WHERE salesorderid='".$soid."'";
	} else {
       		$adb->println("DELETING SO ".$soid);
		$q = "UPDATE vtiger_crmentity "
			." SET deleted='1' "
			." WHERE crmid='".$soid."'";
		$ret = "deleted";
	}
	$rs = $adb->query($q);

	if($ret == "deleted")
		return $ret;
	else
		return true;
}
/************************ REMOVE_PRODUCT END ****************************/

/************************ ADD_PRODUCT START ****************************/
$server->register(
        'add_product',
        array(	'soid'=>'xsd:string',
		'productid'=>'xsd:string',
		'qty'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function add_product($soid,$productid,$qty) {
	global $adb;
        $adb->println("Enter into the function add_product($soid,$productid)");
	if($soid == 0)
		return false;
	else {
      	 	$adb->println("Getting Taxes --- ");
		$prod_info = get_taxinfo($productid);
		if($prod_info["taxid"] == "")
			$prod_info["taxid"] = "1";

		// Check to see if product is already in the order
		// update qty and totals if so.
		$q = "SELECT * FROM vtiger_inventoryproductrel "
			." WHERE id='".$soid."'"
			." AND productid='".$productid."'";
		$rs = $adb->query($q);
		if($adb->num_rows($rs) > 0) {
			$current_product=true;
			$current_qty = $adb->query_result($rs,'0','quantity');
		} else
			$current_product=false;

		if(!$current_product) {
			$q = "INSERT INTO vtiger_inventoryproductrel "
				." (id,productid,quantity,listprice,tax".$prod_info["taxid"].") "
				." VALUES "
				." ('".$soid."','".$productid."','".$qty."',"
				." '".$prod_info["list_price"]."','".$prod_info["percent"]."') ";
			$rs = $adb->query($q);
		} else {
			return update_product_quantity($soid,$productid,($qty+$current_qty));
		}

      	 	$adb->println("Getting Totals ");

		$q = "SELECT total, subtotal "
			." FROM vtiger_salesorder "
			." WHERE salesorderid='".$soid."' ";
		$rs = $adb->query($q);
		$gtotal = $adb->query_result($rs,'0','total');
		$stotal = $adb->query_result($rs,'0','subtotal');

      	 	$adb->println("Calculating GTotals ");
		$newgtotal = calc_totals($gtotal, $qty, $prod_info);
      	 	$adb->println("Calculating STotals ");
		$newstotal = calc_totals($stotal, $qty, $prod_info);

      	 	$adb->println("Updating Salesorder ");
		$q = "UPDATE vtiger_salesorder "
			." SET total='".$newgtotal."', subtotal='".$newstotal."' "
			." ,taxtype='individual' "
			." WHERE salesorderid='".$soid."'";
		$rs = $adb->query($q);
	
		return true;
	}
}
/************************ ADD_PRODUCT END ****************************/

/************************ POPULATE_SALESORDER START ****************************/
$server->register(
        'populate_salesorder',
        array('soid'=>'xsd:string','contactid'=>'xsd:string'),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function populate_salesorder($soid,$contactid) {
	global $adb;
        $adb->println("Enter into the function populate_salesorder($contactid)");
	$date_var = date('YmdHis');
	$current_user = inherit_user($contactid);

	$so = create_entity("SalesOrder",$soid);
	$so->column_fields["assigned_user_id"] = $current_user->id;

	if($contactid != "") {
                $Contact = create_entity("Contacts",$contactid);
                $so->column_fields["account_id"] = $Contact->column_fields["account_id"];
                $so->column_fields["contact_id"] = $contactid;
	}

	$so->column_fields["subject"] = $date_var;
	$so->column_fields["hdnTaxType"] = "individual";
	$so->column_fields["description"] = "Created via Joomla CMS on ".$date_var;
	$so->column_fields["sostatus"] = "Created";
	$ret = billing_addy_update($Contact,$so);
	$ret = shipping_addy_update($Contact,$so);
	$so->save("SalesOrder");
	return;
}
/************************ POPULATE_SALESORDER END ****************************/

/************************ NEW_SALESORDER START ****************************/
$server->register(
        'new_salesorder',
        array('contactid'=>'xsd:string'),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function new_salesorder($contactid) {
	global $adb,$default_ownerid;
        $adb->println("Enter into the function new_salesorder($contactid)");
	$date_var = date('YmdHis');
	$current_user = inherit_user($contactid);

	$q = "SELECT id FROM vtiger_crmentity_seq";
	$rs = $adb->query($q);
	$res = $adb->fetch_array($rs);
	$tid = $res[0];
	$id = ($tid+1);

	$q = "UPDATE vtiger_crmentity_seq SET id='".$id."'";
	$rs = $adb->query($q);

	$q = "INSERT INTO vtiger_crmentity (crmid,smownerid,setype,description,presence) VALUES ($id,$default_ownerid,'SalesOrder','Created by Joomla on ".$date_var."','1')";
	$rs = $adb->query($q);

	$q = "INSERT INTO vtiger_salesorder (salesorderid,subject,sostatus) VALUES ($id,$date_var,'Created')";
	$rs = $adb->query($q);

	return $id;
}
/************************ ADD_PRODUCT END ****************************/

/************************ ASSOCIATE_TO_USER START ****************************/
$server->register(
        'associate_to_user',
        array(
		'contactid'=>'xsd:string',
		'soid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function associate_to_user($contactid,$soid) {
	global $adb;
        $adb->println("\n\r\n\rEnter into the function associate_to_user($contactid,$soid)\n\r\n\r");
	$current_user = inherit_user($contactid);

	$CO = create_entity("Contacts",$contactid);

	$q = "UPDATE vtiger_salesorder "
		." SET accountid='".$CO->column_fields["account_id"]."'"
		." ,contactid='".$contactid."'"
		." WHERE salesorderid='".$soid."'";
	$rs = $adb->query($q);

	$ret = update_addresses($contactid,$soid);
	return soid;
}
/************************ ASSOCIATE_TO_USER END ****************************/

/************************ MAKE_PAYMENT START ****************************/
$server->register(
        'make_payment',
        array(
		'soid'=>'xsd:string',
		'invoiceid'=>'xsd:string',
		'entityid'=>'xsd:string',
		'type'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function make_payment($soid,$invoiceid,$entityid,$type) {
	global $adb;
        $adb->println("\n\r\n\rEnter into the function make_payment($soid,$invoiceid,$entityid,$type)\n\r\n\r");

	require_once("modules/Payments/AuthNet.php");
	$payment = new AuthNet();

	global $current_user;
	$current_user = inherit_user($entityid);

        $payment->entityid = $entityid;
        $payment->billid = $invoiceid;

        $invoice = create_entity("Invoice",$invoiceid);
	$invoice->column_fields["assigned_user_id"] = $current_user->id;
	$pay_stat = $payment->PostPayment();

        $so = create_entity("SalesOrder",$invoice->column_fields["salesorder_id"]);
	$so->column_fields["assigned_user_id"] = $current_user->id;
	if($pay_stat == true) {
        	$invoice->column_fields["invoicestatus"] = "Paid";
        	$so->column_fields["sostatus"] = "Delivered";
	} else {
        	$so->column_fields["sostatus"] = "Approved";
		$adb->query("UPDATE vtiger_crmentity SET deleted='1' WHERE crmid='".$invoiceid."'");
	}
	$so->save("SalesOrder");

	$invoice->save("Invoice");
        return $pay_stat;
}
/************************ MAKE_PAYMENT END ****************************/

/************************ UPDATE_ADDRESS START ****************************/
$server->register(
        'update_addresses',
        array(
		'contactid'=>'xsd:string',
		'soid'=>'xsd:string',
		'type'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function update_addresses($contactid,$soid,$type='all') {
	global $adb;
        $adb->println("\n\r\n\rEnter into the function update_addresses($contactid,$soid,$type)\n\r\n\r");

	global $current_user;
	$current_user = inherit_user($contactid);

	$CO = create_entity("Contacts",$contactid);
	$focus = create_entity("SalesOrder",$soid);
	$focus->column_fields["assigned_user_id"] = $current_user->id;

	if($type == "all") {
		shipping_addy_update($CO,$focus);
		billing_addy_update($CO,$focus);
	} else if($type == "mailing")
		billing_addy_update($CO,$focus);
	else if ($type == "other")
		shipping_addy_update($CO,$focus);

	$focus->save("SalesOrder");
	return $focus->id;
}
function billing_addy_update($CO,$focus) {
	$focus->column_fields["record_id"] = $soid;
	$focus->column_fields["bill_street"] = $CO->column_fields["mailingstreet"];
	$focus->column_fields["bill_city"] = $CO->column_fields["mailingcity"];
	$focus->column_fields["bill_state"] = $CO->column_fields["mailingstate"];
	$focus->column_fields["bill_code"] = $CO->column_fields["mailingzip"];
	$focus->column_fields["bill_country"] = $CO->column_fields["mailingcountry"];
}
function shipping_addy_update($CO,$focus) {
	$focus->column_fields["ship_street"] = $CO->column_fields["otherstreet"];
	$focus->column_fields["ship_city"] = $CO->column_fields["othercity"];
	$focus->column_fields["ship_state"] = $CO->column_fields["otherstate"];
	$focus->column_fields["ship_code"] = $CO->column_fields["otherzip"];
	$focus->column_fields["ship_country"] = $CO->column_fields["othercountry"];
}
/************************ UPDATE_ADDRESS END ****************************/

/************************ CONVERT_TO_INVOICE START ****************************/
$server->register(
        'convert_to_invoice',
        array(
		'soid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function convert_to_invoice($soid) {
	global $adb;
        $adb->println("\n\r\n\rEnter into the function convert_to_invoice($soid)\n\r\n\r");

	$focus = create_entity("Invoice");
	$so_focus = create_entity("SalesOrder",$soid);

	global $current_user;
	$current_user = inherit_user($soid);

        $so = getConvertSoToInvoice($focus,$so_focus,$soid);

        $focus->column_fields['vtiger_purchaseorder'] = $so_focus->column_fields['vtiger_purchaseorder'];
        $focus->column_fields['terms_conditions'] = $so_focus->column_fields['terms_conditions'];
	$focus->column_fields["assigned_user_id"] = $current_user->id;

        $associated_prod = getAssociatedProducts("SalesOrder",$so_focus);

	// Populate info
	foreach($so_focus->column_fields as $key=>$value) {
		$focus->column_fields[$key] = $value;
	}

	$so->save("SalesOrder");

	// Populate products
	$q = "SELECT * FROM vtiger_inventoryproductrel "
		." WHERE id='".$soid."'";
	$rs = $adb->query($q);
	while($row = $adb->fetch_array($rs)) {
		$q = "INSERT INTO vtiger_inventoryproductrel "
			." (id,productid,quantity,listprice,tax1,tax2,tax3) "
			." VALUES "
			." ('".$focus->id."','".$row["productid"]."','".$row["quantity"]."','".$row["listprice"]."','".$row["tax1"]."','".$row["tax2"]."','".$row["tax3"]."') ";
		$t = $adb->query($q);
	}

	return $focus->id;
}
/************************ CONVERT_TO_INVOICE END ****************************/

function calc_totals($cur_total, $qty, $prod_info) {
	global $adb;
        $adb->println("CALCULATING NEW TOTALS!!!");
	$total = ( $prod_info["list_price"] * $qty );
	$this_total = ( ( ( $total * $prod_info["percent"] ) / 100 ) + $total) ;

        $adb->println("OLD TOTAL  ".$cur_total);
        $adb->println("LIST PRICE TOTAL  ".$total);
        $adb->println("QTY  ".$qty);
        $adb->println("NEW TOTAL  ".$this_total);

	$newtotal = ($this_total + $cur_total);
	return $newtotal;

}
function get_taxinfo($productid) {
	global $adb;
	$taxid='1';

	$q = "SELECT unit_price "
		." FROM vtiger_products "
		." WHERE productid='".$productid."' ";
	$rs = $adb->query($q);
	$list_price = $adb->query_result($rs,'0','unit_price');

	$q = "SELECT * FROM vtiger_producttaxrel where productid='".$productid."'";
	$rs = $adb->query($q);
	$taxid = $adb->query_result($rs,'0','taxid');
	$percent = $adb->query_result($rs,'0','taxpercentage');

	return array("list_price"=>$list_price,"taxid"=>$taxid,"percent"=>$percent);
}
?>
