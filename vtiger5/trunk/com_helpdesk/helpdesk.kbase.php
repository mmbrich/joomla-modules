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


if($_GET["faqid"]) {
        $list = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td valign="top">';
        $list .= '<table width="100%" border=0 cellspacing=0 cellpadding=0>';

        for($i=0;$i<count($faq_array);$i++)
        {
                if($faqid == $faq_array[$i]['id'])
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

/*
//Categories & Products
$list .= '<table border=0 width="100%" cellspacing="2" cellpadding="0">';
$list .= '<tr><td width="14"><a href="javascript:;toggleView(\'category\')"><img id="categoryimg" src="templates/rhuk_solarflare_ii/images/minus.gif" border="0" align="absmiddle"></a></td>';
$list .= '<td width="20"><a href="javascript:;toggleView(\'category\')"><img src="templates/rhuk_solarflare_ii/images/category.gif" border="0" align="absmiddle"></a></td>';
$list .= '<td><a href="javascript:;toggleView(\'category\')" class="kbNavHead">Categories</a></td></tr>';
$list .= '<tr><td></td><td></td><td><div id="category" style="display:block">';
$list .= '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
for($i=0,$j=1;$i<count($category_array);$i++,$j++)
{
        $noof_faqs = getNoofFaqsPerCategory($category_array[$i],$faq_array);
        $list .= '<tr><td class="kbNavLink"> ';
        $list .= '<a href=index.php?fun=faqs&category_index='.$i.'>'.$category_array[$i].'</a> <span class="kbNavCnt">('.$noof_faqs.')</span></td></tr>';
}
$list .= '</table></div></td></tr></table>';
// End Categories and Products
*/

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
        $list .= '<div style="border-bottom:1px solid black".<a class="kbFAQ" href="index.php?option=com_helpdesk&kbase=true&faqid='.$faq_array[$i]['id'].'">'.$faq_array[$i]['question'].'</a></div>';
        $list .= '</td></tr><tr><td></td><td class="kbAnswer">';
        $body=$faq_array[$i]['answer'];
        $delimiter = strpos($body, "&lt;!--break--&gt;");
        if ($delimiter) {
         	$list .= substr($body, 0, $delimiter).'<br />
                <br /><a href="index.php?option=com_helpdesk&kbase=true&faqid='.
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
<script language="JavaScript" src="js/cookies.js"></script>
<script type="text/javascript">
/*
function toggleView(view) {
        if (document.getElementById(view).style.display=="block") {
                document.getElementById(view).style.display="none"
                document.getElementById(view+"img").src="templates/rhuk_solarflare_ii/images/plus.gif"
                set_cookie("kb_"+view,"none")
        } else {
                document.getElementById(view).style.display="block"
                document.getElementById(view+"img").src="templates/rhuk_solarflare_ii/images/minus.gif"
                set_cookie("kb_"+view,"block")
        }
}

var view=new Array("category","products")
for (i=0;i<view.length;i++) {
        if (get_cookie("kb_"+view[i])==null || get_cookie("kb_"+view[i])=="" ||
get_cookie("kb_"+view[i])=="block") {
                document.getElementById(view[i]).style.display="block"
                document.getElementById(view[i]+"img").src="templates/rhuk_solarflare_ii/images/minus.gif"
        } else {
                document.getElementById(view[i]).style.display="none"
                document.getElementById(view[i]+"img").src="templates/rhuk_solarflare_ii/images/plus.gif"
        }
}
*/
</script>

