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
class VTigerContact {
	var $conn;
	var $data;
	var $username;
	var $password;
	var $id;
	var $customer_name;
	var $first_name;
	var $last_name;
	var $bday;
	

	function VtigerContact()
	{
		$this->conn = new VtigerConnection("contact");
	}
	function RegisterUser($firstname,$lastname,$email)
	{
		$this->firstname = $firstname;
		$this->lastname = $lastname;

		$this->data = array(	'firstname'=>$firstname,
					'lastname'=>$lastname,
					'email'=>$email
		);

		$this->conn->setData($this->data);
                $res = $this->conn->execCommand('create_basic_contact');
		$this->id=$res;
		return $res;
	}
	function SetField($fieldid,$value) 
	{
		$this->data = array(
			'entityid'=>$this->id,
			'fieldid'=>$fieldid,
			'value'=>$value
		);
		
                $this->conn->setData($this->data);
                $res = $this->conn->execCommand('set_field');
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
	function ForgotPassword($email)
	{
		$this->conn->setData(array('email'=>"$email"));
        	$this->conn->execCommand('send_mail_for_password');
		return;
	}
}
?>
