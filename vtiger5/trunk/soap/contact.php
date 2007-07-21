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

/************************ CHECK_USER  START ****************************/
$server->register(
        'check_user',
        array('email'=>'xsd:string'),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function check_user($email) {
	global $adb;

	$q = "SELECT contactid FROM vtiger_contactdetails WHERE email = '".$email."'";
	$rs = $adb->query($q);

	if($adb->num_rows($rs) <= 0) {
		return 0;
	} else
		return $adb->query_result($rs,'0','contactid');
}
/************************ CHECK_USER  END ****************************/

/************************ CREATE_BASIC_CONTACT START ****************************/
$server->register(
        'create_basic_contact',
        array(  'email'=>'xsd:string',
                'firstname'=>'xsd:string',
                'lastname'=>'xsd:string',
                'password'=>'xsd:string'
        ),
        array('return'=>'xsd:string'),
        $NAMESPACE
);
function create_basic_contact($email,$firstname,$lastname,$password)
{
	require_once("modules/Contacts/Contacts.php");
        global $adb;
        $adb->println("Enter into the function create_basic_contact($email,$firstname,$lastname,$password)");

	$current_user = inherit_user();

        $focus = new Contacts();
        $focus->column_fields["firstname"] = $firstname;
        $focus->column_fields["lastname"] = $lastname;
        $focus->column_fields["email"] = $email;
        $focus->column_fields["assigned_user_id"] = $current_user->id;
        $focus->save("Contacts");

	$q = "INSERT INTO vtiger_portalinfo (id,user_name,user_password,type,isactive) VALUES ('".$focus->id."','".$email."','".$password."','C','0')";
	$adb->query($q);

        $adb->println("Exit from the function ");
        return $focus->id;
}
/************************ CREATE_BASIC_CONTACT END ****************************/

/************************ SET_FIELD START ****************************/
$server->register(
        'set_field',
        array(  'entityid'=>'xsd:string',
                'fieldid'=>'xsd:string',
                'value'=>'xsd:string'
        ),
        array('return'=>'xsd:string'),
        $NAMESPACE
);
function set_field($entityid,$fieldid,$value) {
        global $adb;
        $adb->println("\n\r\n\r\n\rEnter into the function set_field($entityid,$fieldid,$value)");
	$current_user = inherit_user($entityid);

	$focus = new Contacts();
	$rs = $adb->query("SELECT fieldname,tablename,columnname FROM vtiger_field WHERE fieldid='".$fieldid."'");
	$fieldname = $adb->query_result($rs,'0','fieldname');
	$tablename = $adb->query_result($rs,'0','tablename');
	$columnname = $adb->query_result($rs,'0','columnname');

	if($tablename == "vtiger_contactdetails" || $tablename == "vtiger_contactscf")
		$q = "UPDATE ".$tablename." SET ".$columnname."=".$adb->Quote($value)." WHERE contactid='".$entityid."'";
	elseif($tablename == "vtiger_crmentity")
		$q = "UPDATE ".$tablename." SET ".$columnname."=".$adb->Quote($value)." WHERE crmid='".$entityid."'";
	elseif($tablename == "vtiger_contactaddress")
		$q = "UPDATE ".$tablename." SET ".$columnname."=".$adb->Quote($value)." WHERE contactaddressid='".$entityid."'";
	elseif($tablename == "vtiger_contactsubdetails")
		$q = "UPDATE ".$tablename." SET ".$columnname."=".$adb->Quote($value)." WHERE contactsubscriptionid='".$entityid."'";

	$adb->query($q);

        $adb->println("Exit set_field $q \n\r\n\r\n\r");
	return $entityid;
}
/************************ SET_FIELD END ****************************/

/************************ IS_ALLOWED_HELPDESK START ****************************/
$server->register(
        'is_allowed_helpdesk',
        array('contactid'=>'xsd:string'),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function is_allowed_helpdesk($contactid) {
        global $adb;
        $current_date = date("Y-m-d");
        $q = "select id, support_start_date, support_end_date from vtiger_portalinfo inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_portalinfo.id where vtiger_crmentity.deleted=0 and vtiger_portalinfo.id='".$contactid."' and isactive=1 and vtiger_customerdetails.support_end_date >= '".$current_date."'";
        $rs = $adb->query($q);

        if($adb->num_rows($rs) > 0)
                return true;
        else
                return false;
}
/************************ IS_ALLOWED_HELPDESK END ****************************/

/************************ CREATE_ACCOUNT START ****************************/
$server->register(
        'create_account',
        array(
		'account_name'=>'xsd:string',
		'contactid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function create_account($account_name,$contactid) {
        global $adb;
        $current_date = date("Y-m-d");
	$current_user = inherit_user($contactid);

	$account=create_entity("Accounts",'');
	$account->column_fields["accountname"] = $account_name;
	$account->column_fields["assigned_user_id"] = '1';
	$account->column_fields["description"] = 'Created by joomla on '.$current_date;
        $account->column_fields["assigned_user_id"] = $current_user->id;
	$account->save("Accounts");

	$q = "UPDATE vtiger_contactdetails SET accountid='".$account->id."' where contactid='".$contactid."'";
	$tmp = $adb->query($q);

	return $account->id;
}
/************************ CREATE_ACCOUNT END ****************************/

/************************ RELATE_TO_EVENT START ****************************/
$server->register(
        'relate_to_event',
        array(
		'eventid'=>'xsd:string',
		'contactid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE
);

function relate_to_event($eventid,$contactid) {
	global $adb;
        $adb->println("\n\r\n\r\n\rEnter into the function relate_to_event($eventid,$contactid)\n\r\n\r\n\r");
	
	$adb->query("DELETE FROM vtiger_cntactivityrel WHERE contactid='".$contactid."' AND activityid='".$eventid."'");
	$adb->query("INSERT INTO vtiger_cntactivityrel VALUES('".$contactid."' ,'".$eventid."')");
	return 'true';
}
/************************ RELATE_TO_EVENT END ****************************/

/************************ RELATE_TO_POTENTIAL START ****************************/
$server->register(
        'relate_to_potential',
        array(
		'potentialid'=>'xsd:string',
		'contactid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function relate_to_potential($potentialid,$contactid) {
	global $adb;
        $adb->println("Enter into the relate_to_potential($potentialid,$contactid)");

	$ret = $adb->query("DELETE FROM vtiger_contpotentialrel WHERE potentialid='".$potentialid."' AND contactid='".$contactid."'");
	$ret = $adb->query("INSERT INTO vtiger_contpotentialrel VALUES('".$contactid."' ,'".$potentialid."')" );
	return;
}
/************************ RELATE_TO_POTENTIAL END ****************************/

/************************ RELATE_TO_CAMPAIGN START ****************************/
$server->register(
        'relate_to_campaign',
        array(
		'campaignid'=>'xsd:string',
		'contactid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function relate_to_campaign($campaignid,$contactid) {
	global $adb;
        $adb->println("Enter into the relate_to_campaign($campaignid,$contactid)");

	$ret = $adb->query("DELETE FROM vtiger_campaigncontrel WHERE campaignid='".$campaignid."' AND contactid='".$contactid."'");
	$ret = $adb->query( "INSERT INTO vtiger_campaigncontrel VALUES ('".$campaignid."', '".$contactid."')" );
	return;
}
/************************ RELATE_TO_CAMPAIGN END ****************************/

/************************ RELATE_TO_ACCOUNT START ****************************/
$server->register(
        'relate_to_account',
        array(
		'accountid'=>'xsd:string',
		'contactid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function relate_to_account($accountid,$contactid) {
	global $adb;
        $adb->println("Enter into the relate_to_account($accountid,$contactid)");

	$ret = $adb->query("UPDATE vtiger_contactdetails SET accountid='".$accountid."' WHERE contactid='".$contactid."'");
	return;
}
/************************ RELATE_TO_CAMPAIGN END ****************************/

/************************ INSERT_PORTAL_DATA START *************************/
$server->register(
        'insert_portal_data',
        array(
		'username'=>'xsd:string',
		'password'=>'xsd:string',
		'contactid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function insert_portal_data($username,$password,$contactid) {
	global $adb;
        $adb->println("Enter into the function insert_portal_data($username,$password,$contactid)");

	$q = "INSERT INTO vtiger_portalinfo (id,user_name,user_password,type,isactive) VALUES ('".$contactid."','".$username."','".$password."','C','0')";
	$adb->query($q);

	return $contactid;
}
/************************ INSERT_PORTAL_DATA END *************************/

/************************ UPDATE_PASSWORD START ****************************/
$server->register(
        'update_password',
        array(
		'newpass'=>'xsd:string',
		'contactid'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function update_password($newpass,$contactid) {
	global $adb;
        $adb->println("Enter into the update_password($accountid,$contactid)");

	$ret = $adb->query("UPDATE vtiger_portalinfo SET user_password='".$newpass."' WHERE id='".$contactid."'");
	return '1';
}
/************************ UPDATE_PASSWORD END ****************************/

/************************ FORGOT_PASSWORD START ****************************/
$server->register(
        'forgot_password',
        array(
		'email'=>'xsd:string'
	),
        array('return'=>'xsd:string'),
        $NAMESPACE);
function forgot_password ($email) { 
        global $adb;
	$current_user = inherit_user();

        $sql = "SELECT * FROM vtiger_contactdetails"
		." INNER JOIN vtiger_portalinfo "
		." ON vtiger_contactdetails.contactid=vtiger_portalinfo.id "
		." INNER JOIN vtiger_crmentity "
		." ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid "
		." WHERE email='".$email."'"
		." AND vtiger_crmentity.deleted='0'";

	$rs = $adb->query($sql);
        $firstname = $adb->query_result($rs,0,'firstname');
        $lastname = $adb->query_result($rs,0,'lastname');
        $user_name = $adb->query_result($rs,0,'user_name');
        $email = $adb->query_result($rs,0,'email');
        $password = $adb->query_result($rs,0,'user_password');


        $fromquery = "select vtiger_users.first_name, vtiger_users.last_name, vtiger_users.email1 from vtiger_users inner join vtiger_crmentity on vtiger_users.id = vtiger_crmentity.smownerid inner join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_crmentity.crmid where vtiger_contactdetails.email ='".$email."'";
	$rs = $adb->query($fromquery);

        $initialfrom = $adb->query_result($rs,0,'first_name')." ".$adb->query_result($rs,0,'last_name');
        $from = $adb->query_result($rs,0,'email1');

        $contents = "Dear ".$firstname." ".$lastname.",";
	$contents .= "<br><br>Someone (presumably you) requested login details<br> ";
	$contents .= "Your login details follow:";
        $contents .= "<br><br><b>User Name</b> : ".$user_name;
        $contents .= "<br><b>Password</b> : ".$password;

	require_once('modules/Emails/mail.php');
	$mail = new PHPMailer();
        $mail->Subject = "Your Login Details";
        $mail->Body    = $contents;
        $mail->IsSMTP();

        $mailserverresult = $adb->query("select * from vtiger_systems where server_type='email'");
        $mail_server = $adb->query_result($mailserverresult,0,'server');
        $mail_server_username = $adb->query_result($mailserverresult,0,'server_username');
        $mail_server_password = $adb->query_result($mailserverresult,0,'server_password');
        $smtp_auth = $adb->query_result($mailserverresult,0,'smtp_auth');

        $adb->println("MAIL SERVER: $mail_server");
        $adb->println("MAIL SERVER USER: $mail_server_username");
        $adb->println("MAIL SERVER AUTH: $smtp_auth");

        $mail->Host = $mail_server;
        $mail->SMTPAuth = $smtp_auth;
        $mail->Username = $mail_server_username;
        $mail->Password = $mail_server_password;
        $mail->From = $from;
        $mail->FromName = $initialfrom;
        $mail->AddAddress($email);
        $mail->AddReplyTo($current_user->name);
        $mail->WordWrap = 50;

        $mail->IsHTML(true);

        $mail->AltBody = strip_tags($contents);

	if($mail->Send())
        	return '1';
	else
		return $mail->ErrorInfo;
}
/************************ FORGOT_PASSWORD END ****************************/
?>
