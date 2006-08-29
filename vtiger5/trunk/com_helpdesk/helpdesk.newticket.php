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

<table border="0" cellspacing="0" cellpadding="0" width="100%">

<tr>
	<td>
		<div class='moduletable'><h3><?php echo _TICKET_CREATE_TICKET;?></h3></div>
	</td>
</tr>

<tr>
	<td height="25"><?php echo _TICKET_CREATE_INTRO;?></td>
</tr>

<tr>
	<td style="padding-top: 10px">
	<form name="addTicket" method="POST">
	<input type=hidden name="option" value="com_helpdesk">
	<input type=hidden name=username value="'.$_REQUEST['username'].'">
	<input type=hidden name=fun value="save">
	<table border="0" cellspacing="2" cellpadding="2" width="100%">

	<td align="right"><?php echo _TICKET_TITLE;?>: </td>
	<td width="100%"><input name="title" maxlength="255" type="text" value="" style="width:100%" class="inputbox" ></td>
</tr>
<tr>
	<td align="right"><?php echo _TICKET_PRIORITY;?>: </td>
        <td><select name="priority" class="inputbox">
<?
        for($i=0;$i<count($ticketpriorities);$i++)
        {
                echo '<OPTION value="'.$ticketpriorities[$i].'" >'.$ticketpriorities[$i].'</OPTION>';
        }
?>
        </select></td>
</tr>
<tr>
	<td align="right"><?php echo _TICKET_SEVERITY;?>: </td>
        <td><select name="severity" class="inputbox">
<?
        for($i=0;$i<count($ticketseverities);$i++)
        {
                echo '<OPTION value="'.$ticketseverities[$i].'">'.$ticketseverities[$i].'</OPTION>';
        }
?>
        </select></td>
</tr>

        <tr><td align="right"><?php echo _TICKET_CATEGORY;?>: </td>
        <td><select name="category" class="inputbox">
<?
        for($i=0;$i<count($ticketcategories);$i++)
        {
                echo '<OPTION value="'.$ticketcategories[$i].'" >'.$ticketcategories[$i].'</OPTION>';
        }
?>
        </select></td>
</tr>

<tr>
	<td align="right" valign="top"><?php echo _TICKET_DESCRIPTION;?>: </td>
	<td><textarea name="description" style="width:100%;height:120px" class="inputbox"></textarea></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type=submit name=save onclick="this.save.value=true" value='<?php echo _TICKET_CREATE_TICKET;?>' class='button'>&nbsp;&nbsp;</td>

<tr/>
</table>
	</form>
</td>
</tr>
</table>
