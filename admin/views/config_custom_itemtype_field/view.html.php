<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
/**

 */


class PagesAndItemsViewConfig_custom_itemtype_field extends PagesAndItemsViewDefault
{
	function display( $tpl = null )
	{
		$sub_task = JRequest::getVar('sub_task','');
		/*
		ms: no sub_task is here
		if($sub_task=='new')
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_FIELD_CONFIG').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
		}
		else
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_FIELD_CONFIG').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
		}
		*/
		PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_FIELD_CONFIG').'</small>');
		
		if ($model = &$this->getModel('page'))
		{
			$this->assignRef('model', $model);
		}
		parent::display($tpl);
	}
}