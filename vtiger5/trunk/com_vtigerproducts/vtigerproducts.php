<?php
// no direct access
defined('_VALID_MOS') or die('Restricted access');

// check if the bot exist
if (file_exists($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php')) {
	require_once('components/com_vtigerregistration/vtiger/VTigerProduct.class.php');
	$vProduct = new VtigerProduct();
} else {
	echo "You should install bot_vconnection if you want something to happen here ;)";
	flush();exit();
}

// Get the right language if it exists
if (file_exists($mosConfig_absolute_path.'/components/com_vtigerproducts/languages/vtigerproducts_'.$mosConfig_lang.'.php')) {
    include($mosConfig_absolute_path.'/components/com_vtigerproducts/languages/vtigerproducts_'.$mosConfig_lang.'.php');
} else {
    include($mosConfig_absolute_path.'/components/com_vtigerproducts/languages/vtigerproducts_english.php');
}
require_once( $mainframe->getPath( 'front_html' ) );

switch($task) {
	case 'List':
	default:
		global $mosConfig_absolute_path;
		$q = "SELECT name,value FROM #__vtiger_portal_configuration "
			." WHERE name LIKE 'product_%'";
		$database->setQuery($q);
		$configs = $database->loadObjectList();
		foreach($configs as $config) {
			$conf[$config->name] = $config->value;
		}
		$category = mosGetParam( $_REQUEST, 'category' , '');
		$list = $vProduct->ListProducts($category);

		$limit = mosGetParam( $_REQUEST, 'limit' , '10');
		if($limit >= count($list))
			$limit=count($list);

		if($conf["product_show_pagination"] != "on")
			$limit=count($list);
			

		$limit_start = mosGetParam( $_REQUEST, 'limitstart' , '0');
        	require_once( $mosConfig_absolute_path.'/includes/pageNavigation.php' );
        	$pageNav = new mosPageNav( count($list), $limit_start, $limit );
		HTML_product::listProducts( $option, $list, $category, $conf, $limit, $limit_start, $pageNav, $vProduct );
	break;
}
?>
