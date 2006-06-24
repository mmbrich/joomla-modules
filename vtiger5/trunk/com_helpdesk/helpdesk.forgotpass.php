<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

if(isset($_POST["send_pass"])) {
	$user->ForgotPassword($_POST["email"]);
}

?>
<br /><br />
<center>
<form method="POST">
<table border=0 width="65%" align="center">
<tr><td> Email Address:</td><td><input type='text' name='email' style='border:solid 1px gray' size='40'></td></tr>
<tr><td></td><td><input type='submit' class='button' name='send_pass' value=' Send Password '></td></tr>

</table>
</form>
</center>
