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
class VTigerContact extends VTigerForm {
	var $data;
	var $id;
	var $jid;
	var $customer_name;
	var $username;
	var $file = "contact";
	

	function VtigerContact($jid='')
	{
		VTigerConnection::VtigerConnection();
		if($jid != '') {
			$this->jid=$jid;
			$this->LoadUser();
		}
	}
	function LoadUser()
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
	function CreateAccount($account_name)
	{
                $this->data = array(
			'account_name'=> $account_name,
			'contactid'=> $this->id
		);
                $this->setData($this->data);
                return $this->execCommand('create_account');
	}
	function AssociateUserToContact()
	{
		if($this->id) {
			global $database;
			$q = "INSERT INTO #__vtiger_portal_contacts (contactid,entityid) VALUES ('".$this->jid."','".$this->id."')";
        		$database->setQuery( $q );
        		$database->query() or die( $database->stderr() );
		}
	}
	function RegisterUser($email,$firstname,$lastname,$password)
	{
		$this->firstname = $firstname;
		$this->lastname = $lastname;

		$this->data = array(	
					'email'=>$email,
					'firstname'=>$firstname,
					'lastname'=>$lastname,
					'password'=>$password
		);
		$this->setData($this->data);
                $res = $this->execCommand('create_basic_contact');
		$this->id=$res;

		$this->AssociateUserToContact();

		return $res;
	}
	function CheckUser($email) 
	{
		$this->data = array(
			'email'=>$email
		);
                $this->setData($this->data);
                $this->id = $this->execCommand('check_user');
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
		echo $res;
	}
	function ChangePassword($newpasswd)
	{
		global $database, $acl, $basePath,$mainframe;
		if($newpasswd != "") { 
			$this->setData(array(
				'newpass'=>$newpasswd,
				'id'=>$this->id
			));
        		$this->execCommand('update_password');
			return $this->_change_password($newpasswd);
		} else {
                        echo "<script> alert(\""._PASS_MATCH."\"); window.history.go(-1); </script>\n";
                        exit();
		}
	}
	function _change_password($newpass)
	{
        	global $database, $my, $mosConfig_frontend_userparams;
        	$user_id = $my->id;

        	$row = new mosUser( $database );
        	$row->load( $user_id );

        	$orig_password = $row->password;
        	$orig_username = $row->username;

        	mosMakeHtmlSafe($newpass);
        	mosMakeHtmlSafe($row);

                $row->password = md5( $newpass );

        	if (!$row->check()) {
                	echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
                	exit();
        	}

        	if (!$row->store()) {
                	echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
                	exit();
        	}
		return;
	}
	function InsertPortalData($username, $password)
	{
		$this->data = array(
			'username'=>$username,
			'password'=>$password,
			'entityid'=>$this->id
		);
                $this->setData($this->data);
                return $this->execCommand('insert_portal_data');
	}
	function ForgotPassword($email)
	{
		$this->setData(array(
			'email'=>"$email"
		));
        	$ret = $this->execCommand('forgot_password');
		if($ret != '1') {
			echo $ret;
			exit();
		}
	}
        function RelateToEvent($eventid)
	{
		$this->data = array(
                        'eventid'=>$eventid,
                        'contactid'=>$this->id
                );
		$this->setData($this->data);
        	$ret = $this->execCommand('relate_to_event');
		return;
	}
        function RelateToPotential($potentialid)
	{
		$this->setData(array(
			'potentialid'=>$potentialid,
			'contactid'=>$this->id
		));
        	return $this->execCommand('relate_to_potential');
	}
        function RelateToCampaign($campaignid)
	{
		$this->setData(array(
			'campaignid'=>$campaignid,
			'contactid'=>$this->id
		));
        	return $this->execCommand('relate_to_campaign');
	}
        function RelateToAccount($accountid)
	{
		$this->setData(array(
			'accountid'=>$accountid,
			'contactid'=>$this->id
		));
        	return $this->execCommand('relate_to_account');
	}
}
?>
