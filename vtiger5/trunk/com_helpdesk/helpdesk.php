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

require_once( $mainframe->getPath( 'front_html' ) );

$username = mosGetParam( $_POST, 'username', '' );
$password = mosGetParam( $_POST, 'password', '' );

if( isset($username) && isset($password) && $username != "" && $password != "") {
	if($user->Authenticate($username,$password) == "FALSE")
		$task = "";
	else
		mosRedirect( 'index.php?option=com_helpdesk&task=ListTickets');
}

switch($task) {
	case 'NewTicket':
		if(isset($_POST["title"]))
			$user->CreateTicket($_POST["title"],$_POST["description"],$_POST["priority"],$_POST["severity"],$_POST["category"]);
		else
			HTML_helpdesk::newTicket($user);
	break;
	case 'MyProfile':
		HTML_helpdesk::myProfile($user);
	break;
	case 'Kbase':
		HTML_helpdesk::knowledgeBase($user);
	break;
	case 'ShowTicket':
		$tickets = $user->ListTickets();
		HTML_helpdesk::showTicket($user,$_GET["ticketid"],$tickets);
	break;
	case 'LogOut':
		$user->LogOut();
		$msg = "Successful Logout";
		mosRedirect( 'index.php?option=com_helpdesk',$msg);
	break;
	case 'ForgotPass':
		HTML_helpdesk::forgotPass($user);
	break;
	case 'ListTickets':
		$tickets = $user->ListTickets();
		HTML_helpdesk::listTickets($tickets);
	break;
	case 'CloseTicket':
		$user->CloseTicket($_GET["ticketid"]);
		$msg = "Successfully Closed Ticket";
		mosRedirect( 'index.php?option=com_helpdesk&task=ListTickets',$msg);
	break;
	case 'UpdateComment':
		$ticketid = mosGetParam( $_POST, 'ticketid', '' );
		$comments = mosGetParam( $_POST, 'comments', '' );
		$user->UpdateComment($ticketid,$comments);
		$msg = "Successfully Updated Ticket Comment";
		mosRedirect( 'index.php?option=com_helpdesk&task=ShowTicket&ticketid='.$ticketid.'',$msg);
	break;
	default:
		HTML_helpdesk::loginUser($user);
	break;
}


?>
