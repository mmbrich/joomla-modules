<?php
/**
* @version $Id: vfield.php,v 0.0.9 2006/04/09 01:13:30  Exp $
* @package Joomla
* @copyright (C) 2006 - 2011 FOSS Labs
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Joomla is Free Software
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botvfield' );

/**
* Display vtiger fields within your content or popup
*
* @param string vtiger module name (IE: Contacts)
* @param string columnname from vtiger (IE: firstname) 
* @param string view type (edit|detail).  edit will make the field editable,
* detail will wrap the output in a span
*/
function botvfield( $published, &$row, &$params, $page=0 ) {

	global $mosConfig_absolute_path,$fields,$tfields,$vForm;
	$msg = '';

	require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerForm.class.php");
	$vForm = new VtigerForm();

	if(mosGetParam( $_POST, 'vt_module', '') != "") {
		$module = mosGetParam( $_POST, 'vt_module', '');
		$action = mosGetParam( $_POST, 'vt_action', '');
		$entityid = mosGetParam( $_POST, 'vt_entityid', '');

		switch($action) {
			case 'BuyProduct':
				$res = $vForm->BuyProduct($module,$entityid);
				if($res == "failed") {
					echo "FAILED TO SAVE FORM";
					exit();
				}
				mosRedirect('index.php?option=com_salesorder&task=view&soid='.$res);
			break;
			case 'BuySubscription':
				$res = $vForm->BuySubscription($module,$entityid);
				if($res == "failed") {
					echo "FAILED TO SAVE FORM";
					exit();
				}
				mosRedirect('index.php?option=com_salesorder&task=view&soid='.$res);
			break;
			case 'BuyDownload':
				$res = $vForm->BuyDownload($module,$entityid);
				if($res == "failed") {
					echo "FAILED TO SAVE FORM";
					exit();
				}
				mosRedirect('index.php?option=com_salesorder&task=view&soid='.$res);
			break;
			default:
				$res = $vForm->SaveVtigerForm($module,$entityid);
				if($res == "failed") {
					echo "FAILED TO SAVE FORM";
					exit();
				}
				$msg = "Saved Form";
			break;
		}

		$redirect_site = mosGetParam( $_POST, 'vt_redirect_site', '');
		if($redirect_site != "")
			mosRedirect( $redirect_site );
		else
			mosRedirect('index.php?option=com_content&task=view&id='.mosGetParam( $_REQUEST, 'id', '').'&entityid='.$res.'&msg='.$msg);
	}

	// Special Commands
	$regex = "#{vfield}(Action|VFormStart|VFormEnd)(.*?){/vfield}#s";

	if (!$published) {
		$row->text = preg_replace( $regex, '', $row->text );
		return;
	}

	// perform the replacement of special commands
	$row->text = preg_replace_callback( $regex, 'botvfield_replacer', $row->text );

	// Put in javascript
	?>
                <script type='text/javascript'>
                function toggle_cb(field) {
			var Tel = document.getElementsByName(field);
			var el = Tel[0];
                	if(el.value === 'off')
				el.value = 'on';
			else
				el.value = 'off';
                }
        	</script>
	<?

	// define the regular expression for the rest of the fields
	$regex = "#{vfield}(.*?){/vfield}#s";

	// Create fields array
	preg_replace_callback( $regex, 'field_counter', $row->text );

	// Populate the needed info from the fields array
	$tfields = $vForm->GetMultipleFieldDetails($fields);

	// Do final replacement of module fields
	$row->text = preg_replace_callback( $regex, 'vfield_replacer', $row->text );

	return true;
}

// Handle special commands and actions
function botvfield_replacer ( &$matches ) {
	global $mosConfig_absolute_path, $my, $mosConfig_live_site,$vForm;
	$thisParams = explode("|",$matches[2]);

	switch($matches[1]) {
		// Start of a form
		case 'VFormStart':
			if(sizeof($thisParams) < 1) {
				$ret =  "Not enough parameters for VFormStart! You must have at least 3 parameters separated by "
						." \"|\" : e.g. {vfield}VFormStart|Module{/vfield} ".sizeof($thisParams);
				return $ret;
			}

			$entityid = mosGetParam( $_REQUEST, 'productid', '' );
			if($entityid == "")
				$entityid = mosGetParam( $_REQUEST, 'entityid', '' );

			if($entityid == "" && $my->id) {
				require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerContact.class.php");
				$vtContact = new VtigerContact($my->id);
				$entityid = $vtContact->id;
			}

			$out =  "<form name='vt_form' method='POST'>";
			$out .= "<input type='hidden' name='vt_module' value='".$thisParams[1]."' />";
			$out .= "<input type='hidden' name='vt_entityid' value='".$entityid."' />";
			return $out;
		break;

		// End of a form
		case 'VFormEnd':
			if(sizeof($thisParams) < 1) {
				$ret = "Not enough parameters for VFormEnd! You must have at least 1 parameters separated by "
						." \"|\" : e.g. {vfield}VFormEnd|Send Button Value{/vfield}";
				return $ret;
			}
			if($thisParams[1])
				return "<input type='submit' value='".$thisParams[1]."' class='button'></form>";
			else
				return "<input type='submit' value='Submit' class='button'></form>";
		break;

		// Special actions to take with the form
		case 'Action':
			if(sizeof($thisParams) < 1) {
				$ret = "Not enough parameters for VFormEnd! You must have at least 1 parameters separated by "
						." \"|\" : e.g. {vfield}Action|Action type{/vfield}";
				return $ret;
			}
			// If the amount is blank then we need to get it from the qtyindemand
			if($thisParams[2] == "" || !isset($thisParams[2])) {
				$tval = $vForm->GetSingleFieldDetails(
					"Products",
					"qtyindemand",
					mosGetParam( $_REQUEST, 'productid', '' )
				);
			}
			switch($thisParams[1]) {
				case 'BuyProduct':
					if($thisParams[2] == "" || !isset($thisParams[2])) {
						return "<input type='text' name='prd_qty' value='".$tval[0]["value"]."' size='3' /><input type='hidden' name='vt_action' value='BuyProduct' />";
					} else {
						return "<input type='text' name='prd_qty' value='".$thisParams[2]."' size='3' /><input type='hidden' name='vt_action' value='BuyProduct' />";
					}
				break;
				case 'BuySubscription':
					// If the amount is blank then we need to get it from the qtyindemand
					if($thisParams[2] == "" || !isset($thisParams[2])) {
						return "<input type='text' name='prd_qty' value='".$tval[0]["value"]."' size='3' /><input type='hidden' name='vt_action' value='BuySubscription' />";
					} else {
						return "<input type='text' name='prd_qty' value='".$thisParams[2]."' size='3' /><input type='hidden' name='vt_action' value='BuySubscription' />";
					}
				break;
				case 'RedirectSite':
					return "<input type='hidden' name='vt_redirect_site' value='".$thisParams[2]."' />";
				break;
				case 'SetColumn':
					return "<input type='hidden' name='vtiger_".$thisParams[2]."' value='".$thisParams[3]."' />";
				break;
			}
		break;
	}
}

// Create needed array to populate all records at once
function field_counter( &$matches ) {
	global $mosConfig_absolute_path, $my,$fields, $mosConfig_live_site;
	$thisParams = explode("|",$matches[1]);

	if (sizeof($thisParams) < 3) {
		$ret =  "Not enough parameters for vfield! You must have at least 3 parameters separated by "
				." \"|\" : e.g. {vfield}Module|columnname|(edit|detail)|showlabel{/vfield}";
		return $ret;
	}
	$module = $thisParams[0];
	$columnname = $thisParams[1];
	$viewtype = $thisParams[2];

	// Lets see if we can get the info we need from the joomla user
	if($my->id != "" && $my->id != 0 && $my->id && ($module == "Contacts" || $module == "Accounts")) {
		require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerContact.class.php");
		$vtContact = new VtigerContact($my->id);
		$entityid = $vtContact->id;
	} else if($module == "Products") {
		$entityid = mosGetParam( $_REQUEST, 'productid', '' );
	} else {
		$entityid = mosGetParam( $_REQUEST, 'entityid', '' );
	}

	if($thisParams[3] == "showlabel")
		$showlabel=true;
	else
		$showlabel=false;

	if($thisParams[4] != "" && isset($thisParams[4]))
		$picnum=$thisParams[4];
	else
		$picnum='all';

	$num = count($fields);
	$fields[$num] = array();
	$fields[$num]["module"] = $module;
	$fields[$num]["columnname"] = $columnname;
	$fields[$num]["viewtype"] = $viewtype;
	$fields[$num]["showlabel"] = $showlabel;
	$fields[$num]["entityid"] = $entityid;
	$fields[$num]["picnum"] = $picnum;
}

// Replace fields with populated field array
function vfield_replacer( &$matches ) {
	global $mosConfig_absolute_path, $my,$fields, $mosConfig_live_site,$tfields,$vForm;

	$thisParams = explode("|",$matches[1]);

	foreach($tfields as $num=>$field) {
                if($field["module"] == $thisParams[0] 
			&& $field["columnname"] == $thisParams[1] 
			&& $field["viewtype"] == $thisParams[2]
			&& $field["showlabel"] == $thisParams[3]) {
			//print_r($field);
			//echo "<br><br>";
			if($field["viewtype"] == "edit")
                        	return $vForm->_buildEditField($field,$field["showlabel"]);
			else
                       		return $vForm->_buildDetailField($field,$field["showlabel"],$field["picnum"]);
		}
	}
}
?>
