<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );

/**
 * Utility class for 
 *
 * @package     
 * @subpackage  
 * @since       
 */
//abstract 
class htmlAttribute
{

	/**
	 * The ? of 
	 *
	 * @var	array
	 * @access	protected
	 */
	protected $_attributes = array();
	protected $_tag = null;

	function __construct($config = array())
	{
		$this->_attributes['class'] = isset($config['attributes']['class']) ? 'class="'.$config['attributes']['class'].'"' : '';
		$this->_attributes['id'] = isset($config['attributes']['id']) ? 'id="'.$config['attributes']['id'].'"' : '';
		$this->_attributes['name'] = isset($config['attributes']['name']) ? 'name="'.$config['attributes']['name'].'"' : '';
		$this->_attributes['style'] = isset($config['attributes']['style']) ? 'style="'.$config['attributes']['style'].'"' : '';
	}
	
	function start($content = '')
	{

		$attributes = implode(' ',$this->_attributes);
		$html = '<'.$this->_tag.' '.$attributes.'>';
		$html .= $content;
		return $html;
		//return '<'.$this->_tag.' '.$this->_attributes['id'].' '.$this->_attributes['name'].' '.$this->_attributes['class'].' '.$this->_attributes['style'].'>';
	}
	
	function end()
	{
		return '</'.$this->_tag.'>';
	}

}