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

jimport( 'joomla.application.component.model' );

$version = new JVersion();
$joomlaVersion = $version->getShortVersion();
if($joomlaVersion < '1.6')
{
	//require_once(dirname(__FILE__).DS.'../helpers'.DS.'menus.php');
	require_once(dirname(__FILE__).DS.'base'.DS.'menutype15.php');
}
elseif($joomlaVersion < '2.5')
{
	require_once(dirname(__FILE__).DS.'base'.DS.'menutype16.php');
}
else
{
	require_once(dirname(__FILE__).DS.'base'.DS.'menutype25.php');
}

/**

 */

class PagesAndItemsModelMenutypes extends PagesAndItemsModelPiMenutype
{


	public function setState($property, $value=null)
	{
		return null;
		//return $this->state->set($property, $value);
	}

	/**
	 * Gets a standard form of a link for lookups.
	 *
	 * @param	mixed	A link string or array of request variables.
	 *
	 * @return	mixed	A link in standard option-view-layout form, or false if the supplied response is invalid.
	 */
	function buildPageType($link)
	{
		if (empty($link))
		{
			return false;
		}
		// Check if the link is in the form of index.php?...
		if (is_string($link))
		{
			$args = array();
			if (strpos($link, 'index.php') === 0)
			{
				parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);
			}
			else {
				parse_str($link, $args);
			}
			$link = $args;
		}
		$filter = array('option', 'view', 'layout');
		// Only take the option, view and layout parts.
		foreach ($link as $name => $value)
		{
			if (!in_array($name, $filter))
			{
				// Remove the variables we want to ignore.
				unset($link[$name]);
			}
			if($name == 'option')
			{
				$link[$name] = str_replace('com_','',$link[$name]);
			}
		}

		return implode('_',$link);
		//ksort($request);

		//return 'index.php?'.http_build_query($request,'','&');

	}

	public static function getLinkKey($request)
	{
		if (empty($request)) {
			return false;
		}

		// Check if the link is in the form of index.php?...
		if (is_string($request))
		{
			$args = array();
			if (strpos($request, 'index.php') === 0) {
				parse_str(parse_url(htmlspecialchars_decode($request), PHP_URL_QUERY), $args);
			}
			else {
				parse_str($request, $args);
			}
			$request = $args;
		}

		// Only take the option, view and layout parts.
		foreach ($request as $name => $value)
		{
			if (!in_array($name, self::$_filter))
			{
				// Remove the variables we want to ignore.
				unset($request[$name]);
			}
		}

		//ksort($request);

		return 'index.php?'.http_build_query($request,'','&');
	}

	function getId($link)
	{
		if (empty($link))
		{
			return false;
		}
		// Check if the link is in the form of index.php?...
		if (is_string($link))
		{
			$args = array();
			if (strpos($link, 'index.php') === 0)
			{
				parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);
			}
			else {
				parse_str($link, $args);
			}
			$link = $args;
		}
		//$filter = array('option', 'view', 'layout');
		// Only take the option, view and layout parts.
		foreach ($link as $name => $value)
		{
			/*
			if (!in_array($name, $filter))
			{
				// Remove the variables we want to ignore.
				unset($link[$name]);
			}
			*/
			if($name == 'id')
			{
				return $link[$name];
				//$link[$name] = str_replace('com_','',$link[$name]);
			}
		}

		return null; //implode('_',$link);
		//ksort($request);

		//return 'index.php?'.http_build_query($request,'','&');

	}


	/*
	use from extensions/htmls/page_underlayingpages/menuitemtypeselect.php
	*/
	function getTypeListItem($recordId = null,$link)
	{

		// Initialise variables.
		$html		= array();
		$types	= $this->_getTypeOptions();
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		/*
		TODO get the right type

		split $link

		foreach ($types as $name => $list)
		{
			foreach ($list as $item)
			{
				$item->request;

			}
		}



		*/
		/*
		$recordId	= (int) $this->form->getValue('id');
		*/

		$html[] = '<h2 class="modal-title">'.JText::_('COM_MENUS_TYPE_CHOOSE').'</h2>';
		$html[] = '<ul class="menu_types">';

		foreach ($types as $name => $list)
		{
			$html[] = '<li>';
			$html[] = '<dl class="menu_type">';
			$html[] = '	<dt>'.JText::_($name).'</dt>';
			$html[] = '	<dd>';
			$html[] = '		<ul>';
			foreach ($list as $item)
			{
				$html[] = '			<li>';

				$link = '				<a class="choose_type" href="#" onclick="javascript:';
				if($joomlaVersion >= '1.6')
				{
					$link .= 'Joomla.';
				}
				$link .= 'submitbutton(\'page.setType\', \''.
											base64_encode(json_encode(array('id' => $recordId, 'title' => $item->title, 'request' => $item->request, 'type'=>'component'))).'\')"' .
											' title="'.JText::_($item->description).'">'.
											JText::_($item->title).'</a>';
				$html[] = $link;
				$html[] = '			</li>';
			}

			$html[] = '		</ul>';
			$html[] = '	</dd>';
			$html[] = '</dl>';
			$html[] = '</li>';
		}

		$html[] = '<li>';
		$html[] = '<dl class="menu_type">';
		$html[] = '	<dt>'.JText::_('COM_MENUS_TYPE_SYSTEM').'</dt>';
		$html[] = '	<dd>';
		$html[] = '		<ul>';
		$html[] = '			<li>';
		$link = '				<a class="choose_type" href="#" onclick="javascript:';
				if($joomlaVersion >= '1.6')
				{
					$link .= 'Joomla.';
				}
				$link .= 'submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('id' => $recordId, 'title'=>'url'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_EXTERNAL_URL_DESC').'">'.
									JText::_('COM_MENUS_TYPE_EXTERNAL_URL').'</a>';
		$html[] = $link;
		$html[] = '			</li>';
		$html[] = '			<li>';
		$link = '				<a class="choose_type" href="#" onclick="javascript:';
				if($joomlaVersion >= '1.6')
				{
					$link .= 'Joomla.';
				}
				$link .= 'submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('id' => $recordId, 'title'=>'alias'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_ALIAS_DESC').'">'.
									JText::_('COM_MENUS_TYPE_ALIAS').'</a>';
		$html[] = $link;
		$html[] = '			</li>';
		$html[] = '			<li>';
		$link = '				<a class="choose_type" href="#" onclick="javascript:';
				if($joomlaVersion >= '1.6')
				{
					$link .= 'Joomla.';
				}
				$link .= 'submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('id' => $recordId, 'title'=>'separator'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_SEPARATOR_DESC').'">'.
									JText::_('COM_MENUS_TYPE_SEPARATOR').'</a>';
		$html[] = $link;
		$html[] = '			</li>';
		$html[] = '		</ul>';
		$html[] = '	</dd>';
		$html[] = '</dl>';
		$html[] = '</li>';
		$html[] = '</ul>';

		return implode("\n", $html);
	}


	/*

	use from extensions/htmls/page_underlayingpages/menuitemtypeselect.php
	*/
	function getTypeListItems($recordId = null, $pageId = null, $current_menutype = null)
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		// Initialise variables.
		$html		= array();
		$types		= $this->_getTypeOptions();

		//$components = $this->getTypeListComponents($types);
		/*
		$recordId	= (int) $this->form->getValue('id');
		*/

	//	$html[] = '<h2 class="modal-title">'.JText::_('COM_MENUS_TYPE_CHOOSE').'</h2>';
		$html[] = '<ul class="menu_types">';

		foreach ($types as $name => $list)
		{
			$html[] = '<li>';
			$html[] = '<dl class="menu_type">';
			$html[] = '	<dt>'.JText::_($name).'</dt>';
			$html[] = '	<dd>';
			$html[] = '		<ul>';
			foreach ($list as $item)
			{
				$html[] = '			<li>';
				$html[] = '				<a class="choose_type" href="#" ';
								$request = $item->request;
								$request['option'] = str_replace('com_','',$request['option']);
								$pageType = implode('_',$request);
								$html[] = 'onclick="';
								$html[] ='window.parent.document.getElementById(\'pageType\').value = \''.$pageType.'\'; ';
								$html[] ='window.parent.document.getElementById(\'type\').value = \'component\'; ';
								$html[] = 'window.parent.document.getElementById(\'pageTypeType\').value = \''.
											base64_encode(json_encode(array('id' => $recordId, 'title' => $item->title, 'request' => $item->request, 'type'=>'component'))).'\';';
								if($joomlaVersion >= '1.6')
								{
									$html[] ='window.parent.Joomla.submitbutton(\'newMenuItem\'); ';
								}
								else
								{
									$html[] ='window.parent.submitbutton(\'newMenuItem\'); ';
								}
								$html[] ='window.parent.document.getElementById(\'sbox-window\').close();" ';
								$html[] = ' title="'.JText::_($item->description).'"' ;

								$html[] = '>';
								$html[] = JText::_($item->title);
								$html[] = '</a>';
				$html[] = '			</li>';
			}

			$html[] = '		</ul>';
			$html[] = '	</dd>';
			$html[] = '</dl>';
			$html[] = '</li>';
		}
		$extension = 'com_menus';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		
		$html[] = '<li>';
		$html[] = '<dl class="menu_type">';
		$html[] = '	<dt>'.JText::_('COM_MENUS_TYPE_SYSTEM').'</dt>';
		$html[] = '	<dd>';
		$html[] = '		<ul>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" ';
								$html[] = 'onclick="';
								$html[] ='window.parent.document.getElementById(\'pageType\').value = \'url\'; ';
								$html[] ='window.parent.document.getElementById(\'type\').value = \'url\'; ';
								$html[] = 'window.parent.document.getElementById(\'pageTypeType\').value = \''.
											base64_encode(json_encode(array('id' => $recordId, 'title' =>JText::_('COM_MENUS_TYPE_EXTERNAL_URL'), 'request' => array(), 'type'=>'url'))).'\';';
								if($joomlaVersion >= '1.6')
								{
									$html[] ='window.parent.Joomla.submitbutton(\'newMenuItem\'); ';
								}
								else
								{
									$html[] ='window.parent.submitbutton(\'newMenuItem\'); ';
								}
								$html[] ='window.parent.document.getElementById(\'sbox-window\').close();" ';
								$html[] = ' title="'.JText::_('COM_MENUS_TYPE_EXTERNAL_URL_DESC').'"' ;
								$html[] = '>';
								$html[] = JText::_('COM_MENUS_TYPE_EXTERNAL_URL');
								$html[] = '</a>';
		
		
		$html[] = '			</li>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" ';
								$html[] = 'onclick="';
								$html[] ='window.parent.document.getElementById(\'pageType\').value = \'alias\'; ';
								$html[] ='window.parent.document.getElementById(\'type\').value = \'alias\'; ';
								$html[] = 'window.parent.document.getElementById(\'pageTypeType\').value = \''.
											base64_encode(json_encode(array('id' => $recordId, 'title' =>JText::_('COM_MENUS_TYPE_ALIAS'), 'request' => array(), 'type'=>'alias'))).'\';';
								if($joomlaVersion >= '1.6')
								{
									$html[] ='window.parent.Joomla.submitbutton(\'newMenuItem\'); ';
								}
								else
								{
									$html[] ='window.parent.submitbutton(\'newMenuItem\'); ';
								}
								$html[] ='window.parent.document.getElementById(\'sbox-window\').close();" ';
								$html[] = ' title="'.JText::_('COM_MENUS_TYPE_ALIAS_DESC').'"' ;
								$html[] = '>';
								$html[] = JText::_('COM_MENUS_TYPE_ALIAS');
								$html[] = '</a>';
		$html[] = '			</li>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" ';
								$html[] = 'onclick="';
								$html[] ='window.parent.document.getElementById(\'pageType\').value = \'separator\'; ';
								$html[] ='window.parent.document.getElementById(\'type\').value = \'separator\'; ';
								$html[] = 'window.parent.document.getElementById(\'pageTypeType\').value = \''.
											base64_encode(json_encode(array('id' => $recordId, 'title' =>JText::_('COM_MENUS_TYPE_SEPARATOR'), 'request' => array(), 'type'=>'separator'))).'\';';
								if($joomlaVersion >= '1.6')
								{
									$html[] ='window.parent.Joomla.submitbutton(\'newMenuItem\'); ';
								}
								else
								{
									$html[] ='window.parent.submitbutton(\'newMenuItem\'); ';
								}
								$html[] ='window.parent.document.getElementById(\'sbox-window\').close();" ';
								$html[] = ' title="'.JText::_('COM_MENUS_TYPE_SEPARATOR_DESC').'"' ;
								$html[] = '>';
								$html[] = JText::_('COM_MENUS_TYPE_SEPARATOR');
								$html[] = '</a>';
		$html[] = '			</li>';
		$html[] = '		</ul>';
		$html[] = '	</dd>';
		$html[] = '</dl>';
		$html[] = '</li>';
		
		$html[] = '</ul>';

		return implode("\n", $html);
	}

	/*
	use from view=page
	*/
	function getTypeListComponents($types = null)
	{
		// Initialise variables.
		$html		= array();
		if(!$types)
		{
			$types		= $this->_getTypeOptions();
		}

		/*
		$recordId	= (int) $this->form->getValue('id');
		*/
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$pagetypes = ExtensionHelper::importExtension('pagetype',null, null,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'pagetypehelper.php');
		$pagetypes = ExtensionPagetypeHelper::importExtension(null, null,true,null,true);
		$dispatcher = &JDispatcher::getInstance();

		$components = array();


		//$model = new PagesAndItemsModelBase();
		/*
		*************
		* separator *
		*************
		*/
		$pageType = 'separator';
		$name = null;
		$name->pageType = $pageType;
		$results = null;
		$icons = null;
		//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,$this->controller->dirIcons,null));
		//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,$model->getDirIcons(),null));
		$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,PagesAndItemsHelper::getDirIcons(),null));
		//
		$name->icons = $icons;
		$components[$pageType] = $name;
		//$components->$pageType = $name;


		/*
		*************************
		* menulink in J1.6 alias*
		*************************
		*/
		//$joomlaVersion = $model->getJoomlaVersion();
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$pageType = 'menulink';
		}
		else
		{
			$pageType = 'alias';
		}

		$name = null;
		$name->pageType = $pageType;
		$results = null;
		$icons = null;
		//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,$this->controller->dirIcons,null));
		//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,$model->getDirIcons(),null));
		$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,PagesAndItemsHelper::getDirIcons(),null));
		$name->icons = $icons;
		$components[$pageType] = $name;
		//$components->$pageType = $name;

		/*
		*******
		* url *
		*******
		*/
		$pageType = 'url';
		$name = null;
		$name->pageType = $pageType;
		$results = null;
		$icons = null;
		//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,$this->controller->dirIcons,null));
		//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,$model->getDirIcons(),null));
		$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,PagesAndItemsHelper::getDirIcons(),null));
		$name->icons = $icons;
		$components[$pageType] = $name;
		//$components->$pageType = $name;

		/*
		***************************
		* not_installed_no_access *
		***************************
		*/
		$pageType = 'not_installed_no_access';
		$name = null;
		$name->pageType = $pageType;
		$results = null;
		$icons = null;
		//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',$this->controller->dirIcons,null));
		//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',$model->getDirIcons(),null));
		$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',PagesAndItemsHelper::getDirIcons(),null));
		$name->icons = $icons;
		$components[$pageType] = $name;
		//$components->$pageType = $name;
		foreach ($types as $name => $list)
		{
			foreach ($list as $item)
			{

				$component = array();
				$option = null;
				$section = null;
				//$id = null;
				foreach($item->request as $key => $value)
				{
					if($key == 'option')
					{
						$option = $value;
						$value = str_replace('com_','',$value);
					}
					if($key == 'view')
					{
						$section = $value;

					}
					/*
					if($key == 'id')
					{
						$id = $value;
					}
					*/
					$component[] = $value;
				}
				$option = $section ? $option.'.'.$section : $option;
				$pageType = null; //object();
				$pageType = implode('_',$component);
				/*
				and if id
				only if pageType content_article
				add the itemRequest?

				ore id?

				if($id &&  $pageType == 'content_article')
				{

				}

				*/
				$name = null;
				$icons = null;
				//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,$this->controller->dirIcons,$option));
				//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,$model->getDirIcons(),$option));


				//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,COM_PAGESANDITEMS_DIR_ICONS,null));
				$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,$pageType,PagesAndItemsHelper::getDirIcons(),$option));
				if(!$results || !in_array(true,$results))
				{
					//we have no pagetype with this event we will load pagetype component
					//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',$this->controller->dirIcons,$option));
					//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',$model->getDirIcons(),$option));

					//$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',COM_PAGESANDITEMS_DIR_ICONS,null));
					$results = $dispatcher->trigger('onGetPageTypeIcons', array(&$icons,'component',PagesAndItemsHelper::getDirIcons(),$option)); //
				}
				//$return->components->$name->icons = $icons;
				//$return->$name->icons = $icons;
				//$name['icons'] = $icons;
				//$name['pageType'] = $pageType;
				$name->icons = $icons;
				$name->pageType = $pageType;
				//$name->id = $id;

				$components[$pageType] = $name;
				//$components->$pageType = $name;
				//$components[] = $name;
				//$components[] = array($pageType,$name);
			}
		}

		return($components);
		/*



		$html[] = '<h2 class="modal-title">'.JText::_('COM_MENUS_TYPE_CHOOSE').'</h2>';
		$html[] = '<ul class="menu_types">';

		foreach ($types as $name => $list)
		{
			$html[] = '<li>';
			$html[] = '<dl class="menu_type">';
			$html[] = '	<dt>'.JText::_($name).'</dt>';
			$html[] = '	<dd>';
			$html[] = '		<ul>';
			foreach ($list as $item)
			{
				$html[] = '			<li>';
				$html[] = '				<a class="choose_type" href="#" onclick="javascript:Joomla.submitbutton(\'item.setType\', \''.
											base64_encode(json_encode(array('title' => $item->title, 'request' => $item->request))).'\')"' .
											' title="'.JText::_($item->description).'">'.
											JText::_($item->title).'</a>';
				$html[] = '			</li>';
			}

			$html[] = '		</ul>';
			$html[] = '	</dd>';
			$html[] = '</dl>';
			$html[] = '</li>';
		}

		$html[] = '<li>';
		$html[] = '<dl class="menu_type">';
		$html[] = '	<dt>'.JText::_('COM_MENUS_TYPE_SYSTEM').'</dt>';
		$html[] = '	<dd>';
		$html[] = '		<ul>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" onclick="javascript:Joomla.submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('title'=>'url'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_EXTERNAL_URL_DESC').'">'.
									JText::_('COM_MENUS_TYPE_EXTERNAL_URL').'</a>';
		$html[] = '			</li>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" onclick="javascript:Joomla.submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('title'=>'alias'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_ALIAS_DESC').'">'.
									JText::_('COM_MENUS_TYPE_ALIAS').'</a>';
		$html[] = '			</li>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" onclick="javascript:Joomla.submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('title'=>'separator'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_SEPARATOR_DESC').'">'.
									JText::_('COM_MENUS_TYPE_SEPARATOR').'</a>';
		$html[] = '			</li>';
		$html[] = '		</ul>';
		$html[] = '	</dd>';
		$html[] = '</dl>';
		$html[] = '</li>';
		$html[] = '</ul>';

		return implode("\n", $html);
		*/
	}
}

