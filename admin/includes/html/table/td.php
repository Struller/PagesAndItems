<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );
require_once(realpath(dirname(__FILE__)).DS.'th.php');
/**
 * Utility class for 
 *
 * @package     
 * @subpackage  
 * @since       
 */
class htmlElementTd extends htmlElementTh
{

	function __construct($config = array())
	{
		parent::__construct($config);
		$this->_tag = 'td';
	}

	/*
	function start($content = '')
	{
		//$html = '<td '.$this->_attributes['id'].' '.$this->_attributes['name'].' '.$this->_attributes['class'].' '.$this->_attributes['style'].' '.$this->_attributes['abbr'].' '.$this->_attributes['axis'].' '.$this->_attributes['cellIndex'].' '.$this->_attributes['ch'].' '.$this->_attributes['chOff'].' '.$this->_attributes['colSpan'].' '.$this->_attributes['headers'].' '.$this->_attributes['rowSpan'].' '.$this->_attributes['vAlign'].'>';
		$html = parent::start();
		$html .= $content;
		return $html;
	}
	*/
	/*
	function end()
	{
		return '</td>';
	}
	*/
}