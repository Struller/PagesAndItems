<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table');
//require_once(dirname(__FILE__).'/base.php');
/**


require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
 */


class PagesAndItemsModelPage extends JModel //extends MenusModelItem     PagesAndItemsModelBase
{
	var $_menutypes = array();
	var $_menuitems;
	var $_itemtypes  = array();
	var $_currentMenutype = null;
	var $_currentPageId = null;
	var $_allMenuItems;


	var $pageId = null;
	var $menuItem = null; //is the table object
	
	
	var $modelMenu = 0;
	//var $menuItemsTypes = null;
	var $menuItemsType = 0;
	var $lists = null;
	var $pageType = null;
	var $currentMenuitems = null;

	var $canDo = null;
	public $form;
	public $item;
	public $modules;
	public $state;

	public $view;
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');
		//$modelMenutypes = new PagesAndItemsModelMenutypes();
		//$this->menuItemsTypes = $modelMenutypes->getTypeListComponents();
		//$this->menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'tables');
	}

	//remove?
	function setView($view)
	{
		$this->view = $view;
	}

	//section update for menuitems and all underlying menuitems and their items if category blog page is moved
	function section_update_page($section_update_menu_id, $new_section_id)
	{
		$db = JFactory::getDBO();
		//check if menuitem is content-category-blog, and if so, get cat_id
		$config = PagesAndItemsHelper::getConfig();
		$content_category_blog = false;
		
		$db->setQuery("SELECT link, type FROM #__menu WHERE id='$section_update_menu_id' LIMIT 1");
		$rows = $db->loadObjectList();
		$row = $rows[0];
		if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url')){
			$content_category_blog = true;
			$cat_id = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$row->link);
		}

		//only update items on page when its a content_blog_category
		if($content_category_blog)
		{
			$this->update_items_category($cat_id, $new_section_id);
		}

		//update category
		$db->setQuery( "UPDATE #__categories SET section='$new_section_id' WHERE id='$cat_id'");
		$db->query();

		//update all underlying child-pages
		if($config['child_inherit_from_parent_move'])
		{
			$this->section_update_children($section_update_menu_id, $new_section_id);
		}
	}


	//update section id for underlying pages and all items on them
	function section_update_children($section_update_page_id, $new_section_id)
	{
		$db = JFactory::getDBO();
		//in J1.6 we have no section and the field parent is renamed to parent_id
		//$db->setQuery("SELECT id FROM #__menu WHERE parent='$section_update_page_id'"  );
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$db->setQuery("SELECT id FROM #__menu WHERE parent='$section_update_page_id'" );
		}
		else
		{
			$db->setQuery("SELECT id FROM #__menu WHERE parent_id='$section_update_page_id'" );
		}
		$rows = $db->loadObjectList();
		foreach($rows as $row)
		{
			$this->section_update_page($row->id, $new_section_id);
		}
	}



	function change_menutype_check_children($page_id, $new_menutype)
	{
		//in J1.6 we have no section and the field parent is renamed to parent_id
		//$this->db->setQuery("SELECT id FROM #__menu WHERE parent='$page_id'"  );
		$db = JFactory::getDBO();
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$db->setQuery("SELECT id FROM #__menu WHERE parent='$page_d'" );
		}
		else
		{
			$db->setQuery("SELECT id FROM #__menu WHERE parent_id='$page_id'" );
		}

		$rows = $db-> loadObjectList();

		foreach($rows as $row)
		{
			$this->change_menutype($row->id, $new_menutype);
		}
	}

	function change_menutype($page_id, $new_menutype)
	{
		$db = JFactory::getDBO();
		$db->setQuery( "UPDATE #__menu SET menutype='$new_menutype' WHERE id='$page_id'");
		if (!$db->query())
		{
			echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>";
			exit();
		}

		$this->change_menutype_check_children($page_id, $new_menutype);
	}

	function get_sublevel_add_one($menu_id)
	{
		$sublevel = 0;
		$menuitems = PagesAndItemsHelper::getMenuitems();
		foreach($menuitems as $menuitem)
		{
			if($menuitem->id==$menu_id){
				$sublevel = $menuitem->sublevel+1;
				break;
			}
		}

		return $sublevel;
	}

	//TODO only for wich pagetype?
	function get_categories()
	{
		$db = JFactory::getDBO();
		static $pi_category_array;
		if(!$pi_category_array)
		{
			$db->setQuery("SELECT id, title, section FROM #__categories ");
			$pi_categories_object = $db->loadObjectList();

			$pi_category_array = array();
			foreach($pi_categories_object as $category)
			{
				$pi_category_array[] = array($category->id, $category->title, $category->section);
			}
		}
		return $pi_category_array;
	}

	//TODO only for wich pagetype?  and Joomla 1.6 have no sections
	function get_sections()
	{
		$db = JFactory::getDBO();
		static $pi_sections_array;
		if(!$pi_sections_array)
		{
			$db->setQuery("SELECT id, title FROM #__sections ");
			$pi_sections_object = $db->loadObjectList();

			$pi_sections_array = array();
			foreach($pi_sections_object as $pi_section)
			{
				$pi_sections_array[] = array($pi_section->id, $pi_section->title);
			}
		}
		return $pi_sections_array;
	}



	function check_display_item_property($property, $right)
	{
		$display = false;
		$config = PagesAndItemsHelper::getConfig();
		
		if(PagesAndItemsHelper::getIsSuperAdmin() && !$config['item_props_hideforsuperadmin'])
		{
			$display = true;
		}
		else
		{
			//check configuration
			if($config[$property])
			{
				$display = true;
			}
		}
		//$display = true;
		return $display;
	}

/*
**********
from view/page/view.html.php
**********
*/


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

	function getMenuItem()
	{
		$sub_task = JRequest::getVar( 'sub_task', '');
		$menutype = JRequest::getVar( 'menutype');
		$extension = 'com_menus';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		
		
		//$type = JRequest::getVar( 'type'); //from
		$pageType = JRequest::getVar( 'pageType', '' );
		
		$modelMenu = null;
		$item = null;
		$pageId = JRequest::getVar('pageId');
		$app	= JFactory::getApplication();
		$option = JRequest::getVar('option');
		if($sub_task=='new')
		{
			//new
			$type = $app->getUserStateFromRequest( $option.'.page.type', 'type','','cmd' );
			//$app->setUserState( $option.'.page.type',null);
			$pageType = $app->getUserStateFromRequest( $option.'.page.pageType', 'pageType','','cmd' );
			//$app->setUserState( $option.'.page.pageType', null);
			$pageTypeType = $app->getUserStateFromRequest( $option.'.page.pageTypeType', 'pageTypeType','','type','cmd' );
			//$app->setUserState( $option.'.page.pageTypeType', null);
			/*
			$pageTypeType = JRequest::getVar('pageTypeType');
			*/
			$pageTypeType = json_decode(base64_decode($pageTypeType));
			if(isset($pageTypeType->request))
			{
				$url = null;
				foreach($pageTypeType->request as $key => $value)
				{
					$url[$key] = $value;
				}

				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
				{
					JRequest::setVar( 'url',  $url);
					JRequest::setVar( 'edit', false );
					//$modelMenu = &new MenusModelItem();
					$modelMenu = new MenusModelItem();
					$item = $modelMenu->getItem();
				}
				else
				{
					// Push the new ancillary data into the session.
					$app->setUserState('com_menus.edit.item.type',	null);
					$app->setUserState('com_menus.edit.item.link',	null);

					$parent_id = JRequest::getVar('pageId', '');
					$app->setUserState('com_menus.edit.item.parent_id', $parent_id);
					
					
					//to set the correct type we need to tell the model ignore_request
					//$modelMenu = &new MenusModelItem(array('ignore_request'=>true));
					$modelMenu = new MenusModelItem(array('ignore_request'=>true));
					JRequest::setVar( 'id', 0 );
					$modelMenu->setState('item.type',$pageTypeType->type);
					$modelMenu->setState('item.menutype',$menutype);
					$modelMenu->setState('item.parent_id',$parent_id);
					// Check if the link is in the form of index.php?...

					if($url)
					{
						if (is_string($url))
						{
							$args = array();
							if (strpos($url, 'index.php') === 0)
							{
								parse_str(parse_url(htmlspecialchars_decode($url), PHP_URL_QUERY), $args);
							}
							else
							{
								parse_str($url, $args);
							}
							$url = $args;
						}
						// Only take the option, view and layout parts.
						$filter = array('option', 'view', 'layout');
						foreach ($url as $name => $value)
						{
							if (!in_array($name, $filter))
							{
								// Remove the variables we want to ignore.
								unset($url[$name]);
							}
						}
						$link = 'index.php?'.http_build_query($url,'','&');
					}
					else
					{
						$link = '';
					}
					$modelMenu->setState('item.link', $link); //MenusHelper::getLinkKey($url));
					$modelMenu->setState('item.id',0);
					$item = $modelMenu->getItem();
					/*
					$app->setUserState( $option.'.page.type',null);
					$app->setUserState( $option.'.page.pageType', null);
					$app->setUserState( $option.'.page.pageTypeType', null);
					
					*/
					/*
					$this->form		= $modelMenu->getForm();
					$this->item		= $item;
					$this->modules	= $modelMenu->getModules();
					$this->state	= $modelMenu->getState();
					*/
				}
			}
			//End new
		}
		else
		{
			//edit
			$app->setUserState( $option.'.page.type',null);
			$app->setUserState( $option.'.page.pageType', null);
			$app->setUserState( $option.'.page.pageTypeType', null);
			if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
			{
				JRequest::setVar( 'edit', true );
				//JRequest::setVar( 'cid',  array(PagesAndItemsHelper::getPageId()));
				JRequest::setVar( 'cid',  array($pageId));
				//$modelMenu = &new MenusModelItem();
				$modelMenu = new MenusModelItem();
				$item = $modelMenu->getItem();
			}
			else
			{
				$app	= JFactory::getApplication();
				// Push the new ancillary data into the session.
				$app->setUserState('com_menus.edit.item.type',	null);
				$app->setUserState('com_menus.edit.item.link',	null);

				//add addIncludePath
				//to set the correct type we need to tell the model ignore_request
				//$modelMenu = &new MenusModelItem(array('ignore_request'=>true));
				$modelMenu = new MenusModelItem(array('ignore_request'=>true));
				//$modelMenu = &new MenusModelItem();
				

				
				JRequest::setVar( 'id', $pageId ); 
				//JRequest::setVar( 'id', $getPageId );
				$modelMenu->setState('item.id',$pageId); //$modelMenu->setState('item.id',$getPageId);

				$modelMenu->setState('item.menutype',$menutype);
				
				
				//ms: com_menus.edit.item.id
				//$modelMenu->checkout($pageId);
				
				//
				$item = $modelMenu->getItem($pageId); //$item = $modelMenu->getItem($getPageId);
				
				//$config = PagesAndItemsHelper::getConfigAsRegistry();
				//$useCheckedOut = $config->get('useCheckedOut',0);
				$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
				if($useCheckedOut && $sub_task=='edit')
				{
					$modelMenu->checkout();
				}
				
				/*
				TODO
				Notice: Trying to get property of non-object in C:\Users\carsten\Documents\_websites\pages_and_items_17_temp\administrator\components\com_pagesanditems\models\page.php on line 1381 here 1391
				Notice: Trying to get property of non-object in C:\Users\carsten\Documents\_websites\pages_and_items_17_temp\administrator\components\com_pagesanditems\models\page.php on line 1391 here 1401
				Notice: Trying to get property of non-object in C:\Users\carsten\Documents\_websites\pages_and_items_17_temp\administrator\components\com_pagesanditems\models\page.php on line 1393 here 1403
				
				
				check if we have $item and $item->link
				if(!$item) message no item
				if(!$item->link) message no item-link
				*/
				//$modelMenu->setState('item.link',$item->link); //need her???
			}
			//End edit
		}
		
		$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
		if(!$pageType && $item)
		{
			//ms fix for seperator alias and url
			
			if($item->type != 'component') // || $item->type != 'components')
			{
				$pageType = $item->type;
			}
			else
			{
				$model = new PagesAndItemsModelMenutypes();
				if($model)
				{
					$pageType =$model->buildPageType($item->link);
					//if(!isset($this->menuItemsTypes[$pageType]))
					if(!isset($menuItemsTypes[$pageType]))
					{
						$pageType = null;
					}
				}
				
			}
		}
		/*
		if we have no $pageType that must be the root in tree ?
		*/
		if($pageType && $item)
		{
			//we have an pageType
			$this->pageType = $pageType;
			$this->menuItemsType = $menuItemsTypes[$pageType]; //$this->menuItemsTypes[$pageType];
			$this->modelMenu = $modelMenu;
			//$this->pageMenuItem = $item;
			$this->menuItem = $item;
			
			$dispatcher = &JDispatcher::getInstance();
			/*
			we want not get the other loaded pagetypes so we detach
			so only the $pageType raise the event
			*/
			$dispatcher->trigger('onDetach',array($pageType));
			$name = '';
			$results = $dispatcher->trigger('onGetPagetype',array(&$name,$pageType));
			return $item;
		}
		return false;
	}




	//USED
	function getLists()
	{
		// Was showing up null in some cases....
		//menuItem is an #__menus table object

		if (PagesAndItemsHelper::getIsJoomlaVersion('<','1.6') && !$this->menuItem->published)
		{
			$this->menuItem->published = 0;
		}

		$lists = new stdClass();

		//this can be '' ore 'style="display:none;"';
		$lists->display->id = '';
		$lists->display->title = '';
		$lists->display->alias = '';
		$lists->display->link = '';
		$lists->display->menutype = '';
		$lists->display->parent = '';
		$lists->display->published = '';
		$lists->display->ordering = '';
		$lists->display->accesslevel = '';
		$lists->display->menulink = '';
		$lists->display->params = ''; //'style="display:none;"';//'';
		$lists->display->advancedparams = '';
		$lists->display->componentparams = '';
		$lists->display->systemparams = '';
		$lists->hideAll = '';

		//free html
		$lists->add->bottom = '';
		$lists->add->top = '';


		//other way for joomla 1.6
		//
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$lists->published = MenusHelper::Published($this->menuItem); //return radiolist
		}
		else
		{
			$lists->published = $this->form->getInput('published');
			$this->menuItem->name = $this->menuItem->title;
		}
		$this->menuItem->expansion = null;
		if ($this->menuItem->type != 'url')
		{
			$lists->disabled->link = 'readonly="true"';
			$this->menuItem->linkfield = '<input type="hidden" name="link" value="'.$this->menuItem->link.'" />';
			if (($this->menuItem->id) && ($this->menuItem->type == 'component') && (isset($this->menuItem->linkparts['option'])))
			{
				$this->menuItem->expansion = '&amp;expand='.trim(str_replace('com_', '', $this->menuItem->linkparts['option']));
			}
		}
		else
		{
			$lists->disabled->link = null;
			$this->menuItem->linkfield = null;
		}


		if(!$this->menuItem->home)
		{
			$this->menuItem->home = 0;
		}
		$put[] = JHTML::_('select.option',  '0', JText::_( 'No' ));
		$put[] = JHTML::_('select.option',  '1', JText::_( 'Yes' ));
		$lists->home = JHTML::_('select.radiolist',  $put, 'home', '', 'value', 'text', $this->menuItem->home );
		$lists->pageType->html = '';
		$dispatcher = &JDispatcher::getInstance();
		$dispatcher->trigger('onGetLists',array(&$lists,$this->menuItem,$this));
		$this->lists = $lists;
		return $lists;
	}

	//can remove 
	//go to includes/lists/pageslist
	function getChilds($menuItem = null,$currentMenuitems = null)
	{
		//$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
		//$this->menuItem is same getItem()
		if($menuItem)
		{
			$this->menuItem = $menuItem;
		}
		if($currentMenuitems)
		{
			$this->currentMenuitems = $currentMenuitems;
		}
		
		$html = '';
		//$html .= '<script src="components/com_pagesanditems/javascript/reorder_pages.js" language="JavaScript" type="text/javascript">';
		//$html .= '</script>';
		$html .= '<thead class="piheader">';
			$html .= '<tr>';
				$html .= '<th class="piheader">'; //style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
				if($this->menuItem && $this->menuItemsType)
				{
					$menuItemsType = $this->menuItemsType;
					if(isset($menuItemsType->icons->default->imageUrl))
					{
						$image = $menuItemsType->icons->default->imageUrl;
					}
					/*
					if($image)
					{

						$imgClass = explode("class:",$image);
						if(count($imgClass) && count($imgClass) == 2)
						{
							//we have an class
							$html .= '<a ';
							$html .= 'class="icon '.$imgClass[1].'" ';
							$html .= 'alt="" >&nbsp;';
							$html .= '<span>';
							$html .= '</span>';
							$html .= '</a>';
						}
						else
						{
							$html .= '<img src="'.$image.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
						}
						$html .= JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
					}
					else
					*/
					if(!$image)
					{
						//$html .= '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
						//$html .= JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
						$image = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
					}
				}
				else
				{
					/*
					$html .= '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
					$html .= JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
					*/
					$image = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
				}
				
				
				
				$menutype = JRequest::getVar('menutype', null);
				$pageId = JRequest::getVar('pageId', null);
				$title = JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
				if($menutype) // && !$pageId)
				{
					//$html .= '&nbsp; <small>['.PagesAndItemsHelper::getMenutypeTitle($menutype).']</small>';
					$title .= ' <small>['.PagesAndItemsHelper::getMenutypeTitle($menutype).']</small>';
				}
				if($pageId && $this->menuItem)
				{
					//$html .= '&nbsp;<small>['.$this->modelMenu->getItem()->title.']</small>';
					//$title .= ' <small>['.$this->modelMenu->getItem()->title.']</small>';
					$title .= ' <small>['.$this->menuItem->title.']</small>';
				}
				$html .= PagesAndItemsHelper::getThImageTitle($image,$title);
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody id="underlayingPages">';
			$html .= '<tr>';
				$html .= '<td>';
					$html .=  $this->getUnderlyingPages();
				$html .= '</td>';
				$html .= '</tr>';
		$html .= '</tbody>';
		return $html;
	}

	//can remove ?
	//go to includes/lists/pageslist
	//underlyingPages
	function getUnderlyingPages()
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'pageslist.php');
		$PagesList = new PagesList();
		return $PagesList->getUnderlyingPages($this->currentMenuitems);
	}

	function getPageItems()
	{
		$html = '';
		$layout = JRequest::getVar('layout',null);
		if(!$layout)
		{
			$dispatcher = &JDispatcher::getInstance();
			$dispatcher->trigger('onGetPageItems',array(&$html,$this));
		}

		return  $html;
	}



	/*
	 * @params mixed array||boolean $editToolbarButtons eg. array('new',delete'..) ore true for all
	 * @param mixed array||string $newToolbarButtons eg. array('new',delete'..) ore true for all



	getContentItems||getContentItem is call from the pageType
	if we make other pageTypes we must not use getContentItems||getContentItem
	is only for pageTypes that handle content

	*/
	//move to ???
	function getContentItems($editToolbarButtons = true, $newToolbarButtons = true,$showItemtype_select=true,$menuItem = false)
	{
		$db = JFactory::getDBO();
		// ms: here can the error Call to a member function getItem() on a non-object (e-mail from cs)
		// but if the $this->modelMenu not exist we must get an error before see line 1438
		// function getContentItems is call from the extensions/pagetype
		//
		$menuItem = $menuItem ?  $menuItem : $this->menuItem; //$this->modelMenu->getItem(PagesAndItemsHelper::getPageId());
		
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$menu_item_urlparams = $this->modelMenu->getUrlParams();
			$menu_item_id = $menu_item_urlparams->get('id',null);

			$menu_item_view = $menu_item_urlparams->get('view',null);
			$menu_item_layout = $menu_item_urlparams->get('layout',null);
		}
		else
		{
			if(isset($menuItem->request['id']))
			{
				$menu_item_id = $menuItem->request['id'];
			}
			elseif(isset($menuItem->params['id']))
			{
				$menu_item_id = $menuItem->params['id'];
			}
			else
			{
				$menu_item_id = 0;
			}

			if(isset($menuItem->request['view']))
			{
				$menu_item_view = $menuItem->request['view'];
			}
			elseif(isset($menuItem->params['view']))
			{
				$menu_item_view = $menuItem->params['view'];
			}
			else
			{
				$menu_item_view = null;
			}

			if(isset($menuItem->request['layout']))
			{
				$menu_item_layout = $menuItem->request['layout'];
			}
			elseif(isset($menuItem->params['layout']))
			{
				$menu_item_layout = $menuItem->params['layout'];
			}
			else
			{
				$menu_item_layout = null;
			}
		}
		$subOrdering = true;
		//ms: add
		if($menu_item_view == 'featured')
		{
			// Filter by categories
			if(isset($menuItem->params['featured_categories']))
			{
				if (is_array($featuredCategories = $menuItem->params['featured_categories']) )
				{
					if(count($featuredCategories) && $featuredCategories[0] != '' )
					$where[] = "c.catid IN (" . implode(',',$featuredCategories) . ")";
				}
			}
		}
		elseif($menu_item_view == 'categories')
		{
			//in frontend no articles will display from this categorie
		}
		elseif($menu_item_view == 'category' && $menu_item_layout != 'blog')
		{
			//in frontend no articles will display from this categorie
			//jimport( 'joomla.application.component.helper' );
			//$contentParams  = JComponentHelper::getParams('com_content');
			/*
			
			//'show_subcategory_content'
			//the category_blog and categories have an show_subcategory_content and can also all other articles in the subcategories and the level?
			//this is the description:
			//If None, only articles from this category will show. If a number, all articles from the category and the subcategories up to and including that level will show in the blog
			
			
			*/
			if($menu_item_layout == 'blog')
			{
				/*
				att this moment we show only items on the $menu_item_id
				
				$content_show_subcategory_content = $contentParams->get('show_subcategory_content','');
				$show_subcategory_content = $menuItem->params['show_subcategory_content'];
				
				if($show_subcategory_content == '')
				{
					$show_subcategory_content = $content_show_subcategory_content;
				}
				if($show_subcategory_content == -1)
				{
					$maxLevelCat = 0;
				}
				else
				{
					$maxLevelCat = $show_subcategory_content;
				}
				
				if($show_subcategory_content)
				{
					//here we must get the categories with level
					
					$db->setQuery("SELECT * FROM #__categories  WHERE extension='com_content' AND id='".$menu_item_id."' ORDER BY lft ASC" );
					$parent1 = $db->loadObject();
					$level = $parent1->level;

					$db->setQuery("SELECT * FROM #__categories  WHERE extension='com_content' AND parent_id='".$menu_item_id."' ORDER BY lft ASC" );
					$items = $db->loadObjectList();
					
					$options = array();
					//$options['countItems'] = $params->get('show_cat_num_articles_cat', 1) || !$params->get('show_empty_categories_cat', 0);
					$options['published'] = 0;
					require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extendedJCategories.php');
					$categories = extendedJCategories::getInstance('Content', $options);
					$parent = $categories->get($menu_item_id,true);

					if (is_object($parent)) 
					{
						$items = $parent->getChildren(true);
					}
					else {
						$items = false;
					}
					
					$moreCategories = array();
					$moreCategories[] = $menu_item_id;
					foreach($items as $item => $value)
					{
						if( $maxLevelCat && ($value->level > ($level + $maxLevelCat) ) )
						{
							unset($items[$item]);
						}
						else
						{
							$moreCategories[] = $value->id;
							$subOrdering = false;
						}
						
					}
					$where[] = "c.catid IN (" . implode(',',$moreCategories) . ")";
				}
				else
				{
					$where[] = "c.catid='".$menu_item_id."'";
				}
				*/
				$where[] = "c.catid='".$menu_item_id."'";
			}
			else
			{
				//Kategorielist display no content/article
				//$where[] = "c.catid='".$menu_item_id."'";
			}

			//$where[] = "c.catid='".$menu_item_id."'";
		}
		elseif($menu_item_view == 'category' && $menu_item_layout == 'blog')
		{
			if($menu_item_id == -1)
			{
				$queryTemp = "SELECT id "
				. "\nFROM #__categories "
				. "\nWHERE extension='com_content' ";
				$where[] = "c.catid NOT IN (".$queryTemp.")";;
			}
			else
			{
				$where[] = "c.catid='".$menu_item_id."'";
			}

//			$where[] = "c.catid='".$menu_item_id."'";
		}
		elseif($menu_item_view == 'section')
		{
			$where[] = "c.sectionid='".$menu_item_id."'";
		}

		if($menu_item_view == 'archive')
		{
			if($joomlaVersion < '1.6')
			{
				$where[] = "c.state='-1'";
			}
			else
			{
				$where[] = "c.state='2'";
			}
		}
		else
		{
			//$where[] = "(c.state='0' OR c.state='1')";
		}

		if($menu_item_view == 'frontpage' || $menu_item_view == 'featured')
		{
			$frontpage = "\n INNER JOIN #__content_frontpage AS f ON c.id=f.content_id ";
		}
		else
		{
			$frontpage = '';
		}

		if($joomlaVersion < '1.6')
		{
			$menu_item_advancedparams = $this->modelMenu->getAdvancedParams();
			$orderByPri = $menu_item_advancedparams->get('orderby_pri',null);
			$orderBySec = $menu_item_advancedparams->get('orderby_sec',null);
			$orderBy = $menu_item_advancedparams->get('orderby',null);
		}
		else
		{
			/*
			# [string] orderby_pri = ""
			# [string] orderby_sec = "front"
			# [string] order_date = ""
			*/
			//$menu_item_advancedparams = $this->menu_item->getAdvancedParams();
			if(isset($menuItem->params['orderby_pri']))
			{
				$orderByPri = $menuItem->params['orderby_pri'];
			}
			else
			{
				$orderByPri = null;
			}

			if(isset($menuItem->params['orderby_sec']))
			{
				$orderBySec = $menuItem->params['orderby_sec'];
			}
			else
			{
				$orderBySec = null;
			}

			if(isset($menuItem->params['orderby']) && $menuItem->params['orderby'] != '')
			{
				$orderBy = $menuItem->params['orderby'];
			}
			else
			{
				$orderBy = null;
			}
		}
		$ordering = false;
		if($orderBy)
		{
			$orderBySec = $orderBy;
		}
		else
		{
			switch($orderByPri)
			{
				case 'alpha':
					$order = "c.title ASC";
					$orderBySec = null;
				break;
				case 'ralpha':
					$order = "c.title DESC";
					$orderBySec = null;
				break;
				case 'order':
					$ordering = true;
				break;
				default:
				break;
			}
		}
		switch ($orderBySec)
		{
			case 'date':
				$order = 'c.created ASC';
			break;
			case 'rdate':
				$order = 'c.created DESC';
			break;
			case 'alpha':
			default:
				$order = 'c.title ASC';
			break;
			case 'ralpha':
				$order = 'c.title DESC';
			break;
			case 'author':
				$order = 'u.username ASC';
			break;
			case 'rauthor':
				$order = 'u.username DESC';
			break;
			case 'hits':
				$order = 'c.hits ASC';
			break;
			case 'rhits':
				$order = 'c.hits DESC';
			break;
			case 'order':
				$order = 'c.ordering ASC';
				$ordering = true;
			break;
			case 'front':
				$order = 'c.ordering ASC';
				$ordering = true;
			break;
		}
		/*


		*/
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		$query = "SELECT c.id, c.title, c.state, c.catid, i.itemtype, c.created_by, u.username "
		. "\nFROM #__content AS c "
		. $frontpage
		. "\nLEFT JOIN #__pi_item_index AS i ON c.id=i.item_id "
		. "\nLEFT JOIN #__users AS u ON u.id=c.created_by "
		. "\n $where "
		. "\nORDER BY $order ";
		$db->setQuery( $query );



		$rows = $db->loadObjectList();
		$ordering = ($ordering && $subOrdering) ? true : false;


		$html = '';
		$html .= '<div class="paddingList">';
		//itemtype select and button
		//ms: 10.10.2011 we must go other way for canDo
		//$this->getCanDo('com_content');
		$canDoContent = PagesAndItemsHelper::canDoContent();
		//if($showItemtype_select && $this->canDo->com_content->get('core.create'))
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = JRequest::getCmd('sub_task', '');
		if($showItemtype_select && $canDoContent->get('core.create') && (!$useCheckedOut || !$sub_task == 'edit'))
		{
			$html .= PagesAndItemsHelper::itemtype_select(PagesAndItemsHelper::getPageId()); //$this->itemtype_select(PagesAndItemsHelper::getPageId());
			if(count($rows) == 1)
			{
				$html .= '<div class="pi_wrapper">';
					$html .= '<div class="line_top paddingList">';
					$html .= '</div>';
				$html .= '</div>';
			}
			/*
			$html .= '<div class="pi_wrapper">';
				$html .= '<div class="line_top paddingList">';
				$html .= '</div>';
			$html .= '</div>';
			*/
		}
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'itemslist.php');
		$ItemsList = new ItemsList();
		//$toolbar .= $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
		if(count($rows))
		{
			$toolbar = $ItemsList->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
		}
		else
		{
			$toolbar = '';
		}

		return $ItemsList->renderItems($html,$rows, $toolbar,$ordering);
		//return $this->renderItems($html,$rows, $toolbar,$ordering);
	}

	/*
	 * @params mixed array||boolean $editToolbarButtons eg. array('new',delete'..) ore true for all
	 * @param mixed array||string $newToolbarButtons eg. array('new',delete'..) ore true for all
	*/
	function getContentItem($editToolbarButtons = true, $newToolbarButtons = true)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'itemslist.php');
		$ItemsList = new ItemsList();
		$menuItem = $this->menuItem; //$this->modelMenu->getItem(PagesAndItemsHelper::getPageId());
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$menu_item_urlparams = $this->modelMenu->getUrlParams();
			$menu_item_id = $menu_item_urlparams->get('id',null);
		}
		else
		{
			if(isset($menuItem->request['id']))
			{
				$menu_item_id = $menuItem->request['id'];
			}
			elseif(isset($menuItem->params['id']))
			{
				$menu_item_id = $menuItem->params['id'];
			}
			else
			{
				$menu_item_id = 0;
			}
		}
		$db = JFactory::getDBO();
		//check if exist
		$db->setQuery( "SELECT c.id, c.state,c.title, c.catid, i.itemtype, c.created_by, u.username "
			. "\nFROM #__content AS c "
			. "\nLEFT JOIN #__pi_item_index AS i ON c.id=i.item_id "
			. "\nLEFT JOIN #__users AS u ON u.id=c.created_by "
			. "\nWHERE c.id='$menu_item_id' "
			//. "\nAND (c.state='0' OR c.state='1' ) " //
			);
		$rows = $db->loadObjectList();




		if(!count($rows))
		{
			$menu_item_id = 0;
		}

		$html = '';
		$html .= '<div class="paddingList">';
			//itemtype select and button only display if an single Content typ and no id
			//ms: 10.10.2011 we must go other way for canDo
			//$this->getCanDo('com_content');
			$canDoContent = PagesAndItemsHelper::canDoContent();
			$toolbar = false;
			//if(!$menu_item_id && $this->canDo->com_content->get('core.create'))
			if(!$menu_item_id && $canDoContent->get('core.create'))
			{
				$html .= PagesAndItemsHelper::itemtype_select(PagesAndItemsHelper::getPageId()); //$this->itemtype_select(PagesAndItemsHelper::getPageId());

				$html .= '<div class="pi_wrapper">';
					$html .= '<div class="line_top paddingList">';
					$html .= '</div>';
				$html .= '</div>';
				$toolbar = false;
			}
			elseif(!$menu_item_id && !$canDoContent->get('core.create'))
			//elseif(!$menu_item_id && !$this->canDo->com_content->get('core.create'))
			{
				$html .= '<div class="pi_wrapper">';
				$html .= JText::_('COM_PAGESANDITEMS_NO_PERMISSION_CREATE_NEW_ITEM');
				$html .= '</div>';
				$toolbar = false;
			}
			//line_top
			//$html .= $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
			elseif($menu_item_id)
			{
				$toolbar = $ItemsList->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
			}
			return $ItemsList->renderItems($html,$rows,$toolbar,0,'COM_PAGESANDITEMS_ITEM');
			//$this->renderItems($html,$rows,$toolbar,0,'COM_PAGESANDITEMS_ITEM');
	}

	//can remove ?
	function renderItems($html, $rows, $toolbar, $ordering=0,$sliderText = 'COM_PAGESANDITEMS_ITEMS')
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'itemslist.php');
		$ItemsList = new ItemsList();
		return $ItemsList->renderItems($html, $rows, $toolbar, $ordering,$sliderText);
	}

	function getPagePropertys()
	{

		$sub_task = JRequest::getVar( 'sub_task', '');

		$html = '';
		$this->pageId = PagesAndItemsHelper::getPageId();
		
		//$this->getMenuItem();
		if(($this->pageId || $sub_task == 'new') && ($this->menuItem)) // || $this->modelMenu))
		{
			//first we must check for acl
			$canDoMenus = PagesAndItemsHelper::canDoMenus();
			if($sub_task == 'new')
			{
				//$canDoMenus = PagesAndItemsHelper::canDoMenus();
				if(!$canDoMenus->get('core.create') )
				{
					//add message ore text
					PagesAndItemsHelper::to_previous_page_when_no_permission('1');
				}
			}
			else
			{
				//$canDoMenus = PagesAndItemsHelper::canDoMenus($this->pageId);
				if(!$canDoMenus->get('core.edit') )
				{
					//add message ore text but not to_previous_page
					//the user must can get all underlaying-pages and all page items
					$message = JText::_('COM_PAGESANDITEMS_NO_EDIT_PAGE');
					
					$html .= $message;
					return $html;
				}
			}
			
			if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
			{
				$html = $this->getFormPagePropertys();
			}
			else
			{
				$this->form = $this->modelMenu->getForm();
				$this->modules = $this->modelMenu->getModules();
				$html = $this->getFormPagePropertys();
			}
		}
		
		return $html;

	}

	function getForm()
	{
		return $this->form;
	}
	
	function getModules()
	{
		return $this->modules;
	}

	function getMenuItemsType()
	{
		return $this->menuItemsType;
	}

	function isGetPagePropertys()
	{

		$sub_task = JRequest::getVar( 'sub_task', '');

		$html = '';
		$this->pageId = PagesAndItemsHelper::getPageId();
		$app	= JFactory::getApplication();
		$this->getMenuItem();
		if(($this->pageId || $sub_task == 'new') && ($this->menuItem )) //|| $this->modelMenu))
		{
			//first we must check for acl
			$canDoMenus = PagesAndItemsHelper::canDoMenus();
			if($sub_task == 'new')
			{
				//$canDoMenus = PagesAndItemsHelper::canDoMenus();
				if(!$canDoMenus->get('core.create') )
				{
					//add message ore text
					PagesAndItemsHelper::to_previous_page_when_no_permission('1');
				}
			}
			else
			{
				//$canDoMenus = PagesAndItemsHelper::canDoMenus($this->pageId);
				if(!$canDoMenus->get('core.edit') )
				{
					//add message ore text but not to_previous_page
					//the user must can get all underlaying-pages and all page items
					//$message = JText::_('COM_PAGESANDITEMS_NO_EDIT_PAGE');
					$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_NO_EDIT_PAGE'), 'notice');
					
					//$html .= $message;
					
					return false;
				}
			}
			
			if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
			{
				//$html = $this->getFormPagePropertys();
				return true;
			}
			else
			{
				$this->form = $this->modelMenu->getForm();
				$this->modules = $this->modelMenu->getModules();
				return true;
				$html = $this->getFormPagePropertys();
			}
		}
		return false;
		return $html;

	}


	//can remove ?
	//todo move to views/page/tmpl/default_pagepropertys.php
	//an to views/page/tmpl/root_pagepropertys.php = include views/page/tmpl/default_pagepropertys.php
	function getFormPagePropertys()
	{
		$html = '';
		$sub_task = JRequest::getVar( 'sub_task', 'edit');
		$lang = &JFactory::getLanguage();
		//$modelMenu = $this->modelMenu;

		if(isset($this->menuItem->request['option']))
		{
			//$lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR);
			$lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR, null, false, false) || $lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		}
		$menu_item_name = $this->menuItem->title; //must change tothe ???
		$menu_item_description = $this->form->getInput('type');
		$menu_item_parent_id = $this->form->getInput('parent_id');
		
		$buttonLinkMenutype = '';
		$buttonLinkMenutype .= '<div>';
			//here we can set an select type to change se type?
			$buttonLinkMenutype .= $menu_item_description;
		$buttonLinkMenutype .= '</div>';
		$this->getLists();
		$html .= '<script language="JavaScript" type="text/javascript">';
			$html .= '<!--';
			$html .= 'function popupPageBrowser(url)';
			$html .= '{';
				$html .= 'var winl = (screen.width - 400) / 2;';
				$html .= 'var wint = (screen.height - 400) / 2;';
				$html .= "winprops = 'height=400,width=400,top='+wint+',left='+winl+',scrollbars=yes,resizable';";
				$html .= "linkValue = document.getElementById('link').value;";
				$html .= 'linkValue = escape(linkValue);';
				$html .= "urlString = url+'&url='+linkValue;";
				$html .= "win = window.open(urlString, 'pages', winprops);";
				$html .= 'if (parseInt(navigator.appVersion) >= 4)';
				$html .= '{';
					$html .= 'win.window.focus();';
				$html .= '}';
			$html .= '}';
			$html .= '-->';
		$html .= '</script>';

		if($this->menuItem && $this->menuItemsType)
		{
			$menuItemsType = $this->menuItemsType;
			$image = false;
			$imageNew = false;
			$imageEdit = false;
			$imageBulletNew = '';
			$imageBulletEdit = '';
			if(isset($menuItemsType->icons->default->imageUrl))
			{
				$image = $menuItemsType->icons->default->imageUrl;
			}
			if(isset($menuItemsType->icons->new->imageUrl))
			{
				$imageNew = $menuItemsType->icons->new->imageUrl;
			}
			else
			{
				$imageNew = $image;
				$imageBulletNew = PagesAndItemsHelper::getDirIcons().'base/bullets/icon-16-bullet_new.png';
			}
			if(isset($menuItemsType->icons->edit->imageUrl))
			{
				$imageEdit = $menuItemsType->icons->edit->imageUrl;
			}
			else
			{
				$imageEdit = $image;
				$imageBulletEdit = PagesAndItemsHelper::getDirIcons().'base/bullets/icon-16-bullet_edit.png';
			}
			if($sub_task=='new')
			{
				if(!$imageBulletNew && $imageNew)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageNew,JText::_('COM_PAGESANDITEMS_NEW_PAGE'));
				}
				elseif($imageBulletNew && $imageNew)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageNew,JText::_('COM_PAGESANDITEMS_NEW_PAGE'),$imageBulletNew);
				}
				else
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',JText::_('COM_PAGESANDITEMS_NEW_PAGE'));
				}
			}
			else
			{
				if(!$imageBulletEdit && $imageEdit)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageEdit,JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )');
				}
				elseif($imageBulletEdit && $imageEdit)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageEdit,JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )',$imageBulletEdit);
				}
				else
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )');
				}
			}
		}
		else
		{
			if($sub_task=='new')
			{
				$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu.png',JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )');
			}
			else
			{
				$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )');
				
				
			}
		}

		$html .='<table class="piadminform xadminform" width="98%">';
		$html .= '<thead class="piheader">';
			$html .='<tr>';
				$html .='<th>'; // class="piheader">'; // style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
					$html .= $imageDisplay;

				$html .='</th>';
			$html .='</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			$html .='<tr>';
				$html .='<td>';
						JHtml::_('behavior.tooltip');
						JHtml::_('behavior.formvalidation');
						JHTML::_('behavior.modal');

						$html .='<!-- $this->lists->display-> -->';
						$html .='<div class="width-60 fltlft">';
							$html .='<fieldset class="adminform">';
								$html .='<legend>'.JText::_('COM_MENUS_ITEM_DETAILS').'</legend>';

									$html .='<ul class="adminformlist">';
										//do not display when new
										if($this->menuItem->id){
											$html .='<li '.$this->lists->display->id.'>'.$this->form->getLabel('id');
											$html .= $this->form->getInput('id').'</li>';
										}
										$this->form->setFieldAttribute('type', 'type', 'pimenutype');
										$html .='<li>'.$this->form->getLabel('type');
										$html .= $this->form->getInput('type').'</li>';

										$html .='<li>'.$this->form->getLabel('title');
										$html .= $this->form->getInput('title').'</li>';

										if ($this->menuItem->type =='url'):
											$this->form->setFieldAttribute('link','readonly','false');
											$html .='<li>'.$this->form->getLabel('link');
											$html .= $this->form->getInput('link').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('alias');
										$html .= $this->form->getInput('alias').'</li>';

										$html .='<li>'.$this->form->getLabel('note');
										$html .= $this->form->getInput('note').'</li>';

										if ($this->menuItem->type !=='url'):
											$html .='<li>'.$this->form->getLabel('link');
											$html .= $this->form->getInput('link').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('published');
										$html .= $this->form->getInput('published').'</li>';

										$html .='<li>'.$this->form->getLabel('access');
										$html .= $this->form->getInput('access').'</li>';

										$html .='<li>'.$this->form->getLabel('menutype');
										$html .= $this->form->getInput('menutype').'</li>';

										$html .='<li>'.$this->form->getLabel('parent_id');
										$html .= $this->form->getInput('parent_id').'</li>';

										$html .='<li>'.$this->form->getLabel('browserNav');
										$html .= $this->form->getInput('browserNav').'</li>';

										if ($this->menuItem->type == 'component') :
											$html .='<li>'.$this->form->getLabel('home');
											$html .= $this->form->getInput('home').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('language');
										$html .= $this->form->getInput('language').'</li>';

										$html .='<li>'.$this->form->getLabel('template_style_id');
										$html .= $this->form->getInput('template_style_id').'</li>';

								$html .='</ul>';
							$html .='</fieldset>';
						$html .='</div>';

						$html .= '<!-- Menu Item Parameters Section content-->';
						$html .= '<div class="width-40 fltrt">'; //width-100 fltlft">';
							$html .= JHtml::_('sliders.start','menu-sliders-'.$this->menuItem->id);
								/*
									ms:
									check here for pagetype  == 'content_article'
									so we must not add an article here
								*/
								if($this->pageType == 'content_article')
								{
									$this->form->setFieldAttribute('id', 'required', false,'request');
								}

								/*
									ms:
									check here for pagetype  == 'content_category_blog' and sub_tak == 'new'
									so we can make an new category
								*/
								if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new')
								{
									$this->form->setFieldAttribute('id', 'required', false,'request');
								}

								if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' )
								{
									$this->form->setFieldAttribute('id', 'type', 'Picategory','request');
								}
								
								$fieldSets = $this->form->getFieldsets('request');
								if (!empty($fieldSets))
								{
									$fieldSet = array_shift($fieldSets);
									$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$fieldSet->name.'_FIELDSET_LABEL';
									$html .=  JHtml::_('sliders.panel',JText::_($label), 'request-options');
										if (isset($fieldSet->description) && trim($fieldSet->description)) :
											//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
											$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
										endif;

										$html .= '<fieldset class="panelform">';
											$hidden_fields = '';
											$html .= '<ul class="adminformlist">';
											
											foreach ($this->form->getFieldset('request') as $field)
											{
												if (!$field->hidden)
												{
													if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' && strtolower($field->type) == 'picategory')
													{
														$html .= '<li>';
															$html .= $field->input;
														$html .= '</li>';
													}
													else if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' && strtolower($field->type) == 'category')
													{
														$html .= '<table>';
															$html .= '<tr>';
																$html .= '<td>';
																	$html .= '<input type="radio" name="create_new_category" value="0" id="create_new_category_0" />';
																$html .= '</td>';
																$html .= '<td>';
																	$html .= $field->label;
																$html .= '</td>';
																$html .= '<td>';
																	$html .= $field->input;
																$html .= '</td>';
															$html .= '</tr>';
															$html .= '<tr>';
																$html .= '<td>';
																	//the checked part will be configurable in the pagetype config
																	$html .= '<input type="radio" name="create_new_category" value="1" id="create_new_category_1" checked="checked" />';
																$html .= '</td>';
																$html .= '<td colspan="2">';
																	$html .= '<label class="hasTip" for="create_new_category_1" title="';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
																		$html .= '::';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY_TIP');
																		$html .= '">';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
																	$html .= '</label>';
																$html .= '</td>';
															$html .= '</tr>';
														$html .= '</table>';
													}
													else
													{
														$html .= '<li>';
															$html .= $field->label;
															$html .= $field->input;
														$html .= '</li>';
													}

												}
												else
												{
													$hidden_fields.= $field->input;
												}
											}
											$html .= '</ul>';
											$html .= $hidden_fields;
										$html .= '</fieldset>';
								}
									$fieldSets = $this->form->getFieldsets('params');
									foreach ($fieldSets as $name => $fieldSet)
									{
										$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$name.'_FIELDSET_LABEL';
										$html .= JHtml::_('sliders.panel',JText::_($label), $name.'-options');
											if (isset($fieldSet->description) && trim($fieldSet->description))
											{
												//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
												$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
											}
											$html .= '<fieldset class="panelform">';
												$html .= '<ul class="adminformlist">';
												foreach ($this->form->getFieldset($name) as $field)
												{
													$html .= '<li>';
														$html .= $field->label;
														$html .=  $field->input;
													$html .= '</li>';
												}
												$html .= '</ul>';
											$html .= '</fieldset>';
									}

									$html .= '<div class="clr"></div>';
									if (!empty($this->modules))
									{
										$html .=  JHtml::_('sliders.panel',JText::_('COM_MENUS_ITEM_MODULE_ASSIGNMENT'), 'module-options');
											$html .= '<fieldset>';
												$html .= '<table class="adminlist">';
													$html .= '<thead>';
														$html .= '<tr>';
															$html .= '<th class="left">';
																$html .= JText::_('COM_MENUS_HEADING_ASSIGN_MODULE');
															$html .= '</th>';
															$html .= '<th>';
																$html .= JText::_('COM_MENUS_HEADING_DISPLAY');
															$html .= '</th>';
														$html .= '</tr>';
													$html .= '</thead>';
													$html .= '<tbody>';
													foreach ($this->modules as $i => &$module)
													{
														$html .= '<tr class="row<?php echo $i % 2;?>">';
															$html .= '<td>';
																$link = 'index.php?option=com_modules&amp;client_id=0&amp;task=module.edit&amp;id='. $module->id.'&amp;tmpl=component&amp;view=module&amp;layout=modal' ;
																$html .= '<a class="modal" href="'. $link.'" rel="{handler: \'iframe\', size: {x: 900, y: 550}}" title="'.JText::_('COM_MENUS_EDIT_MODULE_SETTINGS').'">';
																	//$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position));
																	$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $module->title, $module->access_title, $module->position);
																$html .='</a>';

														$html .= '</td>';
														$html .= '<td class="center">';
														if (is_null($module->menuid))
														{
															$html .= JText::_('JNONE');
														}
														elseif ($module->menuid != 0)
														{
															$html .= JText::_('COM_MENUS_MODULE_SHOW_VARIES');
														}
														else
														{
															$html .= JText::_('JALL');
														}
														$html .= '</td>';
													$html .= '</tr>';
													}
													$html .= '</tbody>';
												$html .= '</table>';
											$html .= '</fieldset>';
											}
										$html .= JHtml::_('sliders.end');
										$html .= '<input type="hidden" name="task" value="" />';
										$html .= $this->form->getInput('component_id');
										$html .=  JHtml::_('form.token');
									$html .= '</div>';
									$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
								$html .= '<!-- END Menu Item Parameters Section-->';
						//	$html .= '</td>';
						//$html .= '</tr>';
								$html .= '<!-- Manager Section-->';
								$pageId = JRequest::getVar('pageId', null);
								//if($pageId)
								//{
									$new_or_edit = (JRequest::getVar('sub_task','edit') == 'new') ? 0 : 1;
									$managerOtherItemEdit = new JObject();
									$managerOtherItemEdit->text = '';

									$params = null;
									$dispatcher = &JDispatcher::getInstance();
									//$dispatcher->trigger('onGetParams',array(&$params, $item_type));
									$path = JPATH_COMPONENT_ADMINISTRATOR;//realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
									require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
									$extensions = ExtensionManagerHelper::importExtension(null,null, true,null,true);
									$dispatcher->trigger('onManagerOtherItemEdit', array (&$managerOtherItemEdit,'menu',$pageId,$params,$new_or_edit));
									if($managerOtherItemEdit->text != '')
									{
									$html .= '<div class="width-100 fltrt">'; //width-100 fltlft">';
										$html .= $managerOtherItemEdit->text;
									$html .= '</div>';
									}
								//}


		
						
						$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%" >';
						$html .= $this->lists->add->bottom;
						$html .= '</table>';

						$html .= $this->menuItem->linkfield;
						//replace with
						$html .= $this->lists->pageType->html;
						$html .= '<input type="hidden" name="id" value="'.$this->menuItem->id.'" />';
						$html .= '<input type="hidden" name="component_id" value="'.$this->menuItem->component_id.'" />';

						$html .= $this->form->getInput('component_id');
						$html .= JHtml::_('form.token');
						$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
						//$html .= '<input type="hidden" id="pageType" name="pageType" value="'.$this->pageType.'" />';
						$html .= '<input type="hidden" name="type" value="'.$this->menuItem->type.'" />';
				$html .= '</td>';
			$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}





	//todo move to views/page/tmpl/default_pagepropertys.php
	//************************************************************************************************************************************************
	//************************************************************************************************************************************************
	//************************************************************************************************************************************************
	//************************************************************************************************************************************************
	//************************************************************************************************************************************************
	//************************************************************************************************************************************************
	//************************************************************************************************************************************************
	//************************************************************************************************************************************************
	function OLDJ15J16getFormPagePropertys()
	{
		$html = '';
		$sub_task = JRequest::getVar( 'sub_task', 'edit');
		$lang = &JFactory::getLanguage();
		$menu_item = $this->menu_item;

		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$menu_item_component = $menu_item->getComponent();
			$lang->load($menu_item_component->option, JPATH_ADMINISTRATOR);
			$menu_item_urlparams		= $menu_item->getUrlParams();
			$menu_item_params			= $menu_item->getStateParams();
			$menu_item_systemparams		= $menu_item->getSystemParams();
			$menu_item_advancedparams	= $menu_item->getAdvancedParams();
			$menu_item_componentparams	= $menu_item->getComponentParams();

			$menu_item_name			= $menu_item->getStateName();
			$menu_item_description		= $menu_item->getStateDescription();

			$menu_menuTypes 			= MenusHelper::getMenuTypeList();
			$menu_components			= MenusHelper::getComponentList();
		}
		else
		{
			if(isset($this->menuItem->request['option']))
			//$lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR);
			$lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR, null, false, false) || $lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
			$menu_item_name = $this->menuItem->title; //must change tothe ???
			$menu_item_description = $this->form->getInput('type');
			$menu_item_parent_id = $this->form->getInput('parent_id');

			/*

							<li><?php echo $this->form->getLabel('type'); ?>
				<?php echo $this->form->getInput('type'); ?></li>

				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>

				<?php if ($this->item->type =='url'): ?>
					<?php $this->form->setFieldAttribute('link','readonly','false');?>
					<li><?php echo $this->form->getLabel('link'); ?>
					<?php echo $this->form->getInput('link'); ?></li>
				<?php endif ?>


			//$menu_item_component 		= $menu_item->getComponent();



			$menu_item_urlparams		= $menu_item->getUrlParams();
			$menu_item_params			= $menu_item->getStateParams();
			$menu_item_systemparams		= $menu_item->getSystemParams();
			$menu_item_advancedparams	= $menu_item->getAdvancedParams();
			$menu_item_componentparams	= $menu_item->getComponentParams();

			$menu_item_name				= $menu_item->getStateName();
			$menu_item_description		= $menu_item->getStateDescription();

			$menu_menuTypes 			= MenusHelper::getMenuTypes();
			$menu_components			= MenusHelper::getComponentList();





			*/
			//$menu_item = $this->menuItem; //menuItemsType;
			//
			//$lang->load($menu_item_component->option, JPATH_ADMINISTRATOR);
			//$menu_item_description = ''; //$menu_item->description;
		}
		$buttonLinkMenutype = '';
		$buttonLinkMenutype .= '<div>';
			//here we can set an select type to change se type?
			$buttonLinkMenutype .= $menu_item_description;
		$buttonLinkMenutype .= '</div>';
		$this->getLists();
		//TODO $this->getPageTypeHtml
		$html .= '<script language="JavaScript" type="text/javascript">';
			$html .= '<!--';
			$html .= 'function popupPageBrowser(url)';
			$html .= '{';
				$html .= 'var winl = (screen.width - 400) / 2;';
				$html .= 'var wint = (screen.height - 400) / 2;';
				$html .= "winprops = 'height=400,width=400,top='+wint+',left='+winl+',scrollbars=yes,resizable';";
				$html .= "linkValue = document.getElementById('link').value;";
				$html .= 'linkValue = escape(linkValue);';
				$html .= "urlString = url+'&url='+linkValue;";
				$html .= "win = window.open(urlString, 'pages', winprops);";
				$html .= 'if (parseInt(navigator.appVersion) >= 4)';
				$html .= '{';
					$html .= 'win.window.focus();';
				$html .= '}';
			$html .= '}';
			$html .= '-->';
		$html .= '</script>';

		//if($this->menuItem && $this->menuItemsTypes)
		if($this->menuItem && $this->menuItemsType)
		{
			$menuItemsType = $this->menuItemsType;
			$image = false;
			$imageNew = false;
			$imageEdit = false;
			$imageBulletNew = '';
			$imageBulletEdit = '';
			if(isset($menuItemsType->icons->default->imageUrl))
			{
				$image = $menuItemsType->icons->default->imageUrl;
			}
			if(isset($menuItemsType->icons->new->imageUrl))
			{
				$imageNew = $menuItemsType->icons->new->imageUrl;
			}
			else
			{
				$imageNew = $image;
				$imageBulletNew = PagesAndItemsHelper::getDirIcons().'base/bullets/icon-16-bullet_new.png';
			}
			if(isset($menuItemsType->icons->edit->imageUrl))
			{
				$imageEdit = $menuItemsType->icons->edit->imageUrl;
			}
			else
			{
				$imageEdit = $image;
				$imageBulletEdit = PagesAndItemsHelper::getDirIcons().'base/bullets/icon-16-bullet_edit.png';
			}
			if($sub_task=='new')
			{
				if(!$imageBulletNew && $imageNew)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageNew,JText::_('COM_PAGESANDITEMS_NEW_PAGE')); //.' ( '.$menu_item_name.' )');
				}
				elseif($imageBulletNew && $imageNew)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageNew,JText::_('COM_PAGESANDITEMS_NEW_PAGE'),$imageBulletNew); //.' ( '.$menu_item_name.' )',$imageBulletEdit);
				}
				else
				{
					//$imageDisplay ='<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
					//$imageDisplay .= JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )';
					$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',JText::_('COM_PAGESANDITEMS_NEW_PAGE')); //.' ( '.$menu_item_name.' )');
				}
				/*
				if($imageNew)
				{
					$bevore = '';
					$after = '';
					//COMMENT TODO only test for add bullets to image
					if($imageBulletNew !='')
					{
						$bevore = '<div>';
							$imageBulletNew = '<img src="'.$imageBulletNew.'" alt="" style="float: left;left: 0;position: absolute;vertical-align: middle;z-index: 101;" />&nbsp;';
						$after = '</div>';
					}
					$imageDisplay = $bevore;
					//echo '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;float: left;" />&nbsp;';
					$imageDisplay .= '<div style="margin-left: 4px;float: left;left: 0;position: relative;vertical-align: middle;">';
						$imageDisplay .= '<img src="'.$imageNew.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';


						$imageDisplay .= $imageBulletNew;
					$imageDisplay .= '</div>';
					//$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )';
					$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE');
					$imageDisplay .= $after;
				}
				else
				{
					$imageDisplay ='<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
					//$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )';
					$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE');
				}
				*/
			}
			else
			{
				/*
				if($imageEdit)
				{
					$bevore = '';
					$after = '';
					//COMMENT TODO only test for add bullets to image
					if($imageBulletEdit !='')
					{
						$bevore = '<div>';
							$imageBulletEdit = '<img src="'.$imageBulletEdit.'" alt="" style="float: left;left: 0;position: absolute;vertical-align: middle;z-index: 101;" />&nbsp;';
						$after = '</div>';
					}
					$imageDisplay = $bevore;
					//echo '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;float: left;" />&nbsp;';
					$imageDisplay .= '<div style="margin-left: 4px;float: left;left: 0;position: relative;vertical-align: middle;">';

						$imgClass = explode("class:",$imageEdit);
						if(count($imgClass) && count($imgClass) == 2)
						{
							//we have an class
							$imageDisplay .= '<a ';
							$imageDisplay .= 'class="icon '.$imgClass[1].'" ';
							$imageDisplay .= 'alt="" >&nbsp;';
							$imageDisplay .= '<span>&nbsp;';
							$imageDisplay .= '</span>';
							$imageDisplay .= '</a>';
						}
						else
						{

						$imageDisplay .= '<img src="'.$imageEdit.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
						}

						$imageDisplay .= $imageBulletEdit;
					$imageDisplay .= '</div>';
					//echo JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
					$imageDisplay .= JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )';
					$imageDisplay .= $after;
				}
				*/
				if(!$imageBulletEdit && $imageEdit)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageEdit,JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )');
				}
				elseif($imageBulletEdit && $imageEdit)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageEdit,JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )',$imageBulletEdit);
				}
				else
				{
					//$imageDisplay ='<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
					//$imageDisplay .= JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )';
					$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )');
				}
			}
		}
		else
		{
			if($sub_task=='new')
			{
				//$imageDisplay ='<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
				//$imageDisplay .= JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )';
				//todo icon-16-menu_new
				$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu.png',JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )');
			}
			else
			{
				//$imageDisplay ='<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-menu.png" alt="" style="vertical-align: middle;" />&nbsp;';
				//$imageDisplay .= JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )';
				$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )');
				
				
			}
		}

		/*
		$html .= PagesAndItemsHelper::getThImageTitle($image,JText::_('COM_PAGESANDITEMS_ITEMS_ON_PAGE'));
		
		
		*/

		$html .='<table class="piadminform xadminform" width="98%">';
			$html .= '<thead class="piheader">';
			$html .='<tr>';
				$html .='<th>'; // class="piheader">'; // style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
					 //TODO image from pageType if exist
					/*
					if($sub_task=='new')
					{
						echo '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-category.png" alt="" style="vertical-align: middle;" />&nbsp;';
						echo JText::_('COM_PAGESANDITEMS_NEW_PAGE');
					}
					else
					{
							echo '<img src="'.PagesAndItemsHelper::getDirIcons().'icon-16-category.png" alt="" style="vertical-align: middle;" />&nbsp;'; //TODO image from this->menuItem->pageType
						echo JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES');
					}
					*/
					//echo ' ( '.$menu_item_name.' )';
					$html .= $imageDisplay;

				$html .='</th>';
			$html .='</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			$html .='<tr>';
				$html .='<td>';
				/*
				ok different J1.5 and J1.6
				*/
				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
				{
					$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%"'.$this->lists->hideAll.' >';

					$html .='<!-- $this->lists->display-> -->';

					$html .='<tr id="menu_item_id"'.$this->lists->display->id.' >';
						$html .='<td class="key" width="20%" align="right">';
							$html .= JText::_( 'ID' ).': ';
						$html .='</td>';
						if ($this->menuItem->id)
						{
							$html .='<td width="80%">';
								$html .='<strong>'.$this->menuItem->id.'</strong>';
							$html .='</td>';
						}
						$html .= $this->lists->add->top;
					$html .='</tr>';

					$html .='<tr id="menu_item_title"'.$this->lists->display->title.' >';
						$html .='<td class="key" align="right">';
							$html .= JText::_( 'Title' ).': ';
						$html .='</td>';
						$html .='<td>';
							$html .='<input class="inputbox" type="text" name="name" size="150" maxlength="255" value="'.$this->menuItem->name.'" />';
						$html .='</td>';
					$html .='</tr>';

					$html .='<tr id="menu_item_alias">';
						$html .='<td class="key" align="right">';
							$html .= JText::_( 'Alias' ).': ';
						$html .='</td>';
						$html .='<td>';
							$html .='<input class="inputbox" type="text" name="alias" size="150" maxlength="255" value="'.$this->menuItem->alias.'" />';
						$html .='</td>';
					$html .='</tr>';

					$html .='<tr id="menu_item_link">';
						$html .='<td class="key" align="right">';
							$html .= JText::_( 'Link' ).': ';
						$html .='</td>';
						$html .='<td>';
							//$html .='<input class="inputbox" type="text" name="link" size="150" maxlength="255" value="'.$this->menuItem->link.'" $html .= $this->lists->disabled->link.' />';
								if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
								{
									if ($this->menuItem->type =='url')
									{
									//$html .='<input class="inputbox" type="text" name="link" size="150" maxlength="255" value="'.$this->menuItem->link.'" $html .= $this->lists->disabled->link.' />';
									}
									else
									{

									}
								}
								else
								{
									if ($this->menuItem->type =='url')
									{
										$this->form->setFieldAttribute('link','readonly','false');
									}
									/*<li><?php echo $this->form->getLabel('link'); ?>*/
									$html .= $this->form->getInput('link');
								}

							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr style="display: none;visibility: hidden;">';
							$html .= '<td class="key" align="right">';
								$html .= JText::_( 'Display in' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
								{
									$menuTypes = MenusHelper::getMenuTypeList();
								}
								else
								{
									$menuTypes = MenusHelper::getMenuTypes();
								}
								$html .= JHTML::_('select.genericlist', $menuTypes, 'menutype', 'class="inputbox" size="1"', 'menutype', 'title', $this->menuItem->menutype );
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr style="display: none;visibility: hidden;">';
							$html .= '<td class="key" align="right" valign="top">';
								$html .= JText::_( 'Parent Item' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
								{
									$html .= MenusHelper::Parent( $this->menuItem );
								}
								else
								{
									$html .= $menu_item_parent_id;
								}
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_( 'Published' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								$html .= $this->lists->published;
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_('DEFAULT').' '.JText::_( 'MENU ITEM' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								$html .= $this->lists->home; //'todo defaultPage';//$this->lists->published
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr style="display: none;visibility: hidden;">';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_( 'Ordering' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								$html .= JHTML::_('menu.ordering', $this->menuItem, $this->menuItem->id );
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_( 'Access Level' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								$html .= JHTML::_('list.accesslevel',  $this->menuItem );
							$html .= '</td>';
						$html .= '</tr>';
						if ($this->menuItem->type != "menulink")
						{
						$html .= '<tr>';
							$html .= '<td class="key" valign="top" align="right">';
								$html .= JText::_( 'On Click, Open in' ).': ';
							$html .= '</td>';
							$html .= '<td>';
								if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
								{
									$html .= MenusHelper::Target( $this->menuItem );
								}
								else
								{
									$html .= $this->form->getInput('browserNav');
									/*<li><?php echo $this->form->getLabel('browserNav'); ?>
									<?php echo
									$this->form->getInput('browserNav');
									 ?></li>
									*/
								}

							$html .= '</td>';
						$html .= '</tr>';
						}
						$html .= '<tr>';
							$html .= '<td colspan="2" style="line-height: 3px; height: 3px;">&nbsp;';
							$html .= '</td>';
						$html .= '</tr>';

						$html .= '<tr>';
							$html .= '<td valign="top">';
								$html .= '<label>';
									$html .= '<span class="editlinktip hasTip" title="'.JText::_('COM_PAGESANDITEMS_MENUTYPE_TIP').'" >';
										$html .= JText::_('COM_PAGESANDITEMS_MENUTYPE');
									$html .= '</span>';
								$html .= '</label>';
							$html .= '</td>';
							$html .= '<td>';
								//only display the menutype == $menu_item_description;
								$html .= $buttonLinkMenutype;
								//$html .= '<br>';
							$html .= '</td>';
						$html .= '</tr>';
						$html .= '<tr>';
							$html .= '<!-- Menu Item Parameters Section-->';
							$html .= '<td>';
								$html .= '<!-- Menu Item Parameters Section blank column-->';
							$html .= '</td>';
							$html .= '<td >';
								$html .= '<!-- Menu Item Parameters Section content-->';
								if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
								{
								jimport('joomla.html.pane');
								$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));
								$html .= $pane->startPane("menu-pane");
								$html .= '<div '.$this->lists->display->params.' >';
									$html .= $pane->startPanel(JText :: _('Parameters - Basic'), "param-page");
									$html .= $menu_item_urlparams->render('urlparams');
									if(count($menu_item_params->getParams('params')))
									{
										$html .= $menu_item_params->render('params');
									}

									if(!count($menu_item_params->getNumParams('params')) && !count($menu_item_urlparams->getNumParams('urlparams')))
									{
										$html .= '<div style="text-align: center; padding: 5px; ">';
											$html .= JText::_('There are no parameters for this item');
										$html .= '</div>';
									}
								$html .= $pane->endPanel();

								$html .= '</div>';
								if($params = $menu_item_advancedparams->render('params'))
								{
									$html .= $pane->startPanel(JText :: _('Parameters - Advanced'), "advanced-page");
									$html .= $params;
									$html .= $pane->endPanel();
								}
								if ($menu_item_componentparams && ($params = $menu_item_componentparams->render('params')))
								{
									$html .= $pane->startPanel(JText :: _('Parameters - Component'), "component-page");
									$html .= $params;
									$html .= $pane->endPanel();
								}
								if ($menu_item_systemparams && ($params = $menu_item_systemparams->render('params')))
								{
									$html .= $pane->startPanel(JText :: _('Parameters - System'), "system-page");
									$html .= $params;
									$html .= $pane->endPanel();
								}
								$html .= $pane->endPane();
								}
								else
								{
									$html .= '<div class="width-100 fltlft">';
										$html .= JHtml::_('sliders.start','menu-sliders-'.$this->menuItem->id);

											//$html .= $this->loadTemplate('options');
											$fieldSets = $this->form->getFieldsets('request');
											if (!empty($fieldSets))
											{

												$fieldSet = array_shift($fieldSets);
												$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$fieldSet->name.'_FIELDSET_LABEL';
												$html .=  JHtml::_('sliders.panel',JText::_($label), 'request-options');
												if (isset($fieldSet->description) && trim($fieldSet->description)) :
													//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
													$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
												endif;

												$html .= '<fieldset class="panelform">';
													$hidden_fields = '';
													$html .= '<ul class="adminformlist">';
													foreach ($this->form->getFieldset('request') as $field)
													{
														if (!$field->hidden)
														{
															$html .= '<li>';
																$html .= $field->label;
																$html .= $field->input;
															$html .= '</li>';
														}
														else
														{
															$hidden_fields.= $field->input;
														}
													}
													$html .= '</ul>';
													$html .= $hidden_fields;;
												$html .= '</fieldset>';
											}
											$fieldSets = $this->form->getFieldsets('params');
											foreach ($fieldSets as $name => $fieldSet)
											{
												$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$name.'_FIELDSET_LABEL';
												$html .= JHtml::_('sliders.panel',JText::_($label), $name.'-options');
													if (isset($fieldSet->description) && trim($fieldSet->description))
													{
														//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
														$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
													}
													$html .= '<fieldset class="panelform">';
														$html .= '<ul class="adminformlist">';
														foreach ($this->form->getFieldset($name) as $field)
														{
															$html .= '<li>';
																$html .= $field->label;
																$html .=  $field->input;
															$html .= '</li>';
														}
														$html .= '</ul>';
													$html .= '</fieldset>';
											}
											$html .= '<div class="clr"></div>';
											if (!empty($this->modules))
											{
												$html .=  JHtml::_('sliders.panel',JText::_('COM_MENUS_ITEM_MODULE_ASSIGNMENT'), 'module-options');
													$html .= '<fieldset>';
															$html .= '<table class="adminlist">';
																$html .= '<thead>';
															$html .= '<tr>';
																	$html .= '<th class="left">';
																		$html .= JText::_('COM_MENUS_HEADING_ASSIGN_MODULE');
																	$html .= '</th>';
																	$html .= '<th>';
																		$html .= JText::_('COM_MENUS_HEADING_DISPLAY');
																	$html .= '</th>';
																$html .= '</tr>';
																$html .= '</thead>';
																$html .= '<tbody>';
																foreach ($this->modules as $i => &$module)
																{
																	$html .= '<tr class="row<?php echo $i % 2;?>">';
																		$html .= '<td>';
																			$link = 'index.php?option=com_modules&amp;client_id=0&amp;task=module.edit&amp;id='. $module->id.'&amp;tmpl=component&amp;view=module&amp;layout=modal' ;
																			$html .= '<a class="modal" href="'. $link.'" rel="{handler: \'iframe\', size: {x: 900, y: 550}}" title="'.JText::_('COM_MENUS_EDIT_MODULE_SETTINGS').'">';
																				$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->view->escape($module->title), $this->view->escape($module->access_title), $this->view->escape($module->position));
																				//$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $module->title, $module->access_title, $module->position);
																				$html .='</a>';

																		$html .= '</td>';
																		$html .= '<td class="center">';
																		if (is_null($module->menuid))
																		{
																			$html .= JText::_('JNONE');
																		}
																		elseif ($module->menuid != 0)
																		{
																			$html .= JText::_('COM_MENUS_MODULE_SHOW_VARIES');
																		}
																		else
																		{
																			$html .= JText::_('JALL');
																		}
																		$html .= '</td>';
																	$html .= '</tr>';
																}
																$html .= '</tbody>';
															$html .= '</table>';

														//$html .= $this->loadTemplate('modules');
												$html .= '</fieldset>';
											}
											$html .= JHtml::_('sliders.end');
											$html .= '<input type="hidden" name="task" value="" />';
											$html .= $this->form->getInput('component_id');
											$html .=  JHtml::_('form.token');
									$html .= '</div>';
									$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
								}
								$html .= '<!-- END Menu Item Parameters Section-->';
							$html .= '</td>';
						$html .= '</tr>';
						$html .= $this->lists->add->bottom;
						$html .= $this->menuItem->linkfield;
						//replace with
						$html .= $this->lists->pageType->html;
						//echo $this->lists->pageTypeClass->html;
						$html .= '<input type="hidden" name="id" value="'.$this->menuItem->id.'" />';
						if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
						{
							$html .= '<input type="hidden" name="componentid" value="'.$this->menuItem->componentid.'" />';
						}
						else
						{
							$html .= '<input type="hidden" name="component_id" value="'.$this->menuItem->component_id.'" />';
						}
						$html .= '<input type="hidden" name="type" value="'.$this->menuItem->type.'" />';
					$html .= '</table>';
					}
					else
					{
						//J1.6
						//$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%"'.$this->lists->hideAll.' >';

						//$html .= $this->lists->add->top;
						JHtml::_('behavior.tooltip');
						JHtml::_('behavior.formvalidation');
						JHTML::_('behavior.modal');

						//ms: need?
						/*
						$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%" >';
							$html .= $this->lists->add->top;
						$html .= '</table>';
						*/

						$html .='<!-- $this->lists->display-> -->';
						$html .='<div class="width-60 fltlft">';
							$html .='<fieldset class="adminform">';
								$html .='<legend>'.JText::_('COM_MENUS_ITEM_DETAILS').'</legend>';

									$html .='<ul class="adminformlist">';
										//do not display when new
										//echo '$this->menuItem->id'.$this->menuItem->id;
										if($this->menuItem->id){
											$html .='<li '.$this->lists->display->id.'>'.$this->form->getLabel('id');
											$html .= $this->form->getInput('id').'</li>';
										}
										//$this->form->setFieldAttribute('link','readonly','false');
										$this->form->setFieldAttribute('type', 'type', 'pimenutype');
										$html .='<li>'.$this->form->getLabel('type');
										$html .= $this->form->getInput('type').'</li>';

										$html .='<li>'.$this->form->getLabel('title');
										$html .= $this->form->getInput('title').'</li>';

										if ($this->menuItem->type =='url'):
											$this->form->setFieldAttribute('link','readonly','false');
											$html .='<li>'.$this->form->getLabel('link');
											$html .= $this->form->getInput('link').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('alias');
										$html .= $this->form->getInput('alias').'</li>';

										$html .='<li>'.$this->form->getLabel('note');
										$html .= $this->form->getInput('note').'</li>';

										if ($this->menuItem->type !=='url'):
											$html .='<li>'.$this->form->getLabel('link');
											$html .= $this->form->getInput('link').'</li>';
										endif;

										//JGLOBAL_STATE will not load. Would like to change this to JSTATUS
										//but in jfroms this seems not possible
										//replacement in system plugin or
										/*
										COMMENT ms: this is fixed
										i have add in views/page.view.html.php
										JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'forms');
										so the corect xml is loaded

										$this->form->setFieldAttribute('published', 'label', JText::_('JSTATUS'));
										 */




										$html .='<li>'.$this->form->getLabel('published');
										$html .= $this->form->getInput('published').'</li>';

										$html .='<li>'.$this->form->getLabel('access');
										$html .= $this->form->getInput('access').'</li>';

										$html .='<li>'.$this->form->getLabel('menutype');
										$html .= $this->form->getInput('menutype').'</li>';

										$html .='<li>'.$this->form->getLabel('parent_id');
										$html .= $this->form->getInput('parent_id').'</li>';

										$html .='<li>'.$this->form->getLabel('browserNav');
										$html .= $this->form->getInput('browserNav').'</li>';

										if ($this->menuItem->type == 'component') :
											$html .='<li>'.$this->form->getLabel('home');
											$html .= $this->form->getInput('home').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('language');
										$html .= $this->form->getInput('language').'</li>';

										$html .='<li>'.$this->form->getLabel('template_style_id');
										$html .= $this->form->getInput('template_style_id').'</li>';

								$html .='</ul>';
							$html .='</fieldset>';
						$html .='</div>';

						$html .= '<!-- Menu Item Parameters Section content-->';
						$html .= '<div class="width-40 fltrt">'; //width-100 fltlft">';
							$html .= JHtml::_('sliders.start','menu-sliders-'.$this->menuItem->id);
								/*
									ms:
									check here for pagetype  == 'content_article'
									so we must not add an article here
								*/
								if($this->pageType == 'content_article')
								{
									$this->form->setFieldAttribute('id', 'required', false,'request');
								}

								/*
									ms:
									check here for pagetype  == 'content_category_blog' and sub_tak == 'new'
									so we can make an new category
								*/
								if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new')
								{
									$this->form->setFieldAttribute('id', 'required', false,'request');

									/*
									//$element = new JXMLElement
									$element = new JXMLElement('<field ></field>');
									<field name="id" type="radio"
				description="JGLOBAL_CHOOSE_CATEGORY_DESC"
				extension="com_content"
				label="JGLOBAL_CHOOSE_CATEGORY_LABEL"
				required="true"
			/>


									$this->form->setField($element, 'request')
									*/
								}


								//$html .= $this->loadTemplate('options');
								if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' )
								{
									$this->form->setFieldAttribute('id', 'type', 'Picategory','request');
								}
								
								$fieldSets = $this->form->getFieldsets('request');
								if (!empty($fieldSets))
								{
									$fieldSet = array_shift($fieldSets);
									$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$fieldSet->name.'_FIELDSET_LABEL';
									$html .=  JHtml::_('sliders.panel',JText::_($label), 'request-options');
										if (isset($fieldSet->description) && trim($fieldSet->description)) :
											//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
											$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
										endif;

										$html .= '<fieldset class="panelform">';
											$hidden_fields = '';
											$html .= '<ul class="adminformlist">';
											
											foreach ($this->form->getFieldset('request') as $field)
											{
												if (!$field->hidden)
												{
													if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' && strtolower($field->type) == 'picategory')
													{
														$html .= '<li>';
															$html .= $field->input;
														$html .= '</li>';
													}
													else if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' && strtolower($field->type) == 'category')
													{
														$html .= '<table>';
															$html .= '<tr>';
																$html .= '<td>';
																	$html .= '<input type="radio" name="create_new_category" value="0" id="create_new_category_0" />';
																$html .= '</td>';
																$html .= '<td>';
																	$html .= $field->label;
																$html .= '</td>';
																$html .= '<td>';
																	$html .= $field->input;
																$html .= '</td>';
															$html .= '</tr>';
													//	$html .= '</li>';
													//	$html .= '<li>';
															$html .= '<tr>';
																$html .= '<td>';
																	//the checked part will be configurable in the pagetype config
																	$html .= '<input type="radio" name="create_new_category" value="1" id="create_new_category_1" checked="checked" />';
																$html .= '</td>';
																$html .= '<td colspan="2">';
																	$html .= '<label class="hasTip" for="create_new_category_1" title="';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
																		$html .= '::';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY_TIP');
																		$html .= '">';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
																	$html .= '</label>';
																$html .= '</td>';
															$html .= '</tr>';
														$html .= '</table>';
														//$html .= '</li>';
													}
													else
													{
														$html .= '<li>';
															$html .= $field->label;
															$html .= $field->input;
														$html .= '</li>';
													}

														/*
														$html .= '<li>';
															$html .= $field->label;
															$html .= $field->input;
														$html .= '</li>';
														*/
												}
												else
												{
													$hidden_fields.= $field->input;
												}
											}
											$html .= '</ul>';
											$html .= $hidden_fields;
										$html .= '</fieldset>';
								}
									$fieldSets = $this->form->getFieldsets('params');
									foreach ($fieldSets as $name => $fieldSet)
									{
										$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$name.'_FIELDSET_LABEL';
										$html .= JHtml::_('sliders.panel',JText::_($label), $name.'-options');
											if (isset($fieldSet->description) && trim($fieldSet->description))
											{
												//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
												$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
											}
											$html .= '<fieldset class="panelform">';
												$html .= '<ul class="adminformlist">';
												foreach ($this->form->getFieldset($name) as $field)
												{
													$html .= '<li>';
														$html .= $field->label;
														$html .=  $field->input;
													$html .= '</li>';
												}
												$html .= '</ul>';
											$html .= '</fieldset>';
									}

									$html .= '<div class="clr"></div>';
									if (!empty($this->modules))
									{
										$html .=  JHtml::_('sliders.panel',JText::_('COM_MENUS_ITEM_MODULE_ASSIGNMENT'), 'module-options');
											$html .= '<fieldset>';
												$html .= '<table class="adminlist">';
													$html .= '<thead>';
														$html .= '<tr>';
															$html .= '<th class="left">';
																$html .= JText::_('COM_MENUS_HEADING_ASSIGN_MODULE');
															$html .= '</th>';
															$html .= '<th>';
																$html .= JText::_('COM_MENUS_HEADING_DISPLAY');
															$html .= '</th>';
														$html .= '</tr>';
													$html .= '</thead>';
													$html .= '<tbody>';
													foreach ($this->modules as $i => &$module)
													{
														$html .= '<tr class="row<?php echo $i % 2;?>">';
															$html .= '<td>';
																$link = 'index.php?option=com_modules&amp;client_id=0&amp;task=module.edit&amp;id='. $module->id.'&amp;tmpl=component&amp;view=module&amp;layout=modal' ;
																$html .= '<a class="modal" href="'. $link.'" rel="{handler: \'iframe\', size: {x: 900, y: 550}}" title="'.JText::_('COM_MENUS_EDIT_MODULE_SETTINGS').'">';
																	//$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position));
																	$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $module->title, $module->access_title, $module->position);
																$html .='</a>';

														$html .= '</td>';
														$html .= '<td class="center">';
														if (is_null($module->menuid))
														{
															$html .= JText::_('JNONE');
														}
														elseif ($module->menuid != 0)
														{
															$html .= JText::_('COM_MENUS_MODULE_SHOW_VARIES');
														}
														else
														{
															$html .= JText::_('JALL');
														}
														$html .= '</td>';
													$html .= '</tr>';
													}
													$html .= '</tbody>';
												$html .= '</table>';
											$html .= '</fieldset>';
											}
										$html .= JHtml::_('sliders.end');
										$html .= '<input type="hidden" name="task" value="" />';
										$html .= $this->form->getInput('component_id');
										$html .=  JHtml::_('form.token');
									$html .= '</div>';
									$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
								$html .= '<!-- END Menu Item Parameters Section-->';
						//	$html .= '</td>';
						//$html .= '</tr>';
								$html .= '<!-- Manager Section-->';
								$pageId = JRequest::getVar('pageId', null);
								//if($pageId)
								//{
									$new_or_edit = (JRequest::getVar('sub_task','edit') == 'new') ? 0 : 1;
									$managerOtherItemEdit = new JObject();
									$managerOtherItemEdit->text = '';

									$params = null;
									$dispatcher = &JDispatcher::getInstance();
									//$dispatcher->trigger('onGetParams',array(&$params, $item_type));
									$path = JPATH_COMPONENT_ADMINISTRATOR;//realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
									require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
									$extensions = ExtensionManagerHelper::importExtension(null,null, true,null,true);
									$dispatcher->trigger('onManagerOtherItemEdit', array (&$managerOtherItemEdit,'menu',$pageId,$params,$new_or_edit));
									if($managerOtherItemEdit->text != '')
									{
									$html .= '<div class="width-100 fltrt">'; //width-100 fltlft">';
										$html .= $managerOtherItemEdit->text;
									$html .= '</div>';
									}
								//}


		
						
						$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%" >';
						$html .= $this->lists->add->bottom;
						$html .= '</table>';

						$html .= $this->menuItem->linkfield;
						//replace with
						$html .= $this->lists->pageType->html;
						$html .= '<input type="hidden" name="id" value="'.$this->menuItem->id.'" />';
						if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
						{
							$html .= '<input type="hidden" name="componentid" value="'.$this->menuItem->componentid.'" />';
						}
						else
						{
							$html .= '<input type="hidden" name="component_id" value="'.$this->menuItem->component_id.'" />';

							$html .= $this->form->getInput('component_id');
							$html .= JHtml::_('form.token');
							$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';


						}
						//$html .= '<input type="hidden" id="pageType" name="pageType" value="'.$this->pageType.'" />';
						$html .= '<input type="hidden" name="type" value="'.$this->menuItem->type.'" />';
					//$html .= '</table>';
					
					//end J1.6
					}
				$html .= '</td>';
			$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}

}

?>