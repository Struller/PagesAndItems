<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}
require_once(dirname(__FILE__).'/../../../includes/extensions/pagetype.php');
//
class PagesAndItemsExtensionPagetypeUrl extends PagesAndItemsExtensionPagetype
{
	function onGetPageTypeIcons($icons,$pageType,$dirIcons, $component)
	{
		if($pageType != 'url')
		{
			return false;
		}
		$icons = parent::onGetPageTypeIcons($icons,$pageType,$dirIcons, $component);
		return true;
	}
}
?>