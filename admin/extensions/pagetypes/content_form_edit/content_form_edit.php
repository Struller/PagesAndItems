<?php
/**
* @version		2.1.1
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
require_once(dirname(__FILE__).'/../../../includes/extensions/pagetype.php');
//
class PagesAndItemsExtensionPagetypeContentFormEdit extends PagesAndItemsExtensionPagetype
{
	function onGetPageTypeIcons($icons,$pageType,$dirIcons, $component)
	{
		if($pageType != 'content_form_edit')
		{
			return false;
		}
		$icons = parent::onGetPageTypeIcons($icons,$pageType,$dirIcons, $component);
		return true;
	}
}

?>