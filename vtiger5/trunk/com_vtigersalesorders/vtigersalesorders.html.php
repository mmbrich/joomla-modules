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
    			<td>$<?php echo number_format($product["total"],'2','.',',');?></td>

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
	<? 
	}
	function shopping_footer($soid)
	{
	?>
	<br>
	<table border="0" cellpadding="4" cellspacing="2" width="100%">
   	    <tbody>
  		<tr>
    			<td colspan="8"><hr></td>
  		</tr>
	    </tbody>
	</table>
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
			<a href="index.php?option=com_vtigersalesorders&task=checkout&soid=<?php echo $soid;?>">
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
	function gather_info($fields,$vtigerForm,$soid)
	{
		global $my,$mainframe,$params;

		// url of current page that user will be returned to after login
		$url = mosGetParam( $_SERVER, 'REQUEST_URI', null );

		// if return link does not contain https:// & http:// and to url
		if ( strpos($url, 'http:') !== 0 && strpos($url, 'https:') !== 0 ) {
        		// check to see if url has a starting slash
        		if (strpos($url, '/') !== 0) {
                		// adding starting slash to url
                		$url = '/'. $url;
        		}

        		$url = mosGetParam( $_SERVER, 'HTTP_HOST', null ) . $url;

        		// check if link is https://
        		if ( isset( $_SERVER['HTTPS'] ) && ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) ) {
                		$return = 'https://'. $url;
        		} else {
        			// normal http:// link
                		$return = 'http://'. $url;
        		}
		} else {
        		$return = $url;
		}

		// converts & to &amp; for xtml compliance
		$return = str_replace( '&', '&amp;', $return );

		$registration_enabled   = $mainframe->getCfg( 'allowUserRegistration' );
		$login = $params->def( 'login', $return );
		$logout = $params->def( 'logout',$return );
		$message_login = $params->def( 'login_message', 0 );
		$message_logout = $params->def( 'logout_message', 0 );
		$name = $params->def( 'name', 1 );
		$greeting = $params->def( 'greeting',1 );
		$pretext = $params->get( 'pretext' );
		$posttext = $params->get( 'posttext' );
		$validate = josSpoofValue(1);

		?>
		<!-- RETURNING CUSTOMERS -->
		<fieldset>
                	<legend><span class="sectiontableheader">Returning Customers: Please Log In</span></legend>
                	<br>
			<form action="index.php" method="post" name="login" >
  			  <div style="width: 98%; text-align: center;">
				<div style="float: left; width: 30%; text-align: right;">
	  				<label for="username_login">Username:</label>
				</div>
    				<div style="float: left; margin-left: 2px; width: 60%; text-align: left;">
	  				<input id="username_login" name="username" class="inputbox" size="20" type="text">
				</div>
				<br><br>
    				<div style="float: left; width: 30%; text-align: right;">
	  				<label for="passwd_login">Password:</label> 
				</div>
				<div style="float: left; margin-left: 2px; width: 30%; text-align: left;">
	  				<input id="passwd_login" name="passwd" class="inputbox" size="20" type="password">
				</div>
				<div style="float: left; width: 30%; text-align: left;">
					<input type="submit" name="Submit" class="button" value="<?php echo _BUTTON_LOGIN; ?>" />
				</div>
				<br style="clear: both;">
  			  </div>
			
        			<input type="hidden" name="option" value="login" />
        			<input type="hidden" name="op2" value="login" />
        			<input type="hidden" name="lang" value="<?php echo $mosConfig_lang; ?>" />
        			<input type="hidden" name="return" value="<?php echo sefRelToAbs( $login ); ?>" />
        			<input type="hidden" name="message" value="<?php echo $message_login; ?>" />
        			<input type="hidden" name="<?php echo $validate; ?>" value="1" />
			</form>
                	<br>
            	</fieldset>
		<br>
		<!-- END RETURNING CUSTOMERS -->
		<!-- NEW CUSTOMERS -->
        	</fieldset>
        	<br><br>
        		<div class="sectiontableheader">New? Please Provide Your Billing Information</div>
        		<br>
			<form action="https://www.fosslabs.com/index.php" method="post" name="adminForm">
				<div style="width: 100%;">
					<div style="padding: 5px; text-align: center;"><strong>(* = Required)</strong></div>
   					<fieldset>
			     			<legend class="sectiontableheader">Bill To Information</legend>
						<?
						foreach($fields as $field) {
							if(!$field["show"] == 1)
								continue;
						?>
			     				<div style="float: left; width: 30%; text-align: right; vertical-align: bottom; font-weight: bold; padding-right: 5px;">
								<label for="<?php echo $field["field"];?>"><?php echo $field["name"];?></label> 
							</div>
      							<div style="float: left; width: 60%;">
								<?php echo $vtigerForm->_buildEditField($field,'');?>
								<!-- <input name="company" size="30" value="" class="inputbox" type="text"> -->
							</div>
						<? } ?>
					</fieldset>
				</div>
			</form>
		</fieldset>
	<?
	}
	function shipping_info($user_fields,$mailing_fields,$shipping_fields,$soid) {
	?>
			<form action="index.php" method="post" name="adminForm">
    				<input name="option" value="com_vtigersalesorders" type="hidden">
    				<input name="task" value="makePayment" type="hidden">
    				<input name="Itemid" value="1" type="hidden">
    				<input name="soid" value="<?php echo $soid;?>" type="hidden">
    				<h4>Please select a Shipping Address!</h4>
    				<table border="0" cellpadding="2" cellspacing="0" width="100%">
        			    <tbody>
					<?php
					foreach($user_fields as $field) {
					?>
        					<tr>
           						<td align="right" nowrap="nowrap" width="10%"><?php echo $field["fieldlabel"];?>: </td>
           						<td width="90%">
           							<?php echo $field["value"];?>
							</td>
        					</tr>
					<? } ?>
				    </tbody>
				</table><br>

				<!-- Customer Information --> 
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody><tr><td>
    				<table border="0" cellpadding="2" cellspacing="0" width="100%">
        			    <tbody>
					<tr class="sectiontableheader">
            					<th colspan="2" align="left">Billing Information</th>
        				</tr>
					<?php
					foreach($mailing_fields as $field) {
					?>
        					<tr>
           						<td align="right" nowrap="nowrap" width="10%"><?php echo preg_replace("/mailing/i","",$field["fieldlabel"]);?>: </td>
           						<td width="90%">
           							<?php echo $field["value"];?>
							</td>

        					</tr>
					<? } ?>

					<tr><td colspan='2'>&nbsp;</td></tr>
       		 			<tr>
						<td colspan="2" align="center">
							<a href="https://www.fosslabs.com/component/page,account.billing/next_page,checkout.index/option,com_virtuemart/Itemid,1/">
            						(Update Address)</a>
            					</td>
        				</tr>
    				    </tbody>
				</table>
    				<!-- customer information ends -->
        			<!-- Customer Ship To -->

				</td><td>
        			<table border="0" cellpadding="2" cellspacing="0" width="100%">
            			    <tbody>
					<tr class="sectiontableheader">
                				<th colspan="2" align="left">Shipping Information :
                				</th>
            				</tr>
					<?php
					foreach($shipping_fields as $field) {
					?>
        					<tr>
           						<td align="right" nowrap="nowrap" width="10%"><?php echo preg_replace("/other/i","",$field["fieldlabel"]);?>: </td>
           						<td width="90%">
           							<?php echo $field["value"];?>
							</td>

        					</tr>
					<? } ?>
					<tr><td colspan='2'>&nbsp;</td></tr>
       		 			<tr>
						<td colspan="2" align="center">
							<a href="https://www.fosslabs.com/component/page,account.billing/next_page,checkout.index/option,com_virtuemart/Itemid,1/">
            						(Update Address)</a>
            					</td>
        				</tr>
        			    </tbody>
				</table>
				</td></tr>
				</table>
        			<!-- END Customer Ship To -->
        			<br>
    				<br>
    				<table border="0" cellpadding="0" cellspacing="0" width="100%">
        			    <tbody>
					<tr>
            					<td>
							<div align="center">
                						<input class="button" name="submit" value="Next &gt;&gt;" type="submit">
                					</div>
                        			</td>
        				</tr>
    				    </tbody>
				</table>
			</form>
		<?
	}
	function do_payment()
	{
	?>
	<form action="https://www.fosslabs.com/index.php" method="post" name="adminForm">
    		<input name="checkout_next_step" value="99" type="hidden">
    		<input name="checkout_this_step" value="4" type="hidden">
    		<input name="zone_qty" value="" type="hidden">
    		<input name="option" value="com_virtuemart" type="hidden">
    		<input name="Itemid" value="1" type="hidden">
    		<input name="user_id" value="62" type="hidden">
    		<h4>Please select a Payment Method!</h4>

		<fieldset><legend><strong>Credit Card Payment</strong></legend>

		<table border="0" cellpadding="2" cellspacing="0" width="100%">
		    <tbody><tr>
		        <td colspan="2">
		        	<input name="payment_method_id" id="Credit Card" value="3" onchange="javascript: changeCreditCardList();" checked="checked" type="radio">
<label for="Credit Card">Credit Card</label><br>		        </td>
		    </tr>
		    <tr>
		        <td colspan="2"><strong>&nbsp;</strong></td>

		    </tr>
		    <tr>
		        <td align="right" nowrap="nowrap" width="10%">Credit Card Type:</td>
		        <td>
		        	<script language="javascript" type="text/javascript">
				<!--
				var originalOrder = '1';
				var originalPos = 'Credit Card';
				var orders = new Array();	// array in the format [key,value,text]
				orders[0] = new Array( 'Credit Card','VISA','Visa' );
				orders[1] = new Array( 'Credit Card','MC','MasterCard' );
				orders[2] = new Array( 'Credit Card','jcb','JCB' );
				orders[3] = new Array( 'Credit Card','australian_bc','Australian Bankcard' );
				function changeCreditCardList() { 
					var selected_payment = null;
      					for (var i=0; i<document.adminForm.payment_method_id.length; i++)
         					if (document.adminForm.payment_method_id[i].checked)
            						selected_payment = document.adminForm.payment_method_id[i].id;
						changeDynaList('creditcard_code',orders,selected_payment, originalPos, originalOrder);
				}
				//-->
				</script>
		       	 	<script language="Javascript" type="text/javascript"><!--
				writeDynaList( 'class="inputbox" name="creditcard_code" size="1"',
				orders, originalPos, originalPos, originalOrder );
				//-->
				</script>
			</td>
		</tr>
	    	<tr valign="top">
		        <td align="right" nowrap="nowrap" width="10%">
		        	<label for="order_payment_name">Name On Card:</label>
		        </td>
		        <td>
		        <input class="inputbox" id="order_payment_name" name="order_payment_name" value="" type="text">
		        </td>

		</tr>
		<tr valign="top">
		        <td align="right" nowrap="nowrap" width="10%">
		        	<label for="order_payment_number">Credit Card Number:</label>
		        </td>
		        <td>
		        <input class="inputbox" id="order_payment_number" name="order_payment_number" value="" type="text">
		        </td>

		</tr>
				    <tr valign="top">
		        <td align="right" nowrap="nowrap" width="10%">
		        	<label for="credit_card_code">Credit Card Security Code:</label></td>
		        <td>
		            <input class="inputbox" id="credit_card_code" name="credit_card_code" value="" type="text">
		        <span onmouseover=" this.T_TITLE='Tip!'; return escape( 'Please type in the three- or four-digit number on the back of your credit card (On the Front of American Express Cards)' );"><img src="https://www.fosslabs.com/images/M_images/con_info.png" align="middle" border="0">&nbsp;</span>		        </td>
		</tr>

		<tr>
		        <td align="right" nowrap="nowrap" width="10%">Expiration Date:</td>
		        <td><select class="inputbox" name="order_payment_expire_month" size="1">
<option value="0">Month</option>
<option value="01">January</option>
<option value="02">February</option>
<option value="03">March</option>
<option value="04">April</option>

<option value="05">May</option>
<option value="06">June</option>
<option value="07">July</option>
<option value="08">August</option>
<option value="09">September</option>
<option value="10">October</option>
<option value="11">November</option>
<option value="12">December</option>
</select>

/<select class="inputbox" name="order_payment_expire_year" size="1">
<option value="2006">2006</option>
<option value="2007">2007</option>
<option value="2008">2008</option>
<option value="2009">2009</option>
<option value="2010">2010</option>
<option value="2011">2011</option>
<option value="2012">2012</option>
</select>

		       </td>
		    </tr>
    	</tbody></table>
    </fieldset>
              
            <input name="page" value="checkout.index" type="hidden">
            <input name="func" value="checkoutprocess" type="hidden">
            <input name="ship_to_info_id" value="e383a3fa6c1eeeea9a287e1ab8a21b05" type="hidden">
            <input name="shipping_rate_id" value="" type="hidden">
                <br>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody><tr>
            <td>                <div align="center">
                <input class="button" name="submit" value="Next &gt;&gt;" type="submit">
                </div>
                        </td>
        </tr>
    </tbody></table>

</form>
	<?
	}
}
?>
