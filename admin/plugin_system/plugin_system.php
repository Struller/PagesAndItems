<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/
//-- No direct access
defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

/**
 * @package		PagesAndItems
 * @subpackage	system-plugin for Pages-and-Items (com_pagesanditems)
 */
class plgSystemPagesanditems extends JPlugin
{
	/**
	 * Constructor
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 */

	function plgSystemPages_and_items( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		// Do some extra initialisation in this constructor if required
	}


	function onAfterInitialise() 
	{

		$app = JFactory::getApplication();
		if (!$app->isAdmin()) 
		{
			return;
		}
		
		$option = JRequest::getCmd('option');
		$task = JRequest::getCmd('task');
		$view = JRequest::getCmd('view');
		
		//here we check if an menutype is before change
		if($option == 'com_menus' && ($task == 'menu.save' || $task == 'menu.apply' || $task == 'menu.save2new') ) // && $view == 'menu')
		{
			$id	= JRequest::getInt('id');
			if($id)
			{
				//check menutype if change
				$data		= JRequest::getVar('jform', array(), 'post', 'array');
				$menutypeNew = isset($data['menutype']) ? $data['menutype'] : 0;
				$menutypeOld = '';
				$db =& JFactory::getDBO();
				$db->setQuery("SELECT title, menutype FROM #__menu_types WHERE id = '$id' ");
				$row = $db->loadObject();
				if($row)
				{
					$menutypeOld = $row->menutype;
					$app->setUserState("com_pagesanditems.com_menus.menutype.old",($menutypeOld == $menutypeNew) ? 0 : $menutypeOld);
					$app->setUserState("com_pagesanditems.com_menus.menutype.new",($menutypeOld == $menutypeNew) ? 0 : $menutypeNew);
					$app->setUserState("com_pagesanditems.com_menus.menutype.id",($menutypeOld == $menutypeNew) ? 0 : $id);
				}
			}
		}
		//here we check is an menutype before delete
		if($option == 'com_menus' && $task == 'menus.delete' )
		{
			// Get items
			$cid	= JRequest::getVar('cid', array(), '', 'array');
			if (!is_array($cid) || count($cid) < 1) {
				JError::raiseWarning(500, JText::_('COM_MENUS_NO_MENUS_SELECTED'));
			} else {

				// Make sure the item ids are integers
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::toInteger($cid);
				//we need the menutype as array
				$db =& JFactory::getDBO();
				$menutypes = array();
				foreach($cid as $id)
				{
					$db->setQuery("SELECT menutype FROM #__menu_types WHERE id='$id' ");
					$row = $db->loadObject();
					if($row)
					{
						$menutypes[] = $row->menutype;
					}
				}
				$app->setUserState("com_pagesanditems.com_menus.menus.ids",$menutypes);
			}
		}
		return true;
	}

	function onAfterRoute()
	{
		$app = JFactory::getApplication();
		if (!$app->isAdmin()) 
		{
			return;
		}
		
		$option = JRequest::getCmd('option');
		$task = JRequest::getCmd('task');
		$view = JRequest::getCmd('view');
		if($option == 'com_menus') // && ($task == 'menu.save' || $task == 'menu.apply' || $task == 'menu.save2new') ) // && $view == 'menu')
		{
			$data	= JRequest::getVar('jform', array(), 'post', 'array');
			if(!$data)
			{
				//here we check if we have an changed menutype
				$id = $app->getUserState("com_pagesanditems.com_menus.menutype.id");
				if($id)
				{
					//first step is if has the #__menu_types is saved ore error on save?
					$menutypeNew = $app->getUserState("com_pagesanditems.com_menus.menutype.new");
					$db =& JFactory::getDBO();
					$db->setQuery("SELECT title, menutype FROM #__menu_types WHERE id = '$id' ");
					$row = $db->loadObject();
					if($row)
					{
						if($row->menutype == $menutypeNew)
						{
							//ok the item in #__menu_types is saved with new menutype
							//we must get the old
							$menutypeOld = $app->getUserState("com_pagesanditems.com_menus.menutype.old");
							//get PagesAndItemsHelper
							require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
							$config = PagesAndItemsHelper::getConfigAsRegistry();
							//we must take an look in pagesanditems config->menu to set the changed menutype
							if($config->get('menus.'.$menutypeOld,0))
							{
								//$menu = $config->get('menus.'.$menutypeOld);
								$title = $row->title; //$menu[1];
								$config->set('menus.'.$menutypeOld,array($menutypeNew,$title));
								//we have an array like  menu['mainmenu']array('mainmenu','Main'),
								//and if we change  menu['mainmenu']array('newmenu','Main'),
								//in the saved config we use only 'newmenu','Main'
								
								PagesAndItemsHelper::saveConfig($config->toArray());
							}
						}
					}
				}
				
				
				//here we check if an menutype is deleted
				$cid = $app->getUserState("com_pagesanditems.com_menus.menus.ids",array());
				$changed = false;
				if(is_array($cid) && count($cid) && count($cid) >= 1)
				{
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
					$config = PagesAndItemsHelper::getConfigAsRegistry();
					foreach($cid as $menutype)
					{
						if($config->get('menus.'.$menutype,0))
						{
							//check if delete $menutype in #__menu_types
							$db =& JFactory::getDBO();
							$db->setQuery("SELECT menutype FROM #__menu_types WHERE menutype = '$menutype' ");
							$row = $db->loadObject();
							if(!$row)
							{
								//here we set the array menus[$menutype] to array()
								//can we remove from the JRegistry?
								$config->set('menus.'.$menutype,array());
								$changed = true;
							}
						}
					}
					if($changed)
					{
						PagesAndItemsHelper::saveConfig($config->toArray());
					}
				}
				$app->setUserState("com_pagesanditems.com_menus.menutype.old",0);
				$app->setUserState("com_pagesanditems.com_menus.menutype.new",0);
				$app->setUserState("com_pagesanditems.com_menus.menutype.id",0);
				$app->setUserState("com_pagesanditems.com_menus.menus.ids",0);
			}
			

		}
		return true;
	}


	/**
	 * Do something onAfterRender
	 */
	function onAfterRender()
	{
		$option = JRequest::getVar('option', '', 'get');
		//$option = '';
		$view = JRequest::getVar('view', '', 'get');
		$application = JFactory::getApplication();
		if (!$application->isAdmin())
		{

			/*
			we are in frontend
			*/

			$task = JRequest::getVar('task', '', 'get');
			$layout = JRequest::getVar('layout', '', 'get');
			$Itemid = JRequest::getVar('Itemid', '', 'get');
			/*
			in Joomla 1.6
			item edit
			com_content
			&view=form
			&layout=edit
			&a_id=22 is the article id
			&Itemid=437 is the menu id
			is an article edit

			if we have no a_id in the request is article submitt


			*/

			if($option=='com_content' && ($task=='edit' || $layout =='form' || $view == 'form' || $layout == 'edit'))
			{
				if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php'))
				{
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
				}
				else
				{
					return;
				}
				$config = PagesAndItemsHelper::getConfig();

				//get config to check if we need to do a redirect
				$use_pi_frontend_editting = 0;

				if($config['use_pi_frontend_editting']){
					$use_pi_frontend_editting = $config['use_pi_frontend_editting'];
				}

				//only redirect if in pi-config use_pi_frontend_editting is activated
				if($use_pi_frontend_editting)
				{
					//$id = JRequest::getVar('id', JRequest::getVar('a_id', '', 'get'), 'get'); 
					//ce: this line parses the wrong id when SEF urls are used
					$id = JRequest::getVar('a_id', '');
					
					if(strpos($id, ':'))
					{
						$pos = strpos($id, ':');
						$item_id = intval(substr($id, 0, $pos));
					}
					else
					{
						$item_id = intval($id);
					}
					
				

					if($use_pi_frontend_editting == 2)
					{
						$database = JFactory::getDBO();
						$database->setQuery("SELECT * FROM #__pi_item_index WHERE item_id='$item_id'");
						$item_row = $database->loadObject();
						if(!$item_row)
						{
							return;
						}
					}
					$menu_id = JRequest::getVar('Itemid', '', 'get');

					//redirect item new
					if($option=='com_content' && (($view=='article' && $layout=='form') || ($view=='form' && $layout=='edit' && !JRequest::getVar('a_id', null, 'get')) ) )
					{
						//echo 'new item';
						$sub_task = 'new';
					}

					//redirect item edit
					if($option=='com_content' && (($view=='article' && $task=='edit') || ($view=='form' && $layout=='edit' && JRequest::getVar('a_id', null, 'get')) ))
					{
						$sub_task = 'edit';
					}

					$referer = '';
					if($config['item_save_redirect']=='current'){
						$referer = '&return='.JRequest::getString('return',  '', 'get');
					}
					$layout_url = '';
					if($layout=='form'){
						$layout_url = '&layout=form';
					}
					
					if(!$Itemid)
					{
						//$return = JRequest::getString('return',  '', 'get');
						//dump(base64_decode($return));
					}
					
					
					$url = 'index.php?option=com_pagesanditems&view=item'.$layout_url.'&sub_task='.$sub_task.'&checkin=1&item_id='.$item_id.'&itemId='.$item_id.'&Itemid='.$Itemid.'&pageId='.$menu_id.$referer;
					$application->redirect($url);
				}
			}

			return;
		}else{
			//backend


			$option = JRequest::getVar('option', '', 'get');
			$sub_task = JRequest::getCmd('sub_task', '');
			$pageType = JRequest::getCmd('pageType', '');

			$task = JRequest::getCmd('task');

			$layout = JRequest::getCmd('layout');
			if( $option == 'com_content' && ($task == 'edit' || ($view == 'article' && $layout == 'edit') ) )
			{
				if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php'))
				{
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
				}
				else
				{
					return;
				}
				$config = PagesAndItemsHelper::getConfig();
				//&& !$config['plugin_system_disable_content']
				if(!$config['plugin_system_hidde_button']  && !$config['plugin_system_add_button'])
				{
					return;
				}

				$add_button = $config['plugin_system_add_button'] ? 1 : 0;
				$hidde_button  = $config['plugin_system_hidde_button'] ? 1 : 0;


				$id = null;
				$ids = JRequest::getVar('cid',null);
				if(isset($ids) && $task == 'edit')
				{
					$id = $ids[0];
				}
				else
				{
					$id = JRequest::getVar('id',null);
				}

				$item_type = false;

				if($id)
				{
					$database = JFactory::getDBO();
					$database->setQuery("SELECT itemtype FROM #__pi_item_index WHERE item_id='$id' LIMIT 1");
					$rows = $database->loadObjectList();

					if($rows)
					{
						$itemrow = $rows[0];
						$item_type = $itemrow->itemtype;
					}
				}
				//only if item has been made with PI
				if($item_type!='')
				{
					$lang = &JFactory::getLanguage();
					//$lang->load('com_pagesanditems', JPATH_ADMINISTRATOR, null, false, false);
					$extension = 'com_pagesanditems';
					$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

					$body = JResponse::getBody();
					$nl = "\n";
					$script_add = '';
					$style = '';
					$version = new JVersion();
					$joomlaVersion = $version->getShortVersion();
					if($add_button)
					{
						$style .= '<style type="text/css">'.$nl;
						$style .= '.icon-32-PI';
						$style .= '{';
						$dirIcons = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'media').DS.'images'.DS.'icons'));
						$style .= 'background-image:url("'.$dirIcons.'/icon-32-pi.png");';
						$style .= '}'.$nl;
						$style .= '</style>'.$nl;
						$database = JFactory::getDBO();

						$database->setQuery("SELECT * FROM #__content WHERE id='$id' LIMIT 1");
						$contentrow = $database->loadObject();
						if($contentrow)
						{
							//$link = 'index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='.$contentrow->catid.'&itemId='.$id.'&pageType=content_article&checkin=1';
							$link = 'index.php?option=com_pagesanditems&view=item&sub_task=edit&itemId='.$id.'&checkin=1&categoryId='.$contentrow->catid;
							//TODO checkin the article

							$script_add = '';
							if($joomlaVersion < '1.6')
							{
								$script_add .= 'var elementTD = new Element(\'td\');' .$nl;
								$script_add .= 'elementTD.setProperty(\'class\',\'button\');' .$nl;
								$script_add .= 'elementTD.setProperty(\'id\',\'PI_'.$contentrow->catid.'\');' .$nl;
								$script_add .= 'var elementA = new Element(\'a\');' .$nl;
								$script_add .= 'elementA.setProperty(\'class\',\'toolbar\');' .$nl;

								$script_add .= 'elementA.innerHTML = \''.JText::_('edit').'\';' .$nl;

								$script_add .= 'elementA.setProperty(\'href\',\''.$link.'\');' .$nl;
								$script_add .= 'elementA.injectInside(elementTD);' .$nl;

								$script_add .= 'var elementSpan = new Element(\'span\');' .$nl;
								$script_add .= 'elementSpan.setProperty(\'class\',\'icon-32-PI\');' .$nl;

								$script_add .= 'elementSpan.setProperty(\'Title\',\''.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_OPEN_IN_PI').'\');' .$nl;

								$script_add .= 'elementSpan.injectTop(elementA);' .$nl;

								$script_add .= 'var elementApply = $(\'toolbar-apply\');' .$nl;
								$script_add .= 'elementTD.injectAfter(elementApply);' .$nl;
							}
							else
							{
								$script_add .= 'var elementTD = new Element(\'li\');' .$nl;
								$script_add .= 'elementTD.setProperty(\'class\',\'button\');' .$nl;
								$script_add .= 'elementTD.setProperty(\'id\',\'PI_'.$contentrow->catid.'\');' .$nl;
								$script_add .= 'var elementA = new Element(\'a\');' .$nl;
								$script_add .= 'elementA.setProperty(\'class\',\'toolbar\');' .$nl;

								$script_add .= 'elementA.innerHTML = \''.JText::_('edit').'\';' .$nl;


								$script_add .= 'elementA.setProperty(\'href\',\''.$link.'\');' .$nl;
								$script_add .= 'elementA.injectInside(elementTD);' .$nl;

								$script_add .= 'var elementSpan = new Element(\'span\');' .$nl;
								$script_add .= 'elementSpan.setProperty(\'class\',\'icon-32-PI\');' .$nl;

								$script_add .= 'elementSpan.setProperty(\'Title\',\''.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_OPEN_IN_PI').'\');' .$nl;

								$script_add .= 'elementSpan.injectTop(elementA);' .$nl;

								$script_add .= 'var elementCancel = $(\'toolbar-cancel\');' .$nl;
								$script_add .= 'elementTD.injectBefore(elementCancel);' .$nl;
							}
						}
					}


				$script_remove = '';
				//delete save and apply Button ?
				if($hidde_button && $item_type!='text')
				{
					if($joomlaVersion < '1.6')
					{
						$script_remove .= '$(\'toolbar-save\').remove();';
						$script_remove .= '$(\'toolbar-apply\').remove();';
					}
					else
					{
						$script_remove .= '$(\'toolbar-save\').destroy();';
						$script_remove .= '$(\'toolbar-apply\').destroy();';
						$script_remove .= '$(\'toolbar-save-new\').destroy();';
						$script_remove .= '$(\'toolbar-save-copy\').destroy();';
					}
				}

				// create the javascript
				// We can't use $document, because it's already rendered

				$script_onload = '';
				$script_disable = '';

				//ms: at this moment this will not work
				//but in onContentPrepareForm
				/*
				if($this->params->get('disable_content', 0))
				{
					if($joomlaVersion < '1.6')
					{
						$script_disable .= '$(\'catid\').disabled="disabled";' .$nl;
						$script_disable .= '$(\'sectionid\').disabled="disabled";' .$nl;
						$script_disable .= 'var elementText = $(\'text\').clone();' .$nl;
						$script_disable .= 'var elementParent = $(\'text\').getParent();' .$nl;
						$script_disable .= 'var elements = elementParent.getChildren();' .$nl;
						$script_disable .= 'elements.each(function(element,index) ' .$nl;
						$script_disable .= '{' .$nl;
						$script_disable .= 'element.remove()'.$nl;
						$script_disable .= '});' .$nl;
						$script_disable .= 'elementText.removeProperty(\'class\');' .$nl;
						$script_disable .= 'elementText.setStyle(\'display\',\'none\');' .$nl;
						$script_disable .= 'elementText.setProperty(\'readOnly\',\'true\');' .$nl;
						$script_disable .= 'elementText.injectInside(elementParent);' .$nl;
						$script_disable .= 'var elementDivT = new Element(\'div\');' .$nl;

						$script_disable .= 'elementDivT.setHTML(\'<strong>'.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_ONLY_DISPLAY').'</strong>\');' .$nl;

						$script_disable .= 'elementDivT.setStyles({margin:\'8px 0 15px\'});' .$nl;

						$script_disable .= 'elementDivT.injectInside(elementParent);' .$nl;

						$script_disable .= 'var elementDiv = new Element(\'div\');' .$nl;
						$script_disable .= 'elementDiv.setStyle(\'width\',elementText.getStyle(\'width\'));' .$nl;
						$script_disable .= 'elementDiv.setStyle(\'height\',elementText.getStyle(\'height\'));' .$nl;
						$script_disable .= 'elementDiv.setStyles({border:\'1px solid #D5D5D5\',margin:\'8px 0 15px\'});' .$nl;
						$script_disable .= 'elementDiv.setHTML(elementText.getText());' .$nl;
						$script_disable .= 'elementDiv.injectInside(elementParent);' .$nl;
					}
					else
					{
						$script_disable .= 'var elementItemForm = $(\'item-form\');' .$nl;
						$script_disable .= 'var elementItemFormLegend = elementItemForm.getElement(\'legend\');' .$nl;
						$script_disable .= 'elementItemFormLegend.set(\'html\',\''.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_ONLY_DISPLAY').'\');' .$nl;
						$script_disable .= 'var elementItemFormFieldset = elementItemForm.getElement(\'fieldset\');' .$nl;


						$script_onload .= 'var elementItemForm = $(\'item-form\');' .$nl;
						$script_onload .= 'var articletext_parent = $(\'jform_articletext_parent\');' .$nl;
						$script_onload .= 'var elementText = $(\'jform_articletext\');' .$nl;
						$script_onload .= 'var elementDiv = new Element(\'div\');' .$nl;
						$script_onload .= 'elementDiv.setStyle(\'width\',elementText.getStyle(\'width\'));' .$nl;
						$script_onload .= 'elementDiv.setStyle(\'height\',elementText.getStyle(\'height\'));' .$nl;
						$script_onload .= 'elementDiv.setStyles({border:\'1px solid #D5D5D5\',margin:\'8px 0 15px\'});' .$nl;
						$script_onload .= 'elementDiv.set(\'html\',elementText.get(\'html\'));' .$nl;
						$script_onload .= 'elementDiv.injectBefore(articletext_parent);' .$nl;
						$script_onload .= 'articletext_parent.setStyle(\'display\',\'none\');' .$nl;
						$script_onload .= 'var editor_xtd_buttons = $(\'editor-xtd-buttons\');' .$nl;
						$script_onload .= 'if(editor_xtd_buttons)' .$nl;
						$script_onload .= '{' .$nl;
						$script_onload .= '	editor_xtd_buttons.setStyle(\'display\',\'none\');' .$nl;
						$script_onload .= '}' .$nl;
						$script_onload .= 'var toogleButton = elementItemForm.getElement(\'div[class=toggle-editor]\');' .$nl;
						$script_onload .= 'if(toogleButton)' .$nl;
						$script_onload .= '{' .$nl;
						$script_onload .= '	toogleButton.setStyle(\'display\',\'none\');' .$nl;
						$script_onload .= '}' .$nl;

						//toggle-editor

						$script_disable .= 'var elements = elementItemForm.getElements(\'*\');' .$nl;
						//getChildren();' .$nl;

						$script_disable .= 'elements.each(function(element,index) ' .$nl;
						$script_disable .= '{' .$nl;
							$script_disable .= 'element.setProperty(\'readonly\',\'readonly\');' .$nl;
							$script_disable .= 'element.setProperty(\'disabled\',\'disabled\');' .$nl;
							$script_disable .= 'element.set(\'disabled\',\'disabled\');' .$nl;
							$script_disable .= 'element.set(\'disabled\',\'true\');' .$nl;
						$script_disable .= '});' .$nl;


					}
				
				}
					*/
					$script = $nl. '<!-- system plugin Pages and items -->' .$nl.$style.
					'<script type="text/javascript">' .$nl.
					'// <!--' .$nl.
					'window.addEvent(\'domready\', function()' .$nl.
					'{' .$nl.
						$script_add.
						$script_remove.
						//$script_disable.
					'});' .$nl.

					'window.addEvent(\'load\', function()' .$nl.
					'{' .$nl.
						$script_onload.
					'});' .$nl.
					'// -->' .$nl.
					'</script>' .$nl.
					'<!-- / system plugin Pages and items -->';
					$body = str_replace('</head>', $script.'</head>', $body);
					JResponse::setBody($body);
				}
			}
			//end backend
			return;
		}
		return;
	}

	/*
	function onContentAfterSave($context, &$article, $isNew){

		$article_id = $article->id;

		$database = JFactory::getDBO();
		$database->setQuery( "UPDATE #__pi_config SET config='$article_id' WHERE id='test' ");
		$database->query();

		return true;
	}
	*/

}
