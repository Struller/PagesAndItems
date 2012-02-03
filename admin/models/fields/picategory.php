<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
JFormHelper::loadFieldClass('category');

/**
 * Form Field class for the Joomla Platform.
 * Supports an HTML select list of categories
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldPicategory extends JFormFieldCategory
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Picategory';

	
	protected function XgetLabel()
	{
	
	}
	
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();
		
		$html[] = '<ul class="adminformlist">';
			$html[] = '<li>';
				$html[] = '<fieldset class="radio inputbox">';//id="jform_params_show_page_heading_X">';
					$html[] = '<input type="radio" name="create_new_category" value="0" id="create_new_category_0" />';
					$html[] = $this->getLabel();
					// Create a read-only list (no name) with a hidden input to store the value.
					if ((string) $this->element['readonly'] == 'true') {
						$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
						$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
					}
					// Create a regular list.
					else {
						$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
					}
				$html[] = '</fieldset>';
				//$html[] = '<div></div>';
			$html[] = '</li>';
			$html[] = '<li style="clear: both;">';
				$html[] = '<fieldset class="radio inputbox">';// id="jform_params_show_page_heading_X">';
					$html[] = '<input type="radio" name="create_new_category" value="1" id="create_new_category_1" checked="checked" />';
					$html[] = '<label class="hasTip" for="create_new_category_1" title="';
						$html[] = JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
						$html[] = '::';
						$html[] = JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY_TIP').'.';
						$html[] = '<br />'.JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY_TIP2');
						$html[] = '">';
						$html[] = JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
					$html[] = '</label>';
				$html[] = '</fieldset>';
			$html[] = '</li>';
		$html[] = '</ul>';
		return implode($html);
	}
	
	protected function XgetOptions()
	{
		// Initialise variables.
		$options	= array();
		$extension	= $this->element['extension'] ? (string) $this->element['extension'] : (string) $this->element['scope'];
		$published	= (string) $this->element['published'];

		// Load the category options for a given extension.
		if (!empty($extension)) {

			// Filter over published state or not depending upon if it is present.
			if ($published) {
				$options = JHtml::_('category.options', $extension, array('filter.published' => explode(',', $published)));
			}
			else {
				$options = JHtml::_('category.options', $extension);
			}

			// Verify permissions.  If the action attribute is set, then we scan the options.
			if ($action	= (string) $this->element['action']) {

				// Get the current user object.
				$user = JFactory::getUser();

				foreach($options as $i => $option)
				{
					// To take save or create in a category you need to have create rights for that category
					// unless the item is already in that category.
					// Unset the option if the user isn't authorised for it. In this field assets are always categories.
					if ($user->authorise('core.create', $extension.'.category.'.$option->value) != true ) {
						unset($options[$i]);
					}
				}

			}

			if (isset($this->element['show_root'])) {
				array_unshift($options, JHtml::_('select.option', '0', JText::_('JGLOBAL_ROOT')));
			}
		}
		else {
			JError::raiseWarning(500, JText::_('JLIB_FORM_ERROR_FIELDS_CATEGORY_ERROR_EXTENSION_EMPTY'));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}