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

//get default view?
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');

//get helper
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');

class PagesAndItemsViewItem extends PagesAndItemsViewDefault{	

	protected $form;
	protected $item;
	protected $state;
	
	function display( $tpl = null ){
		$helper = new PagesAndItemsHelper();
		$this->assignRef('helper', $helper);

		
		if ($model = &$this->getModel('Page')) 
		{
			if($model->isAdmin)
			{
				$sub_task = JRequest::getVar('sub_task','');
				if($sub_task=='new')
				{
					PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_ITEM').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
				}
				else
				{
					PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_ITEM').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
				}
				$pageTree = $model->getPages();
				$this->assignRef( 'pageTree',$pageTree);

				$menuItemsTypes = $model->menuItemsTypes;
				$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
				JHTML::script('dtree.js', 'administrator/components/com_pagesanditems/javascript/',false);
			}
			else
			{
				if($model->joomlaVersion < '1.6')
				{
					$query = 'SELECT template'
					. ' FROM #__templates_menu'
					. ' WHERE client_id = 1'
					. ' AND menuid = 0'
					;
					$model->db->setQuery($query);
					$template = $model->db->loadResult();
					if($template)
					{
						//$iconCss = JURI::root().'/administrator/templates/'.$template.'/css/icon.css';
						JHTML::stylesheet('general.css', 'administrator/templates/'.$template.'/css/');
					}
				}
				else
				{
					$query = 'SELECT template'
					. ' FROM #__template_styles'
					. ' WHERE client_id = 1'
					. ' AND home = 1'
					;
					$model->db->setQuery($query);
					$template = $model->db->loadResult();
					//dump($template);
					if($template)
					{
						//dump('X');
						//$iconCss = JURI::root().'/administrator/templates/'.$template.'/css/icon.css';
						//JHTML::stylesheet('template.css', 'administrator/templates/'.$template.'/css/');
					}
					//here we need to load de-DE.ini
				}
				
				$menuItemsTypes = $model->menuItemsTypes;
				$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
			}
			/*
			*/
			$this->assignRef( 'model',$model);

		}
		
		//include com_content helper
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
		$ContentHelper = new ContentHelper;
		
		//include com_content model article
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'article.php');
		$ContentModelArticle = new ContentModelArticle;
		
		// set the form path
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'models'.DS.'forms');
		
		// Initialiase variables.
		$item_id = intval(JRequest::getVar('itemId', ''));
		$page_id = intval(JRequest::getVar('pageId', ''));		
		
		$menutype = $helper->get_menutype($page_id);
		$this->assignRef('menutype', $menutype);
		
		//without we get no form-output do not know why   
		$state = $ContentModelArticle->getState();
		//we need to set the state article.id the getForm is need this
		$ContentModelArticle->setState('article.id',$item_id);
		
		$this->form = $ContentModelArticle->getForm();
		$this->item = $ContentModelArticle->getItem($item_id);		
		$this->state = $ContentModelArticle->getState($item_id);		
		
		//load com_content language file
		$lang = &JFactory::getLanguage();
		//$lang->load('com_content', JPATH_ADMINISTRATOR, null, false, false);
		$lang->load('com_content', JPATH_ADMINISTRATOR, null, false);
		
		//load lib_joomla language file
		//$lang->load('lib_joomla', JPATH_ADMINISTRATOR, null, false, false);
		$lang->load('lib_joomla', JPATH_ADMINISTRATOR, null, false);
		
		//check Joomla ACL
		$category_id = $this->item->catid;		
		$canDo = ContentHelper::getActions($category_id, $item_id);				
		if(!$canDo->get('core.edit')){
			echo JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THIS_ITEM');
			exit;
		}
		$this->assignRef('canDo', $canDo);				
		
		//include user for ACL
		$user = JFactory::getUser();
		$this->assignRef('user', $user);
		
		parent::display($tpl);
		$isAdmin = PagesAndItemsHelper::getIsAdmin();
		if($isAdmin)
		$this->addToolbar();
	}
	
	protected function addToolbar()
	{
		$sub_task = JRequest::getVar('sub_task', '');
		
		
		
						
		if($sub_task=='new')
				{
					JToolBarHelper::save( 'item.item_save', JText::_('COM_PAGESANDITEMS_SAVE_ITEM') );
					JToolBarHelper::apply( 'item.item_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
					JToolBarHelper::divider();
					JToolBarHelper::cancel( 'item.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
				}
				else
				{
					JToolBarHelper::save( 'item.item_save', JText::_('COM_PAGESANDITEMS_SAVE_ITEM') );
					JToolBarHelper::apply( 'item.item_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
					//JToolBarHelper::divider();
					
					/*
					ms: i have comment out the next lines
					one problem is if user have change the title,text... all of this will not save
					only the state is changed
					all of this the user can handle in select 'state
					
					
					JToolBarHelper::publish( 'item.item_publish');
					JToolBarHelper::unpublish( 'item.item_unpublish');
					JToolBarHelper::custom( 'item.item_archive','archive','archive','archive',false);


					//JToolBarHelper::archive( 'item.item_archive');//,'archive','archive','archive',false);
					
					//JToolBarHelper::trash( 'item_trash', JText::_('COM_PAGESANDITEMS_TRASH'), '', '', $listSelect = true );
					//TODO visible over extensions/managers/trash ?
					JToolBarHelper::trash( 'item.item_trash');//,'trash','','',false);
					JToolBarHelper::divider();
					*/
					if($this->canDo->get('core.delete'))
					JToolBarHelper::custom( 'item.item_delete','delete','delete','delete',false);
					//JToolBarHelper::divider();
					JToolBarHelper::custom( 'item_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
					//JToolBarHelper::custom( 'item_copy_select', 'copy.png', 'copy_f2.png', JText::_('COM_PAGESANDITEMS_COPY'), $listSelect = false);
					JToolBarHelper::divider();
					JToolBarHelper::cancel( 'item.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
				}
	}
	
	
	function display_select($values, $param, $optionset){
		if(!isset($values[$param])){
			$values[$param] = '';	
		}	
		$html = '<select name="params['.$param.']" class="inputbox">';		
		for($n = 0; $n < count($optionset); $n++){
			$html .= '<option value="'.$optionset[$n][0].'" ';
			if($values[$param]===$optionset[$n][0]){			
				$html .= "selected=\"selected\"";
			} 
			$html .= '>';
			$html .= $optionset[$n][1];
			$html .= '</option>';
		}			
		$html .= '</select>';
		return $html;
	}
	
	
}
?>