<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );
require_once(realpath(dirname(__FILE__).DS.'..').DS.'attributes.php');
/**
 * Utility class for 
 *
 * @package     
 * @subpackage  
 * @since       
 */
class htmlElementThead extends htmlAttribute
{

	function __construct($config = array())
	{
		parent::__construct($config);
		$this->_tag = 'thead';
	}
	
		/*
	function start()
	{
		$html = '<thead '.$this->_attributes['id'].' '.$this->_attributes['name'].' '.$this->_attributes['class'].' '.$this->_attributes['style'].'>';
		return $html;
	}
	
	function end()
	{
		return '</thead>';
	}
	*/
}