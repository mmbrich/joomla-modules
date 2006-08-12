<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

#check if the bot exist
if (file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php')) {
	require_once('components/com_vtigerregistration/vtiger/VTigerProduct.class.php');
	$vProduct = new VtigerProduct();
} else {
	echo "You should install bot_vconnection if you want something to happen here ;)";
	flush();exit();
}
require_once( $mainframe->getPath( 'front_html' ) );

switch($task) {
	case 'List':
	default:
		$category = mosGetParam( $_GET, 'category' , '');
		$list = $vProduct->ListProducts($category);
		HTML_product::listProducts($option,$list,$category);
	break;
}
?>