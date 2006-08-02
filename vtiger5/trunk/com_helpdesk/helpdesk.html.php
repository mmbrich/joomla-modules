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
}
?>
