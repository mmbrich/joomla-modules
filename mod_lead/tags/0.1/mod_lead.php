<?php
/**
* @version 1.0 $
* @package VtigerLead
* @copyright (C) 2005 netXccel http://www.netXccel.com/
* @author Matthew Brichacek <mmbrich@fosslabs.com>
* @license http://www.vtiger.com VPL
*/
 
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
 
require_once('modules/vt_classes/VTigerLead.class.php');

if($_REQUEST["Lead"]) {
	$lead = new VtigerLead($params->get( 'vtiger_lead_soapserver', '' ));

	$result = $lead->addLead($_REQUEST["lastname"],$_REQUEST["email"],$_REQUEST["phone"],$_REQUEST["company"],$_REQUEST["country"],$_REQUEST["description"]);
}
?>
<form name="vtigerlead" method="POST" action="<?$PHP_SELF;?>">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
<tr>
Last Name: <br/><input type="text" name="lastname" class="inputbox" size="15">
<br />
Email: <br /><input type="text" name="email" class="inputbox" size="15">
<br />
Phone: <br /><input type="text" name="phone" class="inputbox" size="15">
<br />
Company Name: <br /><input type="text" name="company" class="inputbox" size="15">
<br />
Country: <br /> <input type="text" name="country" class="inputbox" size="10">
<br />
Description: <br /><textarea rows="2" cols="18" name="description" class="inputbox"></textarea>
<br />
<input name="Lead" class="button" value="Submit" type="submit">
</tbody>
<table>
</form>
