<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
/**
*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'item'.DS.'view.html.php');



/**
 * HTML View class for the 

 */

class PagesAndItemsViewExtension extends PagesAndItemsViewDefault //PagesAndItemsViewItem 
{
	function display( $tpl = null )
	{
		if ($model = &$this->getModel('Page')) 
		{
			$pageTree = $model->getPages();
			$this->assignRef( 'pageTree',$pageTree);

			$menuItemsTypes = $model->menuItemsTypes;
			$this->assignRef( 'menuItemsTypes',$menuItemsTypes);
			$this->assignRef( 'model',$model);
			JHTML::script('dtree.js', 'administrator/components/com_pagesanditems/javascript/',false);
		}
		elseif ($model = &$this->getModel('Base')) 
		{
			$this->assignRef( 'model',$model);
		
		}
		if(!$model->isAdmin)
		{
			if($model->joomlaVersion < '1.6')
			{
				$query = 'SELECT template'
				. ' FROM #__templates_menu'
				. ' WHERE client_id = 1'
				. ' AND menuid = 0'
				;
				$model->db->setQuery($query);
				$template = $model->db->loadResult();
				$iconCss = JURI::root(true).'/administrator/templates/'.$template.'/css/icon.css';
				JHTML::stylesheet('general.css', 'administrator/templates/'.$template.'/css/');
			}
			else
			{
				$query = 'SELECT template'
				. ' FROM #__template_styles'
				. ' WHERE client_id = 1'
				. ' AND home = 1'
				;
				$model->db->setQuery($query);
				$template = $model->db->loadResult();
				if($template)
				{
						//dump('X');
						//$iconCss = JURI::root().'/administrator/templates/'.$template.'/css/icon.css';
					JHTML::stylesheet('template.css', 'administrator/templates/'.$template.'/css/');
				}
			}
		}
		
		//JHTML::script('overlib_mini.js', 'includes/js/',false);
		parent::display($tpl);
	}
}