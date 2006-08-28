<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

$ticketid=$_REQUEST["ticketid"];
$commentresult = $user->GetTicketComments($_REQUEST["ticketid"]);
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
?>
<br />
<div style='height:16px;border-bottom:2px solid gray'>
	<div class='moduletable'>
		<h3><?php echo _TICKET_INFORMATION;?>:</h3>
	</div>
<?

if($ticket['status'] != _TICKET_CLOSED_STATUS) {
 	echo '<div class="pageTitle uline" style="float:right">';
	echo '<a href="'.sefRelToAbs('index.php?option=com_helpdesk&task=CloseTicket&ticketid='.$ticketid).'">'._TICKET_CLOSE_TICKET.'</a>';
	echo '&nbsp;&nbsp;</div>';
}
?>
</div>

<br /><br />

<table border=0 width='95%' cellpadding=0 cellspacing=8>

<tr>
	<td><?php echo _TICKET_ID;?>:</td>
	<td><?php echo $ticket["ticketid"];?></td>
	<td><?php echo _TICKET_PRIORITY;?>:</td>
	<td><?php echo $ticket['priority'];?></td>
	<td><?php echo _TICKET_CREATED_TIME;?>:</td>
	<td><?php echo $ticket["createdtime"];?></td>
</tr>

<tr>
	<td><?php echo _TICKET_CATEGORY;?>:</td>
	<td><?php echo $ticket["category"];?></td>
	<td><?php echo _TICKET_STATUS;?>:</td>
	<td><?php echo $ticket['status'];?></td>
	<td><?php echo _TICKET_MODIFIED_TIME;?>:</td>
	<td><?php echo $ticket["modifiedtime"];?></td>
</tr>
<tr>
	<td><?php echo _TICKET_SEVERITY;?>:</td>
	<td><?php echo $ticket["severity"];?></td>
	<td><?php echo _TICKET_PRODUCT_NAME;?>:</td>
	<td colspan='3'><?php echo $ticket['productname'];?></td>
</tr>

<tr>
	<td><?php echo _TICKET_TITLE;?>:</td>
	<td colspan='5'><?php echo $ticket["title"];?></td>
</tr>
<tr>
	<td><?php echo _TICKET_DESCRIPTION;?>:</td>
	<td colspan='5'><?php echo $ticket["description"];?></td>
</tr>
<tr>
	<td><?php echo _TICKET_SOLUTION;?>:</td>
	<td colspan='5' style='border:1px solid gray'><?php echo $ticket["solution"];?></td>
</tr>

<?
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

?>
	</div></td>
</tr>
<?

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
?>

</table>
