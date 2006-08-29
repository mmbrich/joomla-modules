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
		continue;
        }
}
?>
<br />
<div style='height:16px;border-bottom:2px solid gray;width:100%'>
	<div class='moduletable'>
		<h3><?php echo _TICKET_INFORMATION;?>:</h3>
	</div>
<?

if($ticket['status'] != _TICKET_CLOSED_STATUS) {
 	echo '<div class="pageTitle uline" style="float:right">';
	echo '<a href="'.sefRelToAbs('index.php?option=com_helpdesk&task=CloseTicket&ticketid='.$ticketid).'">'._TICKET_CLOSE_TICKET.'</a>';
	echo '&nbsp;&nbsp;</div><br>';
}
?>
</div>
<br /><br />

<table border=0 width='100%' cellpadding=2 cellspacing=2 >
<tr>
	<td align="right"><?php echo _TICKET_ID;?>:</td>
	<td align="left"><?php echo $ticket["ticketid"];?></td>
	<td align="right"><?php echo _TICKET_PRIORITY;?>:</td>
	<td align="left"><?php echo $ticket['priority'];?></td>
</tr>
<tr>
	<td align="right"><?php echo _TICKET_CATEGORY;?>:</td>
	<td align="left"><?php echo $ticket["category"];?></td>
	<td align="right"><?php echo _TICKET_STATUS;?>:</td>
	<td align="left"><?php echo $ticket['status'];?></td>
</tr>
<tr>
	<td align="right"><?php echo _TICKET_SEVERITY;?>:</td>
	<td align="left"><?php echo $ticket["severity"];?></td>
	<td align="right"><?php echo _TICKET_PRODUCT_NAME;?>:</td>
	<td colspan='3' align="left"><?php echo $ticket['productname'];?></td>
</tr>

<tr>
	<td align="right"><?php echo _TICKET_CREATED_TIME;?>:</td>
	<td align="left"><?php echo $ticket["createdtime"];?></td>
	<td align="right"><?php echo _TICKET_MODIFIED_TIME;?>:</td>
	<td align="left"><?php echo $ticket["modifiedtime"];?></td>
</tr>
<tr>
	<td colspan='4' style='border-top:1px dotted #c0c0c0'>&nbsp;</td>
</tr>

<tr>
	<td align="right"><?php echo _TICKET_TITLE;?>:</td>
	<td colspan='5' align="left"><?php echo $ticket["title"];?></td>
</tr>
<tr>
	<td align="right"><?php echo _TICKET_DESCRIPTION;?>:</td>
	<td colspan='5' align="left"><?php echo $ticket["description"];?></td>
</tr>
<tr>
	<td align="right"><?php echo _TICKET_SOLUTION;?>:</td>
	<td colspan='5' align="left" style='border:1px solid gray'><?php echo $ticket["solution"];?></td>
</tr>

<?
if($commentcount >= 1) {
	echo "<td align='right' valign='top' nowrap>"._TICKET_COMMENTS.": </td>";
        echo "<td colspan='5' align='left'> <div class='inputbox' style='overflow:auto'>";
}
//This is to display the existing comments if any
if($commentcount >= 1 && is_array($commentresult))
{

        $list = '';
        for($j=0;$j<$commentcount;$j++)
        {
		echo '<br><div style="border-top:1px solid gray;border-bottom:1px solid gray">'._TICKET_COMMENT_FROM.' : '.$commentresult[$j]['owner'];
		echo '<br>'._TICKET_COMMENT_ON.' : '.$commentresult[$j]['createdtime'];
                echo '<br>'._TICKET_COMMENT_COMMENT.' : '.$commentresult[$j]['comments'].'</div><br>';
        }
}

?>
	</div>
    </td>
</tr>
<?

if($ticket['status'] != _TICKET_CLOSED_STATUS)
{
?>
	<tr>
		<td align="right"><?php echo _TICKET_ADD_COMMENT;?> :</td>
        	<td align="right" valign="top" colspan="5">
        		<form name="updateComments" method="post" action="index.php">
        		<input type="hidden" name="option" value="com_helpdesk" >
        		<input type="hidden" name="task" value="UpdateComment" >
        		<input type="hidden" name="ticketid" value="<?php echo $ticketid;?>">

        		<textarea name="comments" class="inputbox" style="width:100%;height:120px"></textarea>
        		<br>
			<input class='button' type='submit' name='submit' value='<?php echo _TICKET_UPDATE_BUTTON;?>'>
			</form>
		</td>
	</tr>
<?
}
?>
</table>
