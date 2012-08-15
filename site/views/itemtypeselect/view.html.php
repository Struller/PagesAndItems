<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die();

jimport( 'joomla.application.component.view');

class PagesAndItemsViewItemtypeselect extends JView{

	function display( $tpl = null ){
	
		//get backend language files
		$lang = &JFactory::getLanguage();
		$lang->load('com_pagesanditems', JPATH_ADMINISTRATOR.DS, null, false) || $lang->load('com_pagesanditems', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$path = PagesAndItemsHelper::getDirCSS();
		JHtml::stylesheet($path.'/pagesanditems2.css');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		//$helper = new PagesAndItemsHelper();		
		//$this->assignRef('helper', $helper);
		
		parent::display($tpl);
	}
}

?>