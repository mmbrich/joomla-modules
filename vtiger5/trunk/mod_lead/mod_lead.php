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
        <form name="vtigerlead" method="post" action="<?$PHP_SELF;?>">
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
        <td>
			<label for="mod_lead_lastname">
				<?php echo _LAST_NAME; ?>:
			</label>
			<br />
			<input name="lastname" id="mod_lead_lastname" type="text" class="inputbox" alt="lastname"  />
			<br />
			<label for="mod_lead_email">
				<?php echo _CONTACT_HEADER_EMAIL; ?>:
			</label>
			<br />
			<input name="email" id="mod_lead_email" type="text" class="inputbox" alt="email" />
			<br />
			<label for="mod_lead_phone">
				<?php echo _PHONE; ?>:
			</label>
			<br />
			<input name="phone" id="mod_lead_phone" type="text" class="inputbox" alt="phone" />
			<br />
			<label for="mod_lead_company">
				<?php echo _COMPANY_NAME; ?>:
			</label>
			<br />
			<input name="company" id="mod_lead_company" type="text" class="inputbox" alt="company" />
			<br />
			<label for="mod_lead_country">
				<?php echo _COUNTRY; ?>:
			</label>
			<br />
			<input name="country" id="mod_lead_country" type="text" class="inputbox" alt="country" />
			<br />
			<label for="mod_lead_description">
				<?php echo _DESCRIPTION; ?>:
			</label>
			<br />
			<textarea rows="7" cols="19" name="description" id="mod_lead_description" class="inputbox" ></textarea>
			<br />
			<label for="mod_lead_submit">
				<?php echo _BUTTON_SUBMIT_MAIL;?>:
			</label>
			<br />
			<input name="Lead" id="mod_lead_submit" type="submit" class="button" alt="submit" value="<?php echo _BUTTON_SUBMIT_MAIL;?>" />
			<br />
        </td>
        </tr>
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
    
