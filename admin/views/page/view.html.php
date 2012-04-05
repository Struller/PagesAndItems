<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
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
		$layout = JRequest::getVar('layout','');

		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		$helper = new PagesAndItemsHelper();
		$this->assignRef('helper', $helper);
		$sub_task = JRequest::getVar('sub_task','');
		switch($sub_task)
		{
			case 'new':
				PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_PAGE').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
				//loadPageProperties = true
				//loadMenutypeProperties = false
			break;
			

			case 'newMenutype':
				PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MENUTYPE').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
				//loadPageProperties = false
				//loadMenutypeProperties = true
			break;

			case 'edit':
				PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_PAGE').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
				//loadPageProperties = true
				//loadMenutypeProperties = false
			break;
			
			case 'editMenutype':
				PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MENUTYPE').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
				//loadPageProperties = false
				//loadMenutypeProperties = true
			break;
			
			default:
				if($layout == 'root')
				{
					PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MENUTYPE').'</small>');
				}
				else
				{
					PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_PAGE').'</small>');
				}
				//loadPageProperties = true
				//loadMenutypeProperties = false
			break;
		}

		$pageId = JRequest::getVar('pageId',0);
		$this->assignRef( 'pageId', $pageId);
		
		$item_id = JRequest::getVar('pageId','');
		
		$this->menutype = JRequest::getVar('menutype','');
		
	
		if($sub_task=='new' && ($layout=='root' || $layout==''))
		{
			//new
			$app = JFactory::getApplication();
			$option = JRequest::getVar('option');
			$this->type = $app->getUserStateFromRequest( $option.'.page.type', 'type'); //, 'type','cmd' );
			$this->task = $app->getUserStateFromRequest( $option.'.page.task', 'task'); //, 'type','cmd' );
			$app->setUserState( $option.'.page.task', '');
			$this->pageType = $app->getUserStateFromRequest( $option.'.page.pageType', 'pageType'); //, 'type','cmd' );
			$this->pageTypeType = $app->getUserStateFromRequest( $option.'.page.pageTypeType', 'pageTypeType'); //, 'type','cmd' );
		}
		else
		{
			$this->type = '';
			//$this->pageType = '';
			$this->pageTypeType = '';
			$this->pageType = JRequest::getVar('pageType','');
			$this->task = '';
		}

		$tree = PagesAndItemsHelper::getTree();
		$this->pageTree = $tree->getTree();
		if(($layout == 'root' && $sub_task == 'new' ) || !$layout == 'root')
		{
			$model = &$this->getModel('Page');
			$model->setView($this);
			$this->assignRef( 'model',$model);
			//$pageTree = $model->getPages();
			//$this->assignRef( 'pageTree',$pageTree);
			//$tree = PagesAndItemsHelper::getTree();
			//$this->pageTree = $tree->getTree();
			
			//if($sub_task != 'newMenutype')
			//{
			
				switch($sub_task)
				{
					case 'new':
						
						//$loadPagePropertys = ($this->task !='') ? true : false;
						$loadPagePropertys = true;
					break;

					case 'edit':
						$loadPagePropertys = true;
					break;
				
					default:
						$loadPagePropertys = $pageId ? true : false;
					break;
				}
				if($loadPagePropertys)
				{
					$this->menuItem = $model->getMenuItem();
					//$pagePropertys = $model->getPagePropertys();
					$pagePropertys = ''; //$model->getPagePropertys();
					$this->assignRef( 'pagePropertys',$pagePropertys);
					
					$this->isPagePropertys = $model->isGetPagePropertys();
					$this->canDoMenu = PagesAndItemsHelper::canDoMenus(); //$this->menuItem->parent_id);
				}
				else
				{
					$pagePropertys = '';
					$this->assignRef( 'pagePropertys',$pagePropertys);
					$this->isPagePropertys = false;
					$this->canDoMenu = PagesAndItemsHelper::canDoMenus();
					$this->menuItem = null;
				}
				if($this->isPagePropertys)
				{
					
					$this->menuItemsType = $model->getMenuItemsType();
					$this->lists = $model->getLists(); //$lists
					$this->form = $model->getForm();
					$this->modules = $model->getModules();
					
				}
				else
				{
					$this->menuItemsType = $model->getMenuItemsType();
				/*
					$this->menuItemsType = $model->getMenuItemsType();
					$this->lists = $model->getLists(); //$lists
					$this->form = $model->getForm();
					$this->modules = $model->getModules();
				*/
				}
				
				$pageItems = $model->getPageItems();
				$this->assignRef( 'pageItems',$pageItems);
			//}
			
			//getChilds must go to other as $model
			//$pageChilds = $model->getChilds($tree->pageMenuItem,$tree->currentMenuitems);
			//$this->assignRef( 'pageChilds',$pageChilds);

			$pageType = $model->pageType;
			$this->assignRef( 'pageType',$pageType);


			//}
			
			
			
			
			//$canDo = PagesAndItemsHelper::canDoMenus($this->pageId);
			//$canDo = PagesAndItemsHelper::canDoMenus();
			
			//$canDo = $model->getCanDo('com_menus');
			//$this->assignRef( 'canDo',$canDo);
			//for use in toolbar see models/page.php
			//$config = PagesAndItemsHelper::getConfigAsRegistry();
			//$this->useCheckedOut = $config->get('useCheckedOut',0);
			$this->useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
			$db = JFactory::getDbo();
			$user = JFactory::getUser();
		/*
		$query = $db->getQuery(true);
		$query->select('s.time, s.client_id, u.id, u.name, u.username');
		$query->from('#__session AS s');
		$query->leftJoin('#__users AS u ON s.userid = u.id');
		$query->where('s.guest = 0');
		$query->where('s.client_id = 1');// AND 
		$query->where('u.id <> '.$user->get('id'));
		
		$db->setQuery($query); //, 0, $params->get('count', 5));
		$results = $db->loadObjectList();
		$countAdminUsers = count($results);
		*/
			$app		= JFactory::getApplication();
			$userId		= $user->get('id');
			$userName	= $user->get('name');
			$this->canCreate	= $this->canDoMenu->get('core.create');
			$this->canEdit	= $this->canDoMenu->get('core.edit');
			$this->canCheckin	= $user->authorise('core.manage', 'com_checkin') && ($this->menuItem->checked_out==$user->get('id')|| $this->menuItem->checked_out==0);
		// || !$countAdminUsers);
			$this->canChange	= $this->canDoMenu->get('core.edit.state') && $this->canCheckin;
			
			
			
			
			
		}
		else
		{
			$this->menuItemsType = null;
			$this->menuItem = null;
		}
		$reload = $this->reload();
		$this->assignRef( 'reload',$reload);
		
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'pageslist.php');
		$PagesList = new PagesList();
		$pageChilds = $PagesList->getChilds($tree->pageMenuItem,$tree->currentMenuitems,$this->menuItemsType);
		$this->assignRef( 'pageChilds',$pageChilds);
		
		
		
		//TODO RootMenutype
		//display menutype
		//$menutype = JRequest::getVar('menutype',0);
		
		$this->assignRef( 'layout',$layout);
		//$this->assignRef( 'menutype',$menutype);
		
		
		if($layout == 'root' && $sub_task != 'new' )
		{
			if($sub_task == 'newMenutype')
			{
				$this->oldmenutype = $this->menutype;
				$this->menutype = 0;
			}
			//load model rootMenutype
			//to display the properties from menu?
			$modelMenutype = &$this->getModel('Menutype');
			
			//$modelMenutype->setView($this);
			$this->assignRef( 'modelMenutype',$modelMenutype);
			$db = & JFactory::getDBO();
			$db->setQuery("SELECT * "
				." FROM #__menu_types "
				." WHERE menutype='$this->menutype' "
				." LIMIT 1 "
			);
			$row = $db->loadObject();
			$menutypeId = 0;
			if($row)
			{
				$menutypeId = $row->id;
			}
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'menus.php');
			$this->canDoMenutype = MenusHelper::getActions(); //$menutypeId);
			
			/*
			if(version_compare(JVERSION, '2.5', 'ge'))
			{
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'menus.php');
				$modelMenutypes = new MenusModelMenus();
				
			}
			*/
			if($this->canDoMenutype->get('core.create') || $this->canDoMenutype->get('core.edit') )
			{
				//$menutypeId = JRequest::getVar('menutypeId',0);
				$this->menutypeId = $menutypeId;
				JRequest::setVar('id', $menutypeId);
				$this->menutypeItem	= $modelMenutype->getItem($menutypeId);
				$modelMenutype->setState('menu.id',$menutypeId);
				$this->form		= $modelMenutype->getForm($this->menutypeItem);
				$this->state	= $modelMenutype->getState();
				JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'forms');
				// set the fields path
				JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'fields');

				$lang = &JFactory::getLanguage();
				//$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false);
				$extension = 'com_menus';
				$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
				if($this->menutypeItem->id)
				{
					$this->modules = $modelMenutype->getModules();
				}
			}
			else
			{
				$this->menutypeItem = null;
			}
		}
		else
		{
			$this->menutypeItem = null;
		}
		/*
			show not in root menutype:
			$this->menutypeItem = null; 
			$this->menutypeItem = null;
		*/

		/*
		not here JHTML::stylesheet('pagesanditems2.css', 'administrator/components/com_pagesanditems/css/');
		JHTML::stylesheet('dtree.css', 'administrator/components/com_pagesanditems/css/');
		*/
		//JHTML::script('dtree.js', 'administrator/components/com_pagesanditems/javascript/',false);
		//JHTML::_('behavior.tooltip');


		
		
		$itemId = JRequest::getVar('itemId',0);
		parent::display($tpl);
		$this->addToolbar();
	}

	function reload()
	{
		$html ='';
		$html .='<div class="page_reload" id="page_reload" style="display:none;">';
			$html .='<div>';
				$html .= JText::_('COM_PAGESANDITEMS_RELOAD');
			$html .='</div>';
			$html .='<div>';
				$html .='<img src="'.PagesAndItemsHelper::getDirIcons().'processing.gif" >';
			$html .='</div>';
		$html .='</div>';
		return $html;
	}





	protected function addToolbar()
	{
		$sub_task = JRequest::getVar('sub_task', '');
		//$subsub_task = JRequest::getVar('subsub_task', '');
		//$vName = 'page'

		$layout = JRequest::getVar('layout','');
		if($layout == 'root')
		{
			if($sub_task=='new')
			{
				JToolBarHelper::apply( 'page.root_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
				//JToolBarHelper::save( 'page.root_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::divider();
				JToolBarHelper::cancel( 'menutype.root_cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
			}
			elseif($sub_task=='newMenutype')
			{
				JToolBarHelper::apply( 'menutype.root_menutype_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
				//JToolBarHelper::save( 'page.root_menutype_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::divider();
				JToolBarHelper::cancel( 'menutype.root_cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
			}
			else
			{
				/*ms: add if we have in root edit/create menutype 
				*/
				$create = false;
				$edit = false;
				if($this->menutypeItem && $this->canDoMenutype->get('core.create'))// && PagesAndItemsHelper::getIsSuperAdmin()) // && isset($this->canDoMenutype) && $this->canDoMenutype->get('core.create'))
				{
					JToolBarHelper::addNew('menutype.root_menutype_new');
					//JToolBarHelper::divider();
					$create = true;
				}
				
				if($this->menutypeItem && $this->canDoMenutype->get('core.edit')) //isset($this->canDoMenutype) && $this->canDoMenutype->get('core.edit'))
				{
					if($create)
					{
						JToolBarHelper::divider();
					}
					JToolBarHelper::apply( 'menutype.root_menutype_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
					//JToolBarHelper::save('menutype.root_menutype_save');
					$edit = true;

				}
				if(!$create && !$edit)
				JToolBarHelper::apply( 'menutype.root_save', JText::_('COM_PAGESANDITEMS_SAVE'));
			}
		}
		else
		{
			if($sub_task=='new')
			{
				JToolBarHelper::apply( 'page.page_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
				JToolBarHelper::save( 'page.page_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );
				JToolBarHelper::divider();
				JToolBarHelper::cancel( 'page.cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
			}
			elseif($sub_task=='edit')
			{
				if($this->useCheckedOut)
				{
					if($this->canDoMenu->get('core.edit')) 
					{
						//JToolBarHelper::custom('page.page_checkin','checkin','checkin', JText::_('JTOOLBAR_APPLY').' & '.JText::_('JTOOLBAR_CHECKIN'), false);
						JRequest::setVar('hidemainmenu', true);
						//JToolBarHelper::divider();
						JToolBarHelper::apply( 'page.page_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
						JToolBarHelper::save( 'page.page_checkin'); //page.page_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );
						JToolBarHelper::divider();
						JToolBarHelper::cancel( 'page.cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
					}
				}
				else
				{
					if($this->canDoMenu->get('core.edit'))
					{
						JToolBarHelper::apply( 'page.page_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
						JToolBarHelper::save( 'page.page_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );

					}
					
					if($this->canDoMenu->get('core.edit.state'))
					{
						//this wew can do in the item JToolBarHelper::publish('page.page_publish');
						//this wew can do in the itemJToolBarHelper::unpublish('page.page_unpublish');

						//this wew can do in also in the item but we will offer here
						JToolBarHelper::trash('page.page_trash','JTOOLBAR_TRASH', false);//,'delete','delete','JTOOLBAR_DELETE',false);
						//JToolBarHelper::divider();
					}
					
					if($this->canDoMenu->get('core.delete'))
					{
						
						JToolBarHelper::custom('page.page_delete','delete','delete','JTOOLBAR_DELETE',false);
						if($this->canDoMenu->get('core.edit'))
						{
							JToolBarHelper::divider();
						}
					}

					if($this->canDoMenu->get('core.edit'))
					{
						JToolBarHelper::custom( 'page_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
						JToolBarHelper::divider();
					}
					JToolBarHelper::cancel( 'page.cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
				}
			}
			else
			{

				if($this->useCheckedOut)
				{
					if ($this->canDoMenu->get('core.edit') && $this->canCheckin) 
					{
						JToolBarHelper::custom('page.page_edit','edit','edit', 'JTOOLBAR_EDIT', false);
						JToolBarHelper::divider();
					}
					
					if ($this->canDoMenu->get('core.edit'))
					{
						/*
						Alternativ 1
						JToolBarHelper::apply( 'page.reorder_apply', JText::_('JGRID_HEADING_ORDERING').' '.JText::_('JTOOLBAR_APPLY'));//, JText::_('COM_PAGESANDITEMS_APPLY') );
						JToolBarHelper::save( 'page.reorder_save', JText::_('JGRID_HEADING_ORDERING').' '.JText::_('JTOOLBAR_SAVE'));
						*/
						/*
						Alternativ 2
						JToolBarHelper::custom('page.reorder_apply', 'apply_order','apply_order', JText::_('JTOOLBAR_APPLY'),false);//, 
						JToolBarHelper::custom('page.reorder_save', 'save_order','save_order',JText::_('JTOOLBAR_SAVE'),false);
						*/
						/*
						Alternativ 3 added an icon in the lists header
						*/
					}
					if($this->canDoMenu->get('core.edit.state'))
					{
						//this wew can do in the item JToolBarHelper::publish('page.page_publish');
						//this wew can do in the itemJToolBarHelper::unpublish('page.page_unpublish');

						//this wew can do in also in the item but we will offer here
						JToolBarHelper::trash('page.page_trash','JTOOLBAR_TRASH', false);//,'delete','delete','JTOOLBAR_DELETE',false);
						//JToolBarHelper::divider();
					}
					if($this->canDoMenu->get('core.delete'))
					{
						JToolBarHelper::custom('page.page_delete','delete','delete','JTOOLBAR_DELETE',false);
						if($this->canDoMenu->get('core.edit'))
						{
							JToolBarHelper::divider();
						}
					}
					if ($this->canDoMenu->get('core.edit')) {
						JToolBarHelper::custom( 'page_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
					
					}
					if ($this->canDoMenu->get('core.edit') || $this->canDoMenu->get('core.delete')) 
					{
						JToolBarHelper::divider();
					}
					JToolBarHelper::cancel( 'page.cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
					
				}
				else
				{
					if ($this->canDoMenu->get('core.edit'))
					{
						JToolBarHelper::apply( 'page.page_apply');//, JText::_('COM_PAGESANDITEMS_APPLY') );
						JToolBarHelper::save( 'page.page_save');//, JText::_('COM_PAGESANDITEMS_SAVE') );
					}
				
					if($this->canDoMenu->get('core.delete'))
					{
						JToolBarHelper::custom('page.page_delete','delete','delete','JTOOLBAR_DELETE',false);
						//JToolBarHelper::divider();
					}
					if ($this->canDoMenu->get('core.edit')) {
						JToolBarHelper::custom( 'page_move_select', 'move.png', 'move_f2.png', JText::_('COM_PAGESANDITEMS_MOVE'), false );
						JToolBarHelper::divider();
					}
					JToolBarHelper::cancel( 'page.cancel');//, JText::_('COM_PAGESANDITEMS_CANCEL') );
				}
			}
		}
	}
}

?>