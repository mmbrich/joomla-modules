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

require_once("config.php");
require_once('include/logging.php');
require_once('include/nusoap/nusoap.php');
require_once('include/database/PearDatabase.php');
require_once('modules/Contacts/Contact.php');
require_once('include/utils/utils.php');

$log = &LoggerManager::getLogger('webforms');

//$serializer = new XML_Serializer();
$NAMESPACE = 'http://www.vtiger.com/vtigercrm/';
$server = new soap_server;

$server->configureWSDL('vtigersoap');

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

$server->register(
        'set_field',
        array(  'entityid'=>'xsd:string',
                'fieldid'=>'xsd:string',
                'value'=>'xsd:string'
        ),
        array('return'=>'xsd:string'),
        $NAMESPACE
);

$server->register(
        'is_allowed_helpdesk',
        array('contactid'=>'xsd:string'),
        array('return'=>'xsd:string'),
        $NAMESPACE);

function create_basic_contact($email,$firstname,$lastname,$password)
{
        global $adb;
        $adb->println("Enter into the function unsubscribe_email($emailid)");

        $focus = new Contact();
        $focus->column_fields["firstname"] = $firstname;
        $focus->column_fields["lastname"] = $lastname;
        $focus->column_fields["email"] = $email;
        $focus->save("Contacts");

	$q = "INSERT INTO vtiger_portalinfo (id,user_name,user_password,type,isactive) VALUES ('".$focus->id."','".$email."','".$password."','C','0')";
	$adb->query($q);

        $adb->println("Exit from the function ");
        return $focus->id;
}

function set_field($entityid,$fieldid,$value) {
        global $adb;
        $adb->println("\n\r\n\r\n\rEnter into the function set_field($entityid,$fieldid,$value)");

	$focus = new Contact();
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

function get_field($entityid,$fieldid) {

}

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


/* Begin the HTTP listener service and exit. */
$server->service($HTTP_RAW_POST_DATA);
exit();
?>

