<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'models'.DS.'category.php');
/**
This model is need to set some variables
for item Delete
and that we can use the own (pi) category not categorie
 */

class PagesAndItemsModelCategoriesCategory extends CategoriesModelCategory
{
	public function __construct($config = array())
	{
		// Guess the option from the class name (Option)Model(View).
		if (empty($this->option) && isset($config['option'])) 
		{
			$this->option = $config['option'];
		}
		parent::__construct($config);
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_categories'.DS.'tables');
		
		//$context = $this->option.'.'.$this->name;
	}
	
	protected function canDelete($record)
	{
		if (!empty($record->id)) 
		{
			$user = JFactory::getUser();
			return $user->authorise('core.delete', $record->extension.'.category.'.(int) $record->id);
		}
	}
}

