<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
/**

 */
class PagesAndItemsModelPiMenutype extends JModel
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'MenuType';

	/**
	 * A reverse lookup of the base link URL to Title
	 *
	 * @var	array
	 */
	protected $_rlu = array();

	protected static $_filter = array('option', 'view', 'layout');


	/**
	 * Gets a standard form of a link for lookups.
	 *
	 * @param	mixed	A link string or array of request variables.
	 *
	 * @return	mixed	A link in standard option-view-layout form, or false if the supplied response is invalid.
	 */
	public static function getLinkKey($request)
	{
		if (empty($request))
		{
			return false;
		}
		// Check if the link is in the form of index.php?...
		if (is_string($request))
		{
			$args = array();
			if (strpos($request, 'index.php') === 0)
			{
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

		ksort($request);

		return 'index.php?'.http_build_query($request,'','&');
	}


	/**
	 * Method to get the available menu item type options.
	 *
	 * @return	array	Array of groups with menu item types.
	 * @since	1.6
	 */
	protected function _getTypeOptions()
	{
		libxml_use_internal_errors(true);
		jimport('joomla.filesystem.file');

		// Initialise variables.
		$lang = JFactory::getLanguage();
		$extension = 'com_menus';
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

		$list = array();

		// Get the list of components.
		$db = JFactory::getDBO();
		$query = 'SELECT c.id, c.name, c.link, c.option' .
				' FROM #__components AS c' .
				' WHERE c.link <> "" AND parent = 0 AND enabled = 1' .
				' ORDER BY c.name';
		$db->setQuery( $query );

		$components = $db->loadObjectList();

		foreach ($components as $component)
		{
			$lang->load($component->option, JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load($component->option, JPATH_ADMINISTRATOR.'/components/'.$component->option, null, false, false)
			||	$lang->load($component->option, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load($component->option, JPATH_ADMINISTRATOR.'/components/'.$component->option, $lang->getDefault(), false, false);

			if ($options = $this->_getTypeOptionsByComponent($component->option))
			{
				$list[$component->name] = $options;

				// Create the reverse lookup for link-to-name.
				foreach ($options as $option)
				{
					if (isset($option->request))
					{
						$this->_rlu[$this->getLinkKey($option->request)] = $option->get('title');

						/*
						if (isset($option->request['option']))
						{
							$lang->load($option->request['option'], JPATH_ADMINISTRATOR, null, false, false)
							||	$lang->load($option->request['option'], JPATH_ADMINISTRATOR.'/components/'.$option->request['option'], null, false, false)
							||	$lang->load($option->request['option'], JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
							||	$lang->load($option->request['option'], JPATH_ADMINISTRATOR.'/components/'.$option->request['option'], $lang->getDefault(), false, false);
						}
						*/
					}
				}
			}
			else
			{
				$name = str_replace('com_', '', $component->option);
				$o = new JObject;
				$o->title		= $component->name;
				$o->description	= $name; //(string) $menu['msg'];
				$o->request		= array('option' => $component->option);

				$options[] = $o;
				$list[$component->name] = $options;
			}
		}

		return $list;
	}

	protected function _getTypeOptionsByComponent($component)
	{
		// Initialise variables.
		$options = array();

		$mainXML = JPATH_SITE.'/components/'.$component.'/metadata.xml';

		if (is_file($mainXML))
		{
			$options = $this->_getTypeOptionsFromXML($mainXML, $component);
		}

		if (empty($options))
		{
			$options = $this->_getTypeOptionsFromMVC($component);
		}

		/*
		if (empty($options))
		{
			$mainXML = JPATH_ADMINISTRATOR.'/components/'.$component.'/metadata.xml';
			$options = $this->_getTypeOptionsFromXML($mainXML, $component);
		}

		if (empty($options))
		{
			$mainXML = JPATH_ADMINISTRATOR.'/components/'.$component.'/'.$component.'.xml';
			$options = $this->_getTypeOptionsFromXML($mainXML, $component);
		}

		if (empty($options))
		{
			$mainXML = JPATH_ADMINISTRATOR.'/components/'.$component.'/'.str_replace('com_','',$component).'.xml';
			$options = $this->_getTypeOptionsFromXML($mainXML, $component);
		}
		*/
		return $options;
	}

	protected function _getTypeOptionsFromXML($file, $component)
	{
		// Initialise variables.
		$options = array();

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($file))
		{
			return false;
		}

		// Look for the first menu node off of the root node.
		if (!$menu = $xml->xpath('menu[1]')) {
			return false;
		}
		else {
			$menu = $menu[0];
		}

		// If we have no options to parse, just add the base component to the list of options.
		if (!empty($menu['options']) && $menu['options'] == 'none')
		{
			// Create the menu option for the component.
			$o = new JObject;
			$o->title		= (string) $menu['name'];
			$o->description	= (string) $menu['msg'];
			$o->request		= array('option' => $component);

			$options[] = $o;

			return $options;
		}

		// Look for the first options node off of the menu node.
		if (!$optionsNode = $menu->xpath('options[1]')) {
			return false;
		}
		else {
			$optionsNode = $optionsNode[0];
		}

		// Make sure the options node has children.
		if (!$children = $optionsNode->children()) {
			return false;
		}
		else {
			// Process each child as an option.
			foreach ($children as $child)
			{
				if ($child->getName() == 'option') {
					// Create the menu option for the component.
					$o = new JObject;
					$o->title		= (string) $child['name'];
					$o->description	= (string) $child['msg'];
					$o->request		= array('option' => $component, (string) $optionsNode['var'] => (string) $child['value']);

					$options[] = $o;
				}
				elseif ($child->getName() == 'default') {
					// Create the menu option for the component.
					$o = new JObject;
					$o->title		= (string) $child['name'];
					$o->description	= (string) $child['msg'];
					$o->request		= array('option' => $component);

					$options[] = $o;
				}
			}
		}

		return $options;
	}

	protected function _getTypeOptionsFromMVC($component)
	{
		// Initialise variables.
		$options = array();

		// Get the views for this component.
		$path = JPATH_SITE.'/components/'.$component.'/views';

		if (JFolder::exists($path)) {
			$views = JFolder::folders($path);
		}
		else
		{
			return false;
		}

		foreach ($views as $view)
		{
			// Ignore private views.
			if (strpos($view, '_') !== 0) {
				// Determine if a metadata file exists for the view.
				$file = $path.'/'.$view.'/metadata.xml';

				if (is_file($file))
				{
					// Attempt to load the xml file.
					if ($xml = simplexml_load_file($file))
					{
						// Look for the first view node off of the root node.
						if ($menu = $xml->xpath('view[1]'))
						{
							$menu = $menu[0];

							// If the view is hidden from the menu, discard it and move on to the next view.
							if (!empty($menu['hidden']) && $menu['hidden'] == 'true') {
								unset($xml);
								continue;
							}

							// Do we have an options node or should we process layouts?
							// Look for the first options node off of the menu node.
							if ($optionsNode = $menu->xpath('options[1]')) {
								$optionsNode = $optionsNode[0];

								// Make sure the options node has children.
								if ($children = $optionsNode->children()) {
									// Process each child as an option.
									foreach ($children as $child)
									{
										if ($child->getName() == 'option') {
											// Create the menu option for the component.
											$o = new JObject;
											$o->title		= (string) $child['name'];
											$o->description	= (string) $child['msg'];
											$o->request		= array('option' => $component, 'view' => $view, (string) $optionsNode['var'] => (string) $child['value']);

											$options[] = $o;
										}
										elseif ($child->getName() == 'default') {
											// Create the menu option for the component.
											$o = new JObject;
											$o->title		= (string) $child['name'];
											$o->description	= (string) $child['msg'];
											$o->request		= array('option' => $component, 'view' => $view);

											$options[] = $o;
										}
									}
								}
							}
							else
							{
								$options = array_merge($options, (array) $this->_getTypeOptionsFromLayouts($component, $view));
							}
						}
						unset($xml);
					}

				}
				else
				{
					$options = array_merge($options, (array) $this->_getTypeOptionsFromLayouts($component, $view));
				}
			}
		}

		return $options;
	}

	protected function _getTypeOptionsFromLayouts($component, $view)
	{
		// Initialise variables.
		$options = array();
		$layouts = array();
		$layoutNames = array();
		$templateLayouts = array();
		$lang = JFactory::getLanguage();

		// Get the layouts from the view folder.
		$path = JPATH_SITE.'/components/'.$component.'/views/'.$view.'/tmpl';
		if (JFolder::exists($path))
		{
			$layouts = array_merge($layouts, JFolder::files($path, '.xml$', false, true));
			//$layouts = array_merge($layouts, JFolder::files($path, '.php$', false, true));
		}
		else
		{
			return $options;
		}

		// build list of standard layout names
		foreach ($layouts as $key => $layout)
		{
			// Ignore private layouts.
			if (strpos(JFile::getName($layout), '_') === false)
			{

				//unset $layout if no php file exist no other way
				//
				if(JFile::stripext(JFile::getName($layout)) != 'metadata' )//JFile::exists(JFile::stripext($layout).'.php') )
				{
				//
					$file = $layout;
					// Get the layout name.
					$layoutNames[] = JFile::stripext(JFile::getName($layout));
				}
				else
				{
					unset($layouts[$key]);
				}
			}
		}

		// get the template layouts
		// TODO: This should only search one template -- the current template for this item (default of specified)
		/*
		$folders = JFolder::folders(JPATH_SITE.DS.'templates','',false,true);
		// Array to hold association between template file names and templates
		$templateName = array();
		foreach($folders as $folder)
		{
			if (JFolder::exists($folder.DS.'html'.DS.$component.DS.$view))
			{
				$template = JFile::getName($folder);
				//$lang->load('tpl_'.$template.'.sys', JPATH_SITE, null, false, false)
				//||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE.'/templates/'.$template, null, false, false)
				//||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE, $lang->getDefault(), false, false)
				//||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE.'/templates/'.$template, $lang->getDefault(), false, false);
				$templateLayouts = JFolder::files($folder.DS.'html'.DS.$component.DS.$view, '.xml$', false, true);


				foreach ($templateLayouts as $layout)
				{
					$file = $layout;
					// Get the layout name.
					$templateLayoutName = JFile::stripext(JFile::getName($layout));

					// add to the list only if it is not a standard layout
					if (array_search($templateLayoutName, $layoutNames) === false) {
						$layouts[] = $layout;
						// Set template name array so we can get the right template for the layout
						$templateName[$layout] = JFile::getName($folder);
					}
				}
			}
		}
		*/
		// Process the found layouts.
		foreach ($layouts as $layout)
		{
			// Ignore private layouts.
			if (strpos(JFile::getName($layout), '_') === false)
			{
				/*
				if(!JFile::exists(JFile::stripext($layout).'php') )
				{
					//continue;
				}
				*/
				$file = $layout;
				// Get the layout name.
				$layout = JFile::stripext(JFile::getName($layout));
				//TODO check if the xml name the same as the php name
				// Create the menu option for the layout.
				$o = new JObject;
				$o->title		= ucfirst($layout);
				$o->description	= '';
				$o->request		= array('option' => $component, 'view' => $view);

				// Only add the layout request argument if not the default layout.
				if ($layout != 'default')
				{
					// If the template is set, add in format template:layout so we save the template name
					$o->request['layout'] = (isset($templateName[$file])) ? $templateName[$file] . ':' . $layout : $layout;
				}

				// Load layout metadata if it exists.
				if (is_file($file))
				{
					// Attempt to load the xml file.
					if ($xml = simplexml_load_file($file))
					{
						// Look for the first view node off of the root node.
						if ($menu = $xml->xpath('layout[1]')) {
							$menu = $menu[0];

							// If the view is hidden from the menu, discard it and move on to the next view.
							if (!empty($menu['hidden']) && $menu['hidden'] == 'true') {
								unset($xml);
								unset($o);
								continue;
							}

							// Populate the title and description if they exist.
							if (!empty($menu['title'])) {
								$o->title = trim((string) $menu['title']);
							}

							if (!empty($menu->message[0])) {
								$o->description = trim((string) $menu->message[0]);
							}
						}
					}
					/*
					else
					{
						$errors = libxml_get_errors();
						foreach ($errors as $error)
						{
							echo display_xml_error($error, $xml);
						}
						libxml_clear_errors();
					}
					*/
				}

				// Add the layout to the options array.
				$options[] = $o;
			}
		}

		return $options;
	}


}
