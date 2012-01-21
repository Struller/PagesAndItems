<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

//get backend language files
//$lang = &JFactory::getLanguage();
//$lang->load('com_pagesanditems', JPATH_ADMINISTRATOR.DS, null, false);

//require_once(JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.com_pagesanditems.ini');
//echo 'soep';
//exit;

//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');

//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
//require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'page.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'item'.DS.'view.html.php');
/*
class PagesAndItemsViewItem extends PagesAndItemsViewDefault //PagesAndItemsViewPage //PagesAndItemsViewDefault
{
	function display( $tpl = null )
	{
		//dump(JRequest::get());
		if ($model = &$this->getModel('Page')) 
		{
			if($model->isAdmin)
			{
			
				$pageTree = $model->getPages();
				$this->assignRef( 'pageTree',$pageTree);

				$menuItemsTypes = $model->menuItemsTypes;
				$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
				JHTML::script('dtree.js', 'administrator/components/com_pi_pages_and_items/javascript/',false);
			}
			else
			{
				$query = 'SELECT template'
				. ' FROM #__templates_menu'
				. ' WHERE client_id = 1'
				. ' AND menuid = 0'
				;
				$model->db->setQuery($query);
				$template = $model->db->loadResult();
				$iconCss = JURI::root().'/administrator/templates/'.$template.'/css/icon.css';
				JHTML::stylesheet('general.css', JURI::root().'/administrator/templates/'.$template.'/css/');
				$menuItemsTypes = $model->menuItemsTypes;
				$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
			}
			
			$this->assignRef( 'model',$model);

		}
		*/
		/*
		JHTML::script('modal.js');
		JHTML::stylesheet('modal.css');
		JHTML::_('behavior.modal', 'a.modal-button',array('onOpen'=>'QuarkX'));
		*/
		/*
		not here JHTML::stylesheet('pages_and_items3.css', 'administrator/components/com_pi_pages_and_items/css/');
		JHTML::stylesheet('dtree.css', 'administrator/components/com_pi_pages_and_items/css/');
		*/
		/*
		//JHTML::script('dtree.js', 'administrator/components/com_pi_pages_and_items/javascript/',false);
		JHTML::script('overlib_mini.js', 'includes/js/',false);
		parent::display($tpl);
	}
}
*/
?>