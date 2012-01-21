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

jimport( 'joomla.application.component.view');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'page'.DS.'view.html.php');

/**
 * HTML View class for the  component

 */


class PagesAndItemsViewArchiveTrash extends PagesAndItemsViewDefault
{
	function display($tpl = null)
	{

		
		
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		$typeName = 'ExtensionManagerHelper';
		$typeName::importExtension(null, 'archivetrash',true,null,true);

		PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MANAGERS').': ['.JText::_('PI_EXTENSION_MANAGER_ARCHIVETRASH_NAME').']</small>');
		//$archiveType = JRequest::getVar('archiveType','all');
		//$this->assignRef('archiveType', $archiveType);
		if ($model = &$this->getModel('archivetrash')) 
		{
			$this->assignRef( 'model',$model);
		}
		
		$tables = $model->getTables();
		$this->assignRef( 'tables',$tables);
		
		$table_id = JRequest::getVar('table_id',0);
		$this->assignRef( 'table_id',$table_id);
		
		
		$doc =& JFactory::getDocument();
		
		$js = 'window.addEvent(\'domready\',function(){'."\n";
		//on reload the page we must reset
		$js .='document.id(\'adminForm\').getElement(\'input[id=boxchecked]\').value=\'0\';';
		$js .='});';
		
		$doc->addScriptDeclaration( $js );
		
		$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'..')));
		JHTML::stylesheet('archivetrash.css', $path.'/media/css/');
		
		$table = $tables[$table_id];
		
		$app = JFactory::getApplication();
		$option = JRequest::getVar('option');
		//dump(JRequest::get());
		$filter_order		= $app->getUserStateFromRequest( "$option.archivetrash.filter_order",		'filter_order',		$table->referenceId,	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( "$option.archivetrash.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$filter_state		= $app->getUserStateFromRequest( "$option.archivetrash.filter_state",		'filter_state',		'',			'word' );
		$lists['filter_order'] = $filter_order;
		$lists['filter_order_Dir'] = $filter_order_Dir;
		$lists['filter_state'] = $filter_state;
		/*
		dump($filter_state);
		$filter_state = JRequest::getVar('filter_state','all');
		$this->assignRef( 'filter_state',$filter_state);
		*/
		$stateTypes = array('published','unpublished', 'archive', 'trash', 'delete');
		if(isset($table->stateTypes))
		{
			$stateTypes = $table->stateTypes;
		}
		$dispatcher = &JDispatcher::getInstance();
		/*
			in the model 'archivetrash'
			we have call importExtension where load all extensions from pi

			over $stateTypes the extension can remove eg: 'trash'
			
			over $table the extension can look if this call to the extension if not return false
		*/
		//$dispatcher->trigger('onGetArchiveTrashStateTypes', array ( &$stateTypes,$table));
		
		if($table->tableName == 'menu')
		{
			//tree only if $table->tableName == 'menu'
			//TODO move to models/managerstate.php?
		/*
			JHTML::script('Mif.Tree.js', $path.'/media/creaven-miftree/Source/Core/',false);
			JHTML::script('Mif.Tree.Node.js', $path.'/media/creaven-miftree/Source/Core/',false);
			JHTML::script('Mif.Tree.Hover.js', $path.'/media/creaven-miftree/Source/Core/',false);
			JHTML::script('Mif.Tree.Selection.js', $path.'/media/creaven-miftree/Source/Core/',false);
			JHTML::script('Mif.Tree.Load.js', $path.'/media/creaven-miftree/Source/Core/',false);
			JHTML::script('Mif.Tree.Draw.js', $path.'/media/creaven-miftree/Source/Core/',false);

			JHTML::script('Mif.Tree.KeyNav.js', $path.'/media/creaven-miftree/Source/More/',false);
			JHTML::script('Mif.Tree.Sort.js', $path.'/media/creaven-miftree/Source/More/',false);
			JHTML::script('Mif.Tree.Transform.js', $path.'/media/creaven-miftree/Source/More/',false);
			JHTML::script('Mif.Tree.Drag.js', $path.'/media/creaven-miftree/Source/More/',false);
			JHTML::script('Mif.Tree.Element.js', $path.'/media/creaven-miftree/Source/More/',false);
			JHTML::script('Mif.Tree.Checkbox.js', $path.'/media/creaven-miftree/Source/More/',false);
			JHTML::script('Mif.Tree.Rename.js', $path.'/media/creaven-miftree/Source/More/',false);
			JHTML::script('Mif.Tree.CookieStorage.js', $path.'/media/creaven-miftree/Source/More/',false);
			JHTML::stylesheet('mif-tree_checkboxes.css', $path.'/media/creaven-miftree/Source/assets/styles/');
		*/
			JHTML::_('behavior.framework'); //first we must load mootools
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
			
			/*JHTML::script('Mif.tree.js', $path.'/media/js/',false);*/
			
			JHTML::stylesheet('mif-tree_checkboxes.css', $path.'/media/css/');
			if ($modelPage = &$this->getModel('page')) 
			{
				/*
				for the tree we need all state
				switch($filter_state)
				{
					case 'all':
						$state = "(published='0' OR published='1' OR published='-2' OR published='2')";
					break;
					
					case 'published':
						$state = "published='1'";
					break;
					
					case 'unpublished':
						$state = "published='0'";
					break;
					
					case 'archive':
						$state = "published='2'";
					break;
					
					case 'trash':
						$state = "published='-2'";
					break;
					
				}
				*/
				$state = "(published='0' OR published='1' OR published='-2' OR published='2')";
				$menutypes = $modelPage->getMenutypes();
				$modelMenutypes = new PagesAndItemsModelMenutypes();
				$menuItemsTypes = $modelMenutypes->getTypeListComponents();
				$loops = count($menutypes);
				$menuitems = array();
				for($m = 0; $m < $loops; $m++)
				{
					$menuitems[] = $model->getPageTree($modelPage->getMenutypeMenuitems($menutypes[$m],$state,'array'),$menutypes[$m],$modelPage,$menuItemsTypes,$modelMenutypes,$filter_state);
				}
				$loops = count($menuitems);
				$js = 'window.addEvent(\'domready\',function(){'."\n";
				$js .= '	var baseTree = document.id(\'tree_container\');'."\n";
				for($mi = 0; $mi < $loops; $mi++)
				{
					$js .= '	var tree_container'.$mi.' = new Element(\'div\', {\'id\': \'tree_container'.$mi.'\', \'class\': \'tree_container\'});'."\n";
					$js .= '	tree_container'.$mi.'.inject(baseTree, \'before\');'."\n";
				}
				$js .= '	baseTree.destroy();'."\n";
				for($mi = 0; $mi < $loops; $mi++)
				{

					$js .= '	tree'.$mi.' = new Mif.Tree({'."\n";
					$js .= '		container: document.id(\'tree_container'.$mi.'\')'."\n";
					$js .= '		,forest: true,'."\n";
					$js .= '		initialize: function(){'."\n";
					$js .= '			this.initCheckbox(\'simple\');'."\n";
					$js .= '			//this.initCheckbox(\'deps\');'."\n";
					$js .= '			new Mif.Tree.KeyNav(this);'."\n";
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

					$js .= '	.addEvent(\'load\', function(){'."\n";
					$js .= '		document.id(\'tree_container'.$mi.'\').getElement(\'span[class*=root-first]\').getElement(\'span[class*=mif-tree-gadjet]\').destroy();'."\n";
					$js .= '		document.id(\'tree_container'.$mi.'\').getElement(\'span[class*=root-first]\').getElement(\'span[class*=mif-tree-checkbox]\').destroy();'."\n";
					$js .= '		document.id(\'tree_container'.$mi.'\').getElement(\'span[class*=root-first]\').getParent().addClass(\'mif-tree-node-root-first\');'."\n";
					$js .= '	})'."\n";
					
					$js .= '	.addEvent(\'toggle\',function(node, state){'."\n";
					$js .= '		var size = tree_container'.$mi.'.getElement(\'div[class*=mif-tree-children-root]\').getSize();'."\n";
					$js .= '		tree_container'.$mi.'.setStyle(\'height\',size.y+\'px\');'."\n";
					$js .= '	});'."\n";
					
					$js .= '	var json'.$mi.' = '.$menuitems[$mi].';'."\n";
					
					// load tree from json.
					$js .= '	tree'.$mi.'.load({json: json'.$mi.' });'."\n";

				}
			
				for($mi = 0; $mi < $loops; $mi++)
				{
					$js .= '	var size = tree_container'.$mi.'.getElement(\'div[class*=mif-tree-children-root]\').getSize();'."\n";
					$js .= '	tree_container'.$mi.'.setStyle(\'height\',size.y+\'px\');'."\n";
					
					$js .= '	var showP'.$mi.' = new Element(\'p\');'."\n";
					
					$js .= '	var show'.$mi.' = new Element(\'a\', {\'id\': \'show'.$mi.'\'});'."\n";
					$js .= '	show'.$mi.'.set(\'text\', \''.JText::_('COM_PAGESANDITEMS_OPEN_ALL').'\');'."\n";
					$js .= '	show'.$mi.'.inject(showP'.$mi.', \'top\');'."\n";
					$js .= '	show'.$mi.'.addEvent(\'click\', function(){'."\n";
					$js .= '		tree'.$mi.'.root.recursive(function(){'."\n";
					$js .= '			this.toggle(null, false);'."\n";
					$js .= '		});'."\n";
					$js .= '	});'."\n";
					
					$js .= '	showP'.$mi.'.appendText(\' | \');'."\n";
					
					$js .= '	var close'.$mi.' = new Element(\'a\', {\'id\': \'close'.$mi.'\'});'."\n";
					$js .= '	close'.$mi.'.set(\'text\', \''.JText::_('COM_PAGESANDITEMS_CLOSE_ALL').'\');'."\n";
					$js .= '	close'.$mi.'.inject(showP'.$mi.');'."\n";
					$js .= '	close'.$mi.'.addEvent(\'click\', function(){'."\n";
					$js .= '		tree'.$mi.'.root.recursive(function(){'."\n";
					$js .= '			this.toggle(false, false);'."\n";
					$js .= '		});'."\n";
					$js .= '	});'."\n";
					
					$js .= '	showP'.$mi.'.inject(tree_container'.$mi.', \'before\');'."\n";
					$js .= '	var showDiv'.$mi.' = new Element(\'div\');'."\n";
					$js .= '	showDiv'.$mi.'.inject(tree_container'.$mi.', \'before\');'."\n";
					$js .= '	tree_container'.$mi.'.inject(showDiv'.$mi.');'."\n";
					//$js .= '	showDiv'.$mi.'.inject(tree_container'.$mi.', \'top\');'."\n";
				}
			
				$js .='});'."\n";
				$doc->addScriptDeclaration( $js );
			}
		}
		elseif(!isset($table->output) || $table->tableName == 'content')
		{
			$query = "SELECT * FROM #__".$table->tableName." ";
			$where = array();
			//if($filter_state != 'all' && $filter_state != '*')
			//{
				switch ($filter_state) 
				{
					case 'published':
						if(isset($table->state->publishedName) && isset($table->state->publishedValue))
						{
							$where[] = $table->state->publishedName.'='.$table->state->publishedValue;
						}
						else
						{
							$where[] = 'state=1';
						}
					break;

					case 'unpublished':
						if(isset($table->state->unpublishedName) && isset($table->state->unpublishedValue))
						{
							$where[] = $table->state->unpublishedName.'='.$table->state->unpublishedValue;
						}
						else
						{
							$where[] = 'state=0';
						}
					break;

					case 'archive':
						if(isset($table->state->archivedName) && isset($table->state->archivedValue))
						{
							$where[] = $table->state->archivedName.'='.$table->state->archivedValue;
						}
						else
						{
							$where[] = 'state=2';
						}
					break;

					case 'trash':
						if(isset($table->state->trashedName) && isset($table->state->trashedValue))
						{
							$where[] = $table->state->trashedName.'='.$table->state->trashedValue;
						}
						else
						{
							$where[] = 'state=-2';
						}
					break;

					case '':
						
						$whereOr = '';
						if(isset($table->state->unpublishedName) && isset($table->state->unpublishedValue))
						{
							//$where[] = $table->unpublishedName.'='.$table->unpublishedValue;
							$whereOr .= '('.$table->state->unpublishedName.'='.$table->state->unpublishedValue;
						}
						else
						{
							//$where[] = 'state=0';
							$whereOr .= '(state=0';
						}

						if(isset($table->state->archivedName) && isset($table->state->archivedValue))
						{
							//$where[] = $table->archiveName.'='.$table->archiveValue;
							$whereOr .= ' OR '.$table->state->archivedName.'='.$table->state->archivedValue;
						}
						else
						{
							//$where[] = 'state=2';
							$whereOr .= ' OR state=2';
						}

						if(isset($table->state->trashedName) && isset($table->state->trashedValue))
						{
							//$where[] = $table->trashName.'='.$table->trashValue;
							$whereOr .= ' OR '.$table->state->trashedName.'='.$table->state->trashedValue.')';
						}
						else
						{
							//$where[] = 'state=-2';
							$whereOr .= ' OR state=-2)';
						}
						$where[] = $whereOr;
					break;
					
					case 'all':

					break;
				}
			//}

			$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

			$option = JRequest::getVar('option');
			
			//$orderby = ' ORDER BY '. $table->referenceId.' ASC';
			$orderby = ' ORDER BY '. $filter_order.' '.$filter_order_Dir;
			$query .= $where.$orderby;
			$db = JFactory::getDBO();
			
			$db->setQuery( "SELECT COUNT(*) FROM #__".$table->tableName." ".$where );
			$total = $db->loadResult();
			jimport('joomla.html.pagination');

			$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
			$limitstart	= $app->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
			$pagination = new JPagination( $total, $limitstart, $limit );
			
			$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
			$rows = $db->loadObjectList();
			
			$this->assignRef( 'rows',$rows);
			$this->assignRef( 'pagination',$pagination);
		}
		else
		{
			//nothing to do 
		}
		$k = 0;
		$buttons = array();
		foreach($tables as $tablerow)
		{
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->text = $tablerow->displayName; //.' ('.count($tablerows).')';
			$link = 'index.php?option=com_pagesanditems';
			$link .= '&task=manager.doExecute'; //display';
			$link .= '&extension=archivetrash'; //the name
			$link .= '&extensionType=manager'; //the type
			$link .= '&extensionFolder='; //the folder
			$link .= '&extension_sub_task=display';
			$link .= '&view=archivetrash'; //
			$link .= '&table_id='.$k; //
			//$link .= '&table_name='.$tablerow->tableName; //
			$link .= '&filter_state='.$filter_state; //
			if(isset($tablerow->image))
			{
				$button->imageName = $tablerow->image;
			}
			$button->id = 'button_table_'.$k;
			if($tablerow->tableName == $table->tableName)
			{
				$button->class = 'button button_table active';
			}
			else
			{
				$button->class = 'button button_table';
			}

			$button->onclick = "document.location.href='".$link."'";
			$buttons[] = $button->makeButton();
			$k++;
		}

		//$lists['buttons'] = $buttons;
		$this->assignRef( 'buttons',$buttons);
		


		/*
		if(!isset($table->output) || $table->tableName == 'content' || $table->tableName == 'menu')
		{
		}
		else
		*/

		
		$types[] = JHTML::_('select.option', '',JText::_('JOPTION_SELECT_PUBLISHED'));
		if(in_array("published", $stateTypes))
			$types[] = JHTML::_('select.option',  'published', JText::_('JPUBLISHED' ));
		if(in_array("unpublished", $stateTypes))
			$types[] = JHTML::_('select.option',  'unpublished', JText::_('JUNPUBLISHED' ));
		if(in_array("archive", $stateTypes))
			$types[] = JHTML::_('select.option',  'archive', JText::_('JARCHIVED' ));
		if(in_array("trash", $stateTypes))
			$types[] = JHTML::_('select.option',  'trash', JText::_('JTRASHED' ));
		$types[] = JHTML::_('select.option', 'all', JText::_('JALL' ));
		
		$lists['types'] = JHTML::_('select.genericlist',   $types, 'filter_state', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_state );
		
		//dump($filter_state);
		switch($filter_state)
		{
			case 'all':
			case '':
					if(in_array("published", $stateTypes))
						JToolBarHelper::publishList('restore');
					if(in_array("unpublished", $stateTypes))
						JToolBarHelper::unpublishList();
					if(in_array("archive", $stateTypes))
						JToolBarHelper::archiveList();
					if(in_array("trash", $stateTypes))
						JToolBarHelper::trash('trash');
			break;
			
			case 'published':
					if(in_array("unpublished", $stateTypes))
						JToolBarHelper::unpublishList();
					if(in_array("archive", $stateTypes))
						JToolBarHelper::archiveList();
					if(in_array("trash", $stateTypes))
						JToolBarHelper::trash('trash');
			break;
				
			case 'unpublished':
					if(in_array("published", $stateTypes))
						JToolBarHelper::publishList('restore');
					if(in_array("archive", $stateTypes))
						JToolBarHelper::archiveList();
					if(in_array("trash", $stateTypes))
						JToolBarHelper::trash('trash');
			break;
			
			case 'archive':
					if(in_array("published", $stateTypes))
						JToolBarHelper::publishList('restore');
					if(in_array("unpublished", $stateTypes))
						JToolBarHelper::unpublishList();
					if(in_array("trash", $stateTypes))
						JToolBarHelper::trash('trash');
			break;
				
			case 'trash':
					if(in_array("published", $stateTypes))
						JToolBarHelper::publishList('restore');
					if(in_array("unpublished", $stateTypes))
						JToolBarHelper::unpublishList();
					if(in_array("archive", $stateTypes))
						JToolBarHelper::archiveList();
			break;
		
		}
		if(in_array("delete", $stateTypes))
		{
			JToolBarHelper::divider();
			JToolBarHelper::deleteList('','delete');
		}
		//TODO add options to delete ?
		JToolBarHelper::divider();
		JToolBarHelper::cancel('managers.cancel');
		
		$config = PagesAndItemsHelper::getConfig();
		$this->assignRef( 'config',$config);
		
		$this->assignRef( 'lists',$lists);
		JHTML::_('behavior.tooltip');
		
		parent::display($tpl);

	}
	
}
