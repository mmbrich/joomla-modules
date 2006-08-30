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

/************************ GET_PRODUCT_LIST START ****************************/
$server->register(
	'get_product_list',
	array(
		'category'=>'xsd:string'
	),
	array(
		'return'=>'tns:product_array'
	),
	$NAMESPACE
);
function get_product_list($category='') {
        global $adb;
        $adb->println("Enter into the function get_product_list($category)");

        $tabid=GetTabid("Products");
        $q = "SELECT * FROM vtiger_products INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid WHERE vtiger_crmentity.deleted='0'";
	if($category != '')
		$q .= " AND vtiger_products.productcategory='".$category."'";
	$q .= " ORDER BY vtiger_products.productcategory";

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
                $product[$i]["qtyindemand"] = $adb->query_result($rs,$i,'qtyindemand');
		$rs2 = $adb->query("SELECT path,name,vtiger_attachments.attachmentsid FROM vtiger_attachments INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid=vtiger_attachments.attachmentsid WHERE vtiger_seattachmentsrel.crmid='".$product[$i]["productid"]."' LIMIT 1");
		if($adb->num_rows($rs2) == 1) {
			global $site_URL;
			$path = $adb->query_result($rs2,'0','path');
			$name = $adb->query_result($rs2,'0','name');
			$id = $adb->query_result($rs2,'0','attachmentsid');
			$product[$i]["image"] = $path.$id."_".$name;
        		$adb->println("GOT IMAGE: ".$product[$i]["image"]);
		} else 
			$product[$i]["image"] = "";
        }
        return $product;
}
/************************ GET_PRODUCT_LIST END ****************************/
?>
