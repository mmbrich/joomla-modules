<?php
/**
* @version 1.1 $
* @package vconnection
* @copyright (C) 2006 Pierre-André Vullioud www.paimages.ch            
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once('mambots/system/vt_classes/VTigerConnection.class.php');

$_MAMBOTS->registerFunction( 'onStart', 'VConnectionInit' );

	// pull query data from class variable
		
	
function VConnectionInit() {
	$conn = new VTigerConnection();
	$ret = $conn->CheckConnection();
	if($conn->CheckConnection())
		return;
	else {
		echo "Connection to vtiger failed";
		exit();
	}
}
?>
