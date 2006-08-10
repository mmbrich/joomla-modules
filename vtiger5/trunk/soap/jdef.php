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
?>
