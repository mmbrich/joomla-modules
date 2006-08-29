<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

class HTML_helpdesk {
        function showTicket($user,$ticketid,$tickets) {
                include_once("helpdesk.detailview.php");
        }

        function newTicket($user) {
                include_once("helpdesk.newticket.php");
        }

        function knowledgeBase($user,$articleid='') {
                include_once("helpdesk.kbase.php");
        }

        function listTickets($tickets) {
                echo "<div style='width:100%;text-align:center'>";
		global $open_tickets,$closed_tickets,$start_open_ticket,$per_page_limit,$tickets;
		$start_open_ticket = mosGetParam($_REQUEST, 'start_open_ticket', '0');
		$per_page_limit='100';

                if($tickets == '' || !is_array($tickets))
                        echo "No Tickets";
                else {
		?>
                        <div class='moduletable'><h3><?php echo _TICKET_OPEN_TICKETS;?></h3></div>

                        <!-- OPEN TICKETS -->
                        <table border=1 cellpadding=0 cellspacing=0 width='100%'>
			<thead>
                            <tr>
                        	<th><?php echo _TICKET_ID;?></th>
				<th><?php echo _TICKET_TITLE;?></th>
				<th><?php echo _TICKET_PRIORITY;?></th>
				<th><?php echo _TICKET_STATUS;?></th>
				<th><?php echo _TICKET_PRIORITY;?></th>
				<th><?php echo _TICKET_MODIFIED_TIME;?></th>
				<th><?php echo _TICKET_CREATED_TIME;?></th>
			    </tr>
			</thead>
			<tbody>
		<?
                        for($i=0;$i<$per_page_limit;$i++) {
				$ticket = $tickets[($i+$start_open_ticket)];
                                if($ticket["status"] == _TICKET_OPEN_STATUS) {
		?>
                                        <tr>
						<td style='padding-left:3px'><?php echo $ticket["ticketid"];?></td>
						<td style='padding-left:3px'><a href='<?php echo sefRelToAbs('index.php?option=com_helpdesk&task=ShowTicket&ticketid='.$ticket["ticketid"]);?>'><?php echo $ticket["title"];?></a></td>
						<td style='padding-left:3px'><?php echo $ticket["priority"];?></td>
						<td style='padding-left:3px'><?php echo $ticket["status"];?></td>
						<td style='padding-left:3px'><?php echo $ticket["category"];?></td>
						<td style='padding-left:3px'><?php echo $ticket["modifiedtime"];?></td>
						<td style='padding-left:3px'><?php echo $ticket["createdtime"];?></td>
					</tr>
		<?
				}
                        }
		?>
                        </tbody></table>
			<?php echo make_pagination('open');?>

                        <!-- CLOSED TICKETS -->
                        <br /><br /><div class='moduletable'><h3><?php echo _TICKET_CLOSED_TICKETS;?></h3></div>
                        <table border=1 cellpadding=0 cellspacing=0 width='100%'>
			<thead>
                            <tr>
                        	<th><?php echo _TICKET_ID;?></th>
				<th><?php echo _TICKET_TITLE;?></th>
				<th><?php echo _TICKET_PRIORITY;?></th>
				<th><?php echo _TICKET_STATUS;?></th>
				<th><?php echo _TICKET_PRIORITY;?></th>
				<th><?php echo _TICKET_MODIFIED_TIME;?></th>
				<th><?php echo _TICKET_CREATED_TIME;?></th>
				</thead>
			    </tr>
			</thead>
			<tbody>
		<?
			$closed_tickets=0;
                        for($i=0;$i<count($tickets);$i++) {
                                if($tickets[$i]["status"] == _TICKET_CLOSED_STATUS) {
		?>
                                        <tr>
						<td style='padding-left:3px'><?php echo $tickets[$i]["ticketid"];?></td>
						<td style='padding-left:3px'><a href='<?php echo sefRelToAbs('index.php?option=com_helpdesk&task=ShowTicket&ticketid='.$tickets[$i]["ticketid"]);?>'><?php echo $tickets[$i]["title"];?></a></td>
						<td style='padding-left:3px'><?php echo $tickets[$i]["priority"];?></td>
						<td style='padding-left:3px'><?php echo $tickets[$i]["status"];?></td>
						<td style='padding-left:3px'><?php echo $tickets[$i]["category"];?></td>
						<td style='padding-left:3px'><?php echo $tickets[$i]["modifiedtime"];?></td>
						<td style='padding-left:3px'><?php echo $tickets[$i]["createdtime"];?></td>
					</tr>
		<?
					$closed_tickets++;
				}
                        }
		?>
                        </tbody>
		    </table>
		<?
                }
		?>
                </div>
		<?
        }
}

function make_pagination($type='open') {
	global $closed_tickets,$start_open_ticket,$per_page_limit,$tickets;

	$open_tickets=0;
	for($i=0;$i<count($tickets);$i++) {
		if($tickets[$i]["status"] == "Open")
			$open_tickets++;
	}

	$total_tickets = $open_tickets;
	$total_pages = floor($total_tickets/$per_page_limit);
	$last_ticket = '';

	if($total_pages == 0)
		//echo '<div align="center"><span class="pagenav">&lt;&lt; Start</span> <span class="pagenav">&lt; Prev</span> <span class="pagenav">1</span> <span class="pagenav">Next &gt;</span> <span class="pagenav">End &gt;&gt;</span></div>';
	if($total_pages > 0) {
		$i=0;
		while($i != $total_pages) {
			$pages .= ($i+1)." ";
			$i++;
		}
		$endid = ($total_tickets - $per_page_limit);
		$nextid = "";
		$firstid = "";
		$previousid = "";
		//echo '<div align="center"><span class="pagenav">&lt;&lt; Start</span> <span class="pagenav">&lt; Prev</span> <span class="pagenav">'.$pages.'</span> <span class="pagenav">Next &gt;</span> <span class="pagenav"><a href="'.sefRelToAbs("index.php?option=com_helpdesk&task=ListTickets&start_open_ticket=".$endid).'">End &gt;&gt;</a></span></div>';
	}
}
?>
