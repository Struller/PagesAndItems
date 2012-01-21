<?php
/**
* @version		2.1.3
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
//HTML
class PagesAndItemsExtensionItemtypeHtml extends PagesAndItemsExtensionItemtype
{
	/*
	function onItemtypeDisplay_config_form(&$itemtypeHtml,$item_type)
	{

	}
	*/
	function onGetTables(&$tables,$item_type,$item_id)
	{
		if($item_type != 'html')
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

	function onItemtypeDisplay_item_edit_articletext(&$articletextChanged,$form,$item_type)
	{
		if($item_type != 'html')
		{
			return true;
		}
		$articletextChanged = true;
		$form->setFieldAttribute('articletext','type','textarea');
		$form->setFieldAttribute('articletext','cols','50');
		$form->setFieldAttribute('articletext','rows','10');
		$form->setFieldAttribute('articletext','class','width-100');
		$doc =&JFactory::getDocument();
		$doc->addStyleDeclaration('fieldset.adminform textarea.width-100 {width: 100%;}');
		
		return true;
	}
	
	/*
	function onItemtypeDisplay_item_edit(&$itemtypeHtml,$item_type,$item_id,$text,$itemIntroText,$itemFullText)
	{
		if($item_type != 'html')
		{
			return false;
		}
		$html = '';
		$html .= '<script>';
		$html .= '';
		$html .= '</script>';
		
		$html .= 'HTML:<br /><textarea name="jform[introtext]" cols="85" rows="10">'.$itemIntroText.'</textarea>';
		$itemtypeHtml->text = $itemtypeHtml->text.$html;
		
		return true;
	}
	*/
}

?>