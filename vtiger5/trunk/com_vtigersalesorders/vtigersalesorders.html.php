<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

class HTML_vtigersalesorders {
	function view($order)
	{
		global $my;
		$Itemid = mosGetParam($_REQUEST, 'Itemid', '');
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
			<th><?php echo _NAME;?></th>
			<th><?php echo _PRICE;?></th>
			<th><?php echo _QTY;?></th>
			<th><?php echo _ADJ;?></th>
			<th><?php echo _TAX;?></th>
			<th><?php echo _SUBTOTAL;?></th>
			<th colspan="2" align="center"><?php echo _UPDATE;?></th>
  		</tr>
		<? 
		foreach($order["products"] as $product) { 
		$total = ($product["total"]*$product["quantity"]);
		?>
  			<tr class="sectiontableentry1" valign="top">
			<form action="index.php" method="POST"></form>
			<td>
				<a href="<?php echo sefRelToAbs($product["website"]."&Itemid=".$Itemid."&productid=".$product["productid"]);?>">
				<strong><?php echo $product["name"];?></strong>
				</a><br>
			</td>
    			<td>$<?php echo number_format($product["total"],'2','.',',');?></td>

    			<td><input title="<?php echo _CART_QTY_UPDATE;?>" class="inputbox" size="4" maxlength="4" name="quantity" value="<?php echo $product["quantity"];?>" type="text" id="quantity_<?php echo $product["productid"];?>"></td>

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
        				<input name="update" title="<?php echo _CART_QTY_UPDATE;?>" src="components/com_vtigersalesorders/images/edit_f2.gif" alt="Update" border="0" type="image" onclick="document.getElementById('quan_<?php echo $product["productid"];?>').value = document.getElementById('quantity_<?php echo $product["productid"];?>').value;" />
      				</form>
      			</td>
    			<td>
				<!-- DELETE PRODUCT FROM SO FORM -->
				<form action="index.php" method="post" name="delete">
        				<input name="option" value="com_vtigersalesorders" type="hidden">
        				<input name="task" value="removeProduct" type="hidden">
        				<input name="soid" value="<?php echo $order["salesorderid"];?>" type="hidden">
        				<input name="productid" value="<?php echo $product["productid"];?>" type="hidden">
      					<input name="delete" title="<?php echo _CART_DELETE;?>" src="components/com_vtigersalesorders/images/delete_f2.gif" alt="<?php echo _CART_DELETE;?>" border="0" type="image">
      				</form>
			</td>
  		</tr>
		<? 
		} // END FOREACH 
		//print_r($order);
		?>
		<!--Begin of SubTotal, Tax, Shipping, Coupon Discount and Total listing -->

  		<tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _SUBTOTAL;?>:</td> 
    			<td colspan="3">$<?php echo number_format($order["subtotal"],'2','.',',');?></td>
  		</tr>

		<!-- Adjustments -->
		<? if ($order["txtAdjustment"] != "") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _ADJUSTMENT;?>: </td>
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
    			<td colspan="5" align="right"><?php echo _DISCOUNT_PERCENT;?>: </td>
    			<td colspan="3">
				<font color='red'><?php echo number_format($order["discount_percent"],'2','.',',');?></font>%
    			</td>

  		    </tr>
		<? } else if ($order["discount_amount"] != "") {?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _DISCOUNT_AMOUNT;?>: </td>
    			<td colspan="3">
				$<font color='red'><?php echo number_format($order["discountamount"],'2','.',',');?></font>
    			</td>

  		    </tr>
		<? } ?>

		<!-- Shipping and Handling -->
		<? if ($order["s_h_amount"] != "" && $order["s_h_amount"] != "0.000") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _SHIP_HANDLE;?>: </td>
    			<td colspan="3">
				$<?php echo number_format($order["s_h_amount"],'2','.',',');?>
    			</td>
  		    </tr>
		<? } ?>


		<!-- S&H Taxes -->
		<? if ($order["sh_tax1"] != "" && ($order["s_h_amount"] != "" && $order["s_h_amount"] != "0")) {  // VAT ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _SHIP_HANDLE_TAX;?>: </td>
    			<td colspan="3">
				$<?php echo number_format((($order["s_h_amount"] * $order["sh_tax1"])/100),'2','.',',');?>
    			</td>
  		    </tr>
		<? } else if ($order["sh_tax2"] != "" && ($order["s_h_amount"] != "" && $order["s_h_amount"] != "0")) { //SALES ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _SHIP_HANDLE_TAX;?>: </td>
    			<td colspan="3">
				$<?php echo number_format((($order["s_h_amount"] * $order["sh_tax2"])/100),'2','.',',');?>
    			</td>
  		    </tr>
		<? } else if ($order["sh_tax3"] != "" && ($order["s_h_amount"] != "" && $order["s_h_amount"] != "0")) { //SERVICE ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _SHIP_HANDLE_TAX;?>: </td>
				$<?php echo number_format((($order["s_h_amount"] * $order["sh_tax3"])/100),'2','.',',');?>
    			<td colspan="3">
    			</td>
  		    </tr>
		<? } ?>

		<!-- Group Taxes -->
		<? if ($order["taxtype"] == "group" && $order["txtTax"] != "") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _TAXES;?>: </td>
    			<td colspan="3">
				$<?php echo number_format($order["txtTax"],'2','.',',');?>
    			</td>
  		    </tr>
		<? } else if ($order["taxtype"] == "group") { ?>
  		    <tr class="sectiontableentry2">
    			<td colspan="5" align="right"><?php echo _TAXES;?>: </td>
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
    			<td colspan="5" align="right"><?php echo _TOTAL;?>: </td>
    			<td colspan="3">
				<strong>$<?php echo number_format($order["grandtotal"],'2','.',',');?></strong>
    			</td>

  		</tr>
		<!-- OUT FOR NOW
  		<tr>
		<td width="100%" colspan="7">If you have a coupon code, please enter it below:<br>
    		<form action="index.php" method="POST">
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
	function shopping_footer($soid,$Itemid)
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
			<a href="<?php echo sefRelToAbs('index.php?option=com_vtigerproducts&Itemid='.$Itemid);?>">
     				<img src="components/com_vtigersalesorders/images/back.png" alt="Back" align="middle" border="0" height="32" width="32">
      				<?php echo _CONTINUE_SHOPPING;?>
			</a>
		</h3>
 	</div>
  	<div style="text-align: center; width: 40%; float: left;">
     		<h3>
			<a href="<?php echo sefRelToAbs('index.php?option=com_vtigersalesorders&task=checkout&soid='.$soid.'&Itemid='.$Itemid);?>">
     				<img src="components/com_vtigersalesorders/images/forward.png" alt="Forward" align="middle" border="0" height="32" width="32">
      				<?php echo _CHECKOUT;?>
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
                	<legend><span class="sectiontableheader"><?php echo _RETURN_CUSTOMER;?></span></legend>
                	<br>
			<form action="index.php" method="post" name="login" >
  			  <div style="width: 98%; text-align: center;">
				<div style="float: left; width: 30%; text-align: right;">
	  				<label for="username_login"><?php echo _USERNAME;?>:</label>
				</div>
    				<div style="float: left; margin-left: 2px; width: 60%; text-align: left;">
	  				<input id="username_login" name="username" class="inputbox" size="20" type="text">
				</div>
				<br><br>
    				<div style="float: left; width: 30%; text-align: right;">
	  				<label for="passwd_login"><?php echo _PASSWORD;?>:</label> 
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
        		<div class="sectiontableheader"><?php echo _NEW_CUSTOMER;?></div>
        		<br>
			<form action="index.php" method="post" name="adminForm">
				<div style="width: 100%;">
					<div style="padding: 5px; text-align: center;"><strong><?php echo _REQUIRED;?></strong></div>
   					<fieldset>
			     			<legend class="sectiontableheader"><?php echo _BILL_TO_INFO;?></legend>
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
	function shipping_info($user_fields,$mailing_fields,$shipping_fields,$soid,$Itemid='') {
		global $mainframe;
	?>
			<form action="<?php echo preg_replace("/http/i","https",$mainframe->getCfg('live_site'));?>/index.php" method="post" name="adminForm">
    				<input name="option" value="com_vtigersalesorders" type="hidden">
    				<input name="task" value="getPaymentInfo" type="hidden">
    				<input name="Itemid" value="<?php echo $Itemid;?>" type="hidden">
    				<input name="soid" value="<?php echo $soid;?>" type="hidden">
    				<h4><?php echo _SELECT_SHIPPING;?></h4>
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
							<a href="<?php echo sefRelToAbs('index.php?option=com_vtigersalesorders&Itemid='.$Itemid.'&task=updateAddress&type=mailing&soid='.$soid);?>">
            						(<?php echo _UPDATE_ADDY;?>)</a>
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
							<a href="<?php echo sefRelToAbs('index.php?option=com_vtigersalesorders&Itemid='.$Itemid.'&task=updateAddress&type=other&soid='.$soid);?>">
            						(<?php echo _UPDATE_ADDY;?>)</a>
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
                						<input class="button" name="submit" value="<?php echo _NEXT;?> &gt;&gt;" type="submit">
                					</div>
                        			</td>
        				</tr>
    				    </tbody>
				</table>
			</form>
		<?
	}
	function update_addy($fields,$type,$Itemid)
	{
		global $vtigerForm;
	?>
                <form action="index.php" method="post" name="vt_form">
                	<input name="option" value="com_vtigersalesorders" type="hidden">
                        <input name="task" value="checkout" type="hidden">
                        <input name="Itemid" value="<?php echo $Itemid;?>" type="hidden">
                        <input name="soid" value="<?php echo mosGetParam( $_REQUEST, 'soid', '0' );?>" type="hidden">
			<input name="vt_module" value="Contacts" type="hidden">
			<input name="vt_entityid" value="<?php echo $fields[0]["entityid"];?>" type="hidden">
			<input name="update_address" value="<?php echo $type;?>" type="hidden">

                        <h4>Update Address Information.</h4>
                        <table border="0" cellpadding="2" cellspacing="0" width="100%">
                         	<tbody>
                                 	<?php
                                        foreach($fields as $field) {
                                        ?>
                                                <tr>
                                                        <td align="right" nowrap="nowrap" width="10%"><?php echo $field["fieldlabel"];?>: </td>
                                                        <td >
								<?php echo $vtigerForm->_buildEditField($field,'');?>
                                                        </td>
                                                </tr>
                                        <? } ?>
					<tr>
						<td colspan='2'>
							<input type="submit" name="update" value="Update Information" class="button">
						</td>
					</tr>
                                </tbody>
                        </table>
		</form>
		<br>
	<?
	}
	function get_paymentinfo($cc_fields,$ec_fields,$Itemid)
	{
	global $mainframe;
	?>
	<script language="JavaScript" src="<?php echo $mainframe->getCfg('live_site').'/components/com_vtigerregistration';?>/vtiger/prototype.js" type="text/javascript"></script>
	<script type="text/javascript">
	function changePaymentInfo(element) {
		if(element.id == "Credit Card") {
			var vis_els = document.getElementsByClassName("cc_view");
			var invis_els = document.getElementsByClassName("ec_view");
		} else if(element.id == "ECheck") {
			var vis_els = document.getElementsByClassName("ec_view");
			var invis_els = document.getElementsByClassName("cc_view");
		}

		for( i = 0 ; i < invis_els.length ; i++ ) {
			invis_els[i].style.display = "none";
		}
		for( i = 0 ; i < vis_els.length ; i++ ) {
			vis_els[i].style.display = "";
		}
	}
	</script>
	<form action="index.php" method="post" name="adminForm">
    		<input name="option" value="com_vtigersalesorders" type="hidden">
    		<input name="task" value="makePayment" type="hidden">
    		<input name="Itemid" value="<?php echo $Itemid;?>" type="hidden">
    		<input name="soid" value="<? echo  mosGetParam( $_REQUEST, 'soid', '0' );?>" type="hidden">
    		<h4>Please select a Payment Method!</h4>

		<fieldset><legend><strong><?php echo _PAYMENT_DETAILS;?></strong></legend>

		<table border="0" cellpadding="2" cellspacing="0" width="100%">
		    <tbody><tr>
		        <td colspan="2">
		        	<input name="payment_method_id" id="Credit Card" value="1" onchange="javascript: changePaymentInfo(this);" checked="checked" type="radio">
				<label for="Credit Card"><?php echo _CREDIT_CARD;?></label>		        
		        	<input name="payment_method_id" id="ECheck" value="2" onchange="javascript: changePaymentInfo(this);" type="radio">
				<label for="ECheck"><?php echo _E_CHECK;?></label><br>		        
			</td>
		    </tr>
                    <?php
                    foreach($cc_fields as $field) {
			global $vtigerForm;
                    ?>
                             <tr class="cc_view">
                                      <td align="right" nowrap="nowrap" width="10%"><?php echo $field["fieldlabel"];?>: </td>
                                      <td >
						<?php echo $vtigerForm->_buildEditField($field,'');?>
                                      </td>
                             </tr>
                    <? 
		    } 
                    foreach($ec_fields as $field) {
                    ?>
                             <tr class="ec_view" style="display:none">
                                      <td align="right" nowrap="nowrap" width="10%"><?php echo $field["fieldlabel"];?>: </td>
                                      <td >
						<?php echo $vtigerForm->_buildEditField($field,'');?>
                                      </td>
                             </tr>
                    <? } ?>
    	</tbody></table>
    </fieldset>
              

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody><tr>
            <td>                <div align="center">
                <input class="button" name="submit" value="<?php echo _NEXT;?> &gt;&gt;" type="submit">
                </div>
                        </td>
        </tr>
    </tbody></table>
</form>
	<?
	}
}
?>
