<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.controller' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');

/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerItemType extends PagesAndItemsController
{
	function __construct( $config = array())
	{
		parent::__construct($config);

	}

	function getDispatcher($item_type)
	{
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		$extensions = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);

		$dispatcher = &JDispatcher::getInstance();
		return $dispatcher;
	}

	function config_itemtype_save()
	{
		//here we need the model base for future configcustomitemtype
		//$model = &$this->getModel('Base','PagesAndItemsModel');
		$item_type = JRequest::getVar('item_type', '', 'post');
		$dispatcher = $this->getDispatcher($item_type);
		$msg = '';
		$dispatcher->trigger('onItemtypeConfig_save',array(&$msg,$item_type));
		$msg = '';
		//redirect
		if(JRequest::getVar('sub_task', '')=='apply')
		{
			$url = 'index.php?option=com_pagesanditems&view=config_itemtype&item_type='.$item_type;
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems&view=config&tab=itemtypes';
		}
		$this->setRedirect(JRoute::_($url, false), $msg);
		//$model->redirect_to_url( $url, $msg);

	}

}
