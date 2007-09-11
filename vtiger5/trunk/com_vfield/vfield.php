<?php
/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_absolute_path;

require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerForm.class.php");
$vForm = new VtigerForm();
$Itemid = mosGetParam( $_REQUEST, 'Itemid', $vForm->defaultItemid);
$pageid = mosGetParam( $_REQUEST, 'id', '');

$module = mosGetParam($_REQUEST, 'vt_module', '');
$entityid = mosGetParam($_REQUEST, 'vt_entityid', '');


switch($task) {
       	case 'BuyProduct':
        	$qty = mosGetParam( $_REQUEST, 'prd_qty', '1');
                $res = $vForm->BuyProduct($entityid);
                if($res == "failed") {
                        echo "FAILED TO SAVE FORM";
                        exit();
                }
               	mosRedirect(sefRelToAbs('index.php?option=com_vtigersalesorders&Itemid='.$Itemid.'&task=addProduct&productid='.$entityid.'&soid='.$res.'&prd_qty='.$qty));
        break;
        case 'RelateContact':
                $vt_relation_entityid = mosGetParam( $_REQUST, 'vt_relation_entityid', '');
                $vt_relation_module = mosGetParam( $_REQUEST, 'vt_relation_module', '');
                $vt_entityid = mosGetParam( $_REQUEST, 'vt_entityid', '');

		if($vt_relation_entityid == $vt_entityid)
                        mosRedirect(sefRelToAbs("index.php?option=com_vtigerregistration&task=login"));
                else {
                        $vForm->RelateContact($vt_entityid,$vt_relation_entityid,$vt_relation_module);
                        $msg = "Successfully Added";
                }
                $res = $vt_relation_entityid;
        break;
	case 'SaveForm':
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
$mailto = mosGetParam( $_REQUEST, 'vt_mailto', '');
$mail_subject = mosGetParam( $_REQUEST, 'vt_mail_subject', '');
if($mailto != "" && $mail_subject != "") {
        $vForm->SendFormEmail($mailto,$mail_subject);
}

$redirect_site = mosGetParam( $_REQUEST, 'vt_redirect_site', '');
if($redirect_site != "") {
	if(preg_match("/^http/",$redirect_site))
       		mosRedirect( $redirect_site."&entityid=".$res );
	else
       		mosRedirect( sefRelToAbs($redirect_site."&entityid=".$res) );
} else
       	mosRedirect(sefRelToAbs('index.php?option=com_content&task=view&id='.$pageid.'&entityid='.$res.'&Itemid='.$Itemid.'&msg='.$msg));

?>
