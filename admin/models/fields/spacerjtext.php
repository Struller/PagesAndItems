<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
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
