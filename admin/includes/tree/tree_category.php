<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('JPATH_BASE') or die;
class PagesAndItemsTreeCategory
{
	var $icons = null;
	//var $iconNew = null;
	//var $iconEdit = null;
	
	function getSelectCategoryExtension()
	{
		$select = '';
		//$select .= '<div class="dtree dtree_container">';
		//	$select .= '<div class="dtree">';
			//add here select for categories
					
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension AS value, extension AS text');
			$query->from('#__categories');
			//$query->where("extension !='system'");
			$query->where("extension LIKE '%com_%'");
			$query->group('extension');
			$query->order('extension ASC');
			$db->setQuery($query);
			//$db->loadObjectList();
			$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
			
			$categoryExtension = (strpos($categoryExtension,'com_') === false) ? 'com_'.strtolower($categoryExtension) : strtolower($categoryExtension);
			
			
			//$options[] = JHTML::_('select.option', '', '- '. JText::_('JCATEGORY' ).' '.JText::_( 'JSELECT').' -' );
			
			$options[] = JHTML::_('select.option', '',JText::_('JOPTION_FROM_COMPONENT'));
			
			$lang = JFactory::getLanguage();
			$rows = $db->loadObjectList();
			foreach($rows as $row)
			{
				/*
				$extension = $row->text;
				$lang->load(strtolower($extension.'.sys'), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension.'.sys'), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
				$text = JText::_(strtoupper($row->text) ) <> strtoupper($row->text) ? JText::_(strtoupper($row->text) ) : $row->text;
				*/
				$parts = explode('.', $row->text);
				//FB::dump($parts);
				$component = $parts[0];
				$section = (count($parts) > 1) ? $parts[1] : null;

				$lang->load($component, JPATH_BASE, null, false, false)
				||	$lang->load($component, JPATH_ADMINISTRATOR.'/components/'.$component, null, false, false)
				||	$lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
				||	$lang->load($component, JPATH_ADMINISTRATOR.'/components/'.$component, $lang->getDefault(), false, false);
			
				// if the component section string exits, let's use it
				if ($lang->hasKey($component_section_key = $component.($section?"_$section":''))) {
					$text = JText::_($component_section_key);
				}
				// Else use the component string
				else {
					$text = JText::_(strtoupper($component));
				}
				
				$row->text = $text;
			}
			$options = array_merge( $options, $rows );
			$select .= JHTML::_('select.genericlist', $options, 'categoryExtension', 'class="inputbox" size="1" onchange="Javascript:change_extension();"', 'value', 'text', $categoryExtension );
		//	$select .= '</div>';
		//$select .= '</div>';
		return $select;
	}
	
	function getHiddenCategoryExtension()
	{
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
		$input = '<input type="hidden" id="categoryExtension" name="categoryExtension" value="'.$categoryExtension.'" />';
		return $input;
	}
	
	function getTree($categoryId = 1)
	{
		//load js
		$this->loadBehavior();
		$html = '';
		$html .= '<table class="piadminform xadminform tree" width="98%">';
			$html .= '<tbody>';
				$html .= '<tr>';
					$html .= '<td valign="top">'; //>';
						$html .= '<div class="dtree dtree_container">';
							$html .= '<div class="dtree">';
								$html .= '<div id="tree_container">';
								$html .= '</div>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</td>';
				$html .= '</tr>';
			$html .= '</tbody>';
		$html .= '</table>';
		$this->getCategories();
		return $html;
	}
	
	function loadBehavior()
	{
		$path = PagesAndItemsHelper::getDirComponentAdmin();
		//first we must load mootools
		JHTML::_('behavior.framework',true);
		
		JHTML::script('Mif.Tree.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Node.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Hover.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Selection.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Load.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Draw.js', $path.'/media/js/Core/',false);

		JHTML::script('Mif.Tree.KeyNav.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Sort.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Transform.js', $path.'/media/js/More/',false);
			//JHTML::script('Mif.Tree.Drag.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Element.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Checkbox.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Rename.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.CookieStorage.js', $path.'/media/js/More/',false);
		JHTML::stylesheet('mif-tree_checkboxes.css', $path.'/media/css/');
		JHTML::stylesheet('dtree.css',$path.'/css/');
	}

	//output the tree
	function getCategories()
	{
		$doc =& JFactory::getDocument();
		$js = '';
		$js = 'window.addEvent(\'domready\',function(){'."\n";
				$js .= '	var tree_container = document.id(\'tree_container\');'."\n";

					$js .= '	tree = new Mif.Tree({'."\n";
					$js .= '		container: document.id(\'tree_container\')'."\n";
					$js .= '		,forest: true,'."\n";
					$js .= '		initialize: function(){'."\n";
					//$js .= '			new Mif.Tree.KeyNav(this);'."\n";
					//$js .= '			this.initSelection: function(){'."\n";
					//$js .= '			this.initSelection({'."\n";
					//$js .= '			this.defaults.selectClass = \'\';'."\n";
					//$js .= '			this.wrapper.addEvent(\'mousedown\', this.attachSelect.bindWithEvent(this));'."\n";
					//$js .= '			this.wrapper.removeEvent(\'mousedown\');'."\n";
					//$js .= '			})'."\n";

					$js .= '		},'."\n";
					
					
					$js .= '		types: {'."\n";
					$js .= '			folder: {'."\n";
					$js .= '				openIcon: \'mif-tree-open-icon\','."\n";
					$js .= '				closeIcon: \'mif-tree-close-icon\''."\n";
					$js .= '			},'."\n";
					$js .= '			loader: {'."\n";
					$js .= '				openIcon: \'mif-tree-loader-open-icon\','."\n";
					$js .= '				closeIcon: \'mif-tree-loader-close-icon\','."\n";
					$js .= '				dropDenied: [\'inside\',\'after\']'."\n";
					$js .= '			},'."\n";
					$js .= '			disabled: {'."\n";
					$js .= '				openIcon: \'mif-tree-open-icon\','."\n";
					$js .= '				closeIcon: \'mif-tree-close-icon\','."\n";
					$js .= '				dragDisabled: true,'."\n";
					$js .= '				cls: \'disabled\''."\n";
					$js .= '			},'."\n";
					$js .= '			book: {'."\n";
					$js .= '				openIcon: \'mif-tree-book-icon\','."\n";
					$js .= '				closeIcon: \'mif-tree-book-icon\','."\n";
					$js .= '				loadable: true'."\n";
					$js .= '			},'."\n";
					$js .= '			bin: {'."\n";
					$js .= '				openIcon: \'mif-tree-bin-open-icon\','."\n";
					$js .= '				closeIcon: \'mif-tree-bin-close-icon\''."\n";
					$js .= '			}'."\n";
					$js .= '		},'."\n";
					$js .= '		dfltType: \'folder\','."\n";
					$js .= '		height: 18,'."\n";
					$js .= '		onCheck: function(node){'."\n";
					$js .= '			isChecked(true);'."\n";
					$js .= '			var form = document.id(\'adminForm\');'."\n";
					$js .= '			var input = new Element(\'input\', {'."\n";
					$js .= '				\'type\': \'hidden\','."\n";
					$js .= '				\'name\': \'cid[]\','."\n";
					$js .= '				\'id\': \'cb\'+node.id,'."\n";
					$js .= '				\'value\': node.id'."\n";
					$js .= '				});'."\n";
					$js .= '			form.grab(input);'."\n";
					$js .= '		},'."\n";
					$js .= '		onUnCheck: function(node){'."\n";
					$js .= '			//alert(node.id); //this is the pageId'."\n";
					$js .= '			//todo boxchecked'."\n";
					$js .= '			isChecked(false);'."\n";
					$js .= '			document.id(\'adminForm\').getElement(\'input[id=cb\'+node.id+\']\').destroy();'."\n";
					$js .= '		}'."\n";
					$js .= '	})'."\n";
					
					//$js .= '	.wrapper.removeEvent(\'mousedown\')'."\n";
					
					$js .= '	.addEvent(\'load\', function(){'."\n";
					
					$js .= '		if(document.id(\'tree_container\').getElement(\'span[class*=root-first]\'))document.id(\'tree_container\').getElement(\'span[class*=root-first]\').getElement(\'span[class*=mif-tree-gadjet]\').destroy();'."\n";
					$js .= '		if(document.id(\'tree_container\').getElement(\'span[class*=root-first]\'))document.id(\'tree_container\').getElement(\'span[class*=root-first]\').getParent().addClass(\'mif-tree-node-root-first\');'."\n";
					
					if(JRequest::getVar('categoryId',1))
					{
						$js .= '		tree.select(Mif.id(\'node-id-'.JRequest::getVar('categoryId',1).'\'));'."\n";
					}

					$js .= '	})'."\n";
					/*
					$js .= '	.addEvent(\'mousedown\',function(node, state){'."\n";
					$js .= '	alert(\'mousedown \' + state + \' \' + node);'."\n";
					//$js .= '		var size = tree_container.getElement(\'div[class*=mif-tree-children-root]\').getSize();'."\n";
					//$js .= '		tree_container.setStyle(\'height\',size.y+\'px\');'."\n";
					$js .= '	})'."\n";
					*/
					$js .= '	.addEvent(\'toggle\',function(node, state){'."\n";
					$js .= '		var size = tree_container.getElement(\'div[class*=mif-tree-children-root]\').getSize();'."\n";
					$js .= '		tree_container.setStyle(\'height\',size.y+\'px\');'."\n";
					$js .= '	});'."\n";
					//$js .= '	})'."\n";
					//$js .= '	.wrapper.removeEvent(\'mousedown\');'."\n";
					//$js .= '	tree.wrapper.removeEvent(\'mousedown\');'."\n";
					//for future is it called from view page and other pagetype then content.... 
					//like banners_categories ore banner_category
					$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
					$component = (strpos($categoryExtension,'com_') !== false) ? strtolower($categoryExtension) : 'com_'.strtolower($categoryExtension);
					$db = JFactory::getDBO();
					
					//$app = JFactory::getApplication();
					//$input = $app->input;
					//FB::dump($input);
					//$language = $input->get('filter_language', -1,null);
					$language = PagesAndItemsHelper::getLanguageFilter();
					//if(JRequest::getVar())
					/*
					$db->setQuery("SELECT * FROM #__categories WHERE extension='$component' ORDER BY lft ASC" );
					*/
					
					$query = $db->getQuery(true);
					$query->select('*');
					$query->from('#__categories');
					$query->where("extension=".$db->quote($component));
					
					if($language != '-1')
					{
						
						$query->where('language='.$db->quote($language));
					}
					$query->order('lft ASC');
					$db->setQuery($query);
					//$dbrows = $db->loadAssocList('id');
					//FB::dump($input->get('filter_language',-1,null));
					//FB::dump($dbrows);
					//FB::dump($query->__toString(),'querry');
					
					/*
					//$db->setQuery($query);
					
					*/
					
					//FB::dump($language);
					/*
					if($language != '-1')
					{
						
						//$query->where('language='.$db->qoute($language));
						$db->setQuery("SELECT * FROM #__categories WHERE extension='$component' AND language='$language' ORDER BY lft ASC" );
					}
					else
					{
						$db->setQuery("SELECT * FROM #__categories WHERE extension='$component' ORDER BY lft ASC" );
					}
					*/
					//$query->order('lft ASC');
					//FB::dump($query);
					//$db->setQuery($query);
						
					
					$rows = $db->loadAssocList('id');
					$js .= '	var json = '.$this->getJsonCategoryTree($rows).';'."\n";

					// load tree from json.
					$js .= '	tree.load({json: json });'."\n";
					$js .= '	var size = tree_container.getElement(\'div[class*=mif-tree-children-root]\').getSize();'."\n";
					$js .= '	tree_container.setStyle(\'height\',size.y+\'px\');'."\n";

					$js .= '	var showP = new Element(\'p\');'."\n";

					$js .= '	var show = new Element(\'a\', {\'id\': \'show\'});'."\n";
					$js .= '	show.set(\'text\', \''.JText::_('COM_PAGESANDITEMS_OPEN_ALL').'\');'."\n";
					$js .= '	show.inject(showP, \'top\');'."\n";
					$js .= '	show.addEvent(\'click\', function(){'."\n";
					$js .= '		tree.root.recursive(function(){'."\n";
					$js .= '			this.toggle(true, false);'."\n";
					$js .= '		});'."\n";
					$js .= '	});'."\n";

					$js .= '	showP.appendText(\' | \');'."\n";

					$js .= '	var close = new Element(\'a\', {\'id\': \'close\'});'."\n";
					$js .= '	close.set(\'text\', \''.JText::_('COM_PAGESANDITEMS_CLOSE_ALL').'\');'."\n";
					$js .= '	close.inject(showP);'."\n";
					$js .= '	close.addEvent(\'click\', function(){'."\n";
					$js .= '		tree.root.recursive(function(){'."\n";
					$js .= '			this.toggle(false, false);'."\n";
					$js .= '		});'."\n";
					$js .= '	});'."\n";

					$js .= '	showP.inject(tree_container, \'before\');'."\n";
					$js .= '	var showDiv = new Element(\'div\');'."\n";
					$js .= '	showDiv.inject(tree_container, \'before\');'."\n";
					$js .= '	tree_container.inject(showDiv);'."\n";

				$js .='});'."\n";
				$doc->addScriptDeclaration( $js );
		return '';
	}


	
		//this can move to includes/tree/tree_category.php
	function getJsonCategoryTree($rows,$filter_state = 'none',$menutypeTitle = 'Categories',$id = 1) //$rows,$menutype,$modelPage,$menuItemsTypes,$modelMenutypes,$filter_state)
	{
		
		$dispatcher = &JDispatcher::getInstance();
		$categoryExtension = JRequest::getVar('categoryExtension', 'com_content'); // 'com_banners'); //'content');
		
		$lang = &JFactory::getLanguage();
		$extension = $categoryExtension;
		$lang->load(strtolower($extension.'.sys'), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension.'.sys'), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$componentText = JText::_(strtoupper($categoryExtension) ) <> strtoupper($categoryExtension) ? JText::_(strtoupper($categoryExtension) ) : $categoryExtension;
		
		
		
		$component = (strpos($categoryExtension,'com_') !== false) ? strtolower(str_replace('com_','',$categoryExtension)) : strtolower($categoryExtension);
		
		$pageType = $component.'_categories';
		//$pageType = 'banners_categories';
		
		$icons = null;
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'extensions'.DS.'pagetypehelper.php');
		$results = array();
		if($component != 'content')
		{
			ExtensionPagetypeHelper::importExtension(null, $pageType,true,null,true);
			$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,PagesandItemsHelper::getDirIcons(),null));

			if(!count($results))
			{
				ExtensionPagetypeHelper::importExtension(null, 'component',true,null,true);
				$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',PagesandItemsHelper::getDirIcons(),'com_'.$component.'.categories'));
			}
			if(!$icons)
			{
				$dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',PagesandItemsHelper::getDirIcons(),'com_'.$component.'.categories'));
			}
		}
		//load the includesPagetype?
		/*
		if(!$icons)
		{
			$dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',PagesandItemsHelper::getDirIcons(),'com_'.$component.'.category'));
		}
		if(!$icons)
		{
			$dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',PagesandItemsHelper::getDirIcons(),'com_'.$component.'.category.blog'));
		}
		
		if(!$icons)
		{
			$dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',PagesandItemsHelper::getDirIcons(),'com_'.$component.'_category_blog'));
		}
		*/
		$dispatcher->trigger('onDetach',array($pageType));
		$dispatcher->trigger('onDetach',array('component'));
		
		
		$tree = array();  //stores the tree
		$tree_index = array();  //an array used to quickly find nodes in the tree
		$id_column = "id";  //The column that contains the id of each node
		$parent_column = "parent_id";  //The column that contains the id of each node's parent
		$text_column = "title";  //The column to display when printing the tree to html
		
		if(!$rows)
		{
			return '{}';
			return false;
			/*
			$this->db->setQuery("SELECT * FROM #__categories  WHERE extension='com_content' " );
			$rows = $this->db->loadAssocList('id');
			*/
		}
		//build the tree - this will complete in a single pass if no parents are defined after children
		
		
		$image = PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
		if(isset($icons->default->imageUrl))
		{
			$image = $icons->default->imageUrl;
		}
		if(isset($icons->default->imageUrl))
		{
			$this->icons->default->imageUrl = $icons->default->imageUrl;
		}
		else
		{
			$this->icons->default->imageUrl = PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
		}
		if(isset($icons->edit->imageUrl))
		{
			$this->icons->edit->imageUrl = $icons->edit->imageUrl;
		}
		else
		{
			//$this->icons->edit->imageUrl = PagesAndItemsHelper::getDirIcons().'category/icon-16-category_edit.png';
		}
		if(isset($icons->new->imageUrl))
		{
			$this->icons->new->imageUrl = $icons->new->imageUrl;
		}
		else
		{
			//$this->icons->new->imageUrl = PagesAndItemsHelper::getDirIcons().'category/icon-16-category_new.png';
		}
		
		/*
		else
		{
			if(isset($icons->componentDefault->default->imageUrl))
			{
				$image = $icons->componentDefault->default->imageUrl;
			}
		}
		*/
		
		$rows[0] = array('id' =>-1,'parent_id' =>0,'extension' =>'com_'.$component,'title' => 'no cat_id','alias' => 'no cat_id','published'=>1);
		while(count($rows) > 0)
		{
			foreach($rows as $row_id => $row)
			{
				/*
				$image = PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
				if(isset($icons->default->imageUrl))
				{
					$image = $icons->default->imageUrl;
				}
				else
				{
					if(isset($icons->componentDefault->default->imageUrl))
					{
						$image = $icons->componentDefault->default->imageUrl;
					}
				}
				*/
				$row['icon'] = $image;

				if($row[$parent_column] && $row[$parent_column] != $id)//1)
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

		if($id != 1 && $menutypeTitle != 'Categorie')
		{
			$unique = 0;
		}
		else
		{
			$unique = 1;
		}
		
		$image = PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
		if(isset($icons->default->imageUrl))
		{
			$image = $icons->default->imageUrl;
		}
		else
		{
			if(isset($icons->componentDefault->default->imageUrl))
			{
				$image = $icons->componentDefault->default->imageUrl;
			}
		}
		
		$image = PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
		$image = PagesAndItemsHelper::getDirIcons().'components/content/categories/icon-16-categories.png';
		$imgClass = explode("class:",$image);
		if(count($imgClass) && count($imgClass) == 2)
		{
			//we have an class
			//$html .= '<a ';
			//$html .= 'class="icon '.$imgClass[1].'" ';
			$icon = ',"closeIcon": "'.$imgClass[1].'","openIcon": "'.$imgClass[1].'"';
			//$html .= 'alt="" >&nbsp;';
			//$html .= '<span>';
			//$html .= '</span>';
			//$html .= '</a>';
		}
		else
		{
			//$html .= '<img src="'.$image.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
			$openIconUrl = $image;
			$closeIconUrl = $openIconUrl;
			$icon = ',"closeIconUrl": "'.$closeIconUrl.'","openIconUrl": "'.$openIconUrl.'"';
		}
		
		
		
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = JRequest::getVar('sub_task','');
		
		$json = '';
		//$json .= '[{}';
		$id = '';
		if($menutypeTitle == 'Categories')
		{
			$class = 'class="nonfixed"';
			if($useCheckedOut && ($sub_task == 'edit' || $sub_task == 'new'))
			{
				$link = '#';
				$aClass = 'class="no_underline"';
			}
			else
			{
				$link = 'index.php?option=com_pagesanditems&view=category&categoryId=1&categoryExtension=com_'.$component;
				$aClass = '';
			}
			
			
			
			//$menutypeTitle = addslashes('<div '.$class.' ><a '.$aClass.' href="'.$link.'" >'.JText::_('JCATEGORIES').' [ com_'.$component.' ]</a></div>');
			$menutypeTitle = addslashes('<div '.$class.' ><a '.$aClass.' href="'.$link.'" >'.JText::_('JCATEGORIES').' [ '.$componentText.' ]</a></div>');
			$id = ' ,"id" : "node-id-1" ';
		}
		//$json .= '[{"property": {"name": "'.$menutypeTitle.'","cls": "root-first","uid": "root-first","closeIconUrl": "'.$closeIconUrl.'","openIconUrl": "'.$openIconUrl.'" '.$id.'}}';
		$json .= '[{"property": {"name": "'.$menutypeTitle.'","cls": "root-first","uid": "root-first"'.$icon.$id.'}}';
			$count = 0;
			foreach($tree as $node)
			{
				$count++;
				//go to each top level node and print it and it's children
				$json .= ','.$this->getTreeNodeJson($node, $text_column,$filter_state,$unique,$component,$useCheckedOut ,$sub_task);
			}
		$json .= ']';
		return($json);
	}

	//this can move to includes/tree/tree_category.php
	//recursive function used to print tree structure to html
	function getTreeNodeJson($node, $text_column,$filter_state,$unique,$component,$useCheckedOut ,$sub_task)
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
		//$filter_state = 'all';
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
		if($filter_state == 'none')
		{
			$state = '';
			$class = 'class="nonfixed"';
		}
		else
		{
			$class = 'class="fixed"';
		}
		
		/*
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = JRequest::getVar('sub_task','');
		
		*/
		if($useCheckedOut && ($sub_task == 'edit' || $sub_task == 'new'))
		{
			$link = '#';
			$aClass = 'class="no_underline"';
		}
		else
		{
			$stringsub_task = $useCheckedOut ? '' : '&sub_task=edit';
			$link = 'index.php?option=com_pagesanditems&view=category'.$stringsub_task.'&categoryId='.$node['node']['id'].'&categoryExtension=com_'.$component;
			$aClass = '';
		}
		
		$name = '';
		//add title with the content items?
		//$name .= addslashes($node['node'][$text_column].' <a class="jgrid '.$hasTip.'" '.$title.' '.$onclick.' > '.$state.'</a>');
		$id = '';
		if($unique)
		{
			$id = ' id="mif-tree-node-select-'.$node['node']['id'].'" ';
		}
		
		$name .= addslashes('<div '.$class.' '.$id.'><a '.$aClass.' href="'.$link.'" >'.$node['node'][$text_column].'</a></div><a class="jgrid '.$hasTip.'" '.$title.' '.$onclick.' >'.$state.'</a>');
		
		$expandTo = '';
		if(JRequest::getVar('categoryId',0) && JRequest::getVar('categoryId',0) == $node['node']['id'])
		{
			$expandTo = ',"expandTo": true,"scrollTo":true'; //,"select" : true' ;
		}
		
		
		//$html ='{"property": {"name": "'.$name.'","id": "'.$node['node']['id'].'",'.$closeIcon.' ,'.$openIcon.$hasCheckbox.$expandTo.'}';
		$html ='{"property": {"name": "'.$name.'","id": "node-id-'.$node['node']['id'].'",'.$closeIcon.' ,'.$openIcon.$hasCheckbox.$expandTo.'}';
		if($node['children'])
		{
			$count = 0;
			$html .= ',"children": [';
			//then print it's children nodes
			foreach($node['children'] as $child)
			{
				$count++;
				$html .= $this->getTreeNodeJson($child, $text_column,$filter_state,$unique,$component,$useCheckedOut ,$sub_task);
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

	//this can move to includes/tree/tree_category.php
	//need if in eg. views/item for display category tree
	function getPages()
	{
		$html = '';
		$html .= '<table class="piadminform xadminform tree" width="98%"><tbody><tr><td valign="top">';
			$html .= '<div class="dtree dtree_container">';
				$html .= '<div class="dtree">';
					$html .= '<div id="tree_container">';
					$html .= '</div>';
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</td></tr></tbody></table>';
		$this->getCategories();
	
	
		//$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..')));
		$path = PagesAndItemsHelper::getDirComponentAdmin();
		 //first we must load mootools
		JHTML::_('behavior.framework',true);
		
		JHTML::script('Mif.Tree.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Node.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Hover.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Selection.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Load.js', $path.'/media/js/Core/',false);
		JHTML::script('Mif.Tree.Draw.js', $path.'/media/js/Core/',false);

		JHTML::script('Mif.Tree.KeyNav.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Sort.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Transform.js', $path.'/media/js/More/',false);
			//JHTML::script('Mif.Tree.Drag.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Element.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Checkbox.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.Rename.js', $path.'/media/js/More/',false);
		JHTML::script('Mif.Tree.CookieStorage.js', $path.'/media/js/More/',false);


		JHTML::stylesheet('mif-tree_checkboxes.css', $path.'/media/css/');
		return $html;
		
	}


}
