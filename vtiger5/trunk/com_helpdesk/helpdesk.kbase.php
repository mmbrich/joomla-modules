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
        $list = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td valign="top">';
        $list .= '<table width="100%" border=0 cellspacing=0 cellpadding=0>';

        for($i=0;$i<count($faq_array);$i++)
        {
                if($articleid == $faq_array[$i]['id'])
                {
                        $list .= '<tr><td class="kbFAQ">'.$faq_array[$i]['question'].'</td></tr>';
                        $search = array (
                                        '@&(lt|#60);@i',
                                        '@&(gt|#62);@i',
					"'&(quot|#34);'i",);

                        $replace = array (
                                        '<',
                                        '>',
					'"');
                        $body = preg_replace($search, $replace, $faq_array[$i]['answer']);
                        $list .= '<tr><td><br /><br />'.$body.'</td></tr></table>';

                        $faq_id = $faq_array[$i]['id'];
                        $faq_createdtime = $faq_array[$i]['faqcreatedtime'];
                        $faq_modifiedtime = $faq_array[$i]['faqmodifiedtime'];
                        $faq_productid = $faq_array[$i]['product_id'];
                        $faq_category = $faq_array[$i]['category'];

                        $comments_array = $faq_array[$i]['comments'];
                        $createdtime_array = $faq_array[$i]['createdtime'];
                }
        }
        $list .= '</td></tr></table>';

	echo $list;
return;
}


$list = '<table width="100%" height="100%" border=1 cellspacing=0 cellpadding=0><tr><td valign="top" width="30%">';

$list .= '</td><td class="kbMain" width="70%"> ';

$list = '<table border=0 width="100%" cellspacing=0 cellpadding=0><tr><td>';

for($i=0;$i<count($faq_array);$i++)
{
	$temp[$i] .= $faq_array[$i]['faqmodifiedtime'];
}

$list .= '<div class="kbHead">Recently Created Articles</div>';
$list .= '<br><table width="100%" border="0" cellspacing="3" cellpadding="0">';

for($i=0;$i<count($faq_array);$i++)
{
        $record_exist = true;
        $list .= '<tr><td width="15"><img src="templates/rhuk_solarflare_ii/images/faq.gif" valign="absmiddle"></td><td>';
        $list .= '<div style="border-bottom:1px solid black".<a class="kbFAQ" href="index.php?option=com_helpdesk&task=KbaseArticle&articleid='.$faq_array[$i]['id'].'">'.$faq_array[$i]['question'].'</a></div>';
        $list .= '</td></tr><tr><td></td><td class="kbAnswer">';
        $body=$faq_array[$i]['answer'];
        $delimiter = strpos($body, "&lt;!--break--&gt;");
        if ($delimiter) {
         	$list .= substr($body, 0, $delimiter).'<br />
                <br /><a href="index.php?option=com_helpdesk&task=KbaseArticle&articleid='.
                $faq_array[$i]['id'].'">More...</a><br /></td></tr><tr>
                <td height="10"></td></tr>';
        } else {
                $list .= $faq_array[$i]['answer'].'
                </td></tr><tr><td height="10"><br /></td></tr>';
        }
}
if(!$record_exist)
        $list .= $mod_strings['LBL_NO_FAQ'];

$list .= '</table>';

$list .= '</td></tr></table>';
//$list .= '</td></tr></table>';

echo $list;


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
