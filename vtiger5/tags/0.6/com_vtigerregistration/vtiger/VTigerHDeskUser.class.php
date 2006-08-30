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
require_once($mainframe->getCfg('absolute_path').'/components/com_vtigerregistration/vtiger/VTigerContact.class.php');
class VTigerHDeskUser extends VtigerContact {
	var $data;
	var $id;
	var $jid;
	var $customer_name;
	var $combolist;

	function VtigerHDeskUser($jid='')
	{
		global $database;
		$this->jid = $jid;
		$q = "SELECT entityid FROM #__vtiger_portal_contacts "
			." WHERE #__vtiger_portal_contacts.contactid='".$this->jid."'";
		$database->setQuery( $q );
        	$this->id = $database->loadResult();
	}
        function IsAllowed()
        {
		return $this->IsAllowedHelpdesk();
        }
	function GetTicketComments($ticketid)
	{
		$this->data = array('ticketid'=> $ticketid);
		$this->setData($this->data);
		return $this->execCommand('get_ticket_comments');
	}	
	function ListTickets()
	{
		global $my;
		$this->data = array('user_name'=>$my->email,
				    'id'=>$this->id,
				    'where'=>'',
				    'match'=>'');
		$this->setData($this->data);
		$ret = $this->execCommand('get_tickets_list');
		if($ret)
			return $ret;
		else
			return;
	}
	function ListComboValues() {
		$this->data = array('id'=>$this->id);
		$this->setData($this->data);
		$this->combolist = $this->execCommand('get_combo_values');
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
                        'parent_id'=> $this->id,
                        'product_id'=>"");

		$this->setData($this->data);
		$tmp = $this->execCommand('create_ticket');
		return $tmp;
	}
	function CloseTicket($ticketid)
	{
		$this->data = Array('ticketid'=>"$ticketid");
		$this->setData($this->data);
        	$this->execCommand('close_current_ticket');
	}
	function UpdateComment($ticketid,$comments)
	{
        	$ownerid = $this->id;
        	$createdtime = date("Y-m-d H:i:s");

        	$this->data = array('ticketidid'=>$ticketid,
			'ownerid'=>$ownerid,
			'comments'=>$comments);
		$this->setData($this->data);
        	$this->execCommand('update_ticket_comment');
	}
	function GetKbaseDetails()
	{
		$this->setData(array(''=>''));
        	return $this->execCommand('get_KBase_details');
	}
	function SaveFaqComment($articleid,$comment)
	{
		global $my;
		if($my->id)
			$tcomment = "<div style=\"border-bottom:1px solid #c0c0c0\"><strong>Comment From:</strong> ".$my->name."</div>".$comment;
		else
			$tcomment = "<div style=\"border-bottom:1px solid #c0c0c0\"><strong>Comment From:</strong> ANONYMOUS</div>".$comment;

		$this->setData(array('articleid'=>$articleid, 'comment'=>$tcomment));
        	$ret = $this->execCommand('save_faq_comment');
		return;
	}
}
?>
