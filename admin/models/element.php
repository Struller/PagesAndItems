<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper');
jimport( 'joomla.application.component.model');

/**

 */
class PagesAndItemsModelElement extends JModel
{
	/**
	 * Content data in category array
	 *
	 * @var array
	 */
	var $_list = null;

	var $_page = null;

	/**
	 * Method to get content article data for the frontpage
	 *
	 * @since 1.5
	 */
	function getList()
	{
		$app = &JFactory::getApplication();
		//global $mainframe;

		if (!empty($this->_list))
		{
			return $this->_list;
		}

		// Initialize variables
		$db =& $this->getDBO();
		$filter	= null;

		// Get some variables from the request
		$sectionid			= JRequest::getVar( 'sectionid', -1, '', 'int' );
		$redirect			= $sectionid;
		$option				= JRequest::getCmd( 'option' );
		$filter_order		= $app->getUserStateFromRequest($option.'.articleelement.filter_order',		'filter_order',		'',	'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'.articleelement.filter_order_Dir',	'filter_order_Dir',	'',	'word');
		$catid				= $app->getUserStateFromRequest($option.'.articleelement.catid',				'catid',			0,	'int');
		$filter_authorid	= $app->getUserStateFromRequest($option.'.articleelement.filter_authorid',		'filter_authorid',	0,	'int');
		$filter_sectionid	= $app->getUserStateFromRequest($option.'.articleelement.filter_sectionid',	'filter_sectionid',	-1,	'int');
		$limit				= $app->getUserStateFromRequest('global.list.limit',					'limit', $app->getCfg('list_limit'), 'int');
		$limitstart			= $app->getUserStateFromRequest($option.'.articleelement.limitstart',			'limitstart',		0,	'int');
		$search				= $app->getUserStateFromRequest($option.'.articleelement.search',				'search',			'',	'string');
		if (strpos($search, '"') !== false)
		{
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		//$where[] = "c.state >= 0";
		$where[] = "c.state != -2";

		if (!$filter_order) {
			$filter_order = 'section_name';
		}
		$order = ' ORDER BY '. $filter_order .' '. $filter_order_Dir .', section_name, cc.name, c.ordering';
		$all = 1;

		if ($filter_sectionid >= 0) {
			$filter = ' WHERE cc.section = '.$db->Quote($filter_sectionid);
		}
		$section->title = 'All Articles';
		$section->id = 0;

		/*
		 * Add the filter specific information to the where clause
		 */
		// Section filter
		if ($filter_sectionid >= 0) {
			$where[] = 'c.sectionid = '.(int) $filter_sectionid;
		}
		// Category filter
		if ($catid > 0) {
			$where[] = 'c.catid = '.(int) $catid;
		}
		// Author filter
		if ($filter_authorid > 0) {
			$where[] = 'c.created_by = '.(int) $filter_authorid;
		}

		// Only published articles
		$where[] = 'c.state = 1';

		// Keyword filter
		if ($search) {
			$where[] = 'LOWER( c.title ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		// Build the where clause of the content record query
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');

		// Get the total number of records
		$query = 'SELECT COUNT(*)' .
				' FROM #__pi_item_index AS p' .
				' LEFT JOIN #__content AS c ON c.id = p.item_id' .
				' LEFT JOIN #__categories AS cc ON cc.id = c.catid' .
				' LEFT JOIN #__sections AS s ON s.id = c.sectionid' .
				$where;
		$db->setQuery($query);
		$total = $db->loadResult();
		// Create the pagination object
		jimport('joomla.html.pagination');
		$this->_page = new JPagination($total, $limitstart, $limit);

		// Get the articles
		$query = 'SELECT c.*, g.name AS groupname, cc.title as cctitle, u.name AS editor, f.content_id AS frontpage, s.title AS section_name, v.name AS author' .
				' FROM #__pi_item_index AS p' .
				' LEFT JOIN #__content AS c ON c.id = p.item_id' .
				' LEFT JOIN #__categories AS cc ON cc.id = c.catid' .
				' LEFT JOIN #__sections AS s ON s.id = c.sectionid' .
				' LEFT JOIN #__groups AS g ON g.id = c.access' .
				' LEFT JOIN #__users AS u ON u.id = c.checked_out' .
				' LEFT JOIN #__users AS v ON v.id = c.created_by' .
				' LEFT JOIN #__content_frontpage AS f ON f.content_id = c.id' .
				$where .
				$order;
		$db->setQuery($query, $this->_page->limitstart, $this->_page->limit);
		$this->_list = $db->loadObjectList();

		// If there is a db query error, throw a HTTP 500 and exit
		if ($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		return $this->_list;
	}

	function getPagination()
	{
		if (is_null($this->_list) || is_null($this->_page))
		{
			$this->getList();
		}
		return $this->_page;
	}
}
?>
