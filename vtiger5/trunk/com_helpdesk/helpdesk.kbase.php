<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

$result = $user->GetKbaseDetails();

$category_array = $result[0];
$faq_array = $result[2];

if(@array_key_exists('productid',$result[1][0]) && @array_key_exists('productname',$result[1][0]))
        $product_array = $result[1];
elseif(@array_key_exists('id',$result[1][0]) && @array_key_exists('question',$result[1][0]) && @array_key_exists('answer',$result[1][0]))
        $faq_array = $result[1];

if($articleid != "" && isset($articleid)) {
	?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td valign="top">
        <table width="100%" border=0 cellspacing=0 cellpadding=0>
	<?
        for($i=0;$i<count($faq_array);$i++)
        {
                if($articleid == $faq_array[$i]['id'])
                {
                        $search = array (
                                        '@&(lt|#60);@i',
                                        '@&(gt|#62);@i',
					"'&(quot|#34);'i",);

                        $replace = array (
                                        '<',
                                        '>',
					'"');
                        $body = preg_replace($search, $replace, $faq_array[$i]['answer']);

                        $faq_id = $faq_array[$i]['id'];
                        $faq_createdtime = $faq_array[$i]['faqcreatedtime'];
                        $faq_modifiedtime = $faq_array[$i]['faqmodifiedtime'];
                        $faq_productid = $faq_array[$i]['product_id'];
                        $faq_category = $faq_array[$i]['category'];
                        $comments_array = $faq_array[$i]['comments'];
                        $createdtime_array = $faq_array[$i]['createdtime'];

                        ?> 
				<tr><td class="contentheading"><?php echo $faq_array[$i]['question'];?></td></tr>
                        	<tr><td><br /><br /><?php echo $body;?></td></tr></table> 
			<?
                }
        }
        ?> 
		</td></tr></table> 
	<?
return;
}

?>
<table width="100%" height="100%" border=0 cellspacing=0 cellpadding=0>
<tr>

<td valign="top" width="30%">Category List Goes Here :) </td>

<td class="kbMain" width="70%">

<table border=1 width="100%" cellspacing=0 cellpadding=0>

<tr>
<td>
<div class="moduletable">
<h3><?php echo _RECENT_ARTICLES;?></h3>
</div>
<br>
<table width="100%" border="0" cellspacing="3" cellpadding="0">
<?

global $cur_template;
for($i=0;$i<count($faq_array);$i++) {
	$record_exist = true;
	?>
        <tr>
	<td width="15"><img src="components/com_helpdesk/images/faq.gif" valign="absmiddle">
	</td>
	<td>
        <div style="border-bottom:1px dotted gray">
	<a href="<?php echo sefRelToAbs('index.php?option=com_helpdesk&task=KbaseArticle&articleid='.$faq_array[$i]['id']);?>"><?php echo $faq_array[$i]['question'];?></a> 
	<?php echo "<br />"._FAQ_CATEGORY.": ".$faq_array[$i]['category'];?>
	<?php echo "<br />"._FAQ_CREATED_ON.": ".$faq_array[$i]['faqcreatedtime'];?>
	<?php echo "<br />"._FAQ_LAST_MODIFIED.": ".$faq_array[$i]['faqmodifiedtime'];?>
	</div>
        </td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td class="kbAnswer">
	<?
        $body=$faq_array[$i]['answer'];
        $delimiter = strpos($body, "<!--break-->");
        if ($delimiter) {
        	echo substr($body, 0, $delimiter);
		?>
		<br />
                <br />
		<a href="<?php echo sefRelToAbs('index.php?option=com_helpdesk&task=KbaseArticle&articleid='.$faq_array[$i]['id']);?>">More...</a>
		<br />
		</td>
		<tr>
                <td height="10">&nbsp;</td>
		</tr>
	<?
        } else {
        	echo $faq_array[$i]['answer'];
		?>
                </td>
		</tr>
		<tr>
		<td height="10"><br /></td>
		</tr>
	<?
        }
}
if(!$record_exist)
	echo _NO_FAQS;
?>
</table>
</td>
</tr>
</table>
<?

function getNoofFaqsPerCategory($category_name,$faq_array)
{
        $count = 0;
        for($i=0;$i<count($faq_array);$i++)
        {
                if($category_name == $faq_array[$i]['category'])
                        $count++;
        }
        return $count;
}
function getNoofFaqsPerProduct($productid)
{
        $count = 0;
        for($i=0;$i<count($faq_array);$i++)
        {
                if($productid == $faq_array[$i]['product_id'])
                        $count++;
        }
        return $count;
}
?>
