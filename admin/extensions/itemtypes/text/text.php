<?php
/**
* @version		2.1.5
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
require_once(dirname(__FILE__).'/../../../includes/extensions/itemtype.php');
//TEXT
class PagesAndItemsExtensionItemtypeText extends PagesAndItemsExtensionItemtype
{
	/*
	function onItemtypeDisplay_config_form(&$itemtypeHtml,$item_type)
	{

	}
	*/
	function onGetTables(&$tables,$item_type,$item_id)
	{
		if($item_type != 'text')
		{
			return false;
		}

		//tables are:
		$table->name = 'content';
		$table->reference_id = 'id';
		$table->reference_id_value = $item_id;
		$tables[] = $table;
		$table = null;

		return true;
	}

	function XonItemtypeDisplay_item_edit(&$itemtypeHtml,$item_type,$item_id,$text,$itemIntroText,$itemFullText)
	{
		if($item_type != 'text')
		{
			return false;
		}
		$html = '';
	
		$editor =& JFactory::getEditor();
		$html .= $editor->display( 'text',  $text , '100%', '550', '85', '20' );

		$itemtypeHtml->text = $itemtypeHtml->text.$html;
		return true;
	}
}

?>