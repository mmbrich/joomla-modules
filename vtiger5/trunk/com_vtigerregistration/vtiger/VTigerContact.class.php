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
class VTigerContact extends VtigerConnection {
	var $data;
	var $id;
	var $jid;
	var $customer_name;
	var $username;
	

	function VtigerContact($jid='')
	{
		$this->conn = $this->VtigerConnection("contact");
		if($jid != '') {
			$this->jid=$jid;
			$this->LoadUser();
		}
	}
	private function LoadUser()
	{
                global $database;
                $q = "SELECT * FROM #__vtiger_portal_contacts "
			." INNER JOIN #__users ON #__users.id = #__vtiger_portal_contacts.contactid "
                        ." WHERE #__vtiger_portal_contacts.contactid='".$this->jid."'";
                $database->setQuery( $q );
		$res = $database->loadObjectList();

                $this->id = $res[0]->entityid;
                $this->jid = $res[0]->id;
                $this->customer_name = $res[0]->name;
                $this->username = $res[0]->username;
	}
        function IsAllowedHelpdesk()
        {
                $this->data = array('contactid'=> $this->id);
                $this->setData($this->data);
                return $this->execCommand('is_allowed_helpdesk');
        }
	function RegisterUser($firstname,$lastname,$email,$password,$jid)
	{
		$this->firstname = $firstname;
		$this->lastname = $lastname;

		$this->data = array(	'firstname'=>$firstname,
					'lastname'=>$lastname,
					'email'=>$email,
					'password'=>$password
		);

		$this->setData($this->data);
                $res = $this->execCommand('create_basic_contact');
		$this->id=$res;

		if($this->id > 0) {
			global $database;
			$q = "INSERT INTO #__vtiger_portal_contacts (contactid,entityid) VALUES ('".$jid."','".$this->id."')";
        		$database->setQuery( $q );
        		$database->query() or die( $database->stderr() );
		}

		return $res;
	}
	function CheckAndCreate($email) 
	{
		$this->data = array(
			'email'=>$email
		);
                $this->setData($this->data);
                $this->id = $this->execCommand('check_and_create');
		return $this->id;
	}
	function SetField($fieldid,$value) 
	{
		$this->data = array(
			'entityid'=>$this->id,
			'fieldid'=>$fieldid,
			'value'=>$value
		);
		
                $this->setData($this->data);
                $res = $this->execCommand('set_field');
	}
	function ChangePassword($password,$newpasswd)
	{
		$res = $this->Authenticate($_SESSION["vt_user_name"],$_SESSION["vt_user_pass"]);
		if($res == $_SESSION["vt_id"]) {
        		$this->data = array('id'=>"$this->id",
				'username'=>$_SESSION["vt_user_name"],
				'password'=>$newpasswd);
			$this->setData($this->data);
                	$res = $this->execCommand('change_password');
		} else
			return 'error';
	}
	function ForgotPassword($email)
	{
		$this->conn = $this->VtigerConnection("customerportal");
		$this->setData(array('email'=>"$email"));
        	$this->execCommand('send_mail_for_password');
		$this->conn = $this->VtigerConnection("contact");
		return;
	}
}
?>
