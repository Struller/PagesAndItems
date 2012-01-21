<?php
/**
* @version		1.6.2.2$Id: spacer.php 14401 2010-01-26 14:10:00Z louis $
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
 * Renders a spacer element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldSpacerjtext extends JFormField //JElementSpacerjtext extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'Spacerjtext';

	protected function getLabel()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		//$html[] = '<fieldset>';
		if ($this->value)
		{
			$values = array();
			$tempvalues = explode(';',$this->value);
			foreach($tempvalues as $value)
			{
				$values[] = JText::_($value);
			}
			$value = implode(' ',$values);
			$html[] = '<h4>'.$value.'</h4>';
		}
		else 
		{
			$html[] = '<hr />';
		}
		
		//$html[] = '</fieldset>';

		return implode($html);
	}
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		//$html[] = '<fieldset>';
		if ($this->value)
		{
			$values = array();
			$tempvalues = explode(';',$this->value);
			foreach($tempvalues as $value)
			{
				$values[] = JText::_($value);
			}
			$value = implode(' ',$values);
			//$html[] = '<h4>'.$value.'</h4>';
		}
		else 
		{
			$html[] = '<hr />';
		}
		
		//$html[] = '</fieldset>';

		return implode($html);
	}

}
