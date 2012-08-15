<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a textarea element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

//class JElementTextareajtext extends JElement
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldTextareajtext extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'Textareajtext';

	protected function getInput()
	{
		// Initialize variables.
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$columns	= $this->element['cols'] ? ' cols="'.(int) $this->element['cols'].'"' : '';
		$rows		= $this->element['rows'] ? ' rows="'.(int) $this->element['rows'].'"' : '';

		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		// convert <br /> tags so they are not visible when editing

		$value = JText::_($this->value);
		$value = str_replace('<br />', "\n", $value);

		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		return '<textarea name="'.$this->name.'" id="'.$this->id.'"' .
				$columns.$rows.$class.$disabled.$onchange.'>' .
				htmlspecialchars($value, ENT_COMPAT, 'UTF-8') .
				'</textarea>';
	}
}
