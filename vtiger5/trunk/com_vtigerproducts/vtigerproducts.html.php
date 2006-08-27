<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

class HTML_product {
	function listProducts($option,$details,$category,$config) {
		$itemid = mosGetParam( $_REQUEST, 'Itemid' , '1');
		if(is_array($details)) {
		    foreach($details as $key=>$product) {
		    ?>
		    <table border='0' width='95%' cellpadding='0' cellspacing='0' valign="top">
			<tr>
				<?php if($config["product_images"] == "on") { ?>
				    <td width="133px" height="78px" valign="top" align="center">
					<a href="<?php echo sefRelToAbs($product["website"]."&Itemid=".$itemid."&productid=".$product["productid"]);?>">
						<img src="<?php echo $product["image"];?>" alt="<?php echo $product["productname"];?>" width="129px" height="78px" border='0' />
					</a>
				    </td>
				<? } ?>
				<td valign="top">
					<div style="margin-left:5px;margin-bottom:5px">
						<a href="<?php echo sefRelToAbs($product["website"]."&Itemid=".$itemid."&productid=".$product["productid"]);?>" style="font-size: 16px; font-weight: bold;"><?php echo $product["productname"];?></a>
					</div>
					<div style="margin-left:5px;margin-bottom:5px">
					<?php 
						echo substr($product["product_description"],0,$config["product_desc_len"])."...";
					?>
					</div>
					<div style="margin-left:5px;margin-bottom:5px">
						<a href="<?php echo sefRelToAbs($product["website"]."&Itemid=".$itemid."&productid=".$product["productid"]);?>">Product Details...</a>
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
						<label for="quantity_<?php echo $product["productid"];?>">Quantity:</label>
                				<input id="quantity_<?php echo $product["productid"];?>" class="inputbox" size="3" name="prd_qty" value="1" type="text"><br>
						<input type='hidden' name='vt_action' value='BuyProduct' />

						<input type='submit' value='Add to Cart' class='button'></form>
					</div>
				    </td>
				<? } ?>
			</tr>
		    </table>
		    <?
		    echo "<br><br>";
		    }
		} else {
			echo "<b>No Products Defined</b>";
		}
	}
}
?>
