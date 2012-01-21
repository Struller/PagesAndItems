<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;


class PagesList
{
	function getChilds($menuItem = null,$currentMenuitems = array(),$menuItemsType = null)
	{
		//$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
		//$this->menuItem is same getItem()
		if(!$menuItem)
		{
			//return '';
			//$this->menuItem = $menuItem;
		}
		/*
		if($currentMenuitems)
		{
			$this->currentMenuitems = $currentMenuitems;
		}
		*/
		$html = '';
		$html .= '<thead class="piheader">';
			$html .= '<tr>';
				$html .= '<th>'; // class="piheader">';//style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
				if($menuItem && $menuItemsType)
				{
					$menuItemsType = $menuItemsType;
					if(isset($menuItemsType->icons->default->imageUrl))
					{
						$image = $menuItemsType->icons->default->imageUrl;
					}
					if(!$image)
					{
						$image = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
					}
				}
				else
				{
					$image = PagesAndItemsHelper::getDirIcons().'icon-16-menu.png';
				}

				$menutype = JRequest::getVar('menutype', null);
				$pageId = JRequest::getVar('pageId', null);
				$title = JText::_('COM_PAGESANDITEMS_UNDERLYING_PAGES');
				if($menutype) // && !$pageId)
				{
					$title .= ' <small>['.PagesAndItemsHelper::getMenutypeTitle($menutype).']</small>';
				}
				if($pageId && $menuItem)
				{
					$title .= ' <small>['.$menuItem->title.']</small>';
				}
				$html .= PagesAndItemsHelper::getThImageTitle($image,$title);
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody id="underlayingPages">';
			$html .= '<tr>';
				$html .= '<td>';
					$html .=  $this->getUnderlyingPages($currentMenuitems);
				$html .= '</td>';
				$html .= '</tr>';
		$html .= '</tbody>';
		return $html;
	}
	
	
	function getUnderlyingPages($rows)
	{
		$doc =& JFactory::getDocument();
		$html = '';
		$html .= '<div class="paddingList">';
		$imagePath = PagesAndItemsHelper::getDirIcons();
		$layout = JRequest::getCmd('layout', '');
		if($layout && $layout != '')
		{
			$layout = '&layout='.$layout.'';
		}

		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'extensions'.DS.'htmlhelper.php');
		$htmlelements = ExtensionHtmlHelper::importExtension('page_childs',null,true,null,true);

		$dispatcher = &JDispatcher::getInstance();
		$htmlelement->html = '';
		$option = JRequest::getCmd('option', '');
		
		$canDoMenus = PagesAndItemsHelper::canDoMenus();
		
		JHTML::script('submit_actions_pages.js', PagesAndItemsHelper::getDirJS().'/',false);

		//loop through items and echo data to hidden fields
		$counter = 0;
		$page_ids = array();
		$outputRows = '';
		//we can have more rows so we must count only $row->parent
		$countRows = 0;
		if(count($rows) && count($rows) >= 1)
		foreach($rows as $row)
		{
			if($row->parent == JRequest::getVar('pageId', 1))
			{
				$countRows++;
			}
		}
		//$config = PagesAndItemsHelper::getConfigAsRegistry();
		//$useCheckedOut = $config->get('useCheckedOut',0);
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = JRequest::getCmd('sub_task', '');
		if($countRows)
		{

			
			//headers
			require_once(realpath(dirname(__FILE__).DS.'..'.DS.'..').DS.'includes'.DS.'html'.DS.'tableitems.php');
			$countColumns = 4;
			$config = array('countRows'=>$countRows,'countColumns'=>$countColumns,'itemName'=>'page','itemTask'=>JRequest::getVar('view',''),'output'=>true);
			$table = new htmlTableItems($config);
			$table->table();
			$columns = array();
			$columns[] = array('type'=>'title','config'=> array('attributes'=>array('colSpan'=>3)));
			$columns[] = array('type' => 'state');
			if($useCheckedOut && $sub_task != 'edit')
			{
				$columns[] = array('type' => 'orderingIcon');
			}
			else
			{
				//$columns[] = array('type' => 'orderingIcon','config'=> array('loadJs'=>0)); //only thre icon
				$columns[] = array('type' => 'ordering');
			}
			$table->header($columns);
			$table->tbody();

			//loop through items and echo data to hidden fields
			foreach($rows as $row)
			{
				
				/*
				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6')){
				$root_id = 0;
				}else{
					$root_id = 1;
				}
				$this->pageId = JRequest::getVar('pageId', $root_id);
				*/
				if($row->parent == JRequest::getVar('pageId', 1))
				{
					$table->trColored();
					$page_ids[] = $row->id;
					$areThereUnderlyingPages = true;
					$counter = $counter + 1;
					if($row->type == 'separator' )
					{
						$name = JText::_('COM_PAGESANDITEMS_MENU_ITEM_TYPE').': '.JText::_('SEPARATOR');
						if($row->name != '')
						{
							$name .= ' ('.$row->name.')';
						}
						else
						{
							$name .= ' (empty)';
						}
						$menuName = $name;
					}
					else
					{
						$menuName = $row->name;
					}
					$image = $row->dtree_image;
					$page_title = htmlspecialchars($menuName);
					if(isset($row->dtree_menuName))
					{
						$page_title = stripslashes($row->dtree_menuName);
					}
					$no_access = '';
					if(isset($row->dtree_no_access) && $row->dtree_no_access)
					{
						$no_access = addslashes(JText::_('COM_PAGESANDITEMS_COMPONENT_NOT_INSTALLED_NO_ACCESS')); //Component not_installed_no_access)
					}
	
					$outputRows .= '<input name="reorder_page_id_'.$counter.'" id="reorder_page_id_'.$counter.'" type="hidden" value="'.$row->id.'" />';
					//if (PagesAndItemsHelper::getIsJoomlaVersion('>=','1.6')){
						$outputRows .= '<input name="reorder_lft_'.$counter.'" id="reorder_lft_'.$counter.'" type="hidden" value="'.$row->lft.'" />';
						$outputRows .= '<input name="reorder_rgt_'.$counter.'" id="reorder_rgt_'.$counter.'" type="hidden" value="'.$row->rgt.'" />';
					//}

					//column 1
					$column1 = '';
					if($no_access)
					{
						$column1 .= '<input disabled="disabled" type="checkbox" onclick="isCheckedPage(this.checked);" name="pageCid[]" value="'.$row->id.'" />';
					}
					else
					{
						$column1 .= '<input type="checkbox" name="pageCid[]" id="pageCid_'.$row->id.'" onclick="isCheckedPage(this.checked);" value="'.$row->id.'" />';
					}
					$table->td($column1,array('attributes'=>array('class'=>'items_row_checkbox')));
					
					///column 2+3
					if($no_access)
					{
						
							$image = $row->dtree_imageNoAccess;
							$table->td('<img src="'.$image.'" alt="'.$no_access.'" />');

							$column3 = '<span class="editlinktip hasTip" title="'.$no_access.'">';
							$column3 .= $page_title;
							$column3 .= '</span>';
							$table->td($column3);

					}
					else
					{
						$imgClass = explode("class:",$image);
						if(count($imgClass) && count($imgClass) == 2)
						{
							//we have an class
							$column2 = '<a ';
							$column2 .= 'class="no_underline icon '.$imgClass[1].'" ';
							$column2 .= 'alt="" >&nbsp;';
							$column2 .= '</a>';
						}
						else
						{
							$column2 = '<img src="'.$image.'" alt="'.$no_access.'" />';
						}
						$table->td($column2,array('attributes'=>array('class'=>'items_row_image')));
						
						if($useCheckedOut && $sub_task == 'edit')
						{
							$column3 = '<a class="no_underline">';
								//$column3 .= '<span class="editlinktip hasTip" title="'.$no_access.'">';
								$column3 .= $page_title;
								//$column3 .= '</span>';
							$column3 .= '</a>';
						}
						else
						{
						$stringsub_task = $useCheckedOut ? '': '&sub_task=edit';
						$column3 = '<a href="index.php?option=com_pagesanditems&view=page'.$stringsub_task.'&pageId='.$row->id.'&pageType='.$row->pageType.'&menutype='.$row->menutype.'" alt="'.$no_access.'">';
							$column3 .= $page_title;
						$column3 .= '</a>';
						}
						$table->td($column3);
						
					}

					//column state
					$configTd4 = array('rowPublished'=>$row->published, 'rowId'=>$row->id,'canDo'=>$canDoMenus->get('core.edit.state'));
					$table->tdState('',$configTd4);

					$configTd6 = array('countRows'=>$countRows, 'currentRow'=>$counter);
					$table->tdOrdering('',$configTd6);

				}
			}
			$outputRows .= $table->getOutput();
		}
		else
		{
			$outputRows .= JText::_('COM_PAGESANDITEMS_THISPAGENOUNDERLYINGPAGES');

		}
		/*
		$showSlider = true;
		if( !$counter || $counter == 1)
		{
			$showSlider = false;
		}
		*/
		$configShowSlider = PagesAndItemsHelper::getConfigAsRegistry()->get('showSlider','-1');
		$showSlider = (int)$configShowSlider ? ((int)$configShowSlider == -1 ? ($counter ? 1 : 0) : ($counter && $counter > (int)$configShowSlider) ? 1 : 0 ) : 0;
		//$showSlider = ($counter && $counter > 1) ? 1 : 0; //TODO option in config showSlider -1 = allways, 0 = none , > 0 
		
		
		if($canDoMenus->get('core.create') && (!$useCheckedOut || !$sub_task == 'edit'))
		{
			//ok user can create
			$htmlOptions->menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
			$htmlOptions->current_menutype = PagesAndItemsHelper::getCurrentMenutype();
			
			$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'page_childs', $htmlOptions));
			$html .= $htmlelement->html;

			if(!$showSlider)
			{
				$html .= '<div class="pi_wrapper">';
					$html .= '<div class="line_top paddingList">';
					$html .= '</div>';
				$html .= '</div>';
			}
		}

		if($showSlider)
		{
			$html .= JHtml::_('sliders.start','pages_sliders', array('useCookie'=>1));
				$html .=  JHtml::_('sliders.panel',JText::_('COM_PAGESANDITEMS_PAGES'), 'pages-slider');
					$html .= '<fieldset class="panelform">';
		}

		if($counter)
		{
			$html .= '<div id="target_pages_actions" class="items_target_actions">';
				$html .= '<div class="items_target_actions_buttons">'; //style="float:right;">';
					$htmlelements = ExtensionHtmlHelper::importExtension('page_actions',null,true,null,true);
					$htmlelement->html = '';
					$htmlOptions = null;
					$htmlOptions->canDo = $canDoMenus;
					$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'page_actions', $htmlOptions));
					$html .= $htmlelement->html;
				$html .= '</div>';
			$html .= '</div>';

		}
		$html .= '<div id="target_pages" class="items_target">';
			//here come the list
			if(!$showSlider){$html .= '<fieldset class="noborder">';}
			$html .= $outputRows;
			if(!$showSlider){$html .= '</fieldset>';}
			//$html .= $outputRows;
		$html .= '</div>';
		if($showSlider)
		{
			$html .= '</fieldset>';
			$html .= JHtml::_('sliders.end');
		}
		$html .= '<script language="JavaScript"  type="text/javascript">';
		$html .= "<!--\n";
		
		//need for script
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGES_DELETE", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE1", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE2", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGES_TRASH", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH2", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH3", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_PAGES_ARCHIVE", array("script"=>true));
		
		$config = PagesAndItemsHelper::getConfig();
		$html .= ($config['page_trash_cat']) ? "var page_trash_cat = 1;".";\n" : "var page_trash_cat = 0;".";\n";
		$html .= ($config['page_trash_items']) ? "var page_trash_items = 1;".";\n" : "var page_trash_items = 0;".";\n";

		$html .= ($config['page_delete_cat']) ? "var page_delete_cat = 1;".";\n" : "var page_delete_cat = 0;".";\n";
		$html .= ($config['page_delete_items']) ? "var page_delete_items = 1;".";\n" : "var page_delete_items = 0;".";\n";

		$html .= "var page_ids = new Array(";
		$first = 1;
		foreach($page_ids as $page_ids_page){
			if(!$first){
				$html .= ",";
			}
			$html .= "'".$page_ids_page."'";
			$first = 0;
		}
		$html .= ");\n";
		$html .= "function isCheckedPage(isitchecked) {"."\n";
		$html .= "	if (isitchecked == true) {"."\n";
		$html .= "		document.adminForm.boxcheckedPage.value++;"."\n";
		$html .= "	} else {"."\n";
		$html .= "		document.adminForm.boxcheckedPage.value--;"."\n";
		$html .= "	}"."\n";
		$html .= "}\n";
		$html .= "-->\n";
		$html .= "</script>\n";
		$html .= '<input type="hidden" name="boxcheckedPage" id="boxcheckedPage" value="0" />';

		$html .= '</div>';
		return $html;
	}
}