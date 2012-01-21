<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');

class PagesAndItemsController extends JController
{

	public $joomlaVersion;	
	public $app;
	
	function __construct( $default = array())
	{
		parent::__construct($default);
		require_once(dirname(__FILE__).'/helpers/pagesanditems.php');			
		
		$version = new JVersion();
		$this->joomlaVersion = $version->getShortVersion();
		$this->app = &JFactory::getApplication();

	}	
	
	function display($tpl = null)
	{		
		$isAdmin = PagesAndItemsHelper::getIsAdmin();
		$isSuperAdmin = PagesAndItemsHelper::getIsSuperAdmin();		
		if($isAdmin)
		{
			$view = JRequest::getVar('view');
			$layout = JRequest::getVar('layout');
			$menutype = JRequest::getVar('menutype',0);			

			// redirect to ... if no view is set
			if(!$view || ($view == 'page' && $layout == 'root' && !$menutype ))
			{
				if ($modelPage = &$this->getModel('Page','PagesAndItemsModel'))
				{
					$currentMenutype = $modelPage->getCurrentMenutype();
					if($currentMenutype)
					{
						$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype='.$currentMenutype;						
					}
					else
					{
						$url = 'index.php?option=com_pagesanditems&view=page&layout=root&menutype=mainmenu';						
					}					
					$this->app->redirect($url);
				}
			}
		}//end if is admin

		$vName = strtolower(JRequest::getCmd('view', 'page'));
		$modelName = array();
		$helperPath = array();
		$helperName = array();
		switch ($vName)
		{
			case 'config':
			case 'config_itemtype':
			case 'config_custom_itemtype':
			case 'config_custom_itemtype_field':
				$modelName[] = 'Page';
				$vLayout = JRequest::getCmd( 'layout', 'default' );

			break;
			
			case 'Xextension':
			case 'page':
			case 'item':
			case 'instance_select':
			case 'item_move_select':
			case 'page_move_select':
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'menutypes.php');
				$modelName[] = 'Page';							
				if($this->joomlaVersion < '1.6')
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
			break;
		
			case 'install':
				$modelName[] = 'install';				
				$vLayout = JRequest::getCmd( 'layout', 'default' );
			break;
			
			case 'manage':
				$modelName[] = 'manage';
				$vLayout = JRequest::getCmd( 'layout', 'default' );
			break;
			
			case 'manageextension':
				$modelName[] = 'manageextension';				
				if($this->joomlaVersion < '1.6')
				{
					$vLayout = JRequest::getCmd( 'layout', 'default' );
				}
				else
				{					
					$modelName[] = 'formextension';
					$vLayout = JRequest::getCmd( 'layout', 'edit' );
				}
			break;
			
			case 'element':
				$modelName[] = 'element';				
				$vLayout = JRequest::getCmd( 'layout', 'default' );
				//$helperName[] = 'helper';
				//$helperPath[] = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content';
			break;
			
			default:
				
				$modelName[] = 'Base';
				$vLayout = JRequest::getCmd( 'layout', 'default' );
				//dump($vLayout,'layout');
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
		
		
		//		$isSuperAdmin = PagesAndItemsHelper::getIsAdmin();
		//if($modelBase->isSuperAdmin && $modelBase->isAdmin)
		if($isSuperAdmin && $isAdmin)
		{
			// Load the submenu only for super admins
			require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php');
			PagesAndItemsHelper::addSubmenu(JRequest::getWord('view', 'page'));
		}

		//if($modelBase->isAdmin)
		if($isAdmin)
		{
			require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php');
			//TODO set the toolbar in each view not PagesAndItemsHelper::addToolbar?
			PagesAndItemsHelper::addToolbar($vName,$vLayout); //,$this->pathPluginsItemtypes);
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

		$view->loadHelper('pagesanditems');

		// Set the layout
		$view->setLayout($vLayout);
		$view->display();
	}

	//redirect to view page	
	function cancel()
	{		
		
		$pageId = JRequest::getVar('pageId', 0);		
		if(!$pageId)
		{
			$this->app->redirect("index.php?option=com_pagesanditems&view=page&layout=root", JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
		else
		{
			$this->app->redirect("index.php?option=com_pagesanditems&view=page&sub_task=edit&pageId=".$pageId, JText::_('COM_PAGESANDITEMS_ACTION_CANCELED'));
		}
	}
	
	function ajax_version_checker(){
		$helper = new PagesAndItemsHelper();
		$message = JText::_('COM_PAGESANDITEMS_VERSION_CHECKER_NOT_AVAILABLE');	
		$url = 'http://www.pages-and-items.com/latest_version_pi_j1.6.txt';		
		$file_object = @fopen($url, "r");		
		if($file_object == TRUE){
			$version = fread($file_object, 1000);
			$message = JText::_('COM_PAGESANDITEMS_LATEST_VERSION').' = '.$version;
			if($helper->version!=$version){
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