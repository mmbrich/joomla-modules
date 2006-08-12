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

require_once("soap/jinc.php");

/************************ GET_CURRENT_SALESORDERS  START ****************************/
$server->register(
        'get_current_salesorders',
        array('entityid'=>'xsd:string'),
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
                'sostatus' => array('name'=>'sostatus','type'=>'xsd:string')
        )
);

function get_current_salesorders($entityid) {
	global $adb;
        $adb->println("Enter into the function get_current_salesorders(".$entityid.")");

	$q = "SELECT salesorderid,subject,carrier,pending,type,salestax,adjustment,total,subtotal, "
		." taxtype,discount_percent,discount_amount,s_h_amount,terms_conditions,sostatus "
		." FROM vtiger_salesorder "
		." WHERE contactid='".$entityid."'";
	$rs = $adb->query($q);

	$sos = array();
	while($tmp = $adb->fetch_array($rs)) {
		$sos[] = $tmp;
	}
	return $sos;
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
	$q = "UPDATE vtiger_inventoryproductrel "
		." SET quantity='".$quantity."' "
		." WHERE id='".$soid."' "
		." AND productid='".$productid."'";
	$rs = $adb->query($q);

	// TODO Re-calculate SO
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
	$q = "DELETE FROM vtiger_inventoryproductrel "
		." WHERE id='".$soid."' "
		." AND productid='".$productid."'";
	$rs = $adb->query($q);

	// TODO Re-calculate SO
	return true;
}
/************************ REMOVE_PRODUCT END ****************************/

/************************ ADD_PRODUCT START ****************************/
$server->register(
        'add_product',
        array(	'soid'=>'xsd:string',
		'productid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function add_product($soid,$productid) {
	global $adb;
        $adb->println("Enter into the function remove_product($soid,$productid)");
	if($soid == 0) {
		// New SO
		$blah='1';
	} else {
		// Current SO
		$blah='1';
	}
	return true;
}
/************************ ADD_PRODUCT END ****************************/


/* Begin the HTTP listener service and exit. */
$server->service($HTTP_RAW_POST_DATA);
exit();
?>

