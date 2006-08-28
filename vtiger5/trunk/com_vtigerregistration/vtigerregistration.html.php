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
	function lostPassword() {
		?>
		<br /><br />
		<center>
		<form method="POST" action="index.php" name="lost_pass">
		<table border=0 width="65%" align="center">
		<tr><td> <?php echo _EMAIL_ADDY;?>:</td><td><input type='text' name='email' style='border:solid 1px gray' size='40'></td></tr>
		<tr><td></td><td><input type='submit' class='button' name='send_pass' value=' <?php echo _SEND_PASSWORD;?> '></td></tr>
		<input type="hidden" name="task" value="sendPassword" />
		<input type="hidden" name="option" value="com_vtigerregistration" />
		</table>
		</form>
		</center>
	<?
	}
	function changePass($id) {
	?>
		<script type="text/javascript">
		function validate_pass() {
			var np1 = document.getElementById("np").value;
			var np2 = document.getElementById("np2").value;

			if(np1 != np2) {
				alert("Your passwords do not match");
				return false;
			} else if(np1 == "" || np2 == "") {
				alert("Your may not use blank passwords");
				return false;
			} else
				return true;
		}
		</script>
		<br /><br />
		<center>
			<form method="POST" action="index.php" name="change_pass">
				<table border=0 width="100%" align="center">
					<tr>
						<td > 
							<?php echo _OLD_PWD;?>:
						</td>
						<td>
							<input type='password' name='oldpass' style='border:solid 1px gray' size='20'>
						</td>
					</tr>
					<tr>
						<td > 
							<?php echo _NEW_PWD;?>:
						</td>
						<td>
							<input type='password' name='newpass' id="np" style='border:solid 1px gray' size='20'>
						</td>
					</tr>
					<tr>
						<td > 
							<?php echo _NEW_PWD_CONF;?>:
						</td>
						<td>
							<input type='password' name='newpass2' id="np2" style='border:solid 1px gray' size='20'>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type='submit' class='button' name='send_pass' value=' <?php echo _SEND_PASSWORD;?> ' onclick='return validate_pass();'>
						</td>
					</tr>
					<input type="hidden" name="task" value="savePassword" />
					<input type="hidden" name="option" value="com_vtigerregistration" />
				</table>
			</form>
		</center>
	<?
	}
        function register($fields=array(),$vtField,$soid='',$Itemid='') {
		global $mainframe,$mosConfig_absolute_path;
		// Get the right language if it exists since this function gets called outside of registration at times
		if (file_exists($mosConfig_absolute_path.'/components/com_vtigerregistration/languages/vtigerregistration_'.$mosConfig_lang.'.php')) {
    			include($mosConfig_absolute_path.'/components/com_vtigerregistration/languages/vtigerregistration_'.$mosConfig_lang.'.php');
		} else {
    			include($mosConfig_absolute_path.'/components/com_vtigerregistration/languages/vtigerregistration_english.php');
		}
        ?>
	<script language="JavaScript" src="<?php echo $mainframe->getCfg('live_site').'/components/com_vtigerregistration';?>/vtiger/prototype.js" type="text/javascript"></script>
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
				try {
					el = els[i].childNodes[1];
					el.style.border = '1px solid #cccccc';
					if(el.value == "" || el.value.toString == "undefined") {
						el.style.border = '1px solid red';
						ret = false;
					}
				}catch(e){}
			}
			if(ret)
				$("reg_user").submit();
			else
				alert(msg);
		}
		</script>
                <br>
                <fieldset>
                <legend><span class="sectiontableheader"><?php echo _USER_REG;?></span></legend>
                <br>

		<form action="index.php" method="post" name="mosForm" id="reg_user">
		<table class="contentpane" border="0" cellpadding="0" cellspacing="0" width="100%">
		<tbody><tr>
			<td colspan="2"><?php echo _REQUIRED_FIELDS;?></td>
		</tr>
    		<tr>
                        <td width="30%">
			<?php echo _USERNAME;?> <font color='red'>*</font>
		</td>
		<td>
			<input name="username" size="20" value="" class="inputbox" maxlength="50" type="text">
		</td>
	</tr>

	<tr>
		<td width="30%">
			<?php echo _PASSWORD;?> <font color='red'>*</font>
                        </td>
                        <td>
		  		<input name="password" size="20" value="" class="inputbox" maxlength="50" type="password">
                        </td>
                </tr>

    		<tr>
                        <td width="30%">
				<?php echo _PWD_CONF;?> <font color='red'>*</font>
                        </td>
                        <td>
		  		<input name="password2" size="20" value="" class="inputbox" maxlength="50" type="password">
                        </td>
                </tr>

		<? 
		    foreach($fields as $field) { 
			if($field->show == '0' && $field->type != "2")
				continue;
		?>
		    <tr>
			<td width="30%">
				<?php echo $field->name;
					$ext = 'class=""';
				?>
				<?php if($field->required == "1" || $field->type == "2") {
					echo "<font color='red'>*</font>";
					$ext = 'class="required"';
				} ?>
			</td>
		  	<td <?php echo $ext;?>>
			    <?
				$f = array();
				$f["uitype"] = $field->type;
				$f["columnname"] = $field->field;
				$f["fieldlabel"] = $field->name;
				$f["value"] = '';
				$f["maximumlength"] = $field->size;
				$f["values"] = $field->values;
				echo $vtField->_buildEditField($f,'');
			    ?>
		  	</td>
		    </tr>
		<? } // END FOREACH ?>
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
		<input name="soid" value="<?php echo $soid;?>" type="hidden">
		<input name="Itemid" value="<?php echo $Itemid;?>" type="hidden">
		<input value="<?php echo _SEND_REG;?>" class="button" onclick="submitbutton()" type="button">
		</form>
                </fieldset>
	<?
	}
	function login($pretext,$posttext,$login,$soid='',$Itemid) {
		global $mosConfig_lang;
		$validate = josSpoofValue(1);
		if(mosGetParam( $_REQUEST, 'soid', '' ) != '') {
			$return = 'index.php?option=com_vtigersalesorders&task=checkout&soid='.$soid.'&Itemid='.$Itemid;
		}
	?>
                <fieldset>
                	<legend><span class="sectiontableheader"><?php echo _PLS_LOG_IN;?></span></legend>
                	<br>
        		<form action="index.php" method="post" name="login" >
        		<?php
        			echo $pretext;
        		?>

                        	<div style="width: 98%; text-align: center;">
                                	<div style="float: left; width: 30%; text-align: right;">
                                        	<label for="username_login"><?php echo _USERNAME;?>:</label>
                                	</div>
                                	<div style="float: left; margin-left: 2px; width: 60%; text-align: left;">
                                        	<input id="username_login" name="username" class="inputbox" size="20" type="text">
                                	</div>
                                	<br><br>
                                	<div style="float: left; width: 30%; text-align: right;">
                                        	<label for="passwd_login"><?php echo _PASSWORD;?>:</label>
                                	</div>
                                	<div style="float: left; margin-left: 2px; width: 30%; text-align: left;">
                                        	<input id="passwd_login" name="passwd" class="inputbox" size="20" type="password">
                                	</div><br><br>
                                	<center><div style="width: 30%; text-align: left;">
						<input type="submit" name="Submit" class="button" value="<?php echo _BUTTON_LOGIN; ?>" />
                                	</div></center>
				</div>
        			<?php
        				echo $posttext;
        			?>

        			<input type="hidden" name="option" value="login" />
        			<input type="hidden" name="lang" value="<?php echo $mosConfig_lang;?>" />
        			<input type="hidden" name="<?php echo $validate; ?>" value="1" />
			</form>
		</fieldset>
	<?
	}
}
?>
