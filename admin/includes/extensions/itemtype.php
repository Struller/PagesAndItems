<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;


require_once(dirname(__FILE__).DS.'extension.php');

abstract class PagesAndItemsExtensionItemtype extends PagesAndItemsExtension
{
	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
	}

	function onItemtypeIs_config_form(&$itemtypeHtmlIsConfig,$item_type,$type_id = null)
	{
		if(method_exists($this, 'onItemtypeDisplay_config_form'))
		{
			//$itemtypeHtml = & new JObject();
			$itemtypeHtml = new JObject();
			$itemtypeHtml->text = '';

			if($itemtypeHtml = $this->onItemtypeDisplay_config_form(&$itemtypeHtml,$item_type))
			{
				if($item_type == 'custom')
				{
					$itemtypeHtmlIsConfig->text = '<a href="index.php?option=com_pagesanditems&view=config_custom_itemtype&type_id='.$type_id.'">'.JText::_('COM_PAGESANDITEMS_CONFIG').'</a>';
				}
				else
				{
					$itemtypeHtmlIsConfig->text = '<a href="index.php?option=com_pagesanditems&view=config_itemtype&item_type='.$item_type.'">'.JText::_('COM_PAGESANDITEMS_CONFIG').'</a>';
				}
			}
			//$html = $this->display_config_form($plugin, $type_id, $name, $field_params, $field_id);
		}
		return true;
	}


	function onGetPluginName(&$itemtypeHtml,$item_type)
	{
		if($this->_name != $item_type)
		{
			return false;
		}
		//first we check if an own method exist
		if(method_exists($this, 'getPluginName'))
		{
			$this->getPluginName($itemtypeHtml,$item_type);
		}
		else
		{
			if($this->_folder)
			{
				$extension_folder = str_replace('/','_',$this->_folder);
				$prefix = $this->_type.'_'.$extension_folder;//.DS;

			}
			else
			{
				$prefix = $this->_type;
			}
			//here we load the translatet string
			//
			$text = JText::_(strtoupper('PI_EXTENSION_'.$prefix.'_'.$this->_name));
			if(!strcmp($text, strtoupper('PI_EXTENSION_'.$prefix.'_'.$this->_name)))
			{
				$text = $this->_name;
			}
			$itemtypeHtml = $text; //JText::_(strtoupper('PI_EXTENSION_'.$prefix.'_'.$this->_name));
		}
		return true;
	}
	/*
	function onItemtypeDisplay_item_edit(&$itemtypeHtml,$item_type,$item_id,$text,$itemIntroText,$itemFullText)
	{
		return true;
	}

	function onItemtypeDisplay_item_config(&$itemtypeHtml,$item_type,$item_id,$text,$itemIntroText,$itemFullText)
	{
		return true;
	}

	function onItemtypeToolbar(&$itemtypeHtml,$item_type)
	{
		return true;
	}

	function onItemtypeItem_delete

	function onItemtypeItem_save

	function onItemtypeConfig_save

	*/
}
