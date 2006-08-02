<?php
/**
* @version $Id: toolbar.vtigerregistration.html.php 85 2006-07-10 23:12:03Z mmbrich $
* @package Joomla
* @subpackage Vtiger User Registration
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
        function register($fields=array(),$mainframe) {
        ?>
	<script language="JavaScript" src="<?php echo $mainframe->getCfg('live_site');?>/components/<?php echo _MYNAMEIS;?>/vtiger/prototype.js" type="text/javascript"></script>
		<script type="text/javascript">
		function submitbutton() {
			var els = document.getElementsByClassName("required");
			ret = true;
			var msg = "you must fill in all required fields";
			if($("reg_user").password.value != $("reg_user").password2.value) {
				$("reg_user").password.style.border = '1px solid red';
				$("reg_user").password2.style.border = '1px solid red';
				var msg = "your passwords do not match";
				ret = false;
			}
			for(i=0;i<els.length;i++) {
				els[i].style.border = '1px solid #cccccc';
				if(els[i].value == "" || els[i].value.toString == "undefined") {
					els[i].style.border = '1px solid red';
					ret = false;
				}
			}
			if(ret)
				$("reg_user").submit();
			else
				alert(msg);
		}
		</script>
		<form action="index.php" method="post" name="mosForm" id="reg_user">
		<div class="componentheading">User Registration:</div>

		<table class="contentpane" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody><tr>
			<td colspan="2">Fields marked with an asterisk (<font color="red">*</font>) are required.</td>
		</tr>
    		<tr>
                        <td width="30%">
				Username <font color='red'>*</font>
                        </td>
                        <td>
		  		<input name="username" size="20" value="" class="inputbox" maxlength="50" type="text">
                        </td>
                </tr>

    		<tr>
                        <td width="30%">
				Password <font color='red'>*</font>
                        </td>
                        <td>
		  		<input name="password" size="20" value="" class="inputbox" maxlength="50" type="password">
                        </td>
                </tr>

    		<tr>
                        <td width="30%">
				Confirm Password <font color='red'>*</font>
                        </td>
                        <td>
		  		<input name="password2" size="20" value="" class="inputbox" maxlength="50" type="password">
                        </td>
                </tr>

		<? foreach($fields as $field) { 
			if($field->show == '0' && $field->type != "2")
				continue;
		?>
		    <tr>
			<td width="30%">
				<?php echo $field->name;
					$ext = 'class="inputbox"';
				?>
				<?php if($field->required == "1" || $field->type == "2") {
					echo "<font color='red'>*</font>";
					$ext = 'class="required" style="padding: 2px;border:solid 1px #cccccc;background-color: #ffffff;"';
				} ?>
			</td>
		  	<td>
			    <?
                                switch($field->type) {
                                        case '55':
                                        case '51':
                                        case '2':
                                        case '5': // Date
                                        case '13': // Email
                                        case '1': // Text
                                        case '7': // number
                                        case '9': // percent
                                        case '71': // currency
                                        case '17': // URL
                                        case '11':
		  				echo '<input name="'.$field->field.'" size="'.$field->size.'" value="" '.$ext.' maxlength="50" type="text">';
                                       break;
                                       case '21':
                                       case '19':
		  				echo '<textarea name="'.$field->field.'" value="" '.$ext.' ></textarea>';
                                       break;
                                       case '69': // picture
		  				echo '<input name="'.$field->field.'" size="'.$field->size.'" value="" '.$ext.' maxlength="50" type="file">';
                                       break;
                                       case '56': // checkbox
		  				echo '<input name="'.$field->field.'" size="'.$field->size.'" value="" '.$ext.' maxlength="50" type="checkbox">';
                                       break;
                                       case '15': // Picklist
		  				echo '<select name="'.$field->field.'" >';
						foreach($field->values as $key=>$value) {
							echo '<option value="'.$value.'">'.$value.'</option>';
						}
		  				echo '</select>';
                                       break;
                                       case '33': // Multi Picklist
		  				echo '<select MULTIPLE name="'.$field->field.'[]" >';
						foreach($field->values as $key=>$value) {
							echo '<option value="'.$value.'">'.$value.'</option>';
						}
		  				echo '</select>';
                                       break;
                                       default:
                                                echo $field->uitype;
                                       break;
                                }
			    ?>
		  	</td>
		    </tr>
		<? } ?>
		<tr>
			  <td colspan="2">
			  </td>
		</tr>
		<tr>
			<td colspan="2">
			</td>

		</tr>

		</tbody></table>

		<input name="id" value="0" type="hidden">
		<input name="gid" value="0" type="hidden">
		<input name="useractivation" value="1" type="hidden">
		<input name="option" value="com_vtigerregistration" type="hidden">
		<input name="task" value="saveVtigerRegistration" type="hidden">
		<input value="Send Registration" class="button" onclick="submitbutton()" type="button">
		</form>
	<?
	}
}
?>