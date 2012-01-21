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

class PagesAndItemsModelManagerArchiveTrash extends PagesAndItemsModelBase
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
		$table->displayName = JText::_('COM_PAGESANDITEMS_CONTENT');
		$table->reference_id = 'id';
		$table->reference_display = 'title';
		$table->archiveName = 'state';
		$table->archiveValue = '2';
		$table->image = PagesAndItemsHelper::getDirIcons().'components/content/article/icon-16-article.png'; //article/icon-16-article_a.png';
		$table->extension = '';
		$table->extensionType = '';
		
		$tables[] = $table;
		
		$table = null;

		$table->tableName = 'menu';
		$table->displayName = JText::_('COM_PAGESANDITEMS_MENUS');
		$table->reference_id = 'id';
		$table->reference_display = 'title';
		$table->archiveName = 'published';
		$table->archiveValue = '-1';
		$table->trashName = 'published';
		$table->trashValue = '-2';
		$table->extension = '';
		$table->publishedName = 'published';
		$table->publishedValue = '1';
		$table->unpublishedName = 'published';
		$table->unpublishedValue = '0';
		$table->extensionType = '';
		$table->image = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
		$tables[] = $table;
		
		$table = null;
		/*
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
		$table->tableName = 'pi_customitemtypes';
		$table->displayName = 'pi_customitemtypes'; 
		$table->reference_id = 'id';
		$table->reference_display = 'name';
		$table->extension = '';
		$table->extensionType = '';
		$tables[] = $table;

		$table = null;
		$table->tableName = 'pi_custom_fields';
		$table->displayName = 'pi_custom_fields';
		$table->reference_id = 'id';
		$table->reference_display = 'name';
		$table->extension = '';
		$table->extensionType = '';
		$tables[] = $table;
		
		//	pi_custom_fields_values ??
		
		$table = null;
		$table->tableName = 'pi_extensions';
		$table->displayName =JText::_('COM_PAGESANDITEMS_EXTENSIONS');
		$table->reference_id = 'extension_id';
		$table->reference_display = 'name';
		$table->image = PagesAndItemsHelper::getDirIcons().'extensions/icon-16-plugin.png';
		$table->extension = '';
		$table->extensionType = '';
		$tables[] = $table;
		$table = null;
		*/
		
		$query = 'SELECT *'
			. ' FROM #__pi_extensions'
			. ' GROUP BY type'
			. ' ORDER BY type'
			;
		$this->db->setQuery( $query );
		$types = $this->db->loadObjectList();

		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..');
		
		foreach($types as $type)
		{

			require_once($path.DS.'includes'.DS.'extensions'.DS.$type->type.'helper.php');
			$typeName = 'Extension'.ucfirst($type->type).'Helper';
			$typeName::importExtension(null, null,true,null,true);
		}
				/*
		so we have load all pi extensions
		so we can trigger
		*/

		$dispatcher = &JDispatcher::getInstance();
		$dispatcher->trigger('onArchiveTrashTable', array ( &$tables));

		return $tables;
		
		
	}
	
	
	function getPageTree($rows,$menutype,$modelPage,$menuItemsTypes,$modelMenutypes,$filter_state)
	{
		//$model = new PagesAndItemsModelMenutypes();
		//$modelMenutypes = new PagesAndItemsModelMenutypes();
		//$menuItemsTypes = $modelMenutypes->getTypeListComponents();
		
		$tree = array();  //stores the tree
		$tree_index = array();  //an array used to quickly find nodes in the tree
		$id_column = "id";  //The column that contains the id of each node
		$parent_column = "parent";  //The column that contains the id of each node's parent
		$text_column = "name";  //The column to display when printing the tree to html
		//build the tree - this will complete in a single pass if no parents are defined after children
		while(count($rows) > 0)
		{
			//foreach($rows as $row)
			foreach($rows as $row_id => $row)
			{
				//if($row->$parent_column)
				
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
					//we need an $this->menuItemsTypes->not_installed_no_access
					$pageType = 'not_installed_no_access';
					$not_installed_no_access = true;
				}
				$menuItemsType = $menuItemsTypes[$pageType];
				//dump($menuItemsType);
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
					//if((!array_key_exists($row->$parent_column, $rows)) and (!array_key_exists($row->$parent_column, $tree_index)))
					
					if((!array_key_exists($row[$parent_column], $rows)) and (!array_key_exists($row[$parent_column], $tree_index)))
					{
						unset($rows[$row_id]);
						//dump($row_id,'x');
						//unset($row);
					}
					else
					{
						//if(array_key_exists($row->$parent_column, $tree_index))
						if(array_key_exists($row[$parent_column], $tree_index))
						{
							//dump($row_id,'y');
							$parent = & $tree_index[$row[$parent_column]];
							$parent['children'][$row_id] = array("node" => $row, "children" => array());
							$tree_index[$row_id] = & $parent['children'][$row_id];
							unset($rows[$row_id]);
							
							/*
							$parent = & $tree_index[$row->$parent_column];
							$parent['children'][$row->$id_column] = array("node" => $row, "children" => array());
							$tree_index[$row->$id_column] = & $parent['children'][$row->$id_column];
							unset($row);
							*/
						}
					}
				}
				else
				{
					//dump($row_id,'z');
					$tree[$row_id] = array("node" => $row, "children" => array());
					$tree_index[$row_id] = & $tree[$row_id];
					unset($rows[$row_id]);
					/*
					$tree[$row->$id_column] = array("node" => $row, "children" => array());
					$tree_index[$row->$id_column] = & $tree[$row->$id_column];
					unset($row);
					*/
				}
			}
		}
		//dump(count($rows));
		//we are done with index now so free it
		unset($tree_index);
		//start printing out the tree
		$html = "<div id='tree'>\n";
			$html .= "      <ul>\n";
			foreach($tree as $node)
			{
				//go to each top level node and print it and it's children
				$html .= $this->getTreeNode($node, $text_column, 8, 2);
			}
			$html .= "      </ul>\n";
		$html .= "</div>\n";
		
		
		
		$menutypeTitle = $modelPage->getMenutypeTitle($menutype);
		//$script .= "','index.php?option=com_pagesanditems&view=page&layout=root&menutype=";
		//$script .= strtolower($menutypes[$m]);
		$openIconUrl = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
		$closeIconUrl = $openIconUrl;

		
		$json = '[{"property": {"name": "'.$menutypeTitle.'","cls": "root-first","uid": "root-first","closeIconUrl": "'.$closeIconUrl.'","openIconUrl": "'.$openIconUrl.'"}}';
			//$json .= '"children": [';
			$count = 0;
			foreach($tree as $node)
			{	
				$count++;
				//go to each top level node and print it and it's children
				$json .= ','.$this->getTreeNodeJson($node, $text_column,$filter_state);
				//$json .= $this->getTreeNodeJson($node, $text_column);
				//for all nodes but not the last node we must add an ','
				/*
				if($count != count($tree))
				{
					$json .= ',';
				}
				*/
			}
		$json .= ']';
		//$json .= '}]';
		return($json);
		return $html;
	}

	//recursive function used to print tree structure to html
	function getTreeNodeJson($node, $text_column,$filter_state)
	{
		//print the current node
	//	$html = str_repeat(" ", $indent) . "<li>". $node['node'][$text_column];
		//$html = "<li>". $node['node'][$text_column];
		
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
		switch($filter_state)
		{
			case 'all':
			//$hasCheckbox = '';
			break;
			
			case 'published':
				//$hasCheckbox = "published='1'";
				if($node['node']['published'] <> '1')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
			break;
			
			case 'unpublished':
				//$hasCheckbox = "published='0'";
				if($node['node']['published'] <> '0')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
			break;
			
			case 'archive':
				//$hasCheckbox = "published='2'";
				if($node['node']['published'] <> '2')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
			break;
			
			case 'trash':
				if($node['node']['published'] <> '-2')
				{
					$hasCheckbox = ', "hasCheckbox": false';
				}
			break;
			
		}
		
		
/*

				property: node.property,
				type: node.type,
				state: node.state,
				data: node.data

*/
		//,"cls": "root-first"
		switch($node['node']['published'])
		{
			case '1':
				$state = 'published';
				$state = '<span class="state publish"></span>';
			break;

			case '0':
				$state = 'unpublished';
				$state = '<span class="state unpublish"></span>';
			break;
					
			case '2':
				$state = 'archive';
				$state = '<span class="state archive"></span>';
			break;
		
			case '-2':
				$state = 'trash';
				$state = '<span class="state trash"></span>';
			break;
		
		}
		
		$name = '';
		$name .= addslashes($node['node'][$text_column].' <a class="jgrid"> '.$state.'</a>');
		
		$html ='{"property": {"name": "'.$name.'","id": "'.$node['node']['id'].'",'.$closeIcon.' ,'.$openIcon.$hasCheckbox.'}'; //,"data":{"url":"test"}'; 
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
/*
var json = [	
		{
			"property": {
				"name": "root"
			},
			"children": [
				{
					"property": {
						"name": "node1",
						"openIconUrl": "IconUrl/folder-open.gif",
						"closeIconUrl": "IconUrl/folder-close.gif"

					}
				},
				{
					"property": {
						"name": "node2",
						"hasCheckbox": false
					},
					"children":[
						{
							"property": {
								"name": "node2.1"
							},
							"state": {
								"checked": "checked"
							}
						},
						{
							"property": {
								"name": "node2.2"
							}
						}
					]
				},
				{
					"property": {
						"name": "node4"
					}
				},
				{
					"property": {
						"name": "node3",
						"hasCheckbox": false
					}
				}
			]
		}
	];



*/

	//recursive function used to print tree structure to html
	function getTreeNode($node, $text_column, $indent, $indent_size)
	{
		//print the current node
	//	$html = str_repeat(" ", $indent) . "<li>". $node['node'][$text_column];
		$html = str_repeat(" ", $indent) . "<li>". $node['node'][$text_column];
		if($node['children'])
		{
			$html .= "\n". str_repeat(" ", $indent + $indent_size) . "<ul>\n";
			//then print it's children nodes
			foreach($node['children'] as $child)
			{
				$html .= $this->getTreeNode($child, $text_column, $indent + $indent_size * 2, $indent_size);
			}
			$html .= str_repeat(" ", $indent + $indent_size) . "</ul>\n". str_repeat(" ", $indent);
		}
		$html .= "</li>\n";
		return $html;
	}
}
