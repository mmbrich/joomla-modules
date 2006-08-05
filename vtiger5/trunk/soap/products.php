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

require_once("config.php");
require_once('include/logging.php');
require_once('include/nusoap/nusoap.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');

$log = &LoggerManager::getLogger('webforms');
$NAMESPACE = 'http://www.vtiger.com/vtigercrm/';
$server = new soap_server;
$server->configureWSDL('vtigersoap');


/* START ARRAY DECLARATIONS */
$server->wsdl->addComplexType(
        'product_array',
        'complexType',
        'array',
        '',
        array(
                'productid' => array('name'=>'productid','type'=>'xsd:string'),
                'productname' => array('name'=>'productname','type'=>'xsd:string'),
                'productcode' => array('name'=>'productcode','type'=>'xsd:string'),
                'productcategory' => array('name'=>'productcategory','type'=>'xsd:string'),
                'manufacturer' => array('name'=>'manufacturer','type'=>'xsd:string'),
                'product_description' => array('name'=>'product_description','type'=>'xsd:string'),
                'qty_per_unit' => array('name'=>'qty_per_unit','type'=>'xsd:string'),
                'unit_price' => array('name'=>'unit_price','type'=>'xsd:string'),
                'weight' => array('name'=>'weight','type'=>'xsd:string'),
                'pack_size' => array('name'=>'pack_size','type'=>'xsd:string'),
                'sales_start_date' => array('name'=>'sales_start_date','type'=>'xsd:string'),
                'sales_end_date' => array('name'=>'sales_end_date','type'=>'xsd:string'),
                'start_date' => array('name'=>'start_date','type'=>'xsd:string'),
                'expiry_date' => array('name'=>'expiry_date','type'=>'xsd:string'),
                'cost_factor' => array('name'=>'cost_factor','type'=>'xsd:string'),
                'usageunit' => array('name'=>'usageunit','type'=>'xsd:string'),
                'handler' => array('name'=>'handler','type'=>'xsd:string'),
                'currency' => array('name'=>'currency','type'=>'xsd:string'),
                'website' => array('name'=>'website','type'=>'xsd:string'),
                'taxclass' => array('name'=>'taxclass','type'=>'xsd:string'),
                'serialno' => array('name'=>'serialno','type'=>'xsd:string'),
                'qtyinstock' => array('name'=>'qtyinstock','type'=>'xsd:string')
             )
);
/* END ARRAY DECLARATIONS */

/* GET_FIELD_DETAILS  START */
$server->register(
	'get_product_list',
	array(
		'prod'=>'xsd:string'
	),
	array(
		'return'=>'tns:product_array'
	),
	$NAMESPACE
);
function get_product_list($prod='') {
        global $adb;
        $adb->println("Enter into the function get_field_details($module,$columnname)");

        $tabid=GetTabid("Products");
        $q = "SELECT * FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid WHERE vtiger_crmentity.deleted='0' ORDER BY vtiger_products.productcategory";

        $rs = $adb->query($q);
        for($i=0,$num=$adb->num_rows($rs);$i<$num;$i++) {
                $product[$i]["productid"] = $adb->query_result($rs,$i,'productid');
                $product[$i]["productname"] = $adb->query_result($rs,$i,'productname');
                $product[$i]["productcode"] = $adb->query_result($rs,$i,'productcode');
                $product[$i]["productcategory"] = $adb->query_result($rs,$i,'productcategory');
                $product[$i]["manufacturer"] = $adb->query_result($rs,$i,'manufacturer');
                $product[$i]["product_description"] = $adb->query_result($rs,$i,'product_description');
                $product[$i]["qty_per_unit"] = $adb->query_result($rs,$i,'qty_per_unit');
                $product[$i]["unit_price"] = $adb->query_result($rs,$i,'unit_price');
                $product[$i]["weight"] = $adb->query_result($rs,$i,'weight');
                $product[$i]["pack_size"] = $adb->query_result($rs,$i,'pack_size');
                $product[$i]["sales_start_date"] = $adb->query_result($rs,$i,'sales_start_date');
                $product[$i]["sales_end_date"] = $adb->query_result($rs,$i,'sales_end_date');
                $product[$i]["start_date"] = $adb->query_result($rs,$i,'start_date');
                $product[$i]["expiry_date"] = $adb->query_result($rs,$i,'expiry_date');
                $product[$i]["cost_factor"] = $adb->query_result($rs,$i,'cost_factor');
                $product[$i]["usageunit"] = $adb->query_result($rs,$i,'usageunit');
                $product[$i]["handler"] = $adb->query_result($rs,$i,'handler');
                $product[$i]["currency"] = $adb->query_result($rs,$i,'currency');
                $product[$i]["website"] = $adb->query_result($rs,$i,'website');
                $product[$i]["taxclass"] = $adb->query_result($rs,$i,'taxclass');
                $product[$i]["serialno"] = $adb->query_result($rs,$i,'serialno');
                $product[$i]["qtyinstock"] = $adb->query_result($rs,$i,'qtyinstock');
        }
        return $product;
}
/* GET_FIELD_DETAILS  END */

/****** END OF PUBLIC FUNCTIONS *********/


/* START INTERNAL FUNCTIONS */
function create_entity($module,$entityid='') {
	global $adb;
	$adb->println("Enter into the function create_entity($module,$entityid)");

	if($module == "Products") {
		require_once('modules/Products/Product.php');
		$focus = new Product();
	} else if($module == "Contacts") {
		require_once('modules/Contacts/Contact.php');
		$focus = new Contact();
	} else if($module == "Accounts") {
		require_once('modules/Accounts/Account.php');
		$focus = new Account();
	} else if($module == "Leads") {
		require_once('modules/Leads/Lead.php');
		$focus = new Lead();
	} else if($module == "Activities") {
		require_once('modules/Activities/Activity.php');
		$focus = new Activity();
	} else if($module == "Campaigns") {
		require_once('modules/Campaigns/Campaign.php');
		$focus = new Campaign();
	} else if($module == "HelpDesk") {
		require_once('modules/HelpDesk/HelpDesk.php');
		$focus = new HelpDesk();
	} else if($module == "Invoice") {
		require_once('modules/Invoice/Invoice.php');
		$focus = new Invoice();
	} else if($module == "Potentials") {
		require_once('modules/Potentials/Potential.php');
		$focus = new Potential();
	} else if($module == "Quotes") {
		require_once('modules/Quotes/Quote.php');
		$focus = new Quote();
	} else if($module == "SalesOrder") {
		require_once('modules/SalesOrder/SalesOrder.php');
		$focus = new SalesOrder();
	}

	if($entityid != "" && $entityid != 0) {
		$focus->id = $entityid;
		$focus->mode = "edit";
		$focus->retrieve_entity_info($entityid,$module);
	}
	return $focus;
}

function entityid_sort($a,$b) {
	return strcmp($a["entityid"], $b["entityid"]);
}
/* END INTERNAL FUNCTIONS */



/* Begin the HTTP listener service and exit. */ 
$server->service($HTTP_RAW_POST_DATA); 
exit(); 
?>
