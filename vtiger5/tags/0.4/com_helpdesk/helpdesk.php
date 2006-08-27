<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

#check if the bot exist
if (file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php')) {
	require_once('components/com_vtigerregistration/vtiger/VTigerHDeskUser.class.php');
	$user = new VtigerHDeskUser($my->id);
} else {
	echo "You should install bot_vconnection if you want something to happen here ;)";
	flush();exit();
}

require_once( $mainframe->getPath( 'front_html' ) );
$q = "SELECT name,value FROM #__vtiger_portal_configuration "
	." WHERE name LIKE 'helpdesk_%'";
$database->setQuery($q);
$configs = $database->loadObjectList();
foreach($configs as $config) {
 	$conf[$config->name] = $config->value;
}

if($my->id && $user->IsAllowed($my->id)) {
	if(!isset($task) || $task == '')
		$task = 'ListTickets';
} else {
	if((task == "KbaseArticle" || $task == "Kbase") && $conf["helpdesk_kbase_noauth"] != "on")
		$task = '';
	if($task == "" && $conf["helpdesk_noauth"] == "on")
		$task = 'ListTickets';
	else if(($task != "KbaseArticle" && $task != "Kbase") && $conf["helpdesk_noauth"] != "on")
		$task = '';
}

switch($task) {
	case 'NewTicket':
		$title = mosGetParam( $_POST, 'title', '' );
		if(isset($title) && $title != "") {
			$desc = mosGetParam( $_POST, 'description', '' );
			$prio= mosGetParam( $_POST, 'priority', '' );
			$severity = mosGetParam( $_POST, 'severity', '' );
			$cat= mosGetParam( $_POST, 'category', '' );
			$user->CreateTicket($title,$desc,$prio,$severity,$cat);
			$msg = "Successfully Created Ticket";
			mosRedirect( sefRelToAbs('index.php?option=com_helpdesk&task=ListTickets',$msg));
		} else
			HTML_helpdesk::newTicket($user);
	break;
	case 'Kbase':
		HTML_helpdesk::knowledgeBase($user);
	break;
        case 'KbaseArticle':
		$articleid = mosGetParam( $_GET, 'articleid', '' );
                HTML_helpdesk::knowledgeBase($user,$articleid);
        break;
	case 'ShowTicket':
		$tickets = $user->ListTickets();
		$ticketid = mosGetParam( $_GET, 'ticketid', '' );
		HTML_helpdesk::showTicket($user,$ticketid,$tickets);
	break;
	case 'ListTickets':
		$tickets = $user->ListTickets();
		HTML_helpdesk::listTickets($tickets);
	break;
	case 'CloseTicket':
		$ticketid = mosGetParam( $_GET, 'ticketid', '' );
		$user->CloseTicket($ticketid);
		$msg = "Successfully Closed Ticket";
		mosRedirect( sefRelToAbs('index.php?option=com_helpdesk&task=ListTickets',$msg));
	break;
	case 'UpdateComment':
		$ticketid = mosGetParam( $_POST, 'ticketid', '' );
		$comments = mosGetParam( $_POST, 'comments', '' );
		$user->UpdateComment($ticketid,$comments);
		$msg = "Successfully Updated Ticket Comment";
		mosRedirect( sefRelToAbs('index.php?option=com_helpdesk&task=ShowTicket&ticketid='.$ticketid.'',$msg));
	break;
	default:
		echo "Not Authorized";
	break;
}
?>
