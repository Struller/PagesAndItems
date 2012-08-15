<?php
/**
* @version		2.1.6
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
class htmlElementTr extends htmlAttribute
{
	function __construct($config = array())
	{
		parent::__construct($config);
		/*
			tr Object Collections

			W3C: W3C Standard.
			Collection 	Description 	W3C
			cells 	Returns a collection of all <td> or <th> elements in a table row 	Yes
			
			tr Object Properties
			Property 	Description 	W3C
			align 	Deprecated. Sets or returns the horizontal alignment of the content within a table row. Use style.textAlign instead 	D
			bgColor 	Deprecated. Sets or returns the background color of a table row. Use style.backgroundColor instead 	D
			height 	Deprecated. Sets or returns the height of a table row. Use style.height instead 	D

			ch 	Sets or returns an alignment character for cells in a table row 	Yes
			chOff 	Sets or returns the horizontal offset of the ch property 	Yes
			rowIndex 	Returns the position of a row in the rows collection of a table 	Yes
			sectionRowIndex 	Returns the position of a row in the rows collection of a tbody, thead, or tfoot 	Yes
			vAlign 	Sets or returns the vertical alignment of the content within a table row 	Yes
			
		*/
		$this->_attributes['ch'] = isset($config['attributes']['ch']) ? 'ch="'.$config['attributes']['ch'].'"' : '';
		$this->_attributes['chOff'] = isset($config['attributes']['chOff']) ? 'chOff="'.$config['attributes']['chOff'].'"' : '';
		$this->_attributes['rowIndex'] = isset($config['attributes']['rowIndex']) ? 'rowIndex="'.$config['attributes']['rowIndex'].'"' : '';
		$this->_attributes['sectionRowIndex'] = isset($config['attributes']['sectionRowIndex']) ? 'sectionRowIndex="'.$config['attributes']['sectionRowIndex'].'"' : '';
		$this->_attributes['vAlign'] = isset($config['attributes']['vAlign']) ? 'vAlign="'.$config['attributes']['vAlign'].'"' : '';
		$this->_tag = 'tr';
	}
	
	/*
	function start($content = '')
	{
		//$html = '<tr '.$this->_attributes['id'].' '.$this->_attributes['name'].' '.$this->_attributes['class'].' '.$this->_attributes['style'].'>';
		$html = parent::start();
		$html .= $content;
		return $html;
	}

	function end()
	{
		return '</tr>';
	}
	*/
}