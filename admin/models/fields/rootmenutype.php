<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class JFormFieldRootMenutype extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'RootMenutype';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.menutype AS value, a.title AS text');
		$query->from('#__menu_types AS a');
		/*
		
		$query->select('menutype AS value, a.title AS text');
		$query->from('#__modules');
		or from #__modules where module='mod_menu' OR module LIKE '%menu%'
		
		*/
		
		/*
		if ($menuType = $this->form->getValue('menutype')) {
			$query->where('a.menutype = '.$db->quote($menuType));
		}
		else {
			$query->where('a.menutype != '.$db->quote(''));
		}
		*/
		/*
		// Prevent parenting to children of this item.
		if ($id = $this->form->getValue('id')) {
			$query->join('LEFT', '`#__menu` AS p ON p.id = '.(int) $id);
			$query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
		}
		
		$query->where('a.published != -2');
		$query->group('a.id');
		*/
		$query->order('a.id ASC');
		
		// Get the options.
		$db->setQuery($query);

		
		$options[] = JHTML::_('select.option', '', '- '. JText::_( 'COM_PAGESANDITEMS_EXTENSIONS_SELECT_MENUTYPE' ) .' -' );
		$db->setQuery( $query );
		$options = array_merge( $options, $db->loadObjectList() );
		
		//$options = $db->loadObjectList();
		
		
		
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		/*
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++) {
			//$this->value
			//$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
			if($options[$i]->value == $this->value)
			{
			}
		}
		*/
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}