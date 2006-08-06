<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

class HTML_product {
	function listProducts($option,$details) {
		if(is_array($details)) {
		    foreach($details as $key=>$product) {
		    ?>
		    <table border='0' width='95%' cellpadding='0' cellspacing='0' valign="top">
			<tr>
				<td width="133px" height="78px" valign="top" align="center">
					<a href="<?php echo $product["website"]."&productid=".$product["productid"];?>">
						<img src="http://vtiger-demo.fosslabs.com/sandbox/mmbrich/vtigercrm/storage/2006/August/week1/9_193769.jpg" alt="<?php echo $product["productname"];?>" width="129px" height="78px" border='0' />
					</a>
				</td>
				<td valign="top">
					<div style="margin-left:5px;margin-bottom:5px">
						<a href="<?php echo $product["website"]."&productid=".$product["productid"];?>" style="font-size: 16px; font-weight: bold;"><?php echo $product["productname"];?></a>
					</div>
					<div style="margin-left:5px;margin-bottom:5px">
					<?php 
						if(strlen($product["product_description"]) > 150)
							echo substr($product["product_description"],0,150)."...";
						else
							echo $product["product_description"];
					?>
					</div>
					<div style="margin-left:5px;margin-bottom:5px">
						<a href="<?php echo $product["website"]."&productid=".$product["productid"];?>">Product Details...</a>
					</div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;
				</td>
				<td colspan='1'>
					<div style="margin-left:5px">
						<form name='vt_form' method='POST'>
						<input type='hidden' name='vt_module' value='Products' />
						<input type='hidden' name='vt_entityid' value='<?php echo $product["productid"];?>' />
						<input type='hidden' name='vt_action' value='BuyProduct' />

						<label for="quantity_<?php echo $product["productid"];?>">Quantity:</label>
                				<input id="quantity_<?php echo $product["productid"];?>" class="inputbox" size="3" name="prd_qty" value="1" type="text"><br>

						<input type='submit' value='Add to Cart' class='button'></form>
					</div>
				</td>
			</tr>
		    </table>
		    <?
		    echo "<br><br>";
		    //print_r($product);
		    }
		} else {
			echo "<b>No Products Defined</b>";
		}
	}
}
?>
