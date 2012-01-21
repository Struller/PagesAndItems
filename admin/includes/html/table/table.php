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
class htmlElementTable extends htmlAttribute
{
	function __construct($config = array())
	{
		/*
		W3C: W3C Standard.
		Collection 	Description 	W3C
		cells 	Returns a collection of all <td> or <th> elements in a table 	No
		rows 	Returns a collection of all <tr> elements in a table 	Yes
		tBodies 	Returns a collection of all <tbody> elements in a table 	Yes

		Table Object Properties Deprecated:
		Property	Description
		align		Deprecated. Sets or returns the alignment of a table according to surrounding text. Use style.textAlign instead
		background	Deprecated. Sets or returns the background image of a table. Use style.background instead
		bgColor		Deprecated. Sets or returns the background color of a table. Use style.backgroundColor instead
		border		Deprecated. Sets or returns the width of the table border. Use style.border instead
		height		Deprecated. Sets or returns the height of a table. Use style.height instead
		width		Deprecated. Sets or returns the width of the table. Use style.width instead
		
		Table Object Properties WC3:
		Property	Description
		caption		Returns the caption of a table
		
		cellPadding	Sets or returns the amount of space between the cell border and cell content
		cellSpacing	Sets or returns the amount of space between the cells in a table
		frame		Sets or returns which outer-borders (of a table) that should be displayed
		rules		Sets or returns which inner-borders (between the cells) that should be displayed in a table
		summary		Sets or returns a description of the data in a table
		*/
		parent::__construct($config);
		$this->_attributes['cellPadding'] = isset($config['attributes']['cellPadding']) ? 'cellPadding="'.$config['attributes']['cellPadding'].'"' : '';
		$this->_attributes['cellSpacing'] = isset($config['attributes']['cellSpacing']) ? 'cellSpacing="'.$config['attributes']['cellSpacing'].'"' : '';
		$this->_attributes['frame'] = isset($config['attributes']['frame']) ? 'frame="'.$config['attributes']['frame'].'"' : '';
		$this->_attributes['rules'] = isset($config['attributes']['rules']) ? 'rules="'.$config['attributes']['rules'].'"' : '';
		$this->_attributes['summary'] = isset($config['attributes']['summary']) ? 'summary="'.$config['attributes']['summary'].'"' : '';
		$this->_tag = 'table';
	}

	function start($captionConfig = array()) {
		$caption = isset($captionConfig['content']) ? $captionConfig['content'] : '';
		if($caption)
		{
			//caption align is deprecated but browser do not get the style tag? //align 	top | bottom | left | right
			$captionAlign = isset($captionConfig['align']) ? 'align="'.$captionConfig['align'] .'"' : '';
			$captionId = isset($captionConfig['id']) ? 'id="'.$captionConfig['id'] .'"' : '';
			$captionName = isset($captionConfig['name']) ? 'name="'.$captionConfig['name'] .'"' : '';
			$captionClass = isset($captionConfig['class']) ? 'class="'.$captionConfig['class'] .'"' : '';
			$captionStyle = isset($captionConfig['style']) ? 'style="'.$captionConfig['style'] .'"' : '';
			$caption = '<caption '.$captionAlign.' '.$captionId.' '.$captionName.' '.$captionClass.' '.$captionStyle.'>'.$captionConfig['content'].'</caption>';
		}
		
		$html = parent::start();
		$html .= $caption;
		return $html;
	}
}