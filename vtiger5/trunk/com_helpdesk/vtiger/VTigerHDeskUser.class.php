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
require_once('mambots/system/vt_classes/VTigerConnection.class.php');
class VTigerHDeskUser {
	var $conn;
	var $data;
	var $username;
	var $password;
	var $id;
	var $customer_name;
	var $combolist;

	function VtigerHDeskUser()
	{
		session_start();
		$this->conn = new VtigerConnection("customerportal");
	}
	function Authenticate($username,$password) 
	{
		$this->data = array('username' => $username,
				    'password' => $password);
		$this->conn->setData($this->data);
		$this->username = $username;
		$this->password = $password;
		$result = $this->conn->execCommand('authenticate_user');

		if($result[0] != "" && isset($result[0]))
		{
			$this->id = $result[0];
			$this->customer_name = $result[1];

        		$this->data = Array('id' => "$result[0]",'flag'=>"login");
			$this->conn->setData($this->data);
			$this->conn->execCommand('update_login_details');

			$_SESSION["vt_authenticated"]="true";
			$_SESSION["vt_id"] = $result[0];
			$_SESSION["vt_user_name"] = $result[1];
			$_SESSION["vt_user_pass"] = $result[2];
			$_SESSION["vt_last_login_time"] = $result[3];
			$_SESSION["vt_support_start_date"] = $result[4];
			$_SESSION["vt_support_end_date"] = $result[5];

			return $result[0];
		} else
			return "FALSE";
	}
	function LogOut()
	{
		$_SESSION["vt_authenticated"]="false";
		unset($_SESSION["vt_authenticated"]);
		unset($_SESSION["vt_user_name"]);
		unset($_SESSION["vt_id"]);
		session_destroy();
	}
	function GetTicketComments($ticketid)
	{
		$this->data = array('ticketid'=> $ticketid);
		$this->conn->setData($this->data);
		return $this->conn->execCommand('get_ticket_comments');
	}	
	function ListTickets()
	{
		$this->data = array('user_name'=>$_SESSION["vt_user_name"],
				    'id'=>$_SESSION["vt_id"],
				    'where'=>'',
				    'match'=>'');
		$this->conn->setData($this->data);
		$ret = $this->conn->execCommand('get_tickets_list');
		if($ret)
			return $ret;
		else
			return;
	}
	function ListComboValues() {
		$this->data = array('id'=>$this->id);
		$this->conn->setData($this->data);
		$this->combolist = $this->conn->execCommand('get_combo_values');
		return $this->combolist;
	}
	function CreateTicket($title,$description,$priority,$severity,$category)
	{
        	$this->data = array(
                        'title'=>"$title",
                        'description'=>"$description",
                        'priority'=>"$priority",
                        'severity'=>"$severity",
                        'category'=>"$category",
                        'user_name' => "$username",
                        'parent_id'=> $_SESSION["vt_id"],
                        'product_id'=>"");

		$this->conn->setData($this->data);
		$tmp = $this->conn->execCommand('create_ticket');
		return $tmp;
	}
	function CloseTicket($ticketid)
	{
		$this->data = Array('ticketid'=>"$ticketid");
		$this->conn->setData($this->data);
        	$this->conn->execCommand('close_current_ticket');
	}
	function UpdateComment($ticketid,$comments)
	{
        	$ownerid = $_SESSION["vt_id"];
        	$createdtime = date("Y-m-d H:i:s");

        	$this->data = array('ticketidid'=>$ticketid,
			'ownerid'=>$ownerid,
			'comments'=>$comments);
		$this->conn->setData($this->data);
        	$this->conn->execCommand('update_ticket_comment');
	}
	function ChangePassword($password,$newpasswd)
	{
		$res = $this->Authenticate($_SESSION["vt_user_name"],$_SESSION["vt_user_pass"]);
		if($res == $_SESSION["vt_id"]) {
        		$this->data = array('id'=>"$this->id",
				'username'=>$_SESSION["vt_user_name"],
				'password'=>$newpasswd);
			$this->conn->setData($this->data);
                	$res = $this->conn->execCommand('change_password');
		} else
			return 'error';
	}
	function GetKbaseDetails()
	{
		$this->conn->setData(array(''=>''));
        	return $this->conn->execCommand('get_KBase_details');
	}
	function ForgotPassword($email)
	{
		$this->conn->setData(array('email'=>"$email"));
        	$this->conn->execCommand('send_mail_for_password');
		return;
	}
}
?>
