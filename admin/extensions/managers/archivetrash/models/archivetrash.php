<?php
/**
* @version		1.6.0
* @package		PagesAndItems
* @copyright	Copyright (C) 2006-2010 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'models'.DS.'base.php');
/**

 */

class PagesAndItemsModelArchiveTrash extends PagesAndItemsModelBase
{
	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct() //$id = null,$edit = null)
	{
		parent::__construct();
	}
	
	function getTables()
	{
		$archiveType = JRequest::getVar('archiveType','all');
		
		$tables = array();
		
		$table = null;
		$table->tableName = 'content';
		//$table->displayName = JText::_('COM_PAGESANDITEMS_CONTENT');
		$table->displayName = JText::_('COM_PAGESANDITEMS_ARTICLE');
		$table->referenceId = 'id';
		$table->referenceName = 'title';
		$table->referenceDisplay = 'JGLOBAL_TITLE';
		
		
		$state->name = 'state';
		$state->display = 'JStatus';
		$state->archivedName = 'state';
		$state->archivedValue = '2';

		$state->trashedName = 'state';
		$state->trashedValue = '-2';

		$state->publishedName = 'state';
		$state->publishedValue = '1';
		
		$state->unpublishedName = 'state';
		$state->unpublishedValue = '0';

		$table->state = $state;
		
		$table->stateTypes = array('published','unpublished', 'archive', 'trash', 'delete');
		$table->image = PagesAndItemsHelper::getDirIcons().'components/content/article/icon-16-article.png';
		
		$tables[] = $table;
		
		$table = null;
		$state = null;
		
		$table->tableName = 'menu';
		$table->displayName = JText::_('COM_PAGESANDITEMS_MENUS');
		$table->referenceId = 'id';
		$table->referenceName = 'title';
		$table->referenceDisplay = 'JGLOBAL_TITLE';
		
		$state->name = 'published';
		$state->display = 'JStatus';
		
		$state->archivedName = 'published';
		$state->archivedValue = '2';

		$state->trashedName = 'published';
		$state->trashedValue = '-2';

		$state->publishedName = 'published';
		$state->publishedValue = '1';
		
		$state->unpublishedName = 'published';
		$state->unpublishedValue = '0';
		$table->state = $state;
		
		$table->stateTypes = array('published','unpublished', 'trash', 'delete');
		$table->image = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
		$tables[] = $table;
		
		$table = null;
		$state = null;
		
		
		/*
		$table->tableName = 'pi_customitemtypes';
		$table->displayName = 'customitemtypes'; 
		$table->referenceId = 'id';
		$table->referenceName = 'name';
		$table->referenceDisplay = 'JGLOBAL_TITLE';
		
		$state->name = 'state';
		$state->display = 'JStatus';
		
		$state->archivedName = 'state';
		$state->archivedValue = '2';

		$state->trashedName = 'state';
		$state->trashedValue = '-2';

		$state->publishedName = 'state';
		$state->publishedValue = '1';
		
		$state->unpublishedName = 'state';
		$state->unpublishedValue = '0';
		$table->state = $state;
		
		$table->stateTypes = array('published','unpublished'); //, 'trash', 'delete');
		
		$tables[] = $table;

		$table = null;
		$state = null;
		
		$table->tableName = 'pi_custom_fields';
		$table->displayName = 'custom_fields';

		$table->referenceId = 'id';
		$table->referenceName = 'name';
		$table->referenceDisplay = 'JGLOBAL_TITLE';
		
		$state->name = 'state';
		$state->display = 'JStatus';
		
		$state->archivedName = 'state';
		$state->archivedValue = '2';

		$state->trashedName = 'state';
		$state->trashedValue = '-2';

		$state->publishedName = 'state';
		$state->publishedValue = '1';
		
		$state->unpublishedName = 'state';
		$state->unpublishedValue = '0';
		$table->state = $state;
		
		$table->stateTypes = array('published','unpublished'); //, 'trash', 'delete');
		
		$tables[] = $table;
		
		$table = null;
		$state = null;
		*/
		
		
		/*
		//	pi_custom_fields_values ??
		
		
		$table->tableName = 'pi_extensions';
		$table->displayName =JText::_('COM_PAGESANDITEMS_EXTENSIONS');
		$table->reference_id = 'extension_id';
		$table->reference_display = 'name';
		$table->image = PagesAndItemsHelper::getDirIcons().'extensions/icon-16-plugin.png';
		$table->extension = '';
		$table->extensionType = '';
		$tables[] = $table;
		$table = null;
		
		
	

		$table->tableName = 'pi_item_index';
		$table->displayName = 'pi_item_index';
		$table->reference_id = 'id';
		$table->reference_display = 'item_id';
		$table->extension = '';
		$table->extensionType = '';
		$tables[] = $table;
		
		$table = null;
		$table->tableName = 'pi_item_other_index';
		$table->reference_id = 'id';
		$table->reference_display = 'item_id';
		$table->displayName = 'pi_item_other_index'; 
		$table->extension = '';
		$table->extensionType = '';
		$tables[] = $table;
		
		$table = null;
		$state = null;
		*/
		

		
		$query = "SELECT *"
			. " FROM #__pi_extensions"
			. " WHERE type <> 'language' "
			. " GROUP BY type"
			. " ORDER BY type"
			;
		$this->db->setQuery( $query );
		$types = $this->db->loadObjectList();

		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..');
		
		foreach($types as $type)
		{
			//if($type->type != 'language')
			//{
			
				require_once($path.DS.'includes'.DS.'extensions'.DS.$type->type.'helper.php');
				$typeName = 'Extension'.ucfirst($type->type).'Helper';
				$typeName::importExtension(null, null,true,null,true);
			//}
		}
		/*
		so we have load all pi extensions
		so we can trigger
		*/

		$dispatcher = &JDispatcher::getInstance();
		$dispatcher->trigger('onGetArchiveTrashTable', array ( &$tables));
		
		//onDetach($type)
		//$dispatcher->trigger('deatach', array ( &$tables));
		foreach($types as $type)
		{
			//$dispatcher->trigger('onDetach', array ( $type->type));
		}
		//deatch the extensions
		return $tables;
		
		
	}
	
	
	
	function getPageTree($rows,$menutype,$modelPage,$menuItemsTypes,$modelMenutypes,$filter_state)
	{
		$tree = array();  //stores the tree
		$tree_index = array();  //an array used to quickly find nodes in the tree
		$id_column = "id";  //The column that contains the id of each node
		$parent_column = "parent";  //The column that contains the id of each node's parent
		$text_column = "name";  //The column to display when printing the tree to html
		//build the tree - this will complete in a single pass if no parents are defined after children
		while(count($rows) > 0)
		{
			foreach($rows as $row_id => $row)
			{
				//if($row->$parent_column)
				/*
				$row['underlayingPages'] = $this->getUnderlayingPages($rows,$row['id']);
				$row['contentItems'] = $this->getContentItems($row['id']);
				*/
				$pageType = null;
				if($row['type'] != 'component')
				{
					$pageType = $row['type'];
				}
				else
				{
					$pageType =$modelMenutypes->buildPageType($row['link']);
					if(!isset($menuItemsTypes[$pageType]))
					{
						$pageType = null;
					}
				}
				if(!$pageType)
				{
					//we have an component without option???
					//i think is an unistallet component
					//we set the image to component_no_access
					$pageType = 'not_installed_no_access';
					$not_installed_no_access = true;
				}
				$menuItemsType = $menuItemsTypes[$pageType];
				if(isset($menuItemsType->icons->default->imageUrl))
				{
					$image = $menuItemsType->icons->default->imageUrl;
				}
				else
				{
					if(isset($menuItemsType->icons->componentDefault->default->imageUrl))
					{
						$image = $menuItemsType->icons->componentDefault->default->imageUrl;
					}
				}
				$row['icon'] = $image;
				
				if($row[$parent_column] && $row[$parent_column] != 1)
				{
					if((!array_key_exists($row[$parent_column], $rows)) and (!array_key_exists($row[$parent_column], $tree_index)))
					{
						unset($rows[$row_id]);
					}
					else
					{
						if(array_key_exists($row[$parent_column], $tree_index))
						{

							$parent = & $tree_index[$row[$parent_column]];
							$parent['children'][$row_id] = array("node" => $row, "children" => array());
							$tree_index[$row_id] = & $parent['children'][$row_id];
							unset($rows[$row_id]);

						}
					}
				}
				else
				{
					$tree[$row_id] = array("node" => $row, "children" => array());
					$tree_index[$row_id] = & $tree[$row_id];
					unset($rows[$row_id]);
				}
			}
		}
		//we are done with index now so free it
		unset($tree_index);
		//start printing out the tree
		
		$menutypeTitle = $modelPage->getMenutypeTitle($menutype);

		$openIconUrl = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
		$closeIconUrl = $openIconUrl;
		
		$json = '[{"property": {"name": "'.$menutypeTitle.'","cls": "root-first","uid": "root-first","closeIconUrl": "'.$closeIconUrl.'","openIconUrl": "'.$openIconUrl.'"}}';
			$count = 0;
			foreach($tree as $node)
			{	
				$count++;
				//go to each top level node and print it and it's children
				$json .= ','.$this->getTreeNodeJson($node, $text_column,$filter_state);
			}
		$json .= ']';
		return($json);
	}

	//recursive function used to print tree structure to html
	function getTreeNodeJson($node, $text_column,$filter_state)
	{
		$icon = $node['node']['icon'];
		$imgClass = explode("class:",$icon);
		$class = false;
		if(count($imgClass) == '2')
		{
			//we have an class
			$icon = $imgClass[1];
			$class = true;
		}
		if($class)
		{
			$openIcon = '"closeIcon": "'.$icon.'"';
			$closeIcon = '"openIcon": "'.$icon.'"';
		}
		else
		{
			$openIcon = '"closeIconUrl": "'.$icon.'"';
			$closeIcon = '"openIconUrl": "'.$icon.'"';
		}


		$hasCheckbox = '';
		$arState = array();
		switch($filter_state)
		{
			case 'all':
				$arState = array('published','unpublished', 'archive', 'trash');
			//$hasCheckbox = '';
			break;
			
			case 'published':
				//$hasCheckbox = "published='1'";
				if($node['node']['published'] <> '1')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
				$arState = array('published');
			break;
			
			case 'unpublished':
				//$hasCheckbox = "published='0'";
				if($node['node']['published'] <> '0')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
				$arState = array('unpublished');
			break;
			
			case 'archive':
				//$hasCheckbox = "published='2'";
				if($node['node']['published'] <> '2')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
				$arState = array('archive');
			break;
			
			case 'trash':
				if($node['node']['published'] <> '-2')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
				$arState = array('trash');
			break;
			
			case '':
				if($node['node']['published'] != '0' && $node['node']['published'] != '-2' && $node['node']['published'] != '2')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
				$arState = array('unpublished', 'archive', 'trash');
			break;
			
		}

		$title = '';
		$onclick = '';
		$hasTip = '';
		switch($node['node']['published'])
		{
			case '1':
				//$state = 'published';
				if(in_array("published", $arState))
				{
					$onclick = 'onclick="setCid(\''.$node['node']['id'].'\');Joomla.submitbutton(\'unpublish\');"';
					$title = 'title="'.JText::_('JLIB_HTML_UNPUBLISH_ITEM').'"';
					$hasTip = 'hasTip';
				}
				$state = '<span class="state publish"></span>';
				
			break;

			case '0':
				//$state = 'unpublished';
				if(in_array("unpublished", $arState))
				{
					$onclick = 'onclick="setCid(\''.$node['node']['id'].'\');Joomla.submitbutton(\'restore\');"';
					$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
					$hasTip = 'hasTip';
				}
				$state = '<span class="state unpublish"></span>';
			break;
					
			case '2':
				//$state = 'archive';
				if(in_array("archive", $arState))
				{
					$onclick = 'onclick="setCid(\''.$node['node']['id'].'\');Joomla.submitbutton(\'restore\');"';
					$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
					$hasTip = 'hasTip';
				}
				$state = '<span class="state archive"></span>';
			break;
		
			case '-2':
				$state = 'trash';
				if(in_array("trash", $arState))
				{
					$onclick = 'onclick="setCid(\''.$node['node']['id'].'\');Joomla.submitbutton(\'restore\');"';
					$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
					$hasTip = 'hasTip';
				}
				$state = '<span class="state trash"></span>';
			break;
		
		}
		
		$name = '';
		//add title with the content items?
		$name .= addslashes($node['node'][$text_column].' <a class="jgrid '.$hasTip.'" '.$title.' '.$onclick.' > '.$state.'</a>');
		
		$html ='{"property": {"name": "'.$name.'","id": "'.$node['node']['id'].'",'.$closeIcon.' ,'.$openIcon.$hasCheckbox.'}';
		if($node['children'])
		{
			$count = 0;
			$html .= ',"children": [';
			//then print it's children nodes
			foreach($node['children'] as $child)
			{
				$count++;
				$html .= $this->getTreeNodeJson($child, $text_column,$filter_state);
				if($count != count($node['children']))
				{
					$html .= ',';
				}
			}
			$html .= "]";
			
		}
		$html .='}';
		return $html;
	}




	function getUnderlayingPages($rows,$pageId)
	{
		$config = PagesAndItemsHelper::getConfig();
		foreach($rows as $row)
		{
			if($row->parent == $pageId)
			{
				//need to get the contentItems
				/*
				$this->getContentItems($pageId);
				
				*/
			}
		}
	}
	
	
	function getMenuItem($pageId)
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			JRequest::setVar( 'edit', true );
			JRequest::setVar( 'cid',  array($pageId));
			$menu_item = &new MenusModelItem();
			$item = $menu_item->getItem();
		}
		else
		{
			$app	= JFactory::getApplication();
			// Push the new ancillary data into the session.
			$app->setUserState('com_menus.edit.item.type',	null);
			$app->setUserState('com_menus.edit.item.link',	null);

			$menu_item = &new MenusModelItem();
			JRequest::setVar( 'id', $pageId );
			$menu_item->setState('item.id',$pageId);
			$menu_item->setState('item.menutype',$menutype);
			$item = $menu_item->getItem($pageId);
			$menu_item->setState('item.link',$item->link);
		}
		return $menu_item;
	}
	
	function getContentItems($pageId)
	{
		$menu_item = $this->getMenuItem($pageId);
		$pageMenuItem = $menu_item->getItem($pageId);
		
		
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$menu_item_urlparams = $menu_item->getUrlParams();
			$menu_item_id = $menu_item_urlparams->get('id',null);
		
			$menu_item_view = $menu_item_urlparams->get('view',null);
			$menu_item_layout = $menu_item_urlparams->get('layout',null);
		}
		else
		{
			//dump($pageMenuItem );
			//dump($this->menu_item);
			if(isset($pageMenuItem->request['id']))
			{
				$menu_item_id = $pageMenuItem->request['id'];
			}
			elseif(isset($pageMenuItem->params['id']))
			{
				$menu_item_id = $pageMenuItem->params['id'];
			}
			else
			{
				$menu_item_id = 0;
			}

			if(isset($pageMenuItem->request['view']))
			{
				$menu_item_view = $pageMenuItem->request['view'];
			}
			elseif(isset($pageMenuItem->params['view']))
			{
				$menu_item_view = $pageMenuItem->params['view'];
			}
			else
			{
				$menu_item_view = null;
			}
			
			if(isset($pageMenuItem->request['layout']))
			{
				$menu_item_layout = $pageMenuItem->request['layout'];
			}
			elseif(isset($pageMenuItem->params['layout']))
			{
				$menu_item_layout = $pageMenuItem->params['layout'];
			}
			else
			{
				$menu_item_layout = null;
			}
		}
		
	
		if($menu_item_view == 'category')
		{
			$where[] = "c.catid='".$menu_item_id."'";
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
			$where[] = "(c.state='0' OR c.state='1')";
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
			$menu_item_advancedparams = $menu_item->getAdvancedParams();
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
			if(isset($pageMenuItem->params['orderby_pri']))
			{
				$orderByPri = $pageMenuItem->params['orderby_pri'];
			}
			else
			{
				$orderByPri = null;
			}
			
			if(isset($pageMenuItem->params['orderby_sec']))
			{
				$orderBySec = $pageMenuItem->params['orderby_sec'];
			}
			else
			{
				$orderBySec = null;
			}

			if(isset($pageMenuItem->params['orderby']) && $pageMenuItem->params['orderby'] != '')
			{
				$orderBy = $pageMenuItem->params['orderby'];
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
		
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		$query = "SELECT c.id, c.state, c.title, i.itemtype, c.created_by, u.username "
		. "\nFROM #__content AS c "
		. $frontpage
		. "\nLEFT JOIN #__pi_item_index AS i ON c.id=i.item_id "
		. "\nLEFT JOIN #__users AS u ON u.id=c.created_by "
		. "\n $where "
		. "\nORDER BY $order ";
		$this->db->setQuery( $query );
		$contentItems = $this->db->loadObjectList();
		return $contentItems;
	}
}
