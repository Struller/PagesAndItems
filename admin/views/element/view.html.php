<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML Article Element View class for the Content component

 */
class PagesAndItemsViewElement extends JView
{
	function display($tpl = null)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_content'.DS.'helper.php');
		global $mainframe;

		// Initialize variables
		$db			= &JFactory::getDBO();
		$nullDate	= $db->getNullDate();

		$document	= & JFactory::getDocument();
		$document->setTitle(JText::_('Article Selection'));

		JHTML::_('behavior.modal');

		$template = $mainframe->getTemplate();
		$document->addStyleSheet("templates/$template/css/general.css");

		$limitstart = JRequest::getVar('limitstart', '0', '', 'int');

		$lists = $this->_getLists();
		//Ordering allowed ?
		$ordering = ($lists['order'] == 'section_name' && $lists['order_Dir'] == 'ASC');

		$model = &$this->getModel('element');
		$rows = $model->getList();
		$page = $model->getPagination();

		JHTML::_('behavior.tooltip');
		$this->assignRef('lists', $lists);
		$this->assignRef('rows', $rows);
		$this->assignRef('page', $page);
		parent::display($tpl);
	}

	function _getLists()
	{
		//global $mainframe;
		$app = &JFactory::getApplication();
		// Initialize variables
		$db		= &JFactory::getDBO();

		// Get some variables from the request
		$sectionid			= JRequest::getVar( 'sectionid', -1, '', 'int' );
		$redirect			= $sectionid;
		$option				= JRequest::getCmd( 'option' );
		$filter_order		= $app->getUserStateFromRequest($option.'.articleelement.filter_order',		'filter_order',		'',	'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'.articleelement.filter_order_Dir',	'filter_order_Dir',	'',	'word');
		$filter_state		= $app->getUserStateFromRequest($option.'.articleelement.filter_state',		'filter_state',		'',	'word');
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

		// get list of categories for dropdown filter
		$filter = ($filter_sectionid >= 0) ? ' WHERE cc.section = '.$db->Quote($filter_sectionid) : '';

		// get list of categories for dropdown filter
		$query = 'SELECT cc.id AS value, cc.title AS text, section' .
				' FROM #__categories AS cc' .
				' INNER JOIN #__sections AS s ON s.id = cc.section' .
				$filter .
				' ORDER BY s.ordering, cc.ordering';

		$lists['catid'] = ContentHelper::filterCategory($query, $catid);

		// get list of sections for dropdown filter
		$javascript = 'onchange="document.adminForm.submit();"';
		$lists['sectionid'] = JHTML::_('list.section', 'filter_sectionid', $filter_sectionid, $javascript);

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;

		return $lists;
	}
}
