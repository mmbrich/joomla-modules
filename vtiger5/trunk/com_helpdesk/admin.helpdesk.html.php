<?php
/**
* @version $Id: toolbar.helpdesk.html.php 85 2006-07-10 23:12:03Z mmbrich $
* @package Joomla
* @subpackage Vtiger Help Desk
* @copyright Copyright (C) 2006 FOSS Labs. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_helpdesk {
	function about() {
	?>
                <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
                <table class="adminheading">
                <tr>
                        <th>
                        About VTiger Help Desk for Joomla!
                        </th>
                </tr>
		<tr>
			<td>
				The Vtiger Help Desk module for Joomla! was originally created by <a href="http://www.fosslabs.com">FOSS Labs</a><br>
				<br>Contributors:<br>
				Matthew Brichacek &lt;mmbrich@fosslabs.com&gt; (orignal code)<br>
				Pierre-Andre  Vullioud &lt;vtiger@paimages.ch&gt; (bot_vconnection)<br>
			</td>
		</tr>
		</table>
		</div>
		<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
	<?
	}

	function settings($option,$values) {
	?>
                <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
                <form action="<?PHP_SELF?>" method="post" name="adminForm">
                <table class="adminheading">
                <tr>
                        <th>
                        VTiger Help Desk Settings
                        </th>
                </tr>
                </table>

                <table class="adminform">
                <tr>
                        <th colspan='3'>
                        Options
                        </th>
                </tr>
                   <?php
                   foreach($values as $field) {
                    ?>
                     <tr>
                        <td width="50">
                                <?php if($field->type == "checkbox" || $field->type == "radio") {
                                        if($field->value == "on")
                                                $ext = "CHECKED";
                                        else
                                                $ext = "";
                                ?>
                                        <input type="<?php echo $field->type;?>" name="<?php echo $field->name;?>" value="on" <?php echo $ext;?> />
                                <? } else { ?>
                                        <input type="<?php echo $field->type;?>" name="<?php echo $field->name;?>" size="5" value="<?php echo $field->value;?>" <?php echo $ext;?> />
                                <? } ?>
                        </td>
                        <td align="left">
                                <?php echo $field->descr;?>
                        </td>
                     </tr>
                    <?
                   }
                   ?>
                </table>

                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <input type="hidden" name="name" value="Helpdesk" />
                <input type="hidden" name="admin_menu_link" value="option=com_helpdesk" />
                <input type="hidden" name="admin_menu_alt" value="Manage Help Desk Settings" />
                <input type="hidden" name="option" value="com_helpdesk" />
                <input type="hidden" name="admin_menu_img" value="js/ThemeOffice/component.png" />
                <input type="hidden" name="option" value="<?php echo $option; ?>" />
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />

		</form>
		</div>
		<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
	<?
	}
}
