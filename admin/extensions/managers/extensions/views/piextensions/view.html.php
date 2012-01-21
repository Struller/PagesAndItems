<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
/**
 * HTML View class for the

 */

class PagesAndItemsViewPiextensions extends PagesAndItemsViewDefault
{
	/*
	protected $items;
	protected $pagination;
	protected $state;
	*/

	function display( $tpl = null )
	{
		//PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MANAGEEXTENSIONS').'</small>');
		PagesAndItemsHelper::addTitle(' :: <small>'.JText::_('COM_PAGESANDITEMS_MANAGERS').': ['.JText::_('COM_PAGESANDITEMS_MANAGEEXTENSIONS').']</small>');
		//$path = $path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'..')));
		//JHTML::stylesheet('pagesanditems_icons.css', $path.'/css/');



		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		$this->assignRef( 'joomlaVersion',$joomlaVersion);

		$app = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$db =& JFactory::getDBO();

		$client = JRequest::getWord( 'filter_client', 'both' );

		$filter_order	= $app->getUserStateFromRequest( "$option.$client.filter_order",		'filter_order',		'p.type,p.folder','cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( "$option.$client.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$filter_state	= $app->getUserStateFromRequest( "$option.$client.filter_state",		'filter_state',		'',			'word' );
		$filter_type	= $app->getUserStateFromRequest( "$option.$client.filter_type", 		'filter_type',		1,			'cmd' );

		$filter_language	= $app->getUserStateFromRequest( "$option.$client.filter_language", 	'filter_language',	1,			'cmd' );

		$filter_folder	= $app->getUserStateFromRequest( "$option.$client.filter_folder", 	'filter_folder',		1,			'cmd' );
		$search		= $app->getUserStateFromRequest( "$option.$client.search",			'search',			'',			'string' );
		if (strpos($search, '"') !== false) {
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);

		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart	= $app->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$where = '';

		if ($client == 'admin')
		{
			$where[] = 'p.client_id = 1';
			$client_id = 1;
		}
		elseif ($client == 'site')
		{
			$where[] = 'p.client_id = 0';
			$client_id = 0;
		}
		else
		{
			$where[] = '(p.client_id = 0 OR p.client_id = 1)';
			$client_id = 2;
		}

		// used by filter
		if ( $filter_type != 1 )
		{
			$where[] = 'p.type = '.$db->Quote($filter_type);
			/*
			we will show in type language only p.type = p.name ?
			*/
			if($filter_type == 'language' && $filter_language != 1 )
			{
				$where[] = 'p.element = '.$db->Quote($filter_language);
				//$where[] = 'p.type = p.name';

			}
		}
		else
		{
			//without type == language
			/*
			we will show only if we select 'language' ?

			*/
			$where[] = 'p.type <> '.$db->Quote('language');




		}
		if ( $filter_folder != 1 )
		{
			$where[] = 'p.folder = '.$db->Quote($filter_folder);
		}
		if ( $search )
		{
			$where[] = 'LOWER( p.name ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				//$where[] = 'p.published = 1';
				$where[] = 'p.enabled = 1';
			}
			else if ($filter_state == 'U' )
			{
				$where[] = 'p.enabled = 0';
			}
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		// sanitize $filter_order
		if (!in_array($filter_order, array('p.name', 'p.enabled', 'p.ordering', 'groupname', 'p.folder', 'p.element', 'p.extension_id')))
		{
			$filter_order = 'p.type,p.folder';
		}

		if (!in_array(strtoupper($filter_order_Dir), array('ASC', 'DESC'))) {
			$filter_order_Dir = '';
		}



		if ($filter_order == 'p.ordering')
		{
			$orderby = ' ORDER BY p.type, p.folder, p.ordering '. $filter_order_Dir;
		}
		else
		{
			if($filter_type == 'language')
			{
				$orderby = ' ORDER BY p.element,'. $filter_order .' '. $filter_order_Dir ; //.' ASC';
			}
			else
			{
				$orderby = ' ORDER BY '. $filter_order .' '. $filter_order_Dir; // .', p.ordering ASC';
			}
		}

		// get the total number of records
		$query = 'SELECT COUNT(*)'
			. ' FROM #__pi_extensions AS p'
			. $where
			;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );
/*

if ($group != '' && in_array($type, array('plugin', 'library', ''))) {

			$query->where('folder=' . $this->_db->Quote($group == '*' ? '' : $group));
		}


*/
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$query = 'SELECT p.*, u.name AS editor, g.name AS groupname ';
		}
		else
		{
			$query = 'SELECT p.*, u.name AS editor ';//, g.title AS groupname ';
		}
		$query .= 'FROM #__pi_extensions AS p ';
		$query .= 'LEFT JOIN #__users AS u ON u.id = p.checked_out ';
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
		{
			$query .= 'LEFT JOIN #__groups AS g ON g.id = p.access ';
		}
		else
		{
			//$query .= 'LEFT JOIN #__usergroups AS g ON g.id = p.access ';
			//$query .= 'LEFT JOIN #__usergroup_map AS ugm ON ugm.group_id = g.id AND u.id=ugm.user_id ';
		}
		$query .= $where.' ';
		$query .= 'GROUP BY p.extension_id';
		$query .= $orderby;

		$db->setQuery( $query, $pagination->limitstart, $pagination->limit );
		$rows = $db->loadObjectList();



		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		foreach($rows as $row)
		{
			//$data = false;
			//$row->published = $row->enabled;
			/*
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ginkgo'.DS.'includes'.DS.'plugin'.DS.'helper.php');
			//$plugin = & GinkgoPluginHelper::getPlugin('_base' , $row->element);
			//$plugins = & GinkgoPluginHelper::getPlugins('_base');
			$plugin = & GinkgoPluginHelper::importPlugin( $row->folder,'_base', $row->name);
			$dispatcher = &JDispatcher::getInstance();
			$dispatcher->trigger('onGetManifestCache',array (& $data, $row->extension_id));
			*/
			$data = null;
			if(isset($row->manifest_cache))
			$data = unserialize($row->manifest_cache);

			$row->description = '';
			$row->creationdate = false;
			$row->author = false;
			$row->copyright = false;
			$row->authorEmail = false;
			$row->authorUrl = false;
			//$row->version = false;
			if($data)
			{
				$row->description = $data['description'];
				$row->creationdate = $data['creationdate'];
				$row->author = $data['author'];
				$row->copyright = $data['copyright'];
				$row->authorEmail = $data['authorEmail'];
				$row->authorUrl = $data['authorUrl'];
				//$row->version = $data['version'];
			}
			//TODO check if the files are installed
			$row->installed = true;
			$path = JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions'.DS;
			$path .= $row->type.'s'.DS;
			if($row->folder != '')
			{
				$path .= $row->folder.DS;
			}
			if(!file_exists($path.$row->element.DS.$row->element.'.php') && $row->type != 'language')
			{
				$row->installed = false;
			}
			else
			{
				//load language
				if($row->folder)
				{
					//en-GB.pi_extension_piplugin_indicator_codemirror.ini
					$extension = 'pi_extension_'.$row->type.'_'.$row->folder.'_'.$row->element;
				}
				else
				{
					$extension = 'pi_extension_'.$row->type.'_'.$row->element;
				}
				$source = $path.DS.$row->element;
				$lang = JFactory::getLanguage();
				$lang->load($extension . '.sys', $source, null, false, false)
					||	$lang->load($extension . '.sys', JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions', null, false, false)
					||	$lang->load($extension . '.sys', $source, $lang->getDefault(), false, false)
					||	$lang->load($extension . '.sys', JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions', $lang->getDefault(), false, false)||
				$lang->load($extension, $source, null, false, false)
					||	$lang->load($extension, JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions', null, false, false)
					||	$lang->load($extension, $source, $lang->getDefault(), false, false)
					||	$lang->load($extension, JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions', $lang->getDefault(), false, false);
			}

		}
		// get list of Positions for dropdown filter
		$query = 'SELECT type AS value, type AS text'
			. ' FROM #__pi_extensions'


			//. ' WHERE client_id = '.(int) $client_id
			. ' GROUP BY type'
			. ' ORDER BY type'
			;

		$types[] = JHTML::_('select.option',  1, '- '. JText::_( 'COM_PAGESANDITEMS_EXTENSIONS_SELECT_TYPE' ) .' -' );
		$db->setQuery( $query );
		$types 			= array_merge( $types, $db->loadObjectList() );
		$lists['type']	= JHTML::_('select.genericlist', $types, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_type );



		// get the total number of records
		$query = 'SELECT *'
			. ' FROM #__pi_extensions'
			. ' WHERE type=\'language\' AND client_id=0'
			;
		$db->setQuery( $query );
		$totalSite = $db->loadResult();
		if($totalSite && $filter_type == 'language')
		{
			$options[] = JHtml::_('select.option', 'both', '- '.JText::_('COM_PAGESANDITEMS_LIST_SELECT_CLIENT').' -');
			$options[] = JHtml::_('select.option', 'site', JText::sprintf('JSITE'));
			$options[] = JHtml::_('select.option', 'admin', JText::sprintf('JADMINISTRATOR'));
			$lists['client'] = JHtml::_('select.genericlist', $options, 'filter_client', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $client);
		}
		else
		{
			$lists['client'] = '<input type="hidden" name="filter_client" value="both">';
		}

		if($filter_type == 'language')
		{
			$query = 'SELECT element AS value, element AS text'
			. ' FROM #__pi_extensions'
			. ' WHERE type = '.$db->Quote('language')
			. ' GROUP BY element'
			. ' ORDER BY element'
			;

			$languages[] = JHTML::_('select.option',  1, '- '. JText::_( 'COM_PAGESANDITEMS_EXTENSIONS_SELECT_LANGUAGE' ) .' -' );
			$db->setQuery( $query );
			$languages = array_merge( $languages, $db->loadObjectList() );
			$lists['language']	= JHTML::_('select.genericlist', $languages, 'filter_language', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_language );
		}
		else
		{
			$lists['language'] = '<input type="hidden" name="filter_language" value="1">';
		}

		// get list of Positions for dropdown filter
		$query = 'SELECT folder AS value, folder AS text'
			. ' FROM #__pi_extensions'
			//. ' WHERE client_id = '.(int) $client_id
			//. ' AND folder IS NOT NULL '
			. ' WHERE folder IS NOT NULL '

			. ' AND folder <> \'\' '
			. ' GROUP BY folder'
			. ' ORDER BY folder'
			;

		$folders[] = JHTML::_('select.option',  1, '- '. JText::_( 'COM_PAGESANDITEMS_EXTENSIONS_SELECT_FOLDER' ) .' -' );
		$db->setQuery( $query );
		$folders 			= array_merge( $folders, $db->loadObjectList() );
		$lists['folder']	= JHTML::_('select.genericlist',   $folders, 'filter_folder', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_folder );


		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state ,'JENABLED','JDISABLED');


		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search']= $search;

		$this->assign('client',		$client);


		$this->assignRef('filter_type',$filter_type);
		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('items',		$rows);
		$this->assignRef('pagination',	$pagination);

		parent::display($tpl);
		$this->addToolbar();
		//add here to override pagesanditems2.css
		$mediapath = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../../media/')));
		JHTML::stylesheet('manager_extensions.css', $mediapath.'/css/');
	}

	protected function addToolbar()
	{
		JToolBarHelper::custom('piextensions.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('piextensions.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'piextensions.remove','JTOOLBAR_UNINSTALL');

		//JToolBarHelper::divider();
		//JToolBarHelper::custom('piextensions.refresh', 'refresh', 'refresh','JTOOLBAR_REFRESH_CACHE',true);

		JToolBarHelper::divider();
		JToolBarHelper::cancel('managers.cancel');//, 'COM_PAGESANDITEMS_CANCEL');
	}



	function loadItem($item=null)
	{
		//$item =& $this->items[$index];
		//JHtml::_('jgrid.published', $item->enabled, $i, 'plugins.', $canChange);
		$item->img		= $item->enabled ? 'admin/tick.png' : 'admin/publish_x.png';
		//$item->task		= $item->enabled ? 'manage.disable' : 'manage.enable';
		$item->task		= $item->enabled ? 'piextensions.unpublish' : 'piextensions.publish';
		$item->alt		= $item->enabled ? JText::_('JLIB_HTML_PUBLISH_ITEM') : JText::_('JLIB_HTML_UNPUBLISH_ITEM');
		$item->action	= $item->enabled ? JText::_('JUNPUBLISHED') : JText::_('JPUBLISHED');
		/*
					1	=> array('unpublish',	'JPUBLISHED',	'JLIB_HTML_UNPUBLISH_ITEM',	'JPUBLISHED',	false,	'publish',		'publish'),
			0	=> array('publish',		'JUNPUBLISHED',	'JLIB_HTML_PUBLISH_ITEM',	'JUNPUBLISHED',	false,	'unpublish',	'unpublish'),
		*/


		/*
		if ($item->protected)
		{
			$item->cbd		= 'disabled';
			$item->style	= 'style="color:#999999;"';
		}
		else
		{
		*/
			$item->cbd		= null;
			$item->style	= null;
		//}
		$item->author_info = @$item->authorEmail .'<br />'. @$item->authorUrl;
		return $item;
		//$this->assignRef('item', $item);
	}

}

