<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;


class ItemsList
{
	
	//TODO move to itemsList?
	function addMiniToolbar($editToolbarButtons = true, $newToolbarButtons = true)
	{
		$imagePath = PagesAndItemsHelper::getDirIcons();
		$html = '';
		$html .= '<div class="items_target_actions_buttons" >';

		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'extensions'.DS.'htmlhelper.php');
		//load single
		//$buttons = ExtensionHelper::importExtension('button','page_items', 'publish',true,null,true);
		//load multiple
		//$buttons = ExtensionHelper::importExtension('button','page_items', $editToolbarButtons,true,null,true);

		if(is_array($editToolbarButtons))
		{
			//we load only in array
			$htmlelements = ExtensionHtmlHelper::importExtension('page_items', $editToolbarButtons,true,null,true);
		}
		else
		{
			//we load all
			$htmlelements = ExtensionHtmlHelper::importExtension('page_items',null,true,null,true);
		}
		$dispatcher = &JDispatcher::getInstance();
		$htmlelement->html = '';
		$canDoContent = PagesAndItemsHelper::canDoContent();
		$htmlOptions = null;
		$htmlOptions->canDo = $canDoContent;
		$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'page_items', $htmlOptions));
		$html .= $htmlelement->html;
		$html .= '</div>';
		return $html;
	}
	
	
	//TODO make output like includes/category/categorielist
	function renderItems($html, $rows, $toolbar, $ordering=0,$sliderText = 'COM_PAGESANDITEMS_ITEMS')
	{
		$categoryId = JRequest::getVar('categoryId');
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = JRequest::getVar('sub_task','');
		//get helper
		//include com_content helper ?
		//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'content.php');
		JHTML::script('submit_actions_items.js', PagesAndItemsHelper::getDirJS().'/',false);

		$imagePath = PagesAndItemsHelper::getDirIcons();
		//$html .= '<div id="original_items" style="display: none;">';
		$menutype = JRequest::getVar('menutype','');
		$pageId = JRequest::getVar('pageId','');
		$pageType = JRequest::getVar('pageType','');
		
		$item_ids = array();
		$outputRows = '';
		$counter = 0;
		//
		if(count($rows))
		{
			$path = realpath(dirname(__FILE__).DS.'..');
			require_once($path.DS.'extensions'.DS.'itemtypehelper.php');
			ExtensionItemtypeHelper::importExtension(null, null,true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			
			
			//headers
			require_once(realpath(dirname(__FILE__).DS.'..'.DS.'..').DS.'includes'.DS.'html'.DS.'tableitems.php');
			$countColumns = 5;
			$config = array('countRows'=>count($rows),'countColumns'=>$countColumns,'itemName'=>'item','itemTask'=>JRequest::getVar('view',''),'output'=>true);
			$table = new htmlTableItems($config);
			$table->table();
			$columns = array();
			$columns[] = array('type'=>'title','config'=> array('attributes'=>array('colSpan'=>3)));
			$columns[] = array('type' => 'state');
			$columns[] = array('type' => 'type');
			if($ordering) {
				//$table->th(JText::_('COM_PAGESANDITEMS_ORDERING')); 
				if($useCheckedOut && $sub_task != 'edit')
				{
					$columns[] = array('type' => 'orderingIcon');
				}
				else
				{
					//$columns[] = array('type' => 'orderingIcon','config'=> array('loadJs'=>0)); //only thre icon
					$columns[] = array('type' => 'ordering');
				}
			}
			$table->header($columns);
			$table->tbody();

			//loop through items and echo data to hidden fields
			foreach($rows as $row)
			{
				$table->trColored();
				$item_ids[] = $row->id;
				$content_creat_with = 'pi';
				$image = $imagePath.'base/icon-16-pi_black.png';
				$areThereItems = true;
				$counter = $counter + 1;
				$title = $row->title;
				$title = str_replace('"','&quot;',$title);
				$item_type = $row->itemtype;
				if($item_type=='content'){
					$item_type = 'text';
				}
				
				$pi_config = PagesAndItemsHelper::getConfig();
				if($pi_config['truncate_item_title'])
				{
					$title = PagesAndItemsHelper::truncate_string($title, $pi_config['truncate_item_title']);
				}

				if($item_type == '' )
				{
					$item_type = 'text';
					$content_creat_with = 'joomla';
					$image = $imagePath.'base/icon-16-joomla_black.png';
				}
				else
				{
					if(strpos($item_type, 'ustom_'))
					{
						$itemType = 'custom';
					}
					else
					{
						$itemType = $item_type;
					}
					$imageDir = PagesAndItemsHelper::getDirIcons().'ui_itemtypes/';
					
					$imageNew = $imageDir.'ui-'.$itemType.'.png';
					//for subdomains we must have
					$jpathRoot = str_ireplace(JURI::root(true),'',str_replace(DS,'/',JPATH_ROOT));
					
					//if(file_exists(JPATH_ROOT.$imageNew))
					if(file_exists($jpathRoot.$imageNew))
					{
						
						/*
						$html .= '<div class="width-100">';
						$html .= '<img src="'.$image.'" />';
						$html .= '</div>';
						*/
					}
					else
					{
						$imageNew = null;
						//look in field params
						//$image = $imageDir.'ui-blank.png';
						$params = null;
						$dispatcher->trigger('onGetParams',array(&$params, $itemType));
						if($params)
						if($uiImage = $params->get('uiImage'))
						{
							$folder = '';
							
							$dispatcher->trigger('onGetFolder',array(&$folder, $itemType));
							//$this->onGetFolder(&$folder,$field->plugin);
							
							if(file_exists($folder.DS.$uiImage))
							{
								$folder = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath($folder)));
								$imageNew = str_replace(DS,'/',$folder.DS.$uiImage);
							}
						}
						/*
						$html .= '<div class="width-100">';
						$html .= '<img src="'.$image.'" />';
						$html .= '</div>';
						*/
					}
					if($imageNew)
					{
						$image = $imageNew;
					}
				}

				$no_access = '';
	
				$user		= JFactory::getUser();
				$userId		= $user->get('id');
				$canDoContent = PagesAndItemsHelper::canDoContent(0, $row->id);
				$canEdit	= $canDoContent->get('core.edit');
				$canEditOwn	= $canDoContent->get('core.edit.own') && $row->created_by == $userId;
				if((!$canEdit && !$canEditOwn)) //!$canDoContent->get('core.edit'))
				{
					$no_access = JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THIS_ITEM');
				}
				//check if itemtype is installed
				//comment for check is installed we need an simple query from #__extensions
				// als here we can get publish status
	
				//the translate_item_type can not return the installed ore publish status
				$item_typename = PagesAndItemsHelper::translate_item_type($item_type);
				//if(!$this->checkItemTypeInstall($item_type))
				if(!PagesAndItemsHelper::checkItemTypeInstall($item_type))
				{
					$no_access = JText::_('COM_PAGESANDITEMS_ITEMTYPENOTINSTALLED2');
				}
	
				if(!in_array($item_type,PagesAndItemsHelper::getItemtypes()))
				{
					$no_access = JText::_('COM_PAGESANDITEMS_ITEMTYPENOTPUBLISHED').' '.$item_type;
				}
	
				if($no_access != '')
				{
					$image = $imagePath.'base/icon-16-no_access_slash_small.png';
				}

				$outputRows .= '<input name="reorder_item_id_'.$counter.'" id="reorder_item_id_'.$counter.'" type="hidden" value="'.$row->id.'" />';

			
				$column1 = '';
				if($no_access)
				{
					//column 1
					$column1 .= '<input disabled="disabled" type="checkbox" onclick="isCheckedItem(this.checked);" name="itemCid[]" value="'.$row->id.'" />';
				}
				else
				{
					$column1 .= '<input type="checkbox" name="itemCid[]" id="itemCid_'.$row->id.'" onclick="isCheckedItem(this.checked);" value="'.$row->id.'" />';
				}
				$table->td($column1,array('attributes'=>array('class'=>'items_row_checkbox')));

				//column 2 + 3

				if($no_access)
				{
					//column 2
					$table->td('<img src="'.$image.'" alt="'.$no_access.'" />',array('attributes'=>array('class'=>'items_row_image')));
					
					//column 3
					$column3 = '<span class="editlinktip hasTip" title="'.$no_access.'">';
					$column3 .= $title;
					$column3 .= '</span>';
					$table->td($column3);
				}
				else
				{
					//column 2 
					$table->td('<img src="'.$image.'" alt="'.$content_creat_with.'" />',array('attributes'=>array('class'=>'items_row_image')));
					
					
					
					if($useCheckedOut && $sub_task == 'edit')
					{
						$column3 = '<a class="no_underline">';
							//$column3 .= '<span class="editlinktip hasTip" title="'.$no_access.'">';
							$column3 .= $title;
							//$column3 .= '</span>';
						$column3 .= '</a>';
					}
					else
					{
					
					//column 3
					$column3 = '';
					$column3 .= '<a href="index.php?option=com_pagesanditems&view=item&sub_task=edit&itemId='.$row->id.'&item_type='.$item_type.($categoryId ? '&categoryId='.$row->catid : '').($pageId ? '&pageId='.$pageId.'&menutype='.$menutype.'&pageType='.$pageType : '').'">';
						$column3 .= $title;
					$column3 .= '</a>';
					}
					$table->td($column3);
				}

				//column 4 state
				$configTd4 = array('rowPublished'=>$row->state, 'rowId'=>$row->id,'canDo'=>$canDoContent->get('core.edit.state'));
				$table->tdState('',$configTd4);
				
				//column 5 type
				$table->td($item_typename); //,array('attributes'=>array('ch'=>'right')));
				
				//column 6 ordering
				if($ordering) {
					$configTd6 = array('countRows'=>count($rows), 'currentRow'=>$counter);
					$table->tdOrdering('',$configTd6);
				}
			}

			$outputRows .= $table->getOutput();
		}
		else
		{
			$outputRows .= JText::_('COM_PAGESANDITEMS_NOITEMSONTHISPAGE');
		}

		$categoryId = JRequest::getVar('categoryId');
		
		//$showSlider = ($counter && $counter > 1) ? 1 : 0; //TODO option in config showSlider -1 = allways, 0 = none , > 0 
		$configShowSlider = PagesAndItemsHelper::getConfigAsRegistry()->get('showSlider','-1');
		$showSlider = (int)$configShowSlider ? ((int)$configShowSlider == -1 ? ($counter ? 1 : 0) : ($counter && $counter > (int)$configShowSlider) ? 1 : 0 ) : 0;
		if($showSlider) //$counter > 1)
		{
			$html .= JHtml::_('sliders.start','items_sliders', array('useCookie'=>1));
				$html .=  JHtml::_('sliders.panel',JText::_($sliderText), 'items-slider');
					$html .= '<fieldset class="panelform">';
		}
		$html .= '<div id="target_items_actions" class="items_target_actions">';
		//if($toolbar)
		$html .= $toolbar ? $toolbar : '';

		$html .= '</div>';

		$html .= '<div id="target_items" class="items_target">';
			//here the output for list
			if(!$showSlider) //$counter == 1)
			{
				/*$html .= '<div id="items_sliders" class="pane-sliders">';
					$html .= '<div class="panel">';
						$html .= '<h3 id="items-slider" class="title pane-toggler-down"><a href="#"><span>'.JText::_('COM_PAGESANDITEMS_ITEM').'</span></a></h3>';
						$html .= '<div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: auto;">';
				*/
							$html .= '<fieldset class="noborder">';
								$html .= $outputRows;
							$html .= '</fieldset>';
				/*		$html .= '<div>';
					$html .= '<div>';
				$html .= '<div>';
				*/
			}
			else
			{
				$html .= $outputRows;
			}
		$html .= '</div>';
		if($showSlider) //$counter > 1)
		{
			$html .= '</fieldset>';
			$html .= JHtml::_('sliders.end');
		}

		//need for script
		JText::_("COM_PAGESANDITEMS_CONFIRM_ITEMS_ARCHIVE", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_ITEMS_TRASH", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_ITEMS_DELETE", array("script"=>true));

		$html .= '<script language="JavaScript"  type="text/javascript">';

		$html .= "<!--\n";

		$html .= "var item_ids = new Array(";
		$first = 1;
		foreach($item_ids as $item_ids_item){
			if(!$first){
				$html .= ",";
			}
			$html .= "'".$item_ids_item."'";
			$first = 0;
		}
		$html .= ");\n";

		$html .= "function isCheckedItem(isitchecked) {
	if (isitchecked == true) {

		document.adminForm.boxcheckedItem.value++;
	} else {
		document.adminForm.boxcheckedItem.value--;
	}
}\n";
		$html .= "-->\n";
		$html .= "</script>\n";
		$html .= '<input type="hidden" name="boxcheckedItem" id="boxcheckedItem" value="0" />';
		$html .= '</div>';
		return $html;
	}




	function getContentItems($editToolbarButtons = true, $newToolbarButtons = true,$showItemtype_select=true,$menuItem = false,$sliderText= 'COM_PAGESANDITEMS_ITEMS')
	{
		$db = JFactory::getDBO();
		// ms: here can the error Call to a member function getItem() on a non-object (e-mail from cs)
		// but if the $this->modelMenu not exist we must get an error before see line 1438
		// function getContentItems is call from the extensions/pagetype
		//
		if(!$menuItem)
		{
			//return '';
		}

		//$menuItem = $menuItem ?  $menuItem : $this->menuItem; //$this->modelMenu->getItem(PagesAndItemsHelper::getPageId());
		$where = array();
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
		$canCreateItem = $useCheckedOut ? ($sub_task == 'edit' ? 0 : 1) : 1;
		if($showItemtype_select && $canDoContent->get('core.create') && $canCreateItem) //(!$useCheckedOut || $sub_task != 'edit'))
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
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'itemslist.php');
		//$ItemsList = new ItemsList();
		//$toolbar .= $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
		if(count($rows))
		{
			$toolbar = $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
		}
		else
		{
			$toolbar = '';
		}

		return $this->renderItems($html,$rows, $toolbar,$ordering,$sliderText);
		//return $this->renderItems($html,$rows, $toolbar,$ordering);
	}

	/*
	 * @params mixed array||boolean $editToolbarButtons eg. array('new',delete'..) ore true for all
	 * @param mixed array||string $newToolbarButtons eg. array('new',delete'..) ore true for all
	*/
	function getContentItem($editToolbarButtons = true, $newToolbarButtons = true,$menuItem = false,$sliderText= 'COM_PAGESANDITEMS_ITEM')
	{
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'itemslist.php');
		//$ItemsList = new ItemsList();
		if(!$menuItem)
		{
			//return ;
		}
		//$menuItem = $this->menuItem; //$this->modelMenu->getItem(PagesAndItemsHelper::getPageId());
		
		
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
			$sub_task = JRequest::getVar('sub_task');
			$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
			//if(!$menu_item_id && $this->canDo->com_content->get('core.create'))
			$canCreateItem = $useCheckedOut ? ($sub_task == 'edit' ? 0 : 1) : 1;
			
			if(!$menu_item_id && $canDoContent->get('core.create') && $canCreateItem)
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
				$toolbar = $this->addMiniToolbar($editToolbarButtons,$newToolbarButtons);
			}
			return $this->renderItems($html,$rows,$toolbar,0,'COM_PAGESANDITEMS_ITEM');
			//$this->renderItems($html,$rows,$toolbar,0,'COM_PAGESANDITEMS_ITEM');
	}


}