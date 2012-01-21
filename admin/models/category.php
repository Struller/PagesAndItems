<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'tables');
//require_once(dirname(__FILE__).DS.'page.php');
/**

 */

class PagesAndItemsModelCategory extends JModel //PagesAndItemsModelPage
{
	

	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'tables');
	}

	

	function getChilds($icons = false)
	{
		$parent_id = 1;
		if(JRequest::getVar('categoryId',0))
		{
			$parent_id = (int)JRequest::getVar('categoryId');
		}

		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$component = (strpos($categoryExtension,'com_') !== false) ? strtolower($categoryExtension) : 'com_'.strtolower($categoryExtension);
		
		/*
		$this->_extension	= $options['extension'];
		$this->_table		= $options['table'];
		
		$this->_field		= (isset($options['field'])&&$options['field'])?$options['field']:'catid';
		$this->_key			= (isset($options['key'])&&$options['key'])?$options['key']:'id';
		$this->_statefield 	= (isset($options['statefield'])) ? $options['statefield'] : 'state';
		$options['access']	= (isset($options['access'])) ? $options['access'] : 'true';
		$options['published']	= (isset($options['published'])) ? $options['published'] : 1;
		$this->_options		= $options;
		
		*/
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extendedjcategories.php');
		$options['published'] = 0;
		$options['countItems'] = 1;
		//$options['new'] = 1;
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$categoryExtension = (strpos($categoryExtension,'com_') !== false) ? strtolower(str_replace('com_','',$categoryExtension)) : strtolower($categoryExtension);
		$categories = extendedJCategories::getInstance($categoryExtension , $options);
		if($categories && $parent_id != -1)
		{
			$id = $parent_id > 1 ? $parent_id : 'root';
			$parent = $categories->get($id,true);
			if (is_object($parent))
			{
				$items = $parent->getChildren(false);
			}
			else
			{
				$items = false;
			}
		}
		else
		{
			$items = false;
		}
		if($items)
		{

		}
		/*	
		$parent = $categories->get(($parent_id ? $parent_id : 'root'),true);
		if (is_object($parent)) {
			$items = $parent->getChildren(false);
		}
		else {
			$items = false;
		}
		*/
		$componentTable = JRequest::getVar('componentTable', '#__content');
		$componentField = JRequest::getVar('componentField', 'catid');
		$componentKey = JRequest::getVar('componentKey', 'id');
		
		$db = JFactory::getDBO();
		//$db->setQuery("SELECT * FROM #__categories  WHERE extension='com_content' AND parent_id='$parent_id' ORDER BY lft ASC" );
		$db->setQuery("SELECT * FROM #__categories  WHERE extension='$component' AND parent_id='$parent_id' ORDER BY lft ASC" );
		$rows = $db->loadObjectList();
		/*
		$options['table'] = '#__content';
		$options['extension'] = 'com_content';
		
		*/
		$query = $db->getQuery(true);
		$query->select('c.*');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
		$query->from('#__categories as c');
		$query->where('(c.extension='.$db->Quote($component).' OR c.extension='.$db->Quote('system').')');
		$query->where('c.parent_id='.$db->Quote($parent_id));
		$query->leftJoin($db->quoteName($componentTable).' AS i ON i.'.$db->quoteName($componentField).' = c.id');
		$query->select('COUNT(i.'.$db->quoteName($componentKey).') AS numitems');
		$query->order('c.lft');
		$query->group('c.id');
		$db->setQuery($query);
		//$results = $db->loadObjectList('id');
		
		$rows = $items ? $items : array(); //$results;
		//$rows = $results;
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'categorieslist.php');
		$CategoriesList = new CategoriesList();
		//getItems($maxLevelcatName)
		
		
		$html = '';
		$html .= '<thead class="piheader">';
			$html .= '<tr>';
				$html .= '<th>';// class="piheader">'; //style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
				//TODO get from $component???
				$image = isset($icons->default->imageUrl) ? $icons->default->imageUrl : PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
				
				$html .= PagesAndItemsHelper::getThImageTitle($image,JText::_('COM_PAGESANDITEMS_UNDERLYING_CATEGORIES'));
				
				
				//$html .= '<img src="'.PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png" alt="" style="vertical-align: middle;" />&nbsp;';
				//$html .= JText::_('COM_PAGESANDITEMS_UNDERLYING_CATEGORIES');
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody id="underlayingCategories">';
			$html .= '<tr>';
				$html .= '<td>';
					
					
					//($html,$model,$maxLevelcat = 'maxLevelcat',$open = true)
					//$rows,$hide_arrows = false,$level = false, $showType = false,$open = true)
					//$items,true,$level,true,$open,$parent, $add,$categoryExtension
					//getUnderlyingCategories($rows,$hide_arrows = false,$level = false, $showType = false,$open = true,$parent = false, $add = false,$categoryExtension='com_content')
					$html .= $CategoriesList->getUnderlyingCategories($rows,false,0, false,true,false,false,$categoryExtension);
					//$html .=  $this->getUnderlyingCategories($rows,false,0);
				$html .= '</td>';
				$html .= '</tr>';
		$html .= '</tbody>';
		return $html;
	}
	
	
	function getMenuItem()
	{
		$menuItem = false;
		$catid = JRequest::getVar('categoryId',1);
		if( $catid > 1 || $catid == -1)
		{
			$menuItem->request['id'] = $catid;
			$menuItem->request['view']= 'category';
			$menuItem->request['layout']= 'blog';
		}
		return $menuItem;
	}

	//get the content items
	function getCategoryItems()
	{
		$menuItem = $this->getMenuItem();
		$ContentItems = '';
		if($menuItem)
		{
			
			$model->menuItem = $menuItem;
			$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
			$categoryExtension = (strpos($categoryExtension,'com_') !== false) ? strtolower(str_replace('com_','',$categoryExtension)) : strtolower($categoryExtension);
			$pageType = $categoryExtension.'_'.$menuItem->request['view'].'_'.$menuItem->request['layout'];
			$dispatcher = &JDispatcher::getInstance();
			/*
			we want not get the other loaded pagetypes so we detach
			so only the $pageType raise the event
			*/
			$path = realpath(dirname(__FILE__).DS.'..');
			require_once($path.DS.'includes'.DS.'extensions'.DS.'pagetypehelper.php');
			ExtensionPagetypeHelper::importExtension(null, $pageType,true,null,true);
			
			$dispatcher->trigger('onGetContentItems',array(&$ContentItems,$model));
			//return $ContentItems;
			
			if($ContentItems)
			{
				return $ContentItems;
			}
			
			/*
			$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,PagesandItemsHelper::getDirIcons(),null));
			
			$dispatcher->trigger('onDetach',array($pageType));
			$name = '';
			$results = $dispatcher->trigger('onGetPagetype',array(&$name,$pageType));
			*/
		}
		return false;
		
		
		
		
		//onGetContentItems(&$ContentItems,$model)
		
		$db = JFactory::getDBO();
		$catid = JRequest::getVar('categoryId',1);
		if( $catid > 1 || $catid == -1)
		{
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'itemslist.php');
			$ItemsList = new ItemsList();
			//return $ItemsList->renderItems($html, $rows, $toolbar, $ordering,$sliderText);	
			//check if exist
			if($catid > 1)
			{
				$db->setQuery( "SELECT c.id, c.state,c.title, c.catid, i.itemtype, c.created_by, u.username "
				. "\nFROM #__content AS c "
				. "\nLEFT JOIN #__pi_item_index AS i ON c.id=i.item_id "
				. "\nLEFT JOIN #__users AS u ON u.id=c.created_by "
				. "\nWHERE c.catid='".($catid == -1 ? 0 : $catid)."' "
				//. "\nAND (c.state='0' OR c.state='1' ) " //
				);
			}
			elseif($catid == -1)
			{
				$query = "SELECT id "
				. "\nFROM #__categories "
				. "\nWHERE extension='com_content' ";
				/*
				$db->setQuery( $query );
				$categories = $db->loadResultArray(); //loadAssocList('id');
				$categoriesString = '';
				foreach($categories as $categorie)
				{
					$categoriesString .= "\nAND c.catid!='".$categorie."' ";
				}
				if($categoriesString)
				{
					$categoriesString = "\nOR(".$categoriesString.")";
				}
				*/
				$categoriesString = "\nOR c.catid NOT IN (".$query.")"; //$categories.")";
				$query = "SELECT c.id, c.state,c.title, c.catid, i.itemtype, c.created_by, u.username "
				. "\nFROM #__content AS c "
				. "\nLEFT JOIN #__pi_item_index AS i ON c.id=i.item_id "
				. "\nLEFT JOIN #__users AS u ON u.id=c.created_by "
				. "\nWHERE c.catid NOT IN (".$query.")"
				//. "\nWHERE c.catid='".($catid == -1 ? 0 : $catid)."' "
				//.$categoriesString
				//. implode(' \nAND c.catid!=',$categories)
				
				;
				$db->setQuery($query);
			}
			
			$rows = $db->loadObjectList();

			$count_rows = 1;
			if(!count($rows))
			{
				$count_rows = 0;
			}

			$html = '';
			$html .= '<div class="paddingList">';
				$canDoContent = PagesAndItemsHelper::canDoContent();
				$toolbar = false;
				if($count_rows && count($rows) == 1 && $canDoContent->get('core.create'))
				{
					$html .= PagesAndItemsHelper::itemtype_select(0); //$this->itemtype_select(0);

					$html .= '<div class="pi_wrapper">';
						$html .= '<div class="line_top paddingList">';
						$html .= '</div>';
					$html .= '</div>';
					$toolbar = $ItemsList->addMiniToolbar();
				}
				elseif($count_rows && count($rows) > 1 && $canDoContent->get('core.create'))
				{
					$html .= PagesAndItemsHelper::itemtype_select(0); //$this->itemtype_select(0);
					$toolbar = $ItemsList->addMiniToolbar();
				}
				elseif(!$count_rows)
				{
					$html .= PagesAndItemsHelper::itemtype_select(0); //$this->itemtype_select(0);
					$toolbar = null; //$editToolbarButtons,$newToolbarButtons);
				}
				elseif($count_rows)
				{
					$toolbar = $ItemsList->addMiniToolbar(); //$editToolbarButtons,$newToolbarButtons);
				}
			
			return $ItemsList->renderItems($html, $rows, $toolbar); //, $ordering,$sliderText);	
			
			//return $this->renderItems($html,$rows,$toolbar);
		}
		return false;
	}
}
