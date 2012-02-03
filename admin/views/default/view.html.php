<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Plugins component

 */
class PagesAndItemsViewDefault extends JView
{
	function display( $tpl = null )
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php';

		$helper = new pagesanditemsHelper();
		$this->assignRef('helper', $helper);

		$pi_config = PagesAndItemsHelper::getConfig();
		$this->assignRef('pi_config', $pi_config);

		//$model = &$this->getModel('base');
		//if($model->isAdmin)
		$isAdmin = PagesAndItemsHelper::getIsAdmin();
		if($isAdmin)
		{

			if(!defined('COM_PAGESANDITEMS_TITLE_IS_SET'))
			{
				PagesAndItemsHelper::addTitle();
			}
		}
		//$controller->display_footer();
		//$this->controller->set_title();

		//here we set the title
		//can override from child
		//$this->setTitle();

		parent::display($tpl);
	}

	function setTitle()
	{
		if(PagesAndItemsHelper::getIsAdmin()) //$this->controller->is_admin)
		{
			//require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/pagesanditems.php';
			PagesAndItemsHelper::addTitle(null);
		}
	}

}