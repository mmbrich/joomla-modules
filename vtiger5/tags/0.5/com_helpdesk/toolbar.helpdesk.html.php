<?php
/**
* @version $Id: toolbar.helpdesk.html.php 85 2006-07-10 23:12:03 mmbrich $
* @package Joomla
* @subpackage Helpdesk
* @copyright Copyright (C) 2006 FOSS labs. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Helpdesk
*/
class TOOLBAR_helpdesk {

	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::save();
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::spacer();
		mosMenuBar::help( 'screen.helpdesk' );
		mosMenuBar::endTable();
	}
}
?>
