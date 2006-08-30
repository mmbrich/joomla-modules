<?php
/*
 * The contents of this file are subject to the vtiger CRM Public License
 * Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 *
 * Portions created by Matthew Brichacek are Copyright (C) 2006 FOSS Labs.
 *
 * All Rights Reserved.
 *
 * Contributors: _________________
 *
 */
global $mainframe;
require_once($mainframe->getCfg('absolute_path').'/components/com_vtigerregistration/vtiger/VTigerForm.class.php');
class VTigerProduct extends VtigerForm {
        var $data;
	var $file = "product";

        function VtigerProduct()
        {
		VTigerConnection::VTigerConnection();
        }
	function ListProducts($category='')
	{
                $this->data = array( 'category'=>$category );
                $this->setData($this->data);
                return $this->execCommand('get_product_list');
	}
}
