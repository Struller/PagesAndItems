<?php
/**
* @version		2.1.0
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
class htmlElementTh extends htmlAttribute
{
	function __construct($config = array())
	{
		parent::__construct($config);
		/*
		td/th Object Properties

		W3C: W3C Standard.
		Property 	Description 	W3C
		width 	Deprecated. Sets or returns the width of a data cell. Use style.width instead 	D
		align 	Deprecated. Sets or returns the horizontal alignment of the content in a data cell. Use style.textAlign instead 	D
		background 	Deprecated. Sets or returns the background image of a data cell. Use style.background instead 	D
		bgColor 	Deprecated. Sets or returns the background color of a table. Use style.backgroundColor instead 	D
		height 	Deprecated. Sets or returns the height of a data cell. Use style.height instead 	D
		noWrap 	Deprecated. Sets or returns whether the content in a cell can be wrapped. Use style.whiteSpace instead 	D
		
		
		abbr 		Sets or returns an abbreviated version of the content in a data cell 	Yes
		axis 		Sets or returns a comma-separated list of related data cells 	Yes
		cellIndex 	Returns the position of a cell in the cells collection of a table row 	Yes
		ch 			Sets or returns an alignment character for a data cell 	Yes
		chOff 		Sets or returns the horizontal offset of the ch property 	Yes
		colSpan 	Sets or returns the number of columns a cell should span 	Yes
		headers 	Sets or returns a list of header cell ids for the current data cell 	Yes
		rowSpan 	Sets or returns the number of rows a cell should span 	Yes
		vAlign 		Sets or returns the vertical alignment of the content within a cell 	Yes
		*/
		
		$this->_attributes['abbr'] = isset($config['attributes']['abbr']) ? 'abbr="'.$config['attributes']['abbr'].'"' : '';
		$this->_attributes['axis'] = isset($config['attributes']['axis']) ? 'axis="'.$config['attributes']['axis'].'"' : '';
		$this->_attributes['cellIndex'] = isset($config['attributes']['cellIndex']) ? 'cellIndex="'.$config['attributes']['cellIndex'].'"' : '';
		$this->_attributes['ch'] = isset($config['attributes']['ch']) ? 'ch="'.$config['attributes']['ch'].'"' : '';
		$this->_attributes['chOff'] = isset($config['attributes']['chOff']) ? 'chOff="'.$config['attributes']['chOff'].'"' : '';
		$this->_attributes['colSpan'] = isset($config['attributes']['colSpan']) ? 'colSpan="'.$config['attributes']['colSpan'].'"' : '';
		$this->_attributes['headers'] = isset($config['attributes']['headers']) ? 'headers="'.$config['attributes']['headers'].'"' : '';
		$this->_attributes['rowSpan'] = isset($config['attributes']['rowSpan']) ? 'rowSpan="'.$config['attributes']['rowSpan'].'"' : '';
		$this->_attributes['vAlign'] = isset($config['attributes']['vAlign']) ? 'vAlign="'.$config['attributes']['vAlign'].'"' : '';
		$this->_tag = 'th';
	}
	
	/*
	function start($content = '')
	{
		
		//$html = '<th '.$this->_attributes['id'].' '.$this->_attributes['name'].' '.$this->_attributes['class'].' '.$this->_attributes['style'].' '.$this->_attributes['abbr'].' '.$this->_attributes['axis'].' '.$this->_attributes['cellIndex'].' '.$this->_attributes['ch'].' '.$this->_attributes['chOff'].' '.$this->_attributes['colSpan'].' '.$this->_attributes['headers'].' '.$this->_attributes['rowSpan'].' '.$this->_attributes['vAlign'].' >';
		$html = parent::start();
		$html .= $content;
		return $html;
	}
	*/
	/*
	function end()
	{
		return '</th>';
	}
	*/
}