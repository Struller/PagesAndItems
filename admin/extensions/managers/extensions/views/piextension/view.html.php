<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
 require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
/**
 * HTML View class for the Plugins component

 */

class PagesAndItemsViewPiextension extends PagesAndItemsViewDefault
{
	protected $item;
	protected $form;
	protected $state;

	function display( $tpl = null )
	{
		//		PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MANAGERS').': ['.JText::_('COM_PAGESANDITEMS_EXTENSION_INSTALLER').']</small>');
		/*
		$sub_task = JRequest::getVar('sub_task','');
		if($sub_task=='new')
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_CONFIG').': ['.JText::_('COM_PAGESANDITEMS_NEW').']</small>');
		}
		else
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_CONFIG').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
		}
		*/
		$app = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$refer = $app->getUserState( $option.'.refer');
		if($refer != '' && $refer)
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_CONFIG').' '.JText::_('COM_PAGESANDITEMS_ITEMTYPE').': ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
		}
		else
		{
			PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MANAGERS').': '.JText::_('COM_PAGESANDITEMS_EXTENSIONS').' ['.JText::_('COM_PAGESANDITEMS_EDIT').']</small>');
		}
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion >= '1.6') // && $vLayout == 'edit')
		{

			//$model = & $this->getModel('formextension','PI_Pages_And_ItemsModel'); //,'PagesAndItemsModel');
			// set the form path
			JForm::addFormPath(realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'models'.DS.'forms'));

			//$model = & $this->getModel('extension');
			$model = & $this->getModel();

			$this->state	= $model->getState();
			$this->item		= $model->getItem();

			$this->form		= $model->getForm();
			if(!$this->form)
			{
				//TODO error message and redirect
			}
			$extension = 'com_plugins';
			$lang = &JFactory::getLanguage();
			//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
			$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
			JHtml::_('behavior.formvalidation');
			/*
			???
			if($this->item->type == 'language')
			{
				$this->languageItems = $model->getLanguageItems($this->item);
			}

			*/
		}
		else
		{

			$option = JRequest::getVar('option');

			$db		=& JFactory::getDBO();
		$user 	=& JFactory::getUser();

		$client = JRequest::getWord( 'client', 'site' );
		$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		/*
		$model = & $this->getModel('manageextension','PagesAndItemsModel');
		$model->setState('manageextension.extension_id',$cid[0]);
		//echo 'mo: '.$model->getState('manageextension.extension_id').': mo,';
		$this->item		= $model->getItem($cid[0]);
		echo 'mo: '.$model->getState('manageextension.extension_id').': mo,';
		$this->form		= $model->getForm();
		*/
		/*
		on $model->getForm(); error cannot load component com_pagesanditems

		*/
		//$fieldSets = $this->form->getFieldsets('params');



		$lists 	= array();

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row 	=& JTable::getInstance('piextension','PagesAndItemsTable');

		// load the row from the db table
		$row->load( $cid[0] );

		// fail if checked out not by 'me'

		if ($row->isCheckedOut( $user->get('id') ))
		{
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The plugin' ), $row->title );
			$this->setRedirect( 'index.php?option='. $option .'&client='. $client, $msg, 'error' );
			return false;
		}

		if ($client == 'admin') {
			$where = "client_id='1'";
		} else {
			$where = "client_id='0'";
		}

		// get list of groups
		if ($row->access == 99 || $row->client_id == 1) {
			$lists['access'] = 'Administrator<input type="hidden" name="access" value="99" />';
		} else {
			// build the html select list for the group access
			$lists['access'] = JHTML::_('list.accesslevel',  $row );
		}
		$params = null;
		if ($cid[0])
		{
			$row->checkout( $user->get('id') );

			if ( $row->ordering > -10000 && $row->ordering < 10000 )
			{
				// build the html select list for ordering
				$query = 'SELECT ordering AS value, name AS text'
					. ' FROM #__pi_extensions'
					. ' WHERE type = '.$db->Quote($row->type)
					. ' AND enabled > 0'
					. ' AND '. $where
					. ' AND ordering > -10000'
					. ' AND ordering < 10000'
					. ' ORDER BY ordering'
				;
				$order = JHTML::_('list.genericordering',  $query );
				$lists['ordering'] = JHTML::_('select.genericlist',   $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval( $row->ordering ) );
			} else {
				$lists['ordering'] = '<input type="hidden" name="ordering" value="'. $row->ordering .'" />'. JText::_( 'This plugin cannot be reordered' );
			}

			//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
			//$extension = ExtensionHelper::importExtension($row->type,$row->folder, $row->element,true,null,true);
			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..');
			require_once($path.DS.'includes'.DS.'extensions'.DS.$row->type.'helper.php');
			$extensionHelper = 'Extension'.ucfirst($row->type).'Helper';
			$extension = $extensionHelper::importExtension($row->folder, $row->element,true,null,true);

			if($extension && is_object($extension))
			{
				$params = $extension->getParams();
			}
			//$lang =& JFactory::getLanguage();

			if($row->folder && $row->folder != '')
			{
				$path = $row->type.'s'.DS.str_replace('/',DS,$row->folder);
			}
			else
			{
				$path = $row->type.'s';
			}
			//echo $path;
			//echo(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.$path. DS .$row->element. DS . $row->element .'.xml');
			$data = JApplicationHelper::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS.$path. DS .$row->element. DS . $row->element .'.xml');

			$row->description = $data['description'];


		}
		else
		{
			$row->type		= '';
			$row->folder 		= '';
			$row->ordering 		= 999;
			$row->enabled 	= 1;
			$row->description 	= '';
		}
		$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->enabled );

		$this->assignRef('lists',		$lists);
		$this->assignRef('plugin',		$row);
		/*
		here we use at this moment JParameter in J1.6 is JParameter deprecated
		for get/set we can use JRegistry
		and for output
		*/
		$this->assignRef('params',		$params);
		}
		parent::display($tpl);
		$this->addToolbar();
	}

	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		JToolBarHelper::apply('piextension.apply');//, 'COM_PAGESANDITEMS_APPLY');
		JToolBarHelper::save('piextension.save');//, 'COM_PAGESANDITEMS_SAVE');
		JToolBarHelper::divider();
		JToolBarHelper::cancel('piextension.cancel');//, 'COM_PAGESANDITEMS_CANCEL');
	}
}