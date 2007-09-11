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

	global $mosConfig_absolute_path,$fields,$tfields,$vForm,$Itemid,$task,$pageid;
	$task="SaveForm";
	$msg = '';
	require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerForm.class.php");
	$vForm = new VtigerForm();
	$Itemid = mosGetParam( $_REQUEST, 'Itemid', $vForm->defaultItemid);
	$pageid = mosGetParam( $_REQUEST, 'id', '');

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
			try {
				var Tel = document.getElementsByName(field);
				var el = Tel[0];
                		if(el.value === 'off')
					el.value = 'on';
				else
					el.value = 'off';
			} catch(e) {}
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
	global $mosConfig_absolute_path, $my, $mosConfig_live_site,$vForm,$Itemid,$task,$pageid;
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

			$out = form_validate();
			$out .=  "<form enctype='multipart/form-data' name='vt_form' method='POST' action='index.php'>";
			$out .= "<input type='hidden' name='MAX_FILE_SIZE' value='1000000' />";
			$out .= "<input type='hidden' name='option' value='com_vfield' />";
			$out .= "<input type='hidden' name='Itemid' value='".$Itemid."' />";
			$out .= "<input type='hidden' name='id' value='".$pageid."' />";

			$out .= "<input type='hidden' id='vt_module' name='vt_module' value='".$thisParams[1]."' />";
			$out .= "<input type='hidden' id='vt_entityid' name='vt_entityid' value='".$entityid."' />";
			return $out;
		break;

		// End of a form
		case 'VFormEnd':
			$entityid = mosGetParam( $_REQUEST, 'vt_entityid', '' );
			if(sizeof($thisParams) < 1) {
				$ret = "Not enough parameters for VFormEnd! You must have at least 1 parameters separated by "
						." \"|\" : e.g. {vfield}VFormEnd|Send Button Value{/vfield}";
				return $ret;
			}
			if($thisParams[1]) {
				$out = "<input type='hidden' name='task' value='".$task."' />";
				$out .=  "<input type='submit' value='".$thisParams[1]."' class='button' onclick='return validate_vtiger_form(this);' /></form>";
				if($thisParams[2]) {
					$out .= "<input type='hidden' name='vt_redirect_site' value='".$thisParams[2]."' />";
				}
				return $out;
			} else {
				$out = "<input type='hidden' name='task' value='".$task."' />";
				$out .= "<input type='submit' value='Submit' class='button' onclick='return validate_vtiger_form(this);' /></form>";
				return $out;
			}
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
					$task='BuyProduct';
					if($thisParams[2] == "" || !isset($thisParams[2])) {
						$tval = $vForm->GetSingleFieldDetails(
							"Products",
							"qtyindemand",
							mosGetParam( $_REQUEST, 'productid', '' )
						);
						return "<input type='text' id='vt_prd_qty' name='prd_qty' value='".$tval[0]["value"]."' size='3' class='inputbox' /><input type='hidden' name='task' value='BuyProduct' />";
					} else {
						return "<input type='text' id='vt_prd_qty' name='prd_qty' value='".$thisParams[2]."' size='3' class='inputbox' /><input type='hidden' name='task' value='BuyProduct' />";
					}
				break;
				case 'RedirectSite':
					$task='SaveForm';
					return "<input type='hidden' name='vt_redirect_site' value='".$thisParams[2]."' />";
				break;
				case 'SetColumn':
					$task='SaveForm';
					return "<input type='hidden' name='vtiger_".$thisParams[2]."' value='".$thisParams[3]."' />";
				break;
				case 'RelateContact':
					$task='RelateContact';
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
					$ret .= "<input type='hidden' name='task' value='RelateContact' />";
					$ret .= "<input type='hidden' name='vt_relation_entityid' value='".$entityid."' />";
					return $ret."<input type='hidden' name='vt_relation_module' value='".$thisParams[2]."' />";
				break;
				case 'SendEmail':
					$task='SendEmail';
					$ret .=  "<input type='hidden' name='vt_mailto' value='".$thisParams[2]."' />";
					return $ret."<input type='hidden' name='vt_mail_subject' value='".$thisParams[3]."' />";
				break;
			}
		break;
	}
}

// Create needed array to populate all records at once
function field_counter( &$matches ) {
	global $mosConfig_absolute_path, $my,$fields, $mosConfig_live_site,$Itemid,$task;
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

	if(trim($thisParams[5]) == "required")
		$required='true';
	else
		$required='false';

	$num = count($fields);
	$fields[$num] = array();
	$fields[$num]["module"] = $module;
	$fields[$num]["columnname"] = $columnname;
	$fields[$num]["viewtype"] = $viewtype;
	$fields[$num]["showlabel"] = $showlabel;
	$fields[$num]["entityid"] = $entityid;
	$fields[$num]["picnum"] = $picnum;
	$fields[$num]["required"] = $required;
}

// Replace fields with populated field array
function vfield_replacer( &$matches ) {
	global $mosConfig_absolute_path, $my,$fields, $mosConfig_live_site,$tfields,$vForm,$Itemid,$task;

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
                        	return $vForm->_buildEditField($field,$field["showlabel"],$field["required"]);
			else if($field["viewtype"] == "data") {
				if($field["columnname"] == "imagename" && $field["picnum"] != "all") {
					$values = explode("|",$field["value"]);
					return $vForm->GetImagePath($values[($field["picnum"]-1)]);
				}
                       		return $field["value"];
			} else 
                       		return $vForm->_buildDetailField($field,$field["showlabel"],$field["picnum"]);
		}
	    }
	}
}

function form_validate() {
?>
<script language="javascript" type="text/javascript" src="components/com_vtigerregistration/vtiger/prototype.js"></script>
<script type="text/javascript">
function validate_vtiger_form(form) {
	var els = document.getElementsByClassName("required");
	var ret = true;
	try {
		for(var i=0;i<els.length;i++) {
			if(els[i].childNodes[0].value == "" || typeof(els[i].childNodes[0].value) === "undefined") {
				ret = false;
				els[i].childNodes[0].style.border = "1px solid red";
			} else {
				els[i].childNodes[0].style.border = "1px solid gray";
				els[i].childNodes[0].className = "inputbox";
			}
		}
		if(!ret)
			alert("Please fill out all required fields");
	} catch(e) {}
	
	return ret;
}
</script>
<?
}
?>
