<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');

class PagesAndItemsController extends JController
{

	//public $joomlaVersion;
	public $app;

	function __construct( $default = array())
	{
		parent::__construct($default);
		require_once(dirname(__FILE__).'/helpers/pagesanditems.php');

		//$version = new JVersion();
		//$this->joomlaVersion = $version->getShortVersion();
		$this->app = &JFactory::getApplication();
		/*
		$lang = JFactory::getLanguage();
		*/
		/*
		load sys language
		*/
		/*
		if($isSuperAdmin = PagesAndItemsHelper::getIsSuperAdmin())
		{
			$lang		= JFactory::getLanguage();
			// Load extension-local file.
			$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, null, false, false)
			||	$extension = 'com_joomfish';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		}
		*/
	}

	function display($tpl = null)
	{
		/*
		$lang = JFactory::getLanguage();
		*/
		$isAdmin = PagesAndItemsHelper::getIsAdmin();
		$isSuperAdmin = PagesAndItemsHelper::getIsSuperAdmin();
		if($isAdmin)
		{
			$view = JRequest::getVar('view');
			$layout = JRequest::getVar('layout');
			$menutype = JRequest::getVar('menutype',0);
			$pageId = JRequest::getVar('pageId',0);
			$sub_task = JRequest::getVar('sub_task','');
			$subsub_task = JRequest::getVar('subsub_task','');
			
			//here i test an new method
			/*
			if(!$view || $view == 'page')
			{
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'page.php');
				PagesAndItemsHelperPage::onBeforeView();
			}
			*/
			//here i test an new method
			if(!$view || ($view == 'page' && $layout == 'root' && !$menutype && $sub_task != 'new' && $sub_task != 'newMenutype'))
			{
				$currentMenutype = PagesAndItemsHelper::getCurrentMenutype();
				$msg = null;
				if($currentMenutype)
				{
					$menutype = $currentMenutype;
				}
				else
				{
					$app = JFactory::getApplication();
					//no menutypes display an message?
					$msg = JText::_('COM_PAGESANDITEMS_NO_MENUS_SELECTED');
					$app->enqueueMessage($msg);
					$menutype = '';
					//ms: add if we have in root edit/create menutype JRequest::setVar('subtask', 'new');
				}
				JRequest::setVar('view', 'page');
				JRequest::setVar('layout', 'root');
				
				//JInput::set('some','some');
			}
			elseif($view == 'page' && $pageId && !$menutype)
			{
				if($row = PagesAndItemsHelper::getMenuitem($pageId))
				{
					$menutype = $row->menutype;
				}
			}
			$input = new JInput;
			$menutype ? $input->set('menutype', $menutype) : 0;
			
			
			// redirect to ... if no view is set
			//if(!$view || ($view == 'page' && $layout == 'root' && !$menutype ) || ($view == 'page'  && $layout != 'root' && !$pageId ))
			/*
			if(!$view || ($view == 'page' && $layout == 'root' && !$menutype && $sub_task != 'new' && $subsub_task != 'menutype')) //ms: add if we have in root edit/create menutype && $sub_task != 'new' ))
			{
				
				if ($modelPage = &$this->getModel('Page','PagesAndItemsModel'))
				{
					//$currentMenutype = $modelPage->getCurrentMenutype();
					//if($currentMenutype)
					//{
					//	$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$currentMenutype;
					//	$msg = '';
					//}
					//else
					{//
					//	//no menutypes display an message?
					//	$msg = JText::_(COM_PAGESANDITEMS_NO_MENUS_SELECTED);
					//	$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype=mainmenu';
					//}
					//$this->app->redirect($url,$msg);
					$currentMenutype = PagesAndItemsHelper::getCurrentMenutype();
					$msg = null;
					if($currentMenutype)
					{
						$menutype = $currentMenutype;
					}
					else
					{
						//no menutypes display an message?
						$msg = JText::_('COM_PAGESANDITEMS_NO_MENUS_SELECTED');
						$this->app->enqueueMessage($msg);
						$menutype = '';
						//ms: add if we have in root edit/create menutype JRequest::setVar('subtask', 'new');
					}
					JRequest::setVar('view', 'page');
					JRequest::setVar('layout', 'root');
					JRequest::setVar('menutype', $menutype);
				}
			}*/
		}//end if is admin

		$vName = strtolower(JRequest::getCmd('view', 'page'));
		$modelName = array();
		$helperPath = array();
		$helperName = array();
		switch ($vName)
		{
			case 'config':
			case 'config_itemtype':
			case 'config_custom_itemtype_field':
				//$modelName[] = 'Page';
				$vLayout = JRequest::getCmd( 'layout', 'default' );

			break;
			
			case 'config_custom_itemtype':
				$modelName[] = 'customitemtype';
				$vLayout = JRequest::getCmd( 'layout', 'default' );

			break;


			case 'extension':
			//case 'page':
			case 'item':
			case 'instance_select':
			case 'item_move_select':
			case 'page_move_select':
					$vLayout = JRequest::getCmd( 'layout', 'default' );
			break;
			
			case 'page':
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');
				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
				{
					$helperName[] = 'helper';
				}
				else
				{
					$helperName[] = 'menus';
				}
				$helperPath[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers';
				$this->addModelPath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models' );
				$vLayout = JRequest::getCmd( 'layout', 'default' );
				
				
				//TODO RootMenutype
				if($vLayout == 'root' && $sub_task != 'new')
				{
					//$modelName[] = 'RootMenutype';
					$modelName[] = 'Menutype';
				}
				else
				{
					$modelName[] = 'Page';
					
					
				}
			break;
			
			/*
			case 'categorie':
				$modelName[] = 'Categorie';
				$this->addModelPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'); //???
				$vLayout = 'default';//JRequest::getCmd( 'layout', 'default' );
			break;
			*/
			case 'category':
				$modelName[] = 'Category';
				//$this->addModelPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'); //???
				$vLayout = 'default';//JRequest::getCmd( 'layout', 'default' );
			
			break;

			case 'element':
				$modelName[] = 'element';
				$vLayout = JRequest::getCmd( 'layout', 'default' );
			break;

			case 'managers':
				
				if($isSuperAdmin)
				{
					/*
					//in view managers the com_pagesanditems.sys language is not load
					$lang		= JFactory::getLanguage();
					// Load extension-local file.
					$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, null, false, false)
					||	$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
					*/
				}
				//$modelName[] = 'Base';
				$vLayout = JRequest::getCmd( 'layout', 'default' );
			break;
			
			default:
				//$modelName[] = 'Base';
				$vLayout = JRequest::getCmd( 'layout', 'default' );
			break;
		}
		$document = &JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = &$this->getView( $vName, $vType);


		if(is_array($modelName))
		{
			for($mn = 0; $mn < count($modelName); $mn++)
			{
				//we need $model[$mn] as unique in J1.6
				if($model[$mn] = &$this->getModel($modelName[$mn],'PagesAndItemsModel'))
				{
					// Push the model into the view (not as default)
					$view->setModel($model[$mn], false);
				}
			}
		}
		//in views the com_pagesanditems.sys language is not load
		$lang		= JFactory::getLanguage();
		// Load extension-local file.
		$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load('com_pagesanditems.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);





		/*
		add here for releaseEditId and checkin in all views
		//where we must set the old?
		in all links avaible in view page? (not layout==root)
		and not link (or redirect) to the view page or change the pageId
		
			1. pagetree here we can change the pageId
			2. submenu here we go to other views
			3. ?
			
			but if we make an new Browser tab/window and link to another view?????
		
		*/
		/*
		//ms: com_menus.edit.item.id
		$app	= JFactory::getApplication();
		$oldPageId = $app->getUserState("com_pagesanditems.page.edit.item.id"); //.oldPageId");
		if($pageId)
		{
			$context = 'com_pagesanditems.page.edit.item'; //.oldPageId';
			$app->setUserState('com_pagesanditems.page.edit.item.id',$pageId);
			if($vName == 'page' && $vLayout == 'default')
			{
				$context = 'com_menus.edit.item';
				$this->holdEditId($context, $pageId);
				$context = 'com_pagesanditems.page.edit.item';
				$this->holdEditId($context, $pageId);
			}
		}
		if($oldPageId && $pageId != $oldPageId)
		{
			$context = 'com_menus.edit.item';
			$this->releaseEditId($context, $oldPageId);
			
			$model = $this->getModel( 'Item' ,'MenusModel');
			$model->checkin($oldPageId);
		}
		//ms: end com_menus.edit.item.id
		*/
		
		if(is_array($helperPath))
		{
			for($hp = 0; $hp < count($helperPath); $hp++)
			{
				$view->addHelperPath($helperPath[$hp]);
			}
		}
		if(is_array($helperName))
		{
			for($hn = 0; $hn < count($helperPath); $hn++)
			{
				$view->loadHelper($helperName[$hn]);//make sure we have not use helpers/helper.php in pi??
			}
		}
		/*
		if($vName == 'categorie')
		{
			if($modelCategory = $this->getModel('CategoriesCategory','PagesAndItemsModel'))
			//if($modelCategory = $this->getModel('Category','CategoriesModel'))
			{
				// Push the model into the view
				$view->setModel($modelCategory, false);
			}
		}
		*/
		if($vName == 'category')
		{
			if($modelCategory = $this->getModel('CategoriesCategory','PagesAndItemsModel'))
			//if($modelCategory = $this->getModel('Category','CategoriesModel'))
			{
				// Push the model into the view
				$view->setModel($modelCategory, false);
			}
		}
		
		
		$view->loadHelper('pagesanditems');
		/*
		ms: i am thinking about an additional view
		like pages-items
		called categories-items
		but this must also in the submenu for superAdmin and other users
		first i will add it as an manager 'categoriesanditems'
		
		*/

		if($isSuperAdmin && $isAdmin)
		{
		/*
			work without view categorie?
			set:
			JRequest::setVar('categoryId',0);
			in helpers/pagesanditems.php comment out line 814-822
			*/
			//JRequest::setVar('categoryId',0);
			// Load the submenu only for super admins
			require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php');
			PagesAndItemsHelper::addSubmenu(JRequest::getWord('view', 'page'));
		}
		elseif($isAdmin)
		{
			/*
			work without view categorie?
			set:
			JRequest::setVar('categoryId',0);
			and comment out:
			require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php');
			PagesAndItemsHelper::addSubmenuFirst(JRequest::getWord('view', 'page'));
			in helpers/pagesanditems.php comment out line 814-822
			*/
			$config = PagesAndItemsHelper::getConfigAsRegistry();
			if($config->get('enabled_view_category'))
			{
				require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php');
				PagesAndItemsHelper::addSubmenuFirst(JRequest::getWord('view', 'page'));
			}
		}

		if($isAdmin)
		{
			require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php');
			//TODO set the toolbar in each view not PagesAndItemsHelper::addToolbar?
			PagesAndItemsHelper::addToolbar($vName,$vLayout);
			if($vName == 'item')
			{
				//$view->addTemplatePath(JPATH_COMPONENT_SITE.DS.'views'.DS.$vName.DS.'tmpl');
			}
		}
		else
		{
			$view->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers');
			if($vName == 'item')
			{
				//$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.$vName.DS.'tmpl');
			}
			$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.$vName.DS.'tmpl');
		}

		

		// Set the layout
		$view->setLayout($vLayout);
		$view->display();
	}

	//redirect to view page
	function cancel()
	{

		$pageId = JRequest::getVar('pageId', 0);
		$categoryId = JRequest::getVar('categoryId', 0);
		$menutype = JRequest::getVar('menutype', 0);
		$menutypeString = $menutype ? '&menutype='.$menutype : '';
		$pageIdString = $pageId ? '&pageId='.$pageId : '';
		
		if(!$pageId)
		{
			if($categoryId)
			{
				$this->app->redirect("index.php?option=com_pagesanditems&view=category&categoryId=".$categoryId, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
			}
			else
			{
				$this->app->redirect("index.php?option=com_pagesanditems&view=page&layout=root".$menutypeString, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
			}
			
		}
		else
		{
			$this->app->redirect("index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId=".$pageId.$menutypeString, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
	}

	function ajax_version_checker(){
		//$helper = new PagesAndItemsHelper();
		$message = JText::_('COM_PAGESANDITEMS_VERSION_CHECKER_NOT_AVAILABLE');
		$url = 'http://www.pages-and-items.com/latest_version_pi_j1.6.txt';
		$file_object = @fopen($url, "r");
		if($file_object == TRUE){
			$version = fread($file_object, 1000);
			$message = JText::_('COM_PAGESANDITEMS_LATEST_VERSION').' = '.$version;
			if(PagesAndItemsHelper::getPagesAndItemsVersion() != $version){
				$message .= '<div><span class="warning">'.JText::_('COM_PAGESANDITEMS_NEWER_VERSION').'</span>.</div>';
				//if($this->pi_version_type=='pro'){
					//$download_url = 'http://www.pages-and-items.com/my-extensions';
				//}else{
					$download_url = 'http://www.pages-and-items.com/extensions/pages-and-items';
				//}
				$message .= '<div><a href="'.$download_url.'" target="_blank">'.JText::_('COM_PAGESANDITEMS_DOWNLOAD').'</a></div>';
			}else{
				$message .= '<div><span style="color: #5F9E30;">'.JText::_('COM_PAGESANDITEMS_IS_LATEST_VERSION').'</span>.</div>';
			}
			fclose($file_object);
		}

		//reset version checker session
		$app = JFactory::getApplication();
		$app->setUserState( "com_pagesanditems.latest_version_message", '' );

		echo $message;
		exit;
	}

	function ajax_update_cit_item(){

		//check token
		JRequest::checkToken( 'get' ) or die( '<span style="color: red;">Invalid Token</span>' );

		$itemtype = intval(JRequest::getVar('itemtype',''));
		$item_id = intval(JRequest::getVar('item_id',''));
		$itemtype_name = 'custom_'.$itemtype;

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		ExtensionItemtypeHelper::importExtension(null, array('custom','other_item'),true,null,true);
		$dispatcher = &JDispatcher::getInstance();

		$dispatcher->trigger('update_content_table_from_custom_itemtype',array($item_id, $itemtype_name));
		//check if the saved item has other_items linked to it, if so, update those
		$dispatcher->trigger('update_other_items_if_needed',array($item_id));

		echo '<span style="color: #5F9E30;">'.JText::_('COM_PAGESANDITEMS_UPDATED').'</span>';
		exit;
	}

}
?>