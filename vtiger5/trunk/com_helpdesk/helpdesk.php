<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

#check if the bot exist
if (file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php')) {
	require_once('components/com_helpdesk/vtiger/VTigerHDeskUser.class.php');
	$user = new VtigerHDeskUser();
} else {
	echo "You should install bot_vconnection if you want something to happen here ;)";
	flush();exit();
}

if($_REQUEST["logout"] == "true") {
	$user->LogOut();
}

$head = "<div style='width:100%;height:17px'>";
$head .= "<span style='float:left'><a href='index.php?option=com_helpdesk'>Ticket Home</a></span>";
$head .= "<span style='float:left'> &nbsp;|&nbsp; <a href='index.php?option=com_helpdesk&newticket=true'>New Ticket</a></span>";
$head .= "<span style='float:left'> &nbsp;|&nbsp; <a href='index.php?option=com_helpdesk&myprofile=true'>My Profile</a></span>";
$head .= "<span style='float:left'> &nbsp;|&nbsp; <a href='index.php?option=com_helpdesk&kbase=true'>Knowledge Base</a></span>";

if($_GET["forgotpass"] == "true") {
	include_once("helpdesk.forgotpass.php");
	return;
}

if($_SESSION["vt_authenticated"] != "true" || !isset($_SESSION["vt_authenticated"])) {
	if(isset($_POST["username"]))
		$auth = $user->Authenticate($_POST['username'],$_POST['password']);
	else
		$auth = $user->Authenticate($_SESSION['vt_user_name'],$_SESSION['vt_user_pass']);

	if($_SESSION["vt_authenticated"] != "true" || !isset($_SESSION["vt_authenticated"])) {
		echo "<br /><br /><div style='text-align:center'>Please log-in to access the Customer Support Portal</div>";
		echo "<center><form name='login' method='POST'>";
		echo "<table border=0 align='center'>";
		echo "<tr><td>Username:</td><td><input type='text' name='username' style='border:1px solid gray'></td></tr>";
		echo "<tr><td>Password:</td><td><input type='password' name='password' style='border:1px solid gray'></td></tr>";
		echo "<tr><td colspan='2'><input type='submit' class='button' name='signin' value='Log In'></td><tr>";
		echo "<tr><td colspan='2'>&nbsp;</td><tr>";
		echo "<tr><td>&nbsp;</td><td align='center' style='text-align:center'><a href='index.php?option=com_helpdesk&forgotpass=true'>Forgot Password?</a></td><tr>";
		echo "</table>";
		echo "</form></center>";
		return;
	}
}

if($_SESSION["vt_authenticated"]) {
	$head .= "<span style='float:right'>".$_SESSION["vt_user_name"]."&nbsp;<a href='index.php?option=com_helpdesk&logout=true'>[Logout]</a></span>";
}
$head .= "</div>";
echo $head."<br /><br />";

if($_POST['updatecomment'] == 'true')
{
	$user->UpdateComment($_POST["ticketid"],$_POST["comments"]);
}

if($_GET["newticket"] == "true") {
	if($_POST["title"]) {
		$user->CreateTicket($_POST["title"],$_POST["description"],$_POST["priority"],$_POST["severity"],$_POST["category"]);
		return;
	} else {
		include_once("helpdesk.newticket.php");
		return;
	}
}

if($_GET["myprofile"] == "true") {
	include_once("helpdesk.myprofile.php");
	return;
}

if($_GET["kbase"] == "true") {
	include_once("helpdesk.kbase.php");
	return;
}

if($_GET["closeticket"]) {
	$user->CloseTicket($_GET["closeticket"]);
}

$tickets = $user->ListTickets();
if($_GET["ticketid"]) {
	include_once("helpdesk.detailview.php");
	return;
} 

/* -- MAIN LIST PAGE --*/
echo "<div style='width:100%;text-align:center'>";
if($tickets == '')
        echo "No Tickets";
else {
	echo "<div style='align:left;text-align:left;color:orange;font-size:1.3em;border-bottom:2px solid gray'>Open Tickets</div><br />";
	/* OPEN TICKETS */
	echo "<table border=1 cellpadding=0 cellspacing=0 width='100%'>";
	echo "<thead style='background-color:#618dba;height:30px'><tr>";
	echo "<td>Ticket ID</td><td>Title</td><td>Priority</td><td>Status</td><td>Category</td><td>Modified Time</td><td>Created Time</td></thead></tr><tbody>";
        for($i=0;$i<count($tickets);$i++) {
		if($tickets[$i]["status"] == "Open")
			echo "<tr><td style='padding-left:3px'>".$tickets[$i]["ticketid"]."</td><td style='padding-left:3px'><a href='".$mosConfig_secure_site."index.php?option=com_helpdesk&ticketid=".$tickets[$i]["ticketid"]."'>".$tickets[$i]["title"]."</a></td><td style='padding-left:3px'>".$tickets[$i]["priority"]."</td><td style='padding-left:3px'>".$tickets[$i]["status"]."</td><td style='padding-left:3px'>".$tickets[$i]["category"]."</td><td style='padding-left:3px'>".$tickets[$i]["modifiedtime"]."</td><td style='padding-left:3px'>".$tickets[$i]["createdtime"]."</td></tr>";
        }
	echo "</tbody></table>";

	/* CLOSED TICKETS */
	echo "<br /><br /><div style='align:left;text-align:left;color:orange;font-size:1.3em;border-bottom:2px solid gray'>Closed Tickets</div><br />";
	echo "<table border=1 cellpadding=0 cellspacing=0 width='100%'>";
	echo "<thead style='background-color:#618dba;height:30px'><tr>";
	echo "<td>Ticket ID</td><td>Title</td><td>Priority</td><td>Status</td><td>Category</td><td>Modified Time</td><td>Created Time</td></thead></tr><tbody>";
        for($i=0;$i<count($tickets);$i++) {
		if($tickets[$i]["status"] == "Closed")
			echo "<tr><td style='padding-left:3px'>".$tickets[$i]["ticketid"]."</td><td style='padding-left:3px'><a href='".$mosConfig_secure_site."/component/option,com_helpdesk/ticketid,".$tickets[$i]["ticketid"]."/'>".$tickets[$i]["title"]."</a></td><td style='padding-left:3px'>".$tickets[$i]["priority"]."</td><td style='padding-left:3px'>".$tickets[$i]["status"]."</td><td style='padding-left:3px'>".$tickets[$i]["category"]."</td><td style='padding-left:3px'>".$tickets[$i]["modifiedtime"]."</td><td style='padding-left:3px'>".$tickets[$i]["createdtime"]."</td></tr>";
         }
	echo "</tbody></table>";

}
echo "</div>";
/* -- END OF MAIN LIST -- */
?>
