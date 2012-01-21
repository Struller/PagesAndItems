<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('JPATH_BASE') or die;

class PagesAndItemsTreePage
{
	var $pageMenuItem = null;
	var $currentMenuitems = array();
	
	
	
	function getTree($storeVars=false,$menutype='',$page_id = 0)
	{
		$this->loadBehavior();
		$html = '';
		//$html .= '<table class="adminform tree" width="98%">';
		//	$html .= '<tbody>';
		//		$html .= '<tr>';
		//			$html .= '<td valign="top">';
						$html .= $this->getPages($storeVars);
		//			$html .= '</td>';
		//		$html .= '</tr>';
		//	$html .= '</tbody>';
		//$html .= '</table>';
		
		
		return $html;
	}
	
	function loadBehavior()
	{
		$path = PagesAndItemsHelper::getDirComponentAdmin();
		JHTML::script('dtree.js', $path.'/javascript/',false);
		JHTML::stylesheet('dtree.css',$path.'/css/');
	}
	
	function getPages($storeVars)
	{
		/*
		ADD ms: 23.03.2011
		get all featured articles
		*/
		$pageId = Jrequest::getVar('pageId',0);
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id FROM #__content  WHERE featured='1' " );
		$featureds = $db->loadResultArray();
		//ADD END ms: 23.03.2011

		$doc =& JFactory::getDocument();
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');//need this when saving moving page
		$model = new PagesAndItemsModelMenutypes();
		//see how many loops we need
		$menutypes = PagesAndItemsHelper::getMenutypes();
		$loops = count($menutypes);
		$extension = 'com_menus';
		$lang = &JFactory::getLanguage();
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$html = '';

		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'extensions'.DS.'htmlhelper.php');
		$htmlelements = ExtensionHtmlHelper::importExtension('page_tree',null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		$htmlelement->html = '';
		$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'page_tree'));
		$html .= '<table class="piadminform xadminform tree" width="98%"><tbody><tr><td valign="top">';
		if($htmlelement->html != '')
		{
			$html .= '<div class="dtree dtree_container">';

					$html .= $htmlelement->html;
			$html .= '</div>';
		}
		$menuItemsTypes = PagesAndItemsHelper::getMenuItemsTypes();
		//$config = PagesAndItemsHelper::getConfigAsRegistry();
		//$useCheckedOut = $config->get('useCheckedOut',0);
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = JRequest::getVar('sub_task','');
		for($m = 0; $m < $loops; $m++)
		{
			$menuitems = PagesAndItemsHelper::getMenutypeMenuitems($menutypes[$m]);
			$script = '';
			$html .= '<div class="dtree dtree_container">';
			$html .= '<p><a href="javascript: d'.$m.'.openAll();">'.JText::_('COM_PAGESANDITEMS_OPEN_ALL').'</a> | <a href="javascript: d'.$m.'.closeAll();">'.JText::_('COM_PAGESANDITEMS_CLOSE_ALL').'</a></p>';
			$script .= "d$m = new dTree('d$m');\n";
			/*
			COMMENT:
				here we set the icons for dTree not in dTree.js
				so we can change the dir aeasy
			*/
			$script .= PagesAndItemsHelper::getdTreeIcons("d".$m);
			/*
			$script .= "d$m.config = {target: null,folderLinks	: false,
		useSelection	: true,
		useCookies	: false,
		useLines	: true,
		useIcons	: true,
		useStatusText	: false,
		closeSameLevel	: false,
		inOrder		: false};";
		*/
			//$html .= "};\n";
			
			/*
				COMMENT
				in Joomla 1.6
				we have one parent_id=0 in table #__menus
				but more parent_id=1 in table #__menus

				parent_id=1 in table #__menus = menutype:'', title:Menu_Item_Root, alias:root

			*/
			if (PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
			{
				$script .= "d$m.add(0,-1,'";
			}
			else
			{
				$script .= "d$m.add(1,-1,'";
			}
			$script .= PagesAndItemsHelper::getMenutypeTitle($menutypes[$m]);
			
			if($useCheckedOut && $sub_task == 'edit')
			{
				$script .= "','#";
			}
			else
			{
				$script .= "','index.php?option=com_pagesanditems&view=page&layout=root&menutype=";
				$script .= strtolower($menutypes[$m]);
			}
			
			$script .= "','','','','',true);\n";

			//$imagePath = PagesAndItemsHelper::getDirIcons();
			//make javascript-array from menu-items
			$counter = 0;
			//loop menuitems
			foreach($menuitems as $row)
			{
				$counter++;
				$image = '';
				$imageNoAccess = '';
				$row->menu_item_article_pi = false;
				$row->menu_item_article_no_access = false;
				$itemtype_no_access = array();
				$not_installed_no_access = false;
				$pageType = null;
				if($row->type == 'components'){
					//backward compatibility for site which were migrated from Joomla 1.5
					$row->type = 'component';
				}
				if($row->type != 'component')
				{
					$pageType = $row->type;
				}
				else
				{
					$pageType =$model->buildPageType($row->link);
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
				/*
				ADD ms: 23.03.2011
				only if $pageType == content_article
				for featured article add an own icon
				*/
				if($pageType == 'content_article')
				{
					if($contentId = $model->getId($row->link))
					{
						if(in_array($contentId,$featureds))
						{
							/*
							ok we will look at an extra icon
							only for pageTree and pageChilds
							*/
							if(isset($menuItemsType->icons->featured_default->imageUrl))
							{
								$image = $menuItemsType->icons->featured_default->imageUrl;
							}
						}
					}
				}

				//ADD END ms: 23.03.2011

				if(isset($menuItemsType->icons->no_access->imageUrl))
				{
					$imageNoAccess = $menuItemsType->icons->no_access->imageUrl;
				}
				else
				{
					if(isset($menuItemsType->icons->componentDefault->no_access->imageUrl))
					{
						$imageNoAccess = $menuItemsType->icons->componentDefault->no_access->imageUrl;
					}
				}
				if($not_installed_no_access)
				{
					$image = $imageNoAccess;
					$itemtype_no_access[] = addslashes(JText::_('COM_PAGESANDITEMS_COMPONENT_NOT_INSTALLED_NO_ACCESS'));
					$row->dtree_no_access = 1;
				}
				$row->dtree_image = $image;
				$row->dtree_imageNoAccess = $imageNoAccess;
				$row->pageType = $pageType;

				//here we check for an empty separator
				if($row->type == 'separator' )
				{
					$name = '';
					if($row->name != '')
					{
						//$name .= ' ('.$row->name.')';
						$name .= $row->name;
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
				$row->dtree_menuName = $menuName;
				/*
				in Joomla 1.6 we have parent_id not parent
				but this is fixet in models/page.php
				*/
				$menuName = addslashes($menuName);


				$title = '';
				$script .= "d$m.add(".$row->id;//." id
				$script .= ",".$row->parent;//." , pid
				$script .= ",'".($menuName)."'"; //, name

				/*
				TODO
				user_access
				if(! ....)
				{
					$itemtype_no_access[] = ...
					$image = ...
				}
				*/

				if($itemtype_no_access != '' && !is_array($itemtype_no_access))
				{
					$title = $itemtype_no_access;
					$script .= ",'";
				}
				elseif($itemtype_no_access != '' && is_array($itemtype_no_access) && count($itemtype_no_access))
				{
					$title = implode(', ',$itemtype_no_access);
					$script .= ",'";
				}
				elseif($useCheckedOut && $sub_task == 'edit')
				{
					$title = '';
					$script .= ",'#";
				}
				else
				{
					//$itemtype_no_access = '';
					$stringsub_task = $useCheckedOut ? '': '&sub_task=edit';
					//index.php?option=com_pagesanditems&view=page'.$sub_task.'&pageId='.$row->id.'&pageType='.$row->pageType.'&menutype='.$row->menutype.'
					$script .= ",'index.php?option=com_pagesanditems&view=page&menutype=".$row->menutype."&pageId=".$row->id.$stringsub_task."&pageType=".$pageType;
				}
				$script .= "','".$title."','','".$image."','".$image;
				$script .= "');\n";
				//if($this->pageId == $row->id)
				if($pageId == $row->id && $storeVars)
				{
					$this->pageMenuItem = $row;
				}
				//if( ($row->parent == PagesAndItemsHelper::getPageId() && $row->menutype == PagesAndItemsHelper::getCurrentMenutype()) || (!PagesAndItemsHelper::getPageId() && $row->menutype == PagesAndItemsHelper::getCurrentMenutype()) )
				// && ( ($row->parent == $pageId && $row->menutype == PagesAndItemsHelper::getCurrentMenutype()) || (!$pageId && $row->menutype == PagesAndItemsHelper::getCurrentMenutype()) ));
				if( $storeVars && ( ($row->parent == $pageId && $row->menutype == PagesAndItemsHelper::getCurrentMenutype()) || (!$pageId && $row->menutype == PagesAndItemsHelper::getCurrentMenutype()) ))
				{
					$this->currentMenuitems = $menuitems;
				}
			}
			//end loop menuitems
			$doc->addScriptDeclaration($script);
			//open javascript
			$html .= '<script language="javascript" type="text/javascript">'."\n";
			$html .= "<!--\n";
			$html .= "document.write(d".$m.");\n";
			//if on a certain page, make tree-menu-button selected
			if($menutypes[$m] == PagesAndItemsHelper::getCurrentMenutype())
			{
				//if(PagesAndItemsHelper::getPageId() && PagesAndItemsHelper::getPageId() >1)
				if($pageId && $pageId >1)
				{
					$html .= "d$m.openTo(";
					$html .= $pageId; //PagesAndItemsHelper::getPageId();
					$html .= ", true);\n";
				}
				else
				{
					//ms: make the root selected
					$html .= "d$m.s(0);\n";
				}
			}
			//close javascript
			$html .=  "-->\n";
			$html .=  '</script>'."\n";
			$html .= '</div>';
		}//end loops menutype
		//$html .= '</div>';
		$html .= '</td></tr></tbody></table>';
		
		return $html;
	}
}


