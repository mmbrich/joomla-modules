<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

$ticketid=$_GET["ticketid"];
$commentresult = $user->GetTicketComments($_GET["ticketid"]);
$commentcount=sizeof($commentresult);

$innerarray = $commentresult[0];
$outercount = count($tickets);
$innercount = count($innerarray);

$ticket = array();
for($i=0;$i<$outercount;$i++) {
	if($tickets[$i]['ticketid'] == $ticketid) {
         	$ticket = $tickets[$i];
        }
}
echo "<br /><div style='height:16px;border-bottom:2px solid gray'><div class='moduletable'><h3>Ticket Information:</h3></div>";
if($ticket['status'] != 'Closed') {
 	echo '<div class="pageTitle uline" style="float:right"><a href="'.sefRelToAbs('index.php?option=com_helpdesk&task=CloseTicket&ticketid='.$ticketid).'">Close This Ticket</a>&nbsp;&nbsp;</div>';
}

echo "</div><br /><br />";
echo "<table border=0 width='95%' cellpadding=0 cellspacing=8>";
echo "<tr><td>"._TICKET_ID.":</td><td>".$ticket["ticketid"]."</td><td>"._TICKET_PRIORITY.":</td><td>".$ticket['priority']."</td><td>"._TICKET_CREATED_TIME.":</td><td>".$ticket["createdtime"]."</td></tr>";
echo "<tr><td>"._TICKET_CATEGORY.":</td><td>".$ticket["category"]."</td><td>"._TICKET_STATUS.":</td><td>".$ticket['status']."</td><td>"._TICKET_MODIFIED_TIME.":</td><td>".$ticket["modifiedtime"]."</td></tr>";
echo "<tr><td>"._TICKET_SEVERITY.":</td><td>".$ticket["severity"]."</td><td>"._TICKET_PRODUCT_NAME.":</td><td colspan='3'>".$ticket['productname']."</td></tr>";
echo "<tr><td>"._TICKET_TITLE.":</td><td colspan='5'>".$ticket["title"]."</td></tr>";
echo "<tr><td>"._TICKET_DESCRIPTION.":</td><td colspan='5'>".$ticket["description"]."</td></tr>";
echo "<tr><td>"._TICKET_SOLUTION.":</td><td colspan='5' style='border:1px solid gray'>".$ticket["solution"]."</td></tr>";
if($commentcount >= 1) {
	echo "<td align='right' valign='top' nowrap>"._TICKET_COMMENTS.": </td>";
        echo "<td colspan='5'> <div class='commentArea' style='border:solid 1px gray;overflow:auto'>";
}

//This is to display the existing comments if any
if($commentcount >= 1 && is_array($commentresult))
{

        $list = '';
        for($j=0;$j<$commentcount;$j++)
        {
		$list .= '<br><div style="border-top:1px solid gray;border-bottom:1px solid gray">'._TICKET_COMMENT_FROM.' : '.$commentresult[$j]['owner'];
		$list .= '<br>On : '.$commentresult[$j]['createdtime'];
                $list .= "<br>Comment : ".$commentresult[$j]['comments'].'</div><br>';
        }
	echo $list;

}

echo "</div></td></tr>";

$list='';
if($ticket['status'] != _TICKET_CLOSED_STATUS)
{
	$list .= '<tr><td>'._TICKET_ADD_COMMENT.' :</td>';
        $list .= '<td align="right" valign="top" colspan="5">';
        $list .= '<form name="updateComments" method="post" action="index.php">';

        $list .= '<input type="hidden" name="option" value="com_helpdesk" >';
        $list .= '<input type="hidden" name="task" value="UpdateComment" >';
        $list .= '<input type="hidden" name="ticketid" value="'.$ticketid.'">';

        $list .= '<textarea name="comments" cols="55" rows="7" style="border:1px solid gray"></textarea>';
        $list .= "<br><input class='button' type='submit' name='submit' value='"._TICKET_UPDATE_BUTTON."'></form></td></tr>";
}
echo $list;

echo "</table>";
?>
