<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.categories');

class extendedJCategories extends JCategories
{
	/*
	public function __construct($options)
	{
		parent::__construct($options);
	}
	*/
	
/*
	public function __construct($options = array())
	{
		$options['table'] = '#__content';
		$options['extension'] = 'com_content';
		parent::__construct($options);
	}


*/
	
	public static function getInstance($extension, $options = array())
	{
		$hash = md5($extension.serialize($options));
		
		if (isset(self::$instances[$hash])) {
			return self::$instances[$hash];
		}

		$parts = explode('.', $extension);
		$component = 'com_'.strtolower($parts[0]);
		$section = count($parts) > 1 ? $parts[1] : '';
		$classname = ucfirst(substr($component, 4)).ucfirst($section).'Categories';
		if (!class_exists($classname)) {
			$path = JPATH_SITE . '/components/' . $component . '/helpers/category.php';
			if (is_file($path)) {
				require_once $path;
			}
			else {
				return false;
			}
		}

		//self::$instances[$hash] = new $classname($options);
		parent::$instances[$hash] = new $classname($options);
		/*
		$class = parent::$instances[$hash];
		$options = parent::$instances[$hash]->_options;
		//$class = new $classname($options);
		//self::$instances[$hash] = new extendedJCategories($class->_options);
		*/
		self::$instances[$hash] = new extendedJCategories(parent::$instances[$hash]->_options);
		return self::$instances[$hash];
	}



	protected function _load($id)
	{
		$db	= JFactory::getDbo();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$extension = $this->_extension;
		// Record that has this $id has been checked
		$this->_checkedCategories[$id] = true;
		$query = $db->getQuery(true);

		// Right join with c for category
		$query->select('c.*');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
		$query->from('#__categories as c');
		$query->where('(c.extension='.$db->Quote($extension).' OR c.extension='.$db->Quote('system').')');

		if ($this->_options['access']) {
			$query->where('c.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')');
		}

		if ($this->_options['published'] == 1) {
			$query->where('c.published = 1');
		}
		$query->order('c.lft');


		// s for selected id
		if ($id!='root') {
			// Get the selected category
			$query->leftJoin('#__categories AS s ON (s.lft <= c.lft AND s.rgt >= c.rgt) OR (s.lft > c.lft AND s.rgt < c.rgt)');
			$query->where('s.id='.(int)$id);
		}
		
		/*
		$subQuery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ' .
					'ON cat.lft BETWEEN parent.lft AND parent.rgt WHERE parent.extension = ' . $db->quote($extension) .
					' AND parent.published != 1 GROUP BY cat.id) ';
		$query->leftJoin($subQuery . 'AS badcats ON badcats.id = c.id');
		$query->where('badcats.id is null');
		*/
		
		// i for item
		if (isset($this->_options['countItems']) && $this->_options['countItems'] == 1) {
			if ($this->_options['published'] == 1) {
				$query->leftJoin($db->quoteName($this->_table).' AS i ON i.'.$db->quoteName($this->_field).' = c.id AND i.'.$this->_statefield.' = 1');
			}
			else {
				$query->leftJoin($db->quoteName($this->_table).' AS i ON i.'.$db->quoteName($this->_field).' = c.id');
			}

			$query->select('COUNT(i.'.$db->quoteName($this->_key).') AS numitems');
		}

		// Group by
		$query->group('c.id');

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter()) {
			$query->where('(' . ($id!='root' ? 'c.id=s.id OR ':'') .'c.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . '))');
		}
		
		//$input = $app->input;
		//$language = $input->get('filter_language', -1,null);
		$language = PagesAndItemsHelper::getLanguageFilter();
		
		if($language != '-1')
		{
			$query->where('c.language='.$db->quote($language));
		}
		
		// Get the results
		$db->setQuery($query);
		$results = $db->loadObjectList('id');
		$childrenLoaded = false;

		if (count($results)) {
			// Foreach categories
			foreach($results as $result)
			{
				// Deal with root category
				if ($result->id == 1) {
					$result->id = 'root';
				}

				// Deal with parent_id
				if ($result->parent_id == 1) {
					$result->parent_id = 'root';
				}

				// Create the node
				if (!isset($this->_nodes[$result->id])) {
					// Create the JCategoryNode and add to _nodes
					$this->_nodes[$result->id] = new JCategoryNode($result, $this);

					// If this is not root and if the current node's parent is in the list or the current node parent is 0
					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id == 1)) {
						// Compute relationship between node and its parent - set the parent in the _nodes field
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					// If the node's parent id is not in the _nodes list and the node is not root (doesn't have parent_id == 0),
					// then remove the node from the list
					if (!(isset($this->_nodes[$result->parent_id]) || $result->parent_id == 0)) {
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded) {
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}
				}
				else if ($result->id == $id || $childrenLoaded) {
					// Create the JCategoryNode
					$this->_nodes[$result->id] = new JCategoryNode($result, $this);

					if ($result->id != 'root' && (isset($this->_nodes[$result->parent_id]) || $result->parent_id)) {
						// Compute relationship between node and its parent
						$this->_nodes[$result->id]->setParent($this->_nodes[$result->parent_id]);
					}

					if (!isset($this->_nodes[$result->parent_id])) {
						unset($this->_nodes[$result->id]);
						continue;
					}

					if ($result->id == $id || $childrenLoaded) {
						$this->_nodes[$result->id]->setAllLoaded();
						$childrenLoaded = true;
					}

				}
			}
		}
		else {
			$this->_nodes[$id] = null;
		}
	}
}