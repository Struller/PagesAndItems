<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
//jimport( 'joomla.application.component.controllerform' );
//jimport('joomla.filesystem.file');
//jimport('joomla.filesystem.folder');
/**
controller.php
*/
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');
jimport( 'joomla.application.component.controllerform' );
jimport( 'joomla.form.form' );
/**
 *
 *
 * @package		PagesAndItems
 */
class PagesAndItemsControllerMenutype extends JControllerForm 
{
	function __construct( $config = array())
	{
		parent::__construct($config);

		//$this->registerTask( 'root', 'display' );
		//$this->registerTask( 'root_menutype_apply', 'root_menutype_save' );
	}


	function root_cancel()
	{
		$menutype = JRequest::getVar('menutype', '');
		//$message = JText::_('COM_PAGESANDITEMS_PAGE_CANCEL');
		$menutype = $menutype ? '&menutype='.$menutype : '';
		$url = "index.php?option=com_pagesanditems&view=page&layout=root".$menutype;
		//$app = JFactory::getApplication();
		$this->setMessage(JText::_('COM_PAGESANDITEMS_PAGE_CANCEL'));
		$this->setRedirect(JRoute::_($url, false), $message);
	}


	function root_menutype_new(){
		
		$menutype = JRequest::getVar('menutype', '');
		$menutype = $menutype ? '&menutype='.$menutype : '';
		$url = 'index.php?option=com_pagesanditems&view=page&layout=root'.$menutype.'&sub_task=newMenutype';
		$this->setRedirect(JRoute::_($url, false));
	
	}
	
	function root_menutype_save(){
		
		$this->reorder_save();
		$url = 'index.php?option=com_pagesanditems&view=page&layout=root';
		$menutype = JRequest::getVar('menutype', '');
		$menutype = $menutype ? '&menutype='.$menutype : '';
		
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		//JRequest::setVar('extension', 'com_content');??
		// Initialise variables.
		$app		= JFactory::getApplication();
		$data		= JRequest::getVar('jform', array(), 'post', 'array');
		$context	= 'com_menus.edit.menu';
		$task		= JRequest::getVar('sub_task');
		$recordId	= JRequest::getInt('menutypeId');
		
		//$sub_task = JRequest::getVar('sub_task', '');
		$sub_task = $recordId ? '' :'&sub_task=newMenutype' ;


		if (!$this->checkEditId($context, $recordId)) {
			// Somehow the person just went to the form and saved it - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_($url.$menutype, false));
			//$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));

			return false;
		}

		// Make sure we are not trying to modify an administrator menu.
		if (isset($data['client_id']) && $data['client_id'] == 1){
			JError::raiseNotice(0, JText::_('COM_MENUS_MENU_TYPE_NOT_ALLOWED'));

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_($url.$menutype.$sub_task, false));
			//$this->setRedirect(JRoute::_('index.php?option=com_menus&view=menu&layout=edit', false));

			return false;
		}
		
		JForm::addFormPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'forms');
		// set the fields path
		JForm::addFieldPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'fields');
		//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'menus.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'menu.php');

		$lang = &JFactory::getLanguage();
		$lang->load('com_menus', JPATH_ADMINISTRATOR, null, false) || $lang->load('com_menus', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);;
		// Populate the row id from the session.
		$data['id'] = $recordId;

		// Get the model and attempt to validate the posted data.
		//$model	= $this->getModel('Menu','MenusModel');
		$model	= $this->getModel('Menutype'); //,'MenusModel');
		$form	= $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());

			return false;
		}
		$enabled = isset($data['enabled']) ? $data['enabled'] : 0;
		$data	= $model->validate($form, $data);
		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}
			// Save the data in the session.
			$app->setUserState('com_menus.edit.menu.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_($url.$menutype.$sub_task, false));
			//$this->setRedirect(JRoute::_('index.php?option=com_menus&view=menu&layout=edit', false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data)) {
			// Save the data in the session.
			$app->setUserState('com_menus.edit.menu.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_($url.$menutype.$sub_task, false));

			return false;
		}

		$this->setMessage(JText::_('COM_MENUS_MENU_SAVE_SUCCESS'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState($this->context.'.id');
				$this->holdEditId($context, $recordId);
				$item = $model->getItem();
				// Redirect back to the edit screen.
				
				if($enabled)
				{
					$menutype = $item->menutype ? '&menutype='.$item->menutype : '';
					//here we must add the menu to config and save the config
					$config = PagesAndItemsHelper::getConfigAsRegistry();
					//we must take an look in pagesanditems config->menu to set the changed menutype
					//if($config->get('menus.'.$item->menutype,0))
					//{
						$title = $item->title; //$menu[1];
						$config->set('menus.'.$item->menutype,array($item->menutype,$title));
						//we have an array like  menu['mainmenu']array('mainmenu','Main'),
						//and if we change  menu['mainmenu']array('newmenu','Main'),
						//in the saved config we use only 'newmenu','Main'
						PagesAndItemsHelper::saveConfig($config->toArray());
					//}
				}
				else
				{
					//$menutype = $item->menutype ? '&menutype='.$item->menutype : '';
				}
				
				//jform[enabled]
				$this->setRedirect(JRoute::_($url.$menutype, false));
				//$this->setRedirect(JRoute::_('index.php?option=com_menus&view=menu&layout=edit'.$this->getRedirectToItemAppend($recordId), false));
				break;

			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_($url.$menutype.'&sub_task=newMenutype', false));
				//$this->setRedirect(JRoute::_('index.php?option=com_menus&view=menu&layout=edit', false));
				break;

			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context.'.data', null);

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_($url.$menutype, false));
				//$this->setRedirect(JRoute::_('index.php?option=com_menus&view=menus', false));
				break;
		}
		
		
		
		
		//$this->setMessage(JText::_('COM_PAGESANDITEMS_MENUTYPE_SAVE'));
		//$url = 'index.php?option=com_pagesanditems&view=page&layout=root'; //&sub_task=editMenutype';
		//$this->setRedirect(JRoute::_($url, false),'save Menutype do nothing');
	}
	
	

	function root_save(){
		$this->reorder_save();
		$menutype = JRequest::getVar('menutype', '') ? '&menutype='.JRequest::getVar('menutype', '') : '';
		$url = 'index.php?option=com_pagesanditems&view=page&layout=root'.$menutype;
		$app->redirect($url);
	}
	
	function reorder_save(){
		$db = JFactory::getDBO();
		//$app = &JFactory::getApplication();

		//if pages where reordered update the ordering of these pages
		$pages_are_reordered = JRequest::getVar('items_page_are_reordered',0);
		$pages_total = JRequest::getVar('items_page_total',0);
		//$message = '';
		if($pages_are_reordered==1){
			//saving the menuitems in the root to new order
			for ($n = 1; $n <= $pages_total; $n++){
				$temp_id = intval(JRequest::getVar('reorder_page_id_'.$n, '', 'post'));
				$lft = intval(JRequest::getVar('reorder_lft_'.$n, '', 'post'));
				//ordering needs to be set else rebuild script undoes everything !!!?
				$db->setQuery( "UPDATE #__menu SET lft='$lft', ordering='$n' WHERE id='$temp_id'");
				$db->query();
			}

			//rebuild menu tree
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php');
			$model = new MenusModelItem;
			$model->rebuild();
			//$app->enqueueMessage(JText::_('COM_PAGESANDITEMS_PAGEORDER_SAVED'));
			$this->setMessage(JText::_('COM_PAGESANDITEMS_PAGEORDER_SAVED'));
		}
	}
}
