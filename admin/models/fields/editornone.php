<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die;

jimport('joomla.html.editor');
jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Platform.
 * An editarea field for content creation
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 *
 * @see         JFormfieldEditors
 * @see         JEditor
 */
class JFormFieldEditornone extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Editornone';

	/**
	 * The JEditor object.
	 *
	 * @var    object
	 * @since  11.1
	 */
	protected $editor;

	/**
	 * Method to get the field input markup for the editor area
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$rows		= (int) $this->element['rows'];
		$cols		= (int) $this->element['cols'];
		$height		= ((string) $this->element['height']) ? (string) $this->element['height'] : '250';
		$width		= ((string) $this->element['width']) ? (string) $this->element['width'] : '100%';
		$assetField	= $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';
		$authorField= $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
		$asset		= $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'] ;

		// Build the buttons array.
		$buttons = (string) $this->element['buttons'];

		if ($buttons == 'true' || $buttons == 'yes' || $buttons == '1') {
			$buttons = true;
		}
		elseif ($buttons == 'false' || $buttons == 'no' || $buttons == '0') {
			$buttons = false;
		}
		else {
			$buttons = explode(',', $buttons);
		}
		$hide = ((string) $this->element['hide']) ? explode(',', (string) $this->element['hide']) : array();
		
		// Get an editor object.
		$editor = $this->getEditor();
		$html = '';
		// htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
		$html .= "<textarea disabled=\"disabled\" cols=\"$cols\" rows=\"$rows\" style=\"width: $width; height: $height;\">".htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')."</textarea>";
		$html .= '<div class="hide">';
		$html .= $editor->display($this->name, htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'), $width, $height, $cols, $rows, $buttons ? (is_array($buttons) ? array_merge($buttons, $hide) : $hide) : false, $this->id, $asset, $this->form->getValue($authorField));
		$html .= '</div>';
		return $html;
	}

	/**
	 * Method to get a JEditor object based on the form field.
	 *
	 * @return  object  The JEditor object.
	 * @since   11.1
	 */
	protected function &getEditor()
	{
		// Only create the editor if it is not already created.
		if (empty($this->editor)) {
			// Initialize variables.
			$editor = null;

			// Get the editor type attribute. Can be in the form of: editor="desired|alternative".
			$type = trim((string) $this->element['editor']);

			if ($type) {
				// Get the list of editor types.
				$types = explode('|', $type);

				// Get the database object.
				$db = JFactory::getDBO();

				// Iterate over teh types looking for an existing editor.
				foreach ($types as $element)
				{
					// Build the query.
					$query	= $db->getQuery(true);
					$query->select('element');
					$query->from('#__extensions');
					$query->where('element = '.$db->quote($element));
					$query->where('folder = '.$db->quote('editors'));
					$query->where('enabled = 1');

					// Check of the editor exists.
					$db->setQuery($query, 0, 1);
					$editor = $db->loadResult();

					// If an editor was found stop looking.
					if ($editor) {
						break;
					}
				}
			}

			// Create the JEditor intance based on the given editor.
			$this->editor = JFactory::getEditor($editor ? $editor : null);
		}

		return $this->editor;
	}

	/**
	 * Method to get the JEditor output for an onSave event.
	 *
	 * @return  string  The JEditor object output.
	 * @since   11.1
	 */
	public function save()
	{
		return $this->getEditor()->save($this->id);
	}
}
