<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class pagesanditemsController extends JController{

	//var $helper;

	function display(){		
		
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pagesanditems.php');
		//$this->helper = new PagesAndItemsHelper();
		
		parent::display();
				
	}
	
	function __construct(){			
		
		parent::__construct();	
				
	}
}
?>