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
*/
function botvfield( $published, &$row, &$params, $page=0 ) {

	$msg = mosGetParam( $_REQUEST, 'msg', '');
	if($msg != "")
		echo "<center><font color='red' style='font-weight:bold'>".$msg."</font></center>";

	global $mosConfig_absolute_path,$fields,$tfields,$vForm;
	$msg = '';
	require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerForm.class.php");
	$vForm = new VtigerForm();

	$Itemid = mosGetParam( $_REQUEST, 'Itemid', '1');
	if(mosGetParam( $_POST, 'vt_module', '') != "" && mosGetParam( $_REQUEST, 'option', '') != "com_vtigerregistration") {
		$module = mosGetParam( $_POST, 'vt_module', '');
		$action = mosGetParam( $_POST, 'vt_action', '');
		$entityid = mosGetParam( $_POST, 'vt_entityid', '');

		//print_r($_POST);
		//exit();
		switch($action) {
			case 'BuyProduct':
				$qty = mosGetParam( $_POST, 'prd_qty', '1');
				$res = $vForm->BuyProduct($entityid);
				if($res == "failed") {
					echo "FAILED TO SAVE FORM";
					exit();
				}
				mosRedirect('index.php?option=com_vtigersalesorders&task=addProduct&productid='.$entityid.'&soid='.$res.'&qty='.$qty."&Itemid=".$Itemid);
			break;
			case 'RelateContact':
				$vt_relation_entityid = mosGetParam( $_POST, 'vt_relation_entityid', '');
				$vt_relation_module = mosGetParam( $_POST, 'vt_relation_module', '');
				$vt_entityid = mosGetParam( $_POST, 'vt_entityid', '');

				if($vt_relation_entityid == $vt_entityid)
					mosRedirect("index.php?option=com_vtigerregistration&task=login");
				else {
					$vForm->RelateContact($vt_entityid,$vt_relation_entityid,$vt_relation_module);
					$msg = "Successfully Added";
				}
				$res = $vt_relation_entityid;
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

		// Send an email with all the field details
		$mailto = mosGetParam( $_POST, 'vt_mailto', '');
		$mail_subject = mosGetParam( $_POST, 'vt_mail_subject', '');
		if($mailto != "" && $mail_subject != "") {
			$vForm->SendFormEmail($mailto,$mail_subject);
		}

		$redirect_site = mosGetParam( $_POST, 'vt_redirect_site', '');
		if($redirect_site != "")
			mosRedirect( $redirect_site );
		else
			mosRedirect('index.php?option=com_content&task=view&id='.mosGetParam( $_REQUEST, 'id', '').'&entityid='.$res.'&Itemid='.$Itemid.'&msg='.$msg);
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
			$module = $thisParams[1];

			if($module == "Products")
				$entityid = mosGetParam( $_REQUEST, 'productid', '' );
			if($module == "Events")
				$entityid = mosGetParam( $_REQUEST, 'eventid', '' );
			if($module == "Accounts")
				$entityid = mosGetParam( $_REQUEST, 'accountid', '' );

			if($my->id && $module == "Contacts") {
				require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerContact.class.php");
				$vtContact = new VtigerContact($my->id);
				$entityid = $vtContact->id;
			}

			if($entityid == "")
				$entityid = mosGetParam( $_REQUEST, 'entityid', '' );

			$out =  "<form enctype='multipart/form-data' name='vt_form' method='POST'>";
			$out .= "<input type='hidden' name='MAX_FILE_SIZE' value='1000000' />";
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
			switch($thisParams[1]) {
				case 'BuyProduct':
					if($thisParams[2] == "" || !isset($thisParams[2])) {
						$tval = $vForm->GetSingleFieldDetails(
							"Products",
							"qtyindemand",
							mosGetParam( $_REQUEST, 'productid', '' )
						);
						return "<input type='text' name='prd_qty' value='".$tval[0]["value"]."' size='3' /><input type='hidden' name='vt_action' value='BuyProduct' />";
					} else {
						return "<input type='text' name='prd_qty' value='".$thisParams[2]."' size='3' /><input type='hidden' name='vt_action' value='BuyProduct' />";
					}
				break;
				case 'RedirectSite':
					return "<input type='hidden' name='vt_redirect_site' value='".$thisParams[2]."' />";
				break;
				case 'SetColumn':
					return "<input type='hidden' name='vtiger_".$thisParams[2]."' value='".$thisParams[3]."' />";
				break;
				case 'RelateContact':
					$mod = $thisParams[2];
					$entityid = mosGetParam( $_REQUEST, 'entityid', '');
					if($mod == "Events") {
						if($entityid == '')
							$entityid = mosGetParam( $_REQUEST, 'eventid', '');
				  	} else if($mod == "Potentials") {
						if($entityid == '')
							$entityid = mosGetParam( $_REQUEST, 'potentialid', '');
					} else if($mod == "Campaigns") {
						if($entityid == '')
							$entityid = mosGetParam( $_REQUEST, 'campaignid', '');
					} else if($mod == "Accounts") {
						if($entityid == '')
							$entityid = mosGetParam( $_REQUEST, 'accountid', '');
					}
					$ret .= "<input type='hidden' name='vt_action' value='RelateContact' />";
					$ret .= "<input type='hidden' name='vt_relation_entityid' value='".$entityid."' />";
					return $ret."<input type='hidden' name='vt_relation_module' value='".$thisParams[2]."' />";
				break;
				case 'SendEmail':
					$ret .=  "<input type='hidden' name='vt_mailto' value='".$thisParams[2]."' />";
					return $ret."<input type='hidden' name='vt_mail_subject' value='".$thisParams[3]."' />";
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

	$picnum='all';
	if($thisParams[4] != "" && isset($thisParams[4]))
		$picnum=$thisParams[4];

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

	if(is_array($tfields)) {
	    foreach($tfields as $num=>$field) {
                if($field["module"] == $thisParams[0] 
			&& $field["columnname"] == $thisParams[1] 
			&& $field["viewtype"] == $thisParams[2]
			&& $field["showlabel"] == $thisParams[3]) {

			if (($field["columnname"] == "imagename" )
				&& $field["picnum"] != $thisParams[4])
					continue;
	
			if($field["viewtype"] == "edit")
                        	return $vForm->_buildEditField($field,$field["showlabel"]);
			else if($field["viewtype"] == "data") {
				if($field["columnname"] == "imagename" && $field["picnum"] != "all") {
					$values = explode("|",$field["value"]);
					return $vForm->GetCRMServer()."/".$values[($field["picnum"]-1)];
				}
                       		return $field["value"];
			} else 
                       		return $vForm->_buildDetailField($field,$field["showlabel"],$field["picnum"]);
		}
	    }
	}
}
?>
