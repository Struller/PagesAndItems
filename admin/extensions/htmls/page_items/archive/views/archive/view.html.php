<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
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


class PagesAndItemsViewArchive extends PagesAndItemsViewDefault
{
	function display($tpl = null)
	{
		$archiveType = JRequest::getVar('archiveType','all');
		$this->assignRef('archiveType', $archiveType);
		if ($model = &$this->getModel('Page'))
		{

			$pageTree = $model->getPages();
			$this->assignRef( 'pageTree',$pageTree);

			$menuItemsTypes = $model->menuItemsTypes;
			$this->assignRef( 'menuItemsTypes',$menuItemsTypes);

			$this->assignRef( 'model',$model);
		}
		/*
		not here JHTML::stylesheet('pagesanditems2.css', 'administrator/components/com_pagesanditems/css/');
		JHTML::stylesheet('dtree.css', 'administrator/components/com_pagesanditems/css/');
		*/
		$pathComponent = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../../../../../../../').DS));
		JHTML::script('dtree.js',$path,false); //, 'administrator/components/com_pagesanditems/javascript/',false);
		//JHTML::script('overlib_mini.js', 'includes/js/',false);
		JHTML::_('behavior.tooltip');

		parent::display($tpl);

	}

}
