<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

class HTML_helpdesk {
	function logout($user) {
		$user->LogOut();
	}

	function forgotPass($user) {
		include_once("helpdesk.forgotpass.php");
	}

	function showTicket($user,$ticketid,$tickets) {
		echo showHeader()."<br><br>";
		include_once("helpdesk.detailview.php");
	}

	function newTicket($user) {
		echo showHeader()."<br><br>";
		include_once("helpdesk.newticket.php");
	}

	function knowledgeBase($user) {
		echo showHeader()."<br><br>";
		include_once("helpdesk.kbase.php");
	}

	function myProfile($user) {
		echo showHeader()."<br><br>";
		include_once("helpdesk.myprofile.php");
	}

	function listTickets($tickets) {
		echo showHeader()."<br><br>";
		echo "<div style='width:100%;text-align:center'>";
		if($tickets == '')
        		echo "No Tickets";
		else {
        		echo "<div class='moduletable'><h3>Open Tickets</h3></div>";

        		/* OPEN TICKETS */
        		echo "<table border=1 cellpadding=0 cellspacing=0 width='100%'>";
        		echo "<tr><thead>";
        		echo "<th>Ticket ID</th><th>Title</th><th>Priority</th><th>Status</th><th>Category</th><th>Modified Time</th><th>Created Time</th></thead></tr><tbody>";
        		for($i=0;$i<count($tickets);$i++) {
                		if($tickets[$i]["status"] == "Open")
                        		echo "<tr><td style='padding-left:3px'>".$tickets[$i]["ticketid"]."</td><td style='padding-left:3px'><a href='".$mosConfig_secure_site."index.php?option=com_helpdesk&task=ShowTicket&ticketid=".$tickets[$i]["ticketid"]."'>".$tickets[$i]["title"]."</a></td><td style='padding-left:3px'>".$tickets[$i]["priority"]."</td><td style='padding-left:3px'>".$tickets[$i]["status"]."</td><td style='padding-left:3px'>".$tickets[$i]["category"]."</td><td style='padding-left:3px'>".$tickets[$i]["modifiedtime"]."</td><td style='padding-left:3px'>".$tickets[$i]["createdtime"]."</td></tr>";
        		}
        		echo "</tbody></table>";

        		/* CLOSED TICKETS */
        		echo "<br /><br /><div class='moduletable'><h3>Closed Tickets</h3></div>";
        		echo "<table border=1 cellpadding=0 cellspacing=0 width='100%'>";
        		echo "<thead><tr>";
        		echo "<th>Ticket ID</th><th>Title</th><th>Priority</th><th>Status</th><th>Category</th><th>Modified Time</th><th>Created Time</th></thead></tr><tbody>";
        		for($i=0;$i<count($tickets);$i++) {
                		if($tickets[$i]["status"] == "Closed")
                        		echo "<tr><td style='padding-left:3px'>".$tickets[$i]["ticketid"]."</td><td style='padding-left:3px'><a href='".$mosConfig_secure_site."index.php?option=com_helpdesk&task=ShowTicket&ticketid=".$tickets[$i]["ticketid"]."'>".$tickets[$i]["title"]."</a></td><td style='padding-left:3px'>".$tickets[$i]["priority"]."</td><td style='padding-left:3px'>".$tickets[$i]["status"]."</td><td style='padding-left:3px'>".$tickets[$i]["category"]."</td><td style='padding-left:3px'>".$tickets[$i]["modifiedtime"]."</td><td style='padding-left:3px'>".$tickets[$i]["createdtime"]."</td></tr>";
         		}
        		echo "</tbody></table>";
		}
		echo "</div>";
	}

	function loginUser($user) {
                $auth = "<br /><br /><div style='text-align:center'>Please log-in to access the Customer Support Portal</div>";
                $auth .= "<center><form name='login' method='POST'>";
                $auth .= "<table border=0 align='center'>";
                $auth .= "<tr><td>Username:</td><td><input type='text' name='username' style='border:1px solid gray'></td></tr>";
                $auth .= "<tr><td>Password:</td><td><input type='password' name='password' style='border:1px solid gray'></td></tr>";
                $auth .= "<tr><td colspan='2'><input type='submit' class='button' name='signin' value='Log In'></td><tr>";
                $auth .= "<tr><td colspan='2'>&nbsp;</td><tr>";
                $auth .= "<tr><td>&nbsp;</td><td align='center' style='text-align:center'><a href='index.php?option=com_helpdesk&task=ForgotPass'>Forgot Password?</a></td><tr>";
                $auth .= "</table>";
                $auth .= "</form></center>";

		echo $auth;
	}
}

function showHeader() {
	$head = "<div style='width:100%;height:17px'>";
	$head .= "<span style='float:left'><a href='index.php?option=com_helpdesk&task=ListTickets'>Ticket Home</a></span>";
	$head .= "<span style='float:left'> &nbsp;|&nbsp; <a href='index.php?option=com_helpdesk&task=NewTicket'>New Ticket</a></span>";
	$head .= "<span style='float:left'> &nbsp;|&nbsp; <a href='index.php?option=com_helpdesk&task=MyProfile'>My Profile</a></span>";
	$head .= "<span style='float:left'> &nbsp;|&nbsp; <a href='index.php?option=com_helpdesk&task=Kbase'>Knowledge Base</a></span>";

        $head .= "<span style='float:right'>".$_SESSION["vt_user_name"]."&nbsp;<a href='index.php?option=com_helpdesk&task=LogOut'>[Logout]</a></span>";
        $head .= "</div>";
        return $head;
}
?>
