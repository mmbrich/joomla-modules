<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

	$result = $user->ListComboValues();
        for($i=0;$i<count($result);$i++)
        {
                if($result[$i]['productid'] != '')
                {
                        $productslist[0] = $result[$i]['productid'];
                }
                if($result[$i]['productname'] != '')
                {
                        $productslist[1] = $result[$i]['productname'];
                }
                if($result[$i]['ticketpriorities'] != '')
                {
                        $ticketpriorities = $result[$i]['ticketpriorities'];
                }
                if($result[$i]['ticketseverities'] != '')
                {
                        $ticketseverities = $result[$i]['ticketseverities'];
                }
                if($result[$i]['ticketcategories'] != '')
                {
                        $ticketcategories = $result[$i]['ticketcategories'];
                }
        }

        $noofrows = count($productslist[0]);

        for($i=0;$i<$noofrows;$i++)
        {
                if($i > 0)
                        $productarray .= ',';
                $productarray .= "'".$productslist[1][$i]."'";
        }
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">

<tr>
<td style='align:left;text-align:left;color:orange;font-size:1.3em;border-bottom:2px solid gray'>
Create Ticket
</td>
</tr>

<tr>
<td height="25">Please enter the required information in the following fields to submit a ticket.</td>
</tr>

<tr>
<td style="padding-top: 10px">
<form name="addTicket" method="POST">
<input type=hidden name=username value="'.$_REQUEST['username'].'">
<input type=hidden name=fun value="save">
<table border="0" cellspacing="2" cellpadding="2">
<td>Title: </td>
<td><input name="title" maxlength="255" type="text" value="" style="border:1px solid gray"></td>
</tr>
<?
        $list .= '<tr><td align="right">Priority: </td>';
        $list .= '<td><select name="priority">';
        for($i=0;$i<count($ticketpriorities);$i++)
        {
                $list .= '<OPTION value="'.$ticketpriorities[$i].'">'.$ticketpriorities[$i].'</OPTION>';
        }
        $list .= '</select></td></tr>';
        $list .= '<tr><td align="right">Severity: </td>';
        $list .= '<td><select name="severity">';
        for($i=0;$i<count($ticketseverities);$i++)
        {
                $list .= '<OPTION value="'.$ticketseverities[$i].'">'.$ticketseverities[$i].'</OPTION>';
        }
        $list .= '</select></td></tr>';

        $list .= '<tr><td align="right">Category: </td>';
        $list .= '<td><select name="category">';
        for($i=0;$i<count($ticketcategories);$i++)
        {
                $list .= '<OPTION value="'.$ticketcategories[$i].'">'.$ticketcategories[$i].'</OPTION>';
        }
        $list .= '</select></td></tr>';
	echo $list;
?>
<tr><td align="right" valign="top">Description: </td>
<td><textarea name="description" rows="10" cols="60" style="border:1px solid gray"></textarea></td></tr>

<tr><td></td><td><input type=submit name=save onclick="this.save.value=true" value='Create Ticket' class='button'>&nbsp;&nbsp;</td>

<tr/></table></form>
</td></tr>
</table>
