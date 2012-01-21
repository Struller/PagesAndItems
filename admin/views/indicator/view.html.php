<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
/**
*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'item'.DS.'view.html.php');



/**
 * HTML View class for the

 */

class PagesAndItemsViewIndicator extends PagesAndItemsViewDefault //PagesAndItemsViewItem
{
	function display( $tpl = null )
	{
		/*
		JHTML::script('popup_extension.js',PagesAndItemsHelper::getDirJS(),false);
		$path = PagesAndItemsHelper::getDirCSS();
		JHtml::stylesheet($path.'/pagesanditems2.css');
		JHTML::stylesheet('popup.css', $path.'/');
		//JHtml::stylesheet($path.'/pages_and_items_extension.css');
		//$popup = JRequest::getVar('popup', 0 );
		
		*/
		parent::display($tpl);
	}
}