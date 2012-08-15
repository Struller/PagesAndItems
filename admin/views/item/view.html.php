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

//get default view?
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');

//get helper
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');

class PagesAndItemsViewItem extends PagesAndItemsViewDefault{

	protected $form;
	protected $item;
	protected $state;

	function display( $tpl = null ){
		$this->db = JFactory::getDBO();
		$helper = new PagesAndItemsHelper();
		$this->assignRef('helper', $helper);

		//if we come from com_content we must checkin
		if(JRequest::getVar('checkin',0))
		{
			$pk = JRequest::getVar('itemId',0);
			$user = JFactory::getUser();

			// Get an instance of the row to checkin.
			$table = JTable::getInstance('content'); //, $prefix, $config); //'content';
			if (!$table->load($pk)) {
				//$this->setError($table->getError());
				//return false;
			}

			// Check if this is the user having previously checked out the row.
			if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin')) {
				//$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));
				//return false;
			}

			// Attempt to check the row in.
			if (!$table->checkin($pk)) {
				//$this->setError($table->getError());
				//return false;
			}
		}

		/*
		$page_id = intval(JRequest::getVar('pageId', null));
		if(!$page_id)
		{
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'extensions'.DS.'managers'.DS.'categoriesanditems'.DS.'models'.DS.'managercategoriesanditems.php');
			$model = new PagesAndItemsModelManagerCategoriesanditems();
		}
		else
		{
			$model = &$this->getModel('Page');
		}
		*/
		/*
		$model = PagesAndItemsHelper::toogleModelPageCategories($this);
		
		if ($model)
		{
			$this->assignRef( 'model',$model);
		}
		*/	
		
			if(PagesAndItemsHelper::getIsAdmin())
			{
				$sub_task = JRequest::getVar('sub_task','');
				if($sub_task=='new')
				{
					PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_ITEM').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
				}
				elseif($sub_task == 'edit')
				{
					PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_ITEM').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
				}
				else
				{
					PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_ITEM').'</small>');
				}
				/*
				$pageTree = $model->getPages();
				$this->assignRef( 'pageTree',$pageTree);
				JHTML::script('dtree.js', 'administrator/components/com_pagesanditems/javascript/',false);
				*/
				//$menuItemsTypes = $model->menuItemsTypes;
				//we must first load
				$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
				$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
				
				$tree = PagesAndItemsHelper::getTree();
				$this->pageTree = $tree->getTree();
				
				
				
				
				//JHTML::script('dtree.js', 'administrator/components/com_pagesanditems/javascript/',false);
			}
			else
			{
				if(PagesAndItemsHelper::getJoomlaVersion() < '1.6')
				{
					$query = 'SELECT template'
					. ' FROM #__templates_menu'
					. ' WHERE client_id = 1'
					. ' AND menuid = 0'
					;
					$this->db->setQuery($query);
					$template = $this->db->loadResult();
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
					$this->db->setQuery($query);
					$template = $this->db->loadResult();
					if($template)
					{
						//$iconCss = JURI::root().'/administrator/templates/'.$template.'/css/icon.css';
						//JHTML::stylesheet('template.css', 'administrator/templates/'.$template.'/css/');
					}
					//here we need to load de-DE.ini
				}

				//$menuItemsTypes = $model->menuItemsTypes;
				$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
				$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
			}
			/*
			*/
			

		//}

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

		$menutype = PagesAndItemsHelper::get_menutype($page_id);
		$this->assignRef('menutype', $menutype);

		//without we get no form-output do not know why
		$state = $ContentModelArticle->getState();
		//we need to set the state article.id the getForm is need this
		$ContentModelArticle->setState('article.id',$item_id);
		
		
		$categoryId = intval(JRequest::getVar('categoryId', null));
		$this->useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = JRequest::getVar('sub_task', ($this->useCheckedOut ? '' : 'edit'));
		if($sub_task == 'new' && $categoryId)
		{
			//on new article we set the default catid
			JRequest::setVar('catid', $categoryId);
			//$ContentModelArticle->setState('article.catid',$categoryId);
		}
		/*
		$pageType = JRequest::getVar('pageType', null);
		if($sub_task == 'new' && $pageType)
		{
			//JRequest::setVar('catid', $categoryId);
			//$ContentModelArticle->setState('article.catid',$categoryId);
		}
		*/
		
		$this->form = $ContentModelArticle->getForm();
		$this->item = $ContentModelArticle->getItem($item_id);
		$this->state = $ContentModelArticle->getState();//$item_id);

		//load com_content language file
		$lang = &JFactory::getLanguage();
		//$lang->load('com_content', JPATH_ADMINISTRATOR, null, false, false);
		//$lang->load('com_content', JPATH_ADMINISTRATOR, null, false);
		$extension = 'com_content';
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		//load lib_joomla language file
		//$lang->load('lib_joomla', JPATH_ADMINISTRATOR, null, false, false);
		$extension = 'lib_joomla';
		//$lang->load('lib_joomla', JPATH_ADMINISTRATOR, null, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		
		//check Joomla ACL

		$category_id = $this->item->catid;
		$canDo = ContentHelper::getActions($category_id, $item_id);

		$this->assignRef('canDo', $canDo);


		//include user for ACL
		$user = JFactory::getUser();
		$this->assignRef('user', $user);



		
		$app		= JFactory::getApplication();
		$userId		= $user->get('id');
		$userName	= $user->get('name');
		$this->canCreate	= $this->canDo->get('core.create');
		$this->canEdit	= $this->canDo->get('core.edit');
		
		$this->canEditOwn	= $this->canDo->get('core.edit.own') && $this->item->created_by == $userId;
		//ms: i am not sure is this the right way
		if(!$this->canEdit)
		{
			$this->canEdit = $this->canEditOwn;
		}
		
		$this->canCheckin	= $user->authorise('core.manage', 'com_checkin') && ($this->item->checked_out==$user->get('id')|| $this->item->checked_out==0);
		// || !$countAdminUsers);
		$this->canChange	= $this->canDo->get('core.edit.state') && $this->canCheckin;
		if($this->useCheckedOut && $sub_task == 'edit' && $this->canEdit)
		{
			$ContentModelArticle->checkout();
		}

		//JHtml::_('behavior.tooltip');

		parent::display($tpl);
		$isAdmin = PagesAndItemsHelper::getIsAdmin();
		if($isAdmin)
		$this->addToolbar();
	}

	protected function addToolbar()
	{
		$sub_task = JRequest::getVar('sub_task', '');
		$isNew = ($this->item->id == 0);
		if($sub_task=='new')
		{
			JToolBarHelper::apply( 'item.item_apply'); //, JText::_('COM_PAGESANDITEMS_APPLY') );
			JToolBarHelper::save( 'item.item_save'); //, JText::_('COM_PAGESANDITEMS_SAVE_ITEM') );
			JToolBarHelper::divider();
			JToolBarHelper::cancel( 'item.cancel'); //, JText::_('COM_PAGESANDITEMS_CANCEL') );
		}
		elseif($this->useCheckedOut)
		{
			if($sub_task=='edit')
			{
				//if($this->canDo->get('core.edit') ) 
				if($this->canEdit ) 
				{
					//JToolBarHelper::custom('category.category_checkin','checkin','checkin', JText::_('JTOOLBAR_APPLY').' & '.JText::_('JTOOLBAR_CHECKIN'), false);
					JRequest::setVar('hidemainmenu', true);
				}
				JToolBarHelper::apply( 'item.item_apply'); //, JText::_('COM_PAGESANDITEMS_APPLY') );
				//JToolBarHelper::save( 'item.item_save'); //, JText::_('COM_PAGESANDITEMS_SAVE_ITEM') );
				JToolBarHelper::save( 'item.item_checkin'); //,'checkin');
				
				
				//add 25.04
				// If the user can create new items, allow them to see Save & New
				if ($this->canDo->get('core.create')) {
					//JToolBarHelper::save2new('item.item_save2new');
				}
				//add 25.04
				// If an existing item, can save to a copy only if we have create rights.
				if (!$isNew && $this->canDo->get('core.create')) {
					JToolBarHelper::save2copy('item.save2copy');
				}
				
				
				JToolBarHelper::divider();
				JToolBarHelper::cancel( 'item.cancel'); //, JText::_('COM_PAGESANDITEMS_CANCEL') );
			}
			else
			{
				//if ($this->canDo->get('core.edit') && $this->canCheckin) 
				if ($this->canEdit && $this->canCheckin) 
				{
					JToolBarHelper::custom('item.item_edit','edit','edit', 'JTOOLBAR_EDIT', false);
					JToolBarHelper::divider();
				}
				if($this->canDo->get('core.delete'))
				JToolBarHelper::custom( 'item.item_delete','delete','delete','delete',false);
				//JToolBarHelper::divider();
				JToolBarHelper::custom( 'item_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
				JToolBarHelper::divider();
				JToolBarHelper::cancel( 'item.cancel'); //, JText::_('COM_PAGESANDITEMS_CANCEL') );
			}
		}
		else
		{
			JToolBarHelper::apply( 'item.item_apply'); //, JText::_('COM_PAGESANDITEMS_APPLY') );
			JToolBarHelper::save( 'item.item_save'); //, JText::_('COM_PAGESANDITEMS_SAVE_ITEM') );
			
			
			//add 25.04
			// If the user can create new items, allow them to see Save & New
			if ($this->canDo->get('core.create')) {
				//JToolBarHelper::save2new('item.item_save2new');
			}
			//add 25.04
			// If an existing item, can save to a copy only if we have create rights.
			if (!$isNew && $this->canDo->get('core.create')) {
				JToolBarHelper::save2copy('item.save2copy');
			}
			
			//JToolBarHelper::divider();

			if($this->canDo->get('core.delete'))
			JToolBarHelper::custom( 'item.item_delete','delete','delete','delete',false);
			//JToolBarHelper::divider();
			JToolBarHelper::custom( 'item_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
			//JToolBarHelper::custom( 'item_copy_select', 'copy.png', 'copy_f2.png', JText::_('COM_PAGESANDITEMS_COPY'), $listSelect = false);
			JToolBarHelper::divider();
			JToolBarHelper::cancel( 'item.cancel'); //, JText::_('COM_PAGESANDITEMS_CANCEL') );
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