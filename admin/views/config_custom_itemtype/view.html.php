<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');

class PagesAndItemsViewConfig_custom_itemtype extends PagesAndItemsViewDefault
{
	function display( $tpl = null )
	{
		$this->db = JFactory::getDBO();
		$sub_task = JRequest::getVar('sub_task','');
		if($sub_task=='new')
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_CONFIG').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
		}
		else
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_CONFIG').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
		}

		/*
		if ($model = &$this->getModel('page'))
		{
			$this->assignRef('model', $model);
		}
		*/
		$model = & $this->getModel('customitemtype');
		$typeId = JRequest::getVar('type_id',0);

		$this->state = $model->getState();
		
		$this->item = $model->getItem($typeId);
		
		$this->form = $model->getForm();
		
		JHTML::_('behavior.tooltip');
		
		parent::display($tpl);
		$this->addToolbar();
	}
	
	protected function addToolbar()
	{
		$sub_task = JRequest::getVar('sub_task','');

		if(!$sub_task && $sub_task!='new')
		{
			JToolBarHelper::custom( 'config_itemtype_render', 'copy.png', 'copy_f2.png', JText::_('COM_PAGESANDITEMS_RENDER_ITEMTYPES'), false );
			JToolBarHelper::divider();
		}

		JToolBarHelper::apply( 'customitemtype.config_custom_itemtype_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
		JToolBarHelper::save( 'customitemtype.config_custom_itemtype_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );
		JToolBarHelper::divider();
		if(!$sub_task && $sub_task != 'new')
		{

			//TODO visible over extensions/managers/archive ?
			//can we make only an placeholder so we can add over the extensions?
			//ore trigger the extensions/managers/archive to make the button
			//like trigger('onToolbarButton',array('archive','config_custom_itemtype_archive'))
			// ms: at this moment no customitemtype archive
			//JToolBarHelper::custom( 'customitemtype.config_custom_itemtype_archive','archive','archive','archive',false);
			//TODO visible over extensions/managers/trash ?
			// ms: at this moment no customitemtype trash
			//JToolBarHelper::trash( 'customitemtype.config_custom_itemtype_trash','trash','trash','trash',false);
			JToolBarHelper::custom('customitemtype.config_custom_itemtype_delete','delete','delete','JTOOLBAR_DELETE',false);
			JToolBarHelper::divider();
		}
		JToolBarHelper::cancel( 'customitemtype.cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
	}
}