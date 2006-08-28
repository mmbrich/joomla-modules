<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

class HTML_product {
	function listProducts( $option, $details, $category, $config, $limit, $limit_start, $pageNav ) {
		$Itemid = mosGetParam( $_REQUEST, 'Itemid' , '');

		$link = "index.php?option=com_vtigerproducts&category=".$category;
		if(is_array($details)) {
		    if($config["product_show_pagination"] == "on") {
                	echo "<div style='float:left'>".$pageNav->writePagesCounter()."</div>";
			echo "<div style='float:right'>Number of products per page: ".$pageNav->getLimitBox($link)."</div>";
			echo "<br clear='both'><br>";
		    }
		    for($i=0;$i<$limit;$i++) {
			$product = $details[($i+$limit_start)];
			if( ( $i + $limit_start ) >= count($details) ) 
				break;
		    	?>
		    	<table border='0' width='95%' cellpadding='0' cellspacing='0' valign="top">
			<tr>
				<?php if($config["product_images"] == "on") { ?>
				    <td width="133px" height="78px" valign="top" align="center">
					<a href="<?php echo sefRelToAbs($product["website"]."&Itemid=".$Itemid."&productid=".$product["productid"]);?>">
						<img alt="<?php echo $product["productname"];?>" src="<?php echo $product["image"];?>" width="129px" height="78px" border='0' />
					</a>
				    </td>
				<? } ?>
				<td valign="top">
					<div style="margin-left:5px;margin-bottom:5px">
						<a href="<?php echo sefRelToAbs($product["website"]."&Itemid=".$Itemid."&productid=".$product["productid"]);?>" style="font-size: 16px; font-weight: bold;"><?php echo $product["productname"];?></a>
					</div>
					<div style="margin-left:5px;margin-bottom:5px">
					<?php 
						echo substr($product["product_description"],0,$config["product_desc_len"])."...";
					?>
					</div>
					<div style="margin-left:5px;margin-bottom:5px">
						<a href="<?php echo sefRelToAbs($product["website"]."&Itemid=".$Itemid."&productid=".$product["productid"]);?>"><?php echo _PROD_DETAILS;?></a>
					</div>
				</td>
			</tr>
			<tr>
				<?php if($config["product_addcart"] == "on") { ?>
				    <td colspan='2'>
					<div style="margin-left:5px;text-align:center">
						<form name='vt_form' action='index.php' method='POST'>
						<input type='hidden' name='vt_module' value='Products' />
						<input type='hidden' name='vt_entityid' value='<?php echo $product["productid"];?>' />
						<label for="quantity_<?php echo $product["productid"];?>"><?php echo _PROD_QUANTITY;?>:</label>
                				<input id="quantity_<?php echo $product["productid"];?>" class="inputbox" size="3" name="prd_qty" value="<?php echo $product["qtyindemand"];?>" type="text"><br>
						<input type='hidden' name='vt_action' value='BuyProduct' />

						<input type='submit' value='<?php echo _ADD_TO_CART;?>' class='button'></form>
					</div>
				    </td>
				<? } ?>
			</tr>
		    	</table>
		    	<?
		    	echo "<br><br>";
		    }
		    if($config["product_show_pagination"] == "on") {
			echo "<div align='center'>".$pageNav->writePagesLinks($link)."</div>";
		    }
		} else {
			echo "<b>"._NO_PRODUCTS."</b>";
		}
	}
}
?>
