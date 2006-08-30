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


/************* START PRODUCTS *****************/
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
                'qtyinstock' => array('name'=>'qtyinstock','type'=>'xsd:string'),
                'qtyindemand' => array('name'=>'qtyindemand','type'=>'xsd:string'),
                'image' => array('name'=>'image','type'=>'xsd:string')
             )
);
/********************** END PRODUCTS *********************/

/********************** START FIELDS *********************/
$server->wsdl->addComplexType(
        'field_type_array',
        'complexType',
        'array',
        '',
        array(
                'fieldid' => array('name'=>'fieldid','type'=>'xsd:string'),
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'uitype' => array('name'=>'uitype','type'=>'xsd:string'),
                'fieldname' => array('name'=>'fieldname','type'=>'xsd:string'),
                'fieldlabel' => array('name'=>'fieldlabel','type'=>'xsd:string'),
                'maximumlength' => array('name'=>'maximumlength','type'=>'xsd:string'),
                'value' => array('name'=>'value','type'=>'xsd:string'),
                'required' => array('name'=>'required','type'=>'xsd:string'),
                'values' => array('name'=>'values','type'=>'xsd:string')
        )
);
$server->wsdl->addComplexType(
        'multi_field_return_array',
        'complexType',
        'array',
        '',
        array(
                'fieldid' => array('name'=>'fieldid','type'=>'xsd:string'),
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'uitype' => array('name'=>'uitype','type'=>'xsd:string'),
                'fieldname' => array('name'=>'fieldname','type'=>'xsd:string'),
                'fieldlabel' => array('name'=>'fieldlabel','type'=>'xsd:string'),
                'maximumlength' => array('name'=>'maximumlength','type'=>'xsd:string'),
                'value' => array('name'=>'value','type'=>'xsd:string'),
                'values' => array('name'=>'values','type'=>'xsd:string'),
                'module' => array('name'=>'module','type'=>'xsd:string'),
                'viewtype' => array('name'=>'viewtype','type'=>'xsd:string'),
                'showlabel' => array('name'=>'showlabel','type'=>'xsd:string'),
                'entityid' => array('name'=>'entityid','type'=>'xsd:string'),
                'required' => array('name'=>'required','type'=>'xsd:string'),
                'picnum' => array('name'=>'picnum','type'=>'xsd:string')
        )
);

$server->wsdl->addComplexType(
        'multi_field_type_array',
        'complexType',
        'array',
        '',
        array(
                'module' => array('name'=>'module','type'=>'xsd:string'),
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'viewtype' => array('name'=>'viewtype','type'=>'xsd:string'),
                'showlabel' => array('name'=>'showlabel','type'=>'xsd:string'),
                'entityid' => array('name'=>'entityid','type'=>'xsd:string'),
                'required' => array('name'=>'required','type'=>'xsd:string'),
                'picnum' => array('name'=>'picnum','type'=>'xsd:string')
        )
);

$server->wsdl->addComplexType(
        'save_field_type',
        'complexType',
        'array',
        '',
        array(
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'value' => array('name'=>'value','type'=>'xsd:string')
        )
);
$server->wsdl->addComplexType(
        'mod_fields',
        'complexType',
        'array',
        '',
        array(
                'columnname' => array('name'=>'columnname','type'=>'xsd:string'),
                'fieldlabel' => array('name'=>'fieldlabel','type'=>'xsd:string')
        )
);

$server->wsdl->addComplexType(
        'mods',
        'complexType',
        'array',
        '',
        array(
                'module' => array('name'=>'module','type'=>'xsd:string')
        )
);

$server->wsdl->addComplexType(
        'field_array',
        'complexType',
        'array',
        '',
        array(
                'id' => array('name'=>'id','type'=>'xsd:string'),
                'field' => array('name'=>'field','type'=>'xsd:string'),
                'name' => array('name'=>'name','type'=>'xsd:string'),
                'type' => array('name'=>'type','type'=>'xsd:string'),
                'maximumlength' => array('name'=>'maximumlength','type'=>'xsd:string'),
                'order' => array('name'=>'order','type'=>'xsd:string')
             )
);
/************************ END FIELDS ***************************/

/************************ START CONTACTS **********************/
$server->wsdl->addComplexType(
        'current_register_fields',
        'complexType',
        'array',
        '',
        array(
                'fieldid' => array('name'=>'fieldid','type'=>'xsd:string'),
                'columnname' => array('name'=>'columnname','type'=>'xsd:string')
        )
);
/************************ END CONTACTS **********************/

/************************ START SALESORDERS **********************/
$server->wsdl->addComplexType(
        'salesorder_products',
        'complexType',
        'array',
        '',
        array(
                'productid' => array('name'=>'productid','type'=>'xsd:string'),
                'name' => array('name'=>'name','type'=>'xsd:string'),
                'code' => array('name'=>'website','type'=>'xsd:string'),
                'serialno' => array('name'=>'website','type'=>'xsd:string'),
                'website' => array('name'=>'website','type'=>'xsd:string'),
                'quantity' => array('name'=>'quantiy','type'=>'xsd:string'),
                'tax1' => array('name'=>'tax1','type'=>'xsd:string'),
                'tax2' => array('name'=>'tax2','type'=>'xsd:string'),
                'tax3' => array('name'=>'tax3','type'=>'xsd:string'),
                'discount_amount' => array('name'=>'discount_amount','type'=>'xsd:string'),
                'discount_percent' => array('name'=>'discount_percent','type'=>'xsd:string'),
                'total' => array('name'=>'total','type'=>'xsd:string')
        )
);
$server->wsdl->addComplexType(
        'salesorder_details',
        'complexType',
        'array',
        '',
        array(
                'salesorderid' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'subject' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'potentialid' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'customerno' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'quote_id' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'vtiger_purchaseorder' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'duedate' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'txtTax' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'txtAdjustment' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'exciseduty' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'grandtotal' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'subtotal' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'taxtype' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'discount_percent' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'discount_amount' => array('name'=>'salesorderid','type'=>'xsd:string'),
                's_h_amount' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'sh_tax1' => array('name'=>'sh_tax1','type'=>'xsd:string'),
                'sh_tax2' => array('name'=>'sh_tax2','type'=>'xsd:string'),
                'sh_tax3' => array('name'=>'sh_tax3','type'=>'xsd:string'),
                'account_id' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'bill_street' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'ship_street' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'bill_city' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'ship_city' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'bill_state' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'ship_state' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'bill_code' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'ship_code' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'bill_country' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'ship_country' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'bill_pobox' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'ship_pobox' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'description' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'terms_conditions' => array('name'=>'salesorderid','type'=>'xsd:string'),
                'pending' => array('name'=>'pending','type'=>'xsd:string'),
                'carrier' => array('name'=>'carrier','type'=>'xsd:string'),
                'sostatus' => array('name'=>'sostatus','type'=>'xsd:string'),
                'products' => array('name'=>'products','type'=>'tns:salesorder_products')
        )
);
/************************ END SALESORDERS **********************/

$server->wsdl->addComplexType(
        'challenge_response',
        'complexType',
        'array',
        '',
        array(
                'sessionid' => array('name'=>'sessionid','type'=>'xsd:string'),
                'challenge' => array('name'=>'challenge','type'=>'xsd:string')
        )
);
?>
