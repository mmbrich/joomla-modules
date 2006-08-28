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
                        for($i=0;$i<count($tickets);$i++) {
                                if($tickets[$i]["status"] == _TICKET_OPEN_STATUS) {
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
				}
                        }
		?>
                        </tbody></table>

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
?>
