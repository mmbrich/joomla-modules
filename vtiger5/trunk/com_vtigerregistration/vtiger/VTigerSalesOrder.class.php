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
require_once($mainframe->getCfg('absolute_path').'/mambots/system/vt_classes/VTigerConnection.class.php');
class VTigerSalesOrder extends VTigerConnection {
        var $data;
	var $soid;
	var $contact;
	var $file = "salesorder";

        function VtigerSalesOrder()
        {
		global $mainframe;
                $this->conn = $this->VtigerConnection($this->file);
		require_once($mainframe->getCfg('absolute_path').'/components/com_vtigerregistration/vtiger/VTigerContact.class.php');
		$this->contact = new VtigerContact();
        }
	function Checkid($id)
	{
		$this->contact->jid=$id;
		$this->contact->LoadUser();
		if(!$this->contact->id || $this->contact->id == 0)
			return false;
		else
			return true;
	}
	function GetCurrentSalesOrders($id)
	{
		$this->contact->jid=$id;
		$this->contact->LoadUser();

	       	$this->data = array('entityid' => $this->contact->id);
                $this->setData($this->data);
                $result = $this->execCommand('get_current_salesorders');
                return $result;
	}
	function GetSalesOrderDetails($soid)
	{
		$this->soid=$soid;
	       	$this->data = array('soid' => $this->soid);
                $this->setData($this->data);
                $result = $this->execCommand('get_salesorder');
                return $result;
	}
	function AddToSalesOrder($productid)
	{
	       	$this->data = array(
			'soid' => $this->soid,
			'productid' => $productid
		);
                $this->setData($this->data);
                $result = $this->execCommand('add_product');
                return $result;
	}
	function RemoveFromSalesOrder($productid)
	{
	       	$this->data = array(
			'soid' => $this->soid,
			'productid' => $productid
		);
                $this->setData($this->data);
                $result = $this->execCommand('remove_product');
                return $result;
	}
	function UpdateProductQuantity($productid,$quantity)
	{
	       	$this->data = array(
			'soid' => $this->soid,
			'productid' => $productid,
			'quantity' => $quantity
		);
                $this->setData($this->data);
                $result = $this->execCommand('update_product_quantity');
                return $result;
	}
}
?>
