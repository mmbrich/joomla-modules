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
global $mosConfig_absolute_path;
	// define the regular expression for the bot
	$regex = "#{vfield}(.*?){/vfield}#s";

	if (!$published) {
		$row->text = preg_replace( $regex, '', $row->text );
		return;
	}

	// perform the replacement
	$row->text = preg_replace_callback( $regex, 'botvfield_replacer', $row->text );

	return true;
}

function botvfield_replacer ( &$matches ) {
	global $mosConfig_absolute_path,$my, $_MAMBOTS;
	require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerField.class.php");
	$vField = new VtigerField();

	global $mosConfig_live_site;
	$thisParams = explode("|",$matches[1]);
	
	if (sizeof($thisParams) < 3 && $thisParams[0] != "VFormStart" && $thisParams[0] != "VFormEnd") 
		return "Not enough parameters for vfield! You must have at least 3 parameters separated by \"|\" : e.g. {vfield}Module|columnname|(edit|detail)|showlabel{/vfield}";
	else if(sizeof($thisParams) != 3 && $thisParams[0] == "VFormStart")
		return "Not enough parameters for VFormStart! You must have at least 3 parameters separated by \"|\" : e.g. {vfield}VFormStart|Module|Form Name{/vfield}";
	else if(sizeof($thisParams) < 1 && $thisParams[0] == "VFormEnd")
		return "Not enough parameters for VFormEnd! You must have at least 1 parameters separated by \"|\" : e.g. {vfield}VFormEnd|Send Button Value{/vfield}";

	// Regular Fields
	if($thisParams[0] != "VFormStart" && $thisParams[0] != "VFormEnd") {
		$module = $thisParams[0];
		$columnname = $thisParams[1];
		$viewtype = $thisParams[2];

		if($thisParams[3] == "showlabel")
			$showlabel=true;
		else
			$showlabel=false;

		// Check if we are a landing page.
		$entityid = mosGetParam( $_GET, 'entity', '' );

		// If not a landing page, see if we are authenticated and can personalize from that ID
		if($entityid == "") {
			if($my->id) {
				require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerContact.class.php");
				$vtContact = new VtigerContact($my->id);
				$entityid = $vtContact->id;
			}
		}

		$tmp =  $vField->CreateFieldHTML($module,$columnname,$viewtype,$showlabel,$entityid);
		return $tmp;

	// Form Start
	} else if($thisParams[0] == "VFormStart") {
		$out =  "<form name='Vtiger_".$thisParams[1]."_".$thisPararms[2]."' method='POST'>";
		$out .= "<input type='hidden' name='option' value='com_content' />";
		$out .= "<input type='hidden' name='return_option' value='' />";
		return $out;

	// Form End
	} else if($thisParams[0] == "VFormEnd") {
		if($thisParams[1])
			return "<input type='submit' value='".$thisParams[1]."' class='button'></form>";
		else
			return "<input type='submit' value='Submit' class='button'></form>";
	}
}
?>
