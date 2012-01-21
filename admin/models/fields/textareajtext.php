<?php
/**
* @version		1.6.2.2$Id: textarea.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Parameter
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
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
