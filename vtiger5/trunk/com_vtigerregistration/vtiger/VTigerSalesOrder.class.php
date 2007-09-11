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
class VTigerSalesOrder extends VTigerForm {
        var $data;
	var $soid;
	var $id;
	var $contact;
	var $file = "salesorder";

        function VtigerSalesOrder()
        {
		VTigerConnection::VtigerConnection();
		global $mainframe;
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
	function UpdateAddresses($contactid,$type)
	{
	       	$this->data = array(
			'contactid' => $contactid,
			'soid' => $this->id,
			'type' => $type
		);
                $this->setData($this->data);
                $result = $this->execCommand('update_addresses',$this->GetSecureMode());
                return $result;
	}
	function GetCurrentSalesOrders($id='')
	{
                if (isset($_COOKIE["current_salesorder"]) && $id == "") {
                        $this->soid = $_COOKIE["current_salesorder"];
	       		$this->data = array(
				'entityid' => '',
				'soid' => $this->soid
			);
		} else {
			$this->contact->jid=$id;
			$this->contact->LoadUser();
	       		$this->data = array(
				'entityid' => $this->contact->id,
				'soid' => ''
			);
		}
                $this->setData($this->data);
                $result = $this->execCommand('get_current_salesorders',$this->GetSecureMode());
                return $result;
	}
	function SetSecureMode($mode='0')
	{
		session_start();
		$_SESSION["j_secure_mode"] = $mode;
	}
	function GetSecureMode()
	{
		session_start();
		return $_SESSION["j_secure_mode"];
	}
	function GetSalesOrderDetails($soid)
	{
		$this->soid=$soid;
	       	$this->data = array('soid' => $this->soid);
                $this->setData($this->data);
                $result = $this->execCommand('get_salesorder',$this->GetSecureMode());
                return $result;
	}
	function CreateNewSalesOrder($contactid)
	{
		$this->contact->id = $contactid;
	       	$this->data = array('contactid' => $this->contact->id);
                $this->setData($this->data);
                $result = $this->execCommand('new_salesorder',$this->GetSecureMode());
		$this->soid = $result;
                return $result;
	}
	function PopulateSalesOrder($soid,$contactid)
	{
		$this->contact->id = $contactid;
	       	$this->data = array('contactid' => $this->contact->id);
                $this->setData($this->data);
                $result = $this->execCommand('populate_salesorder',$this->GetSecureMode());
                //return $result;
		return;
	}
	function AddToSalesOrder($productid,$qty)
	{
		if(!$this->soid) {
			global $my;
			if($this->Checkid($my->id))
				$this->CreateNewSalesOrder($this->contact->id);
		}
	       	$this->data = array(
			'soid' => $this->soid,
			'productid' => $productid,
			'qty' => $qty
		);
                $this->setData($this->data);
                $result = $this->execCommand('add_product',$this->GetSecureMode());
                return $result;
	}
	function AssociateToUser()
	{
		global $my;
		$this->soid = $_COOKIE["current_salesorder"];

		$this->contact->jid=$my->id;
		$this->contact->LoadUser();

	       	$this->data = array(
			'contactid' => $this->contact->id,
			'soid' => $this->soid
		);
                $this->setData($this->data);
                $result = $this->execCommand('associate_to_user',$this->GetSecureMode());
		if($result != "failed") {
			setcookie("current_salesorder", "", time()-3600, '/');
			mosRedirect('index.php');
		}
                return $result;
	}
	function RemoveFromSalesOrder($productid)
	{
	       	$this->data = array(
			'soid' => $this->soid,
			'productid' => $productid
		);
                $this->setData($this->data);
                $result = $this->execCommand('remove_product',$this->GetSecureMode());
		if($result == "deleted") {
			setcookie("current_salesorder", "", time()-3600, '/');
                	return 1;
		} else 
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
                $result = $this->execCommand('update_product_quantity',$this->GetSecureMode());
                return $result;
	}
	function ConvertToInvoice()
	{
	       	$this->data = array('soid' => $this->soid);
                $this->setData($this->data);
                $result = $this->execCommand('convert_to_invoice',$this->GetSecureMode());
                return $result;
	}
	function MakePayment($invoiceid,$payment_type)
	{
		$this->GoSecure();
	       	$this->data = array(
			'soid' => $this->soid,
			'invoiceid' => $invoiceid,
			'entityid' => $this->contact->id,
			'paytype' => $payment_type
		);
                $this->setData($this->data);
                $result = $this->execCommand('make_payment',$this->GetSecureMode());
                return $result;
	}
	function IsOwner()
	{
		global $my;
		if($my->id)
			$this->contact->LoadUser();
	       	$this->data = array(
			'soid' => $this->id,
			'contactid' => $this->contact->id
		);
                $this->setData($this->data);
                $result = $this->execCommand('check_so_owner',$this->GetSecureMode());
		if($result == true)
			return true;
		else
			return false;
	}
}
?>
