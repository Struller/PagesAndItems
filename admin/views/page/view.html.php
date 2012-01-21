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


class PagesAndItemsViewPage extends PagesAndItemsViewDefault //JView //PagesViewDefault
{
	/*
	protected $modelPage;
	protected $form;
	protected $item;
	protected $modules;
	protected $state;
	*/
	
	protected $form;
	protected $item;
	protected $modules;
	protected $state;

	
	function display( $tpl = null )
	{
		/*
		ms: this is needed for the correct view of the fields
		and we need not com_pagesanditems/models/forms/item.xml, item_alias.xml, item_component.xml, item_options.xnl, item_separator.xml and item_url.xml
		*/
		
		jimport( 'joomla.form.form');
		// set the form path
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'forms');
		
		// set the fields path
		JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'fields');
		
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		$helper = new PagesAndItemsHelper();
		$this->assignRef('helper', $helper);
		$sub_task = JRequest::getVar('sub_task','');
		if($sub_task=='new')
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_PAGE').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
		}
		elseif($sub_task=='edit')
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_PAGE').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
		}
		else
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_PAGE').'</small>');
		}
		//PI ACL
		$item_id = JRequest::getVar('pageId','');
		$layout = JRequest::getVar('layout','');
		if($layout!='root' && $item_id){
			//not root	
			if($sub_task=='new'){
				//new page
				$this->helper->to_previous_page_when_no_permission('1');
					
			}else{
				//edit page
				$this->helper->to_previous_page_when_no_permission('2');
			}	
		}
		
		
		if ($model = &$this->getModel('Page')) 
		{	
			$model->setView($this);
			$this->assignRef( 'model',$model);
			
			$pageTree = $model->getPages();
			$this->assignRef( 'pageTree',$pageTree);

			$pagePropertys = $model->getPagePropertys();
			$this->assignRef( 'pagePropertys',$pagePropertys);
			
			//set some defaults
		//if($sub_task=='new'){		
			//$this->pageMenuItem->title = 'nieuwe dingen';
		//}
		
		
			//for Joomla 1.6 we can just load the page properties with the underneath script and use the fields in the tmpl
			//somehow this needs to be done before the menu-item data can be loaded in the form	
			//$model->getMenuItem();	
			
			$pageChilds = $model->getChilds();
			$this->assignRef( 'pageChilds',$pageChilds);
			
			$reload = $model->reload();
			$this->assignRef( 'reload',$reload);			
			
			$pageType = $model->pageType;
			$this->assignRef( 'pageType',$pageType);
			
			$pageItems = $model->getPageItems();
			$this->assignRef( 'pageItems',$pageItems);
		
			$canDo = $model->getCanDo('com_menus');
			$this->assignRef( 'canDo',$canDo);
			//for use in toolbar see models/page.php
		}
		
		
		
		
		/*
		method to get the page properties, only for Joomla 1.6
		
		//include com_menus model for item
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
		$MenusModelItem = new MenusModelItem;
		
		
		
		// Initialiase variables.
		$menu_id = intval(JRequest::getVar('pageId', ''));
				
		//trying all sorts of stuff to get the right data in the form
		$state = $MenusModelItem->getState();		
		$MenusModelItem->setState('item.id', $menu_id);
		$MenusModelItem->setState('item.type', 'component');		
		$this->state = $MenusModelItem->getState($menu_id);	
		
		//$this->item = $MenusModelItem->getItem($menu_id);
		$this->form = $MenusModelItem->getForm();
		//$this->form = $MenusModelItem->getForm(array('title'=>'soep'), 1);
		$this->item = $MenusModelItem->getItem($menu_id);	
		//$this->form = $MenusModelItem->getForm($this->item, 1);	
		$this->modules = $MenusModelItem->getModules();		
		$this->state = $MenusModelItem->getState($menu_id);	
		*/
		
/*
		if ($model = &$this->getModel('PagesAndItems')) 
		{
			
			$this->baseModel = $model;
			$joomlaVersion = $model->getJoomlaVersion();
			$this->assignRef( 'joomlaVersion',$joomlaVersion);
			
			//$this->controller->menutypes = $model->getMenutypes();
			//$this->controller->menuitems = $model->getMenuitems();
			//$this->controller->current_menutype = $model->getCurrentMenutype();
			//$this->controller->current_pageId = $model->getCurrentPageId();
			
			//$this->controller->itemtypes = $model->getItemtypes();
			
			//$currentMenutype = JRequest::getVar('menutype',$this->controller->menutypes[0]);
			$currentMenutype = JRequest::getVar('menutype',$this->baseModel->getCurrentMenutype());
			$this->assignRef( 'currentMenutype', $currentMenutype);
		}
*/
		/*
		if(!$this->controller->is_admin)
		{
			$query = 'SELECT template'
			. ' FROM #__templates_menu'
			. ' WHERE client_id = 1'
			. ' AND menuid = 0'
			;
			$this->controller->db->setQuery($query);
			$template = $this->controller->db->loadResult();
			$iconCss = JURI::root().'/administrator/templates/'.$template.'/css/icon.css';
			JHTML::stylesheet('general.css', JURI::root().'/administrator/templates/'.$template.'/css/');
		}

		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		$this->assignRef( 'joomlaVersion',$joomlaVersion);
		*/
		/*
		not here JHTML::stylesheet('pagesanditems.css', 'administrator/components/com_pagesanditems/css/');
		JHTML::stylesheet('dtree.css', 'administrator/components/com_pagesanditems/css/');
		*/
		JHTML::script('dtree.js', 'administrator/components/com_pagesanditems/javascript/',false);
		//JHTML::script('overlib_mini.js', 'includes/js/',false);
		//JHTML::_('behavior.tooltip');

		$pageId = JRequest::getVar('pageId',0);
		$this->assignRef( 'pageId', $pageId);
		$itemId = JRequest::getVar('itemId',0);
/*		
		$pageMenuItem = 0;
		$this->assignRef( 'pageMenuItem',$pageMenuItem);
		$menu_item = 0;
		$this->assignRef( 'menu_item',$menu_item);
		$menuItemsTypes = null;

		if ($this->joomlaVersion < '1.6')
		{
			if($model = &$this->getModel('menutypes','pagesanditemsModel') )
			{
				$model = &$this->getModel('menutypes','pagesanditemsModel');
				$menuItemsTypes = $model->getTypeListComponents();
			}
		}
		else
		{
			$model = new PagesAndItemsModelMenutypes();
			$menuItemsTypes = $model->getTypeListComponents();
		}
		$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
		
		$menuItemsType = 0;
		$this->assignRef( 'menuItemsType',$menuItemsType);
		
		$lists = new stdClass();
		$this->assignRef('lists', $lists);

		$pages_edit = JRequest::getVar('pages_edit',false);
		
		$this->assignRef( 'pages_edit',$pages_edit);
		
		$pageType = null;
		$this->assignRef( 'pageType',$pageType);

		$currentMenuitems = null;
		$this->assignRef( 'currentMenuitems', $currentMenuitems);
		//TODO move all the functions from this to PagesAndItemsModelPage??
*/
		//dump(JRequest::get());
		parent::display($tpl);
		$this->addToolbar();
	}
	
	protected function addToolbar()
	{
		$sub_task = JRequest::getVar('sub_task', '');
		//$vName = 'page'
		
		$layout = JRequest::getVar('layout','');
		//dump($layout);
		if($layout == 'root')
				{
					if($sub_task=='new')
					{
						JToolBarHelper::save( 'page.root_save', JText::_('COM_PAGESANDITEMS_SAVE') );
						JToolBarHelper::apply( 'page.root_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
						JToolBarHelper::divider();
						JToolBarHelper::cancel( 'page.root_cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
					}
					else
					{
						JToolBarHelper::apply( 'page.root_save', JText::_('COM_PAGESANDITEMS_SAVE') );
					}
				}
				else
				{
					if($sub_task=='new')
					{
						JToolBarHelper::save( 'page.page_save', JText::_('COM_PAGESANDITEMS_SAVE') );
						JToolBarHelper::apply( 'page.page_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
						JToolBarHelper::divider();
						JToolBarHelper::cancel( 'page.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
					}
					else
					{
						JToolBarHelper::save( 'page.page_save', JText::_('COM_PAGESANDITEMS_SAVE') );
						JToolBarHelper::apply( 'page.page_apply', JText::_('COM_PAGESANDITEMS_APPLY') );
						/*
						ms: i have comment out the next lines
						one problem is if user have change the title... all of this will not save
						only the state is changed
						all of this the user can handle in select 'state
	
						JToolBarHelper::divider();
						JToolBarHelper::publish( 'page.page_publish');
						JToolBarHelper::unpublish( 'page.page_unpublish');
						JToolBarHelper::trash( 'page.page_trash','JTOOLBAR_TRASH',false);
						JToolBarHelper::divider();
						*/
						if($this->canDo->get('core.delete'))
						JToolBarHelper::custom('page.page_delete','delete','delete','JTOOLBAR_DELETE',false);
						//JToolBarHelper::divider();
						
						JToolBarHelper::custom( 'page_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
						JToolBarHelper::divider();
						JToolBarHelper::cancel( 'page.cancel', JText::_('COM_PAGESANDITEMS_CANCEL') );
					}
				}
	}
	
	
}
//where is the toolbar ?
?>