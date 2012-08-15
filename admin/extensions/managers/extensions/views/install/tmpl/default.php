<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}
//$path = str_replace(DS,'/',str_replace(JPATH_SITE,'',JPATH_COMPONENT_ADMINISTRATOR));

if ($this->showMessage)
{
	echo $this->loadTemplate('message');
}
//echo '<div id="page_content" class="width-100 fltlft">';
echo $this->loadTemplate('form');
//echo '</div>';
echo '<div class="width-100 fltlft">';
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
echo '</div>';
//
?>
