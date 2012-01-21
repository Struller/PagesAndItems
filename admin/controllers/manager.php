<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'extension.php');
/**
 * @package		PagesAndItems
*/
class PagesAndItemsControllerManager extends PagesAndItemsControllerExtension
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	function __construct( $config = array())
	{
		parent::__construct($config);
	}

	function display($tpl = null)
	{
		$extensionTask = JRequest::getVar('extensionTask',null);
		$extension = JRequest::getVar('extension', ''); //is the extensionName
		$extensionFolder = JRequest::getVar('extensionFolder', '');
		$extensionType = JRequest::getVar('extensionType', '');
		JRequest::setVar('view', JRequest::getVar('view', 'managers'));
		JRequest::setVar( 'layout', 'manager' );
		/*
		need the extension an own model
		trigger this?
		
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
		$typeName = 'ExtensionManagerHelper';
		$typeName::importExtension(null, $extension,true,null,true);
		
		$dispatcher = &JDispatcher::getInstance();
		$models = array();
		$dispatcher->trigger('onGetModelName', array ( &$models));
		
		if(is_array($models))
		{
			for($mn = 0; $mn < count($models); $mn++)
			{
				//we need $model[$mn] as unique
				if($model[$mn] = &$this->getModel($models[$mn],'PagesAndItemsModel'))
				{
					// Push the model into the view (not as default)
					$view->setModel($model[$mn], false);
				}
			}
		}
		
		
		
		*/
		
		
		parent::display($tpl);
	}
}