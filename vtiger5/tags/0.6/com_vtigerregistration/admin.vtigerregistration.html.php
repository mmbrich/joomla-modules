<?php
/**
* @version $Id: toolbar.vtigerregistration.html.php 85 2006-07-10 23:12:03Z mmbrich $
* @package Joomla
* @subpackage Vtiger Registartion
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

class HTML_vtigerregistration {
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

        function settings($option,$fields,$values) {
		global $mainframe;
        ?>
		<script language="JavaScript" src="<?php echo $mainframe->getCfg('live_site');?>/components/<?php echo _MYNAMEIS;?>/vtiger/prototype.js" type="text/javascript"></script>
		<script type="text/javascript">
		function change_order(id,type,order) {
			var order = parseInt(order);
			var ord = $(order.toString());
			if(type == 'up') {
				order--;
				$(order.toString()).value = (order+1);
				ord.value = (order);
			} else {
				order++;
				$(order.toString()).value = (order-1);
				ord.value = (order);
			}
			$("task").value = 'save';
			$("vtiger_fields").submit();
		}
		function enable_field(fieldid) {
			$("added_"+fieldid).value = "on";
			$("task").value = 'save';
			$("vtiger_fields").submit();
		}
		function disable_field(fieldid) {
			$("added_"+fieldid).value = "";
			undo_require(fieldid);
		}
		function undo_require(fieldid) {
			$("required_"+parseInt(fieldid)).value = "off";
			$("task").value = 'save';
			$("vtiger_fields").submit();
		}
		function do_require(fieldid) {
			$("required_"+parseInt(fieldid)).value = "on";
			$("task").value = 'save';
			$("vtiger_fields").submit();
		}
		</script>
                <div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
                <table class="adminheading">
                <tr>
                        <th>
                        VTiger User Maintenance
                        </th>
                </tr>
                </table>
                <form action="<?PHP_SELF;?>" method="post" name="adminForm" id="vtiger_fields">
                <table class="adminform">
		<tr>
			<td><input type="button" name="syncContacts" value="Syncronize" onclick="window.location.href = window.location.href+'&task=syncContacts';return false;"></td>
			<td>Syncronize Vtiger Contacts and Joomla Users.  This will first remove any stale relationships (from deleted joomla users) and then it will create CRM contacts for any users that are missing in the CRM.</td>
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
		<br>

                <table class="adminheading">
                <tr>
                        <th>
                        VTiger User Registration Settings
                        </th>
                </tr>
                </table>

		<br>
                <table class="adminform">
		<tr>
			<th>Field Name</th><th>Enable</th><th>Required</th><th>Order</th><th>Field Type</th></tr>
		</tr>
		<?
			$cnt = count($fields);
			$i=0;
			foreach($fields as $allowed_fields) {
				echo "<tr>";
			  	echo "<td align='right'>";
					echo "<input type='text' name='jname_".$allowed_fields["id"]."' value='".$allowed_fields["name"]."' />";
			  	echo "</td>";
			  	echo "<td align='left' width='100px'>";
					if($allowed_fields["type"] == "2" 
						|| $allowed_fields["field"] == "firstname" 
						|| $allowed_fields["field"] == "email") {
						echo '<img src="images/tick.png" alt="Enable" border="0" height="12" width="12">';
						echo '<input type="hidden" name="add_'.$allowed_fields["id"].'" value="on" id="added_'.$allowed_fields["id"].'"/>';
					} elseif($allowed_fields["show"] == "1") {
						echo '<a href="#disable" onclick="disable_field(\''.$allowed_fields["id"].'\');" title="Move Up">';
						echo '<img src="images/tick.png" alt="Disable" border="0" height="12" width="12">';
						echo '</a> &nbsp; &nbsp; &nbsp;';
						echo '<input type="hidden" name="add_'.$allowed_fields["id"].'" value="on" id="added_'.$allowed_fields["id"].'"/>';
					} else {
						echo '<a href="#reorder" onclick="enable_field(\''.$allowed_fields["id"].'\');" title="Move Up">';
						echo '<img src="images/publish_x.png" alt="Enable" border="0" height="12" width="12">';
						echo '</a> &nbsp; &nbsp; &nbsp;';
						echo '<input type="hidden" name="add_'.$allowed_fields["id"].'" value="" id="added_'.$allowed_fields["id"].'"/>';
					}
			  	echo "</td>";
			  	echo "<td align='left' width='100px'>";
					if($allowed_fields["type"] == "2" 
						|| $allowed_fields["field"] == "firstname" 
						|| $allowed_fields["field"] == "email") {
						echo '<img src="images/tick.png" alt="Enable" border="0" height="12" width="12">';
						echo '<input type="hidden" name="require_'.$allowed_fields["id"].'" value="on" id="required_'.$allowed_fields["id"].'" />';
					} elseif($allowed_fields["required"] == "1") {
						echo '<a href="#norequire" onclick="undo_require(\''.$allowed_fields["id"].'\');" title="Move Up">';
						echo '<img src="images/tick.png" alt="Undo Require" border="0" height="12" width="12">';
						echo '</a> &nbsp; &nbsp; &nbsp;';
						echo '<input type="hidden" name="require_'.$allowed_fields["id"].'" value="on" id="required_'.$allowed_fields["id"].'"/>';
					} else {
						echo '<a href="#reorder" onclick="do_require(\''.$allowed_fields["id"].'\');" title="Move Up">';
						echo '<img src="images/publish_x.png" alt="Required" border="0" height="12" width="12">';
						echo '</a> &nbsp; &nbsp; &nbsp;';
						echo '<input type="hidden" name="require_'.$allowed_fields["id"].'" value="" id="required_'.$allowed_fields["id"].'"/>';
					}
			  	echo "</td>";
				echo '<td>';
					if($i != "0") {
						echo '<a href="#reorder" onclick="change_order(\''.$allowed_fields["id"].'\',\'up\',\''.$i.'\');" title="Move Up">';
						echo '<img src="images/uparrow.png" alt="Move Up" border="0" height="12" width="12">';
						echo '</a> &nbsp; &nbsp; &nbsp;';
					} else {
						echo '<img src="images/uparrow.png" alt="Move Up" border="0" height="12" width="12" style="visibility:hidden"> &nbsp; &nbsp; &nbsp;';
					}
					if(($i+1) != $cnt) {
						echo '<a href="#reorder" onclick="change_order(\''.$allowed_fields["id"].'\',\'down\',\''.$i.'\');" title="Move Down">';
						echo '<img src="images/downarrow.png" alt="Move Down" border="0" height="12" width="12">';
						echo '</a>';
					}
					echo "<input type='hidden' name='order_".$allowed_fields["id"]."' value='".$i."' id='".$i."' />";
					echo "<input type='hidden' name='columnname_".$allowed_fields["id"]."' value='".$allowed_fields["field"]."' />";
					echo "<input type='hidden' name='fieldlabel_".$allowed_fields["id"]."' value='".$allowed_fields["name"]."' />";
					echo "<input type='hidden' name='size_".$allowed_fields["id"]."' value='".$allowed_fields["size"]."' />";
			  	echo "</td>";
			  	echo "<td align='right'>";
					switch($allowed_fields["type"]) {
						case '55':
							echo "Text/Number";
						break;
						case '51':
							echo "Text/Number -- CRM Relationship";
						break;
						case '7':
							echo "Number";
						break;
						case '11':
							echo "Phone Number";
						break;
						case '17':
							echo "URL";
						break;
						case '56':
							echo "Checkbox";
						break;
						case '9':
							echo "Percent";
						break;
						case '71':
							echo "Currency";
						break;
						case '2':
							echo "Required Text/Number";
						break;
						case '5':
							echo "Date";
						break;
						case '13':
							echo "Email";
						break;
						case '21':
						case '19':
							echo "Text Area";
						break;
						case '69':
							echo "Image Upload";
						break;
						case '1':
							echo "Text";
						break;
						case '15':
							echo "Picklist";
						break;
						case '33':
							echo "Multi-Select Box";
						break;

						default:
							echo $allowed_fields["type"];
						break;
						
					}
					echo "<input type='hidden' name='uitype_".$allowed_fields["id"]."' value='".$allowed_fields["type"]."' />";
			  	echo "</td>";
				echo "</tr>";
				$field_t .= $allowed_fields["id"].',';
			$i++;
			}
			echo "<input type='hidden' name='fields' value='".$field_t."' />";
		?>
                </table>

                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <input type="hidden" name="name" value="User Registration" />
                <input type="hidden" name="admin_menu_link" value="option=com_vtigerregistration" />
                <input type="hidden" name="admin_menu_alt" value="Manage User Registration Settings" />
                <input type="hidden" name="option" value="com_vtigerregistration" />
                <input type="hidden" name="admin_menu_img" value="js/ThemeOffice/component.png" />
                <input type="hidden" name="option" value="<?php echo $option; ?>" />
                <input type="hidden" name="task" value="" id="task" />
                <input type="hidden" name="boxchecked" value="0" />

                </form>
                </div>
                <script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
        <?
        }
}
?>
