<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
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
		$this->db = JFactory::getDBO();
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
		/*
		if ($model = &$this->getModel('page'))
		{
			$this->assignRef('model', $model);
		}
		*/
		parent::display($tpl);
				$this->addToolbar();
	}
	
	protected function addToolbar()
	{
		$sub_task = JRequest::getVar('sub_task','');
	
		JToolBarHelper::apply( 'customitemtypefield.config_custom_itemtype_field_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
		JToolBarHelper::save( 'customitemtypefield.config_custom_itemtype_field_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );
		JToolBarHelper::divider();
		if(!$sub_task && $sub_task != 'new')
		{
					// ms: at this moment no  archive
					//JToolBarHelper::custom( 'customitemtypefield.config_custom_itemtype_field_archive','archive','archive','archive',false);
					// ms: at this moment no trash
					//JToolBarHelper::trash( 'customitemtypefield.config_custom_itemtype_field_trash','trash','trash','trash',false);
					// ms: at this moment no delete
					//JToolBarHelper::custom('customitemtypefield.config_custom_itemtype_field_delete','delete','delete','delete',false);
		}
		JToolBarHelper::cancel( 'customitemtypefield.cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
	
	}
}