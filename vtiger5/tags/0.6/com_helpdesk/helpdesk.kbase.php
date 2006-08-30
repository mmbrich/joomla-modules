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


$Itemid = mosGetParam($_REQUEST, 'Itemid', '');
$task = mosGetParam($_REQUEST, 'task', '');
// Get pagination info
$limit = mosGetParam($_REQUEST, 'limit', '10');
$limit_start = mosGetParam($_REQUEST, 'limitstart', '0');
$num_articles = count($faq_array);

if($limit >= $num_articles)
	$limit = $num_articles;

// Get category info
$search_category = mosGetParam($_REQUEST, 'category', '');

require_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/pageNavigation.php' );

if($search_category != "")
	$num_articles = getNoofFaqsPerCategory($search_category,$faq_array);

$pageNav = new mosPageNav( $num_articles, $limit_start, $limit );
$link = "index.php?option=com_helpdesk&task=".$task."&Itemid=".$Itemid;

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
				<tr>
					<td class="contentheading">
						<strong><u><?php echo $faq_array[$i]['question'];?></u></strong><br>
						<?php echo _FAQ_CATEGORY.": ".$faq_category;?><br>
						<?php echo _FAQ_CREATED_ON.": ".$faq_createdtime;?><br>
						<?php echo _FAQ_LAST_MODIFIED.": ".$faq_modifiedtime;?>
					</td>
				</tr>
                        	<tr>
					<td>
					<br /><br />
					<?php echo $body;?>
					</td>
				</tr>
			<?
			if(is_array($comments_array)) {
				echo "<tr><td><br><br><strong>"._TICKET_COMMENTS.":</strong></td></tr>";
				$cnt=count($comments_array);
				$j=($cnt-1);
				foreach($comments_array as $comment) {
				?>
                        		<tr>
						<td style="padding:5px" class="inputbox">
						<?php echo $comments_array[$j];?>
						<br><br>
						<font color="red"><?php echo _FAQ_CREATED_ON;?>:</font> <?php echo $createdtime_array[$j];?>
						</td>
					</tr>
						<tr><td>&nbsp;</td>
					</tr>
				<?
				$j--;
				}
			}
			global $my;
			if($my->id) {
			?>
			<tr>
				<td>
					<br><strong><?php echo _KBASE_POST_COMMENT;?>:</strong><br>
					<form name="addFaqComment method="POST" action="index.php">
						<textarea name="faq_comment" style="width:100%;height:100px" class="inputbox"></textarea><br>
						<input type="hidden" name="option" value="com_helpdesk" />
						<input type="hidden" name="task" value="SaveFaqComment" />
						<input type="hidden" name="articleid" value="<?php echo $articleid;?>" />
						<input type="submit" name="submit_comment" value="<?php echo _KBASE_SUBMIT_COMMENT;?>" class="button"/>
					</form>
				</td>
			</tr>
			<?
			}
			?>
			<tr>
				<td>
				<span class="article_seperator">&nbsp;</span>
				<div class="back_button">
					<a href="javascript:history.go(-1)">
					[ <?php echo _BACK;?> ]</a>
				</div>
				</td>
			</tr>
			<?
                }
        }
        ?> 
		</td></tr></table> 
	</td></tr></table> 
	<?
return;
}

echo "<div style='width:100%'>";
  echo "<div style='float:left'>".$pageNav->writePagesCounter()."</div>";
  echo "<div style='float:right'>"._KBASE_NUM_ARTICLES.": ".$pageNav->getLimitBox($link)."</div>";
echo "</div>";
echo "<br clear='both'><br>";
?>

<table width="100%" border=0 cellspacing=0 cellpadding=0>
<tr>

<?php if($_COOKIE["kbase_menu"] == "open" || !isset($_COOKIE["kbase_menu"])) {?>
	<td valign="top" width="30%" id="kbase_menu" >
<? } else { ?>
	<td valign="top" width="30%" id="kbase_menu" style="display:none" >
<? } ?>
  <script type="text/javascript">
	function hideMenu() {
		document.getElementById("kbase_menu").style.display = "none";
		document.getElementById("kbase_unhide").style.display = "block";
		document.getElementById("kbase_hide").style.display = "none";
		document.getElementById("kbase_table").style.borderLeft = "";
		var date = new Date();
		date.setTime(date.getTime()+(1*24*60*60*1000));
		var expires = "expires="+date.toGMTString();

		document.cookie = 'kbase_menu=closed; '+expires+'; path=/';
	}
	function showMenu() {
		document.getElementById("kbase_menu").style.display = "";
		document.getElementById("kbase_unhide").style.display = "none";
		document.getElementById("kbase_hide").style.display = "";
		document.getElementById("kbase_table").style.borderLeft = "1px solid #c0c0c0";
		var date = new Date();
		date.setTime(date.getTime()+(1*24*60*60*1000));
		var expires = "expires="+date.toGMTString();

		document.cookie = 'kbase_menu=open; '+expires+'; path=/';
	}
  </script>
	<?php if($_COOKIE["kbase_menu"] == "open" || !isset($_COOKIE["kbase_menu"])) { $style=""; } else {$style="display:none"; } ?>
	<span id="kbase_hide" style="<?php echo $style;?>"><?php echo _KBASE_MENU_CLOSE;?> <a href='javascript:;' onclick='hideMenu();'>[X]</a><br></span>

  	<?php 
		$Itemid = mosGetParam($_REQUEST, 'Itemid', '');

		echo "<div><strong>Categories:</strong><br>";
			echo "<a href='".sefRelToAbs('index.php?option=com_helpdesk&task=Kbase&Itemid='.$Itemid)."'>"._KBASE_MENU_ALL."</a> &nbsp; (".count($faq_array).")<br>";
		foreach($category_array as $category) {
			echo "<a href='".sefRelToAbs('index.php?option=com_helpdesk&task=Kbase&category='.$category.'&Itemid='.$Itemid)."'>";
			echo $category."</a> &nbsp;";
			echo "(".getNoofFaqsPerCategory($category,$faq_array).")<br>";
		}

		/*
		echo "<br><br><div><strong>"._KBASE_MENU_PRODUCTS.":</strong><br>";
		foreach($product_array as $product) {
			echo "<a href='javascript:;' onclick=''>".$product["productname"]."</a> &nbsp;";
			echo "(".getNoofFaqsPerProduct($product['productid']).")<br>";
		}
		*/
  	?>
</td>

<td class="kbMain" width="70%">

<?php if($_COOKIE["kbase_menu"] == "open" || !isset($_COOKIE["kbase_menu"])) { $style="border-left: 1px solid #c0c0c0"; } else {$style=""; } ?>
<table border=0 width="100%" cellspacing=0 cellpadding=0 style="<?php echo $style;?>" id="kbase_table">

<tr>
<td>
<?php if($_COOKIE["kbase_menu"] != "open" && isset($_COOKIE["kbase_menu"])) { $style=""; } else {$style="display:none"; } ?>
	<span id="kbase_unhide" style="<?php echo $style;?>"><?php echo _KBASE_MENU_OPEN;?> <a href="javascript:;" onclick="showMenu();" >[X]</a></span>

<div class="moduletable">
<h3><?php echo _RECENT_ARTICLES;?></h3>
</div>
<br>
<table width="100%" border="0" cellspacing="3" cellpadding="0">
<?

global $cur_template;
for( $i=0 ; $i<$limit ; $i++ ) {
	$cfaq = $faq_array[($i+$limit_start)];
	if($search_category != "" && $search_category != $cfaq['category'])
		continue;

	$record_exist = true;
	?>
        <tr>
	<td width="15"><img src="components/com_helpdesk/images/faq.gif" valign="absmiddle">
	</td>
	<td>
        <div style="border-bottom:1px dotted gray">
	<a href="<?php echo sefRelToAbs('index.php?option=com_helpdesk&task=KbaseArticle&articleid='.$cfaq['id']);?>"><?php echo $cfaq['question'];?></a> 
	<?php echo "<br />"._FAQ_CATEGORY.": ".$cfaq['category'];?>
	<?php echo "<br />"._FAQ_CREATED_ON.": ".$cfaq['faqcreatedtime'];?>
	<?php echo "<br />"._FAQ_LAST_MODIFIED.": ".$cfaq['faqmodifiedtime'];?>
	</div>
        </td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td class="kbAnswer">
	<?
        $body=$cfaq['answer'];
        $delimiter = strpos($body, "<!--break-->");
        if ($delimiter) {
        	echo substr($body, 0, $delimiter);
		?>
		<br />
                <br />
		<a href="<?php echo sefRelToAbs('index.php?option=com_helpdesk&task=KbaseArticle&articleid='.$cfaq['id']);?>">More...</a>
		<br />
		</td>
		<tr>
                <td height="10">&nbsp;</td>
		</tr>
	<?
        } else {
        	echo $cfaq['answer'];
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
</tr>
</table>
<?
echo "<div align='center'>".$pageNav->writePagesLinks($link)."</div>";

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
	global $faq_array;
        $count = 0;
        for($i=0;$i<count($faq_array);$i++)
        {
                if($productid == $faq_array[$i]['product_id'])
                        $count++;
        }
        return $count;
}
?>
