<?php
/**
* @version 1.1 $
* @package VtigerLead
* @copyright (C) 2005 Foss Labs <mmbrich@fosslabs.com>
*                2006 Pierre-Andr?ullioud www.paimages.ch
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
 
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
 
# Get the right language if it exists
if (file_exists($mosConfig_absolute_path.'/modules/vtiger/languages/lead_'.$mosConfig_lang.'.php')) {
    include($mosConfig_absolute_path.'/modules/vtiger/languages/lead_'.$mosConfig_lang.'.php');
} else {
    include($mosConfig_absolute_path.'/modules/vtiger/languages/lead_english.php');
}

#check if the bot exist
if (file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php'))
{
    require_once('modules/vtiger/VTigerLead.class.php');
    
    if($_REQUEST["Lead"]) {
      	$lead = new VtigerLead();
       	$result = $lead->addLead($_REQUEST["lastname"],$_REQUEST["email"],$_REQUEST["phone"],$_REQUEST["company"],$_REQUEST["country"],$_REQUEST["description"]);
       	echo _SUCCESS;
     }
     else
     {    
        ?>
        <form name="vtigerlead" method="POST" action="<?$PHP_SELF;?>">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody>
        <tr>
        <?php echo _LAST_NAME; ?>: <br/><input type="text" name="lastname" class="inputbox" size="15">
        <br />
        <?php echo _CONTACT_HEADER_EMAIL; ?>: <br /><input type="text" name="email" class="inputbox" size="15">
        <br />
        <?php echo _PHONE; ?>: <br /><input type="text" name="phone" class="inputbox" size="15">
        <br />
        <?php echo _COMPANY_NAME; ?>: <br /><input type="text" name="company" class="inputbox" size="15">
        <br />
        <?php echo _COUNTRY; ?>: <br /> <input type="text" name="country" class="inputbox" size="10">
        <br />
        <?php echo _DESCRIPTION; ?>: <br /><textarea rows="5" cols="18" name="description" class="inputbox"></textarea>
        <br />
        <input name="Lead" class="button" value="<?php echo _BUTTON_SUBMIT_MAIL;?>" type="submit">
        </tbody>
        </table>
        </form>
        <?
    }
    }
    else
    {
    echo _INSTALL_BOT;
    }
    ?>
    
