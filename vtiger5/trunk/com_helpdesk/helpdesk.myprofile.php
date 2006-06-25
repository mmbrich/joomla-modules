<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

$err='';
if($_POST["change_pass"] == "true") {
	if($_POST["new_password"] == $_POST["confirm_password"] && $_POST["new_password"] != "") {
		$res = $user->ChangePassword($_POST["old_password"],$_POST["new_password"]);
		echo $res;
		if($res == "error")
			$err = "Please check your password and try again";
	} else
		$err = "Password do not match";
}

        $list .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';
        $list .= '<tr><td><div class="moduletable"><h3>My Details</h3></div></td></tr></table>';
        $list .= '<table border="0" cellspacing="4" cellpadding="2" style="margin-top:10px">';

        $list .= '<tr><td align="right" nowrap>Last Login : </td>';
        $list .= '<td><b>'.$_SESSION["vt_last_login_time"].'</b></td></tr>';
        $list .= '<tr><td align="right" nowrap>Support Start Date : </td>';
        $list .= '<td><b>'.$_SESSION["vt_support_start_date"].'</b></td></tr>';
        $list .= '<tr><td align="right" nowrap>Support End Date : </td>';
        $list .= '<td><b>'.$_SESSION["vt_support_end_date"].'</b></td></tr>';
        $list .= '</table>';

        $list .= '<br><br>';

        $list .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';
        $list .= '<tr><td><div class="moduletable"><h3>Change Password</h3></div></td></tr>';
        $list .= '<tr><td style="padding-top: 10px">';

        if($err != '')
                $list .= "<div style='margin-bottom:5px;background-color:yellow;width:50%;border:1px solid red'>".$err."</div>";
        $list .= '<form name="Submit" method="POST"> ';
	$list .= '<input type="hidden" name="change_pass" value="true" />';
        $list .= '<table border="0" cellspacing="2" cellpadding="2">';
        $list .= '<tr><td align="right">Old Password: </td>';
        $list .= '<td><input name="old_password" maxlength="255" type="password" value="" style="border:1px solid gray"></td></tr>';
        $list .= '<tr><td align="right">New Password: </td>';
        $list .= '<td><input name="new_password" maxlength="255" type="password" value="" style="border:1px solid gray"></td></tr>';
        $list .= '<tr><td align="right">Confirm Password: </td>';
        $list .= '<td><input name="confirm_password" maxlength="255" type="password" value="" style="border:1px solid gray"></td></tr>';
        $list .= '<tr><td></td><td><input type=submit class="button" name=savepassword onclick="this.savepassword.value=true" value="Update Password">&nbsp;&nbsp;</td>';
        $list .= '</table></form></table>';

        echo $list;
?>
