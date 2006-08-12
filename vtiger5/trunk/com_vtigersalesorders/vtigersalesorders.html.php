<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

class HTML_vtigersalesorders {
	function view($order)
	{
		global $my;
		if(!is_array($order["products"])) {
			echo "<h3>Cart is empty</h3>";
			return;
		} 
		//print_r($order);
	?>

	<h2>Cart</h2>
	<!-- Cart Begins here -->
	<input name="option" value="com_virtuemart" type="hidden">
	<table border="0" cellpadding="4" cellspacing="2" width="100%">
   	    <tbody>
		<tr class="sectiontableheader" align="left">
			<th>Name</th>
			<th>Price</th>
			<th>Quantity</th>
			<th>Adj</th>
			<th>Tax</th>
			<th>Subtotal</th>
			<th colspan="2" align="center">Update</th>
  		</tr>
		<? 
		foreach($order["products"] as $product) { 
		$total = ($product["total"]*$product["quantity"]);
		?>
  			<tr class="sectiontableentry1" valign="top">
			<form action="http://www.fosslabs.com/index.php" method="post"></form>
			<td>
				<a href="<?php echo $product["website"];?>&productid=<?php echo $product["productid"];?>">
				<strong><?php echo $product["name"];?></strong>
				</a><br>
			</td>
    			<td>$<?php echo number_format($total,'2','.',',');?></td>

    			<td><input title="Update Quantity In Cart" class="inputbox" size="4" maxlength="4" name="quantity" value="<?php echo $product["quantity"];?>" type="text" id="quantity_<?php echo $product["productid"];?>"></td>

			<!-- Discounts -->
			<? if ($product["discount_amount"] != "") { 
				$adj = ($product["discount_amount"]);
				$total = ($total - $adj);
    				?> 
					<td>$<?php echo number_format($product["discount_amount"],'2','.',',');?></td> 
			 	<?
			} else if($product["discount_percent"] != "") { 
				$adj = (($total * $product["discount_percent"])/100);
				$total = ($total - $adj);
    				?> 
					<td><font color='red'><?php echo $product["discount_percent"];?>%</font></td> 
			 	<?
			} else { 
				$adj = "0";
    				?> 
					<td>$<?php echo number_format($adj,'2','.',',');?></td> 
			 	<?
			} 
			?>

			<!-- taxes -->
			<?
			if($order["taxtype"] == "individual") {
				if ($product["tax2"] != "") { // Sales Tax
					$tax = (($total * $product["tax2"])/100);
				} else if ($product["tax3"] != "") {  // Service Tax
					$tax = (($total * $product["tax3"])/100);
				} else if ($product["tax1"] != "") { // VAT Tax
					$tax = (($total * $product["tax1"])/100);
				} else { // No Tax
					$tax = "0";
				}
			} else {
					$tax = "0";
			}

			?>
    			<td>$<?php echo number_format($tax,'2','.',',');?></td>


			<!-- Product Total -->
    			<td>$<?php echo number_format(((($product["total"] * $product["quantity"])-$adj)+$tax),'2','.',',');?></td>

    			<td>
				<!-- UPDATE QUANTITY FORM -->
				<form action="index.php" method="post" name="update">
        				<input name="option" value="com_vtigersalesorders" type="hidden">
        				<input name="task" value="updateQuantity" type="hidden">
        				<input name="soid" value="<?php echo $order["salesorderid"];?>" type="hidden">
        				<input name="product_id" value="<?php echo $product["productid"];?>" type="hidden">
        				<input name="quantity" type="hidden" id="quan_<? echo $product["productid"];?>">
        				<input name="update" title="Update Quantity In Cart" src="http://www.fosslabs.com/components/com_virtuemart/shop_image/ps_image/edit_f2.gif" alt="Update" border="0" type="image" onclick="document.getElementById('quan_<?php echo $product["productid"];?>').value = document.getElementById('quantity_<?php echo $product["productid"];?>').value;" />
      				</form>
      			</td>
    			<td>
				<!-- DELETE PRODUCT FROM SO FORM -->
				<form action="index.php" method="post" name="delete">
        				<input name="option" value="com_vtigersalesorders" type="hidden">
        				<input name="task" value="removeProduct" type="hidden">
        				<input name="soid" value="<?php echo $order["salesorderid"];?>" type="hidden">
        				<input name="productid" value="<?php echo $product["productid"];?>" type="hidden">
      					<input name="delete" title="Delete Product From Cart" src="http://www.fosslabs.com/components/com_virtuemart/shop_image/ps_image/delete_f2.gif" alt="Delete Product From Cart" border="0" type="image">
      				</form>
			</td>
  		</tr>
		<? 
		} // END FOREACH 
		//print_r($order);
		?>
		<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->

  		<tr class="sectiontableentry2">
    			<td colspan="5" align="right">Subtotal:</td> 
    			<td colspan="3">$<?php echo number_format($order["subtotal"],'2','.',',');?></td>
  		</tr>

		<!-- Adjustments -->
		<? if ($order["txtAdjustment"] != "") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">Adjustment: </td>
    			<td colspan="3">
				<? if(preg_match("/\-/",$order["txtAdjustment"])) {
					echo "$<font color='red'>".number_format( substr( $order["txtAdjustment"], (strpos($order["txtAdjustment"],"-")+1), strlen($order["txtAdjustment"]) ),'2','.',',')."</font>";
				} else { ?>
					$<?php echo number_format($order["txtAdjustment"],'2','.',',');?>
				<? } ?>
    			</td>

  		    </tr>
		<? } ?>

		<!-- Discounts -->
		<? if ($order["discount_percent"] != "") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">Discount Percent: </td>
    			<td colspan="3">
				<font color='red'><?php echo number_format($order["discount_percent"],'2','.',',');?></font>%
    			</td>

  		    </tr>
		<? } else if ($order["discount_amount"] != "") {?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">Discount Amount: </td>
    			<td colspan="3">
				$<font color='red'><?php echo number_format($order["discountamount"],'2','.',',');?></font>
    			</td>

  		    </tr>
		<? } ?>

		<!-- Shipping and Handling -->
		<? if ($order["s_h_amount"] != "" && $order["s_h_amount"] != "0.000") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">Shipping &amp; Handling: </td>
    			<td colspan="3">
				$<?php echo number_format($order["s_h_amount"],'2','.',',');?>
    			</td>
  		    </tr>
		<? } ?>


		<!-- S&H Taxes -->
		<? if ($order["sh_tax1"] != "" && ($order["s_h_amount"] != "" && $order["s_h_amount"] != "0")) {  // VAT ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">S&amp;H Taxes: </td>
    			<td colspan="3">
				$<?php echo number_format((($order["s_h_amount"] * $order["sh_tax1"])/100),'2','.',',');?>
    			</td>
  		    </tr>
		<? } else if ($order["sh_tax2"] != "" && ($order["s_h_amount"] != "" && $order["s_h_amount"] != "0")) { //SALES ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">S&amp;H Taxes: </td>
    			<td colspan="3">
				$<?php echo number_format((($order["s_h_amount"] * $order["sh_tax2"])/100),'2','.',',');?>
    			</td>
  		    </tr>
		<? } else if ($order["sh_tax3"] != "" && ($order["s_h_amount"] != "" && $order["s_h_amount"] != "0")) { //SERVICE ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">S&amp;H Taxes: </td>
				$<?php echo number_format((($order["s_h_amount"] * $order["sh_tax3"])/100),'2','.',',');?>
    			<td colspan="3">
    			</td>
  		    </tr>
		<? } ?>

		<!-- Group Taxes -->
		<? if ($order["taxtype"] == "group" && $order["txtTax"] != "") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">Taxes: </td>
    			<td colspan="3">
				$<?php echo number_format($order["txtTax"],'2','.',',');?>
    			</td>
  		    </tr>
		<? } else if ($order["taxtype"] == "group") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right">Taxes: </td>
    			<td colspan="3">
				$0.00
    			</td>
  		    </tr>
		<? } ?>

  		<tr>
    			<td colspan="3">&nbsp;</td>
    			<td colspan="5"><hr></td>
  		</tr>

  		<tr class="sectiontableentry2">
    			<td colspan="5" align="right">Total: </td>
    			<td colspan="3">
				<strong>$<?php echo number_format($order["grandtotal"],'2','.',',');?></strong>
    			</td>

  		</tr>
  		<tr>
    			<td colspan="8"><hr></td>
  		</tr>
		<!-- OUT FOR NOW
  		<tr>
		<td width="100%" colspan="7">If you have a coupon code, please enter it below:<br>
    		<form action="http://www.fosslabs.com/index.php" method="post">
			<input name="coupon_code" maxlength="30" class="inputbox" type="text" width="10">
			<input name="Itemid" value="36" type="hidden">
			<input name="do_coupon" value="yes" type="hidden">
			<input name="option" value="com_virtuemart" type="hidden">
			<input name="page" value="shop.cart" type="hidden">
			<input value="Submit" class="button" type="submit">
		</form>
		</td>
  		</tr>
		-->
	    </tbody>
	</table>

	<br>
 	<div style="text-align: center; width: 40%; float: left;">
     		<h3>
			<a href="index.php?option=com_vtigerproducts">
     				<img src="http://www.fosslabs.com/components/com_virtuemart/shop_image/ps_image/back.png" alt="Back" align="middle" border="0" height="32" width="32">
      				Continue Shopping
			</a>
		</h3>
 	</div>
  	<div style="text-align: center; width: 40%; float: left;">
     		<h3>
			<a href="index.php?option=com_vtigersalesorders&task=checkout">
     				<img src="http://www.fosslabs.com/components/com_virtuemart/shop_image/ps_image/forward.png" alt="Forward" align="middle" border="0" height="32" width="32">
      				Checkout
			</a>
		</h3>

 	</div>
 
 	<br style="clear: both;"><br>

	<? 
	}
	function list_all($orders)
	{
		echo "Hello";
	}
}
?>
