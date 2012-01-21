<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die;

jimport('joomla.event.dispatcher');

/**
 * Indicator class to handle WYSIWYG indicators
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
 /*

	 * Get an indicator object
	 *
	 * @param   string  $indicator The indicator to load, depends on the indicator plugins that are installed
	 *
	 * @return Indicator object
	public static function getIndicator($indicator = null)
	{
		//jimport('joomla.html.indicator');

		//get the indicator configuration setting
		if (is_null($indicator)) {
			//$conf	= self::getConfig();
			//$indicator	= $conf->get('indicator');
			return false;
		}

		return Indicator::getInstance($indicator);
	}
 */
class Indicator extends JObservable
{
	/**
	 * Indicator Plugin object
	 *
	 * @var  object
	 */
	protected $_indicator = null;

	/**
	 * Indicator Plugin name
	 *
	 * @var  string
	 */
	protected $_name = null;

	protected $_id = null;

	protected $_params = null;

	/**
	 * Object asset
	 *
	 * @var  string
	 */
	protected $asset = null;

	/**
	 * Object author
	 *
	 * @var  string
	 */
	protected $author = null;

	/**
	 * Constructor
	 *
	 * @param   string  The indicator name
	 */
	public function __construct($indicator = 'none')
	{
		$this->_name = $indicator;
	}

	/**
	 * Returns the global Indicator object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $indicator  The indicator to use.
	 *
	 * @return  object  Indicator  The Indicator object.
	 */
	public static function getInstance($indicator = 'none')
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		$signature = serialize($indicator);

		if (empty ($instances[$signature])) {
			$instances[$signature] = new Indicator($indicator);
		}

		return $instances[$signature];
	}

	/**
	 * Initialise the indicator
	 */
	public function initialise($id)
	{
		//check if indicator is already loaded
		if (is_null(($this->_indicator))) {
			return;
		}
		$args['id'] = $id;
		$args['event'] = 'onInit';

		$return = '';
		$results[] = $this->_indicator->update($args);

		foreach ($results as $result)
		{
			if (trim($result)) {
				//$return .= $result;
				$return = $result;
			}
		}
		$doc = JFactory::getDocument();
		$doc->addCustomTag($return);
		
		$contentCss = "";
		$contentCss .= ".indicators-xtd-buttons{margin:5px 0 0 0;float:left;width:100%;}";
		$contentCss .= ".indicator{margin:0 0 5px;float:left;max-width:100%}.indicator_wrapper{padding:1px;float:left;max-width:100%}";
		$contentCss .= ".readonly.indicator  {border: 0 none; color: #666666;font-family: Arial,Helvetica,sans-serif;font-size: 1.091em;font-weight: bold;padding-top: 1px;}";
		$contentCss .= "textarea.readonly.indicator  {border: 0 none; color: #666666;font-family: Arial,Helvetica,sans-serif;font-size: 1.091em;font-weight: bold;padding-top: 1px;}";
		$contentCss .= ".indicator_dimension_null {height:0px;width:0px;overflow:hidden;}";
		$contentCss .= ".indicatorborder {border: 1px solid silver;}";
		$doc->addStyleDeclaration($contentCss);
	}

	/**
	 * Display the indicator area.
	 *
	 * @param   string   $name      The control name.
	 * @param   string   $html      The contents of the text area.
	 * @param   string   $width     The width of the text area (px or %).
	 * @param   string   $height    The height of the text area (px or %).
	 * @param   integer  $col       The number of columns for the textarea.
	 * @param   integer  $row       The number of rows for the textarea.
	 * @param   boolean  $buttons   True and the indicator buttons will be displayed.
	 * @param   string   $id        An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   string   $asset     The object asset
	 * @param   object   $author
	 * @param   array    $params    Associative array of indicator parameters.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	public function display($name, $html, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array('buttonType' => 'editors-xtd'))
	{
		if (empty($id)) {
			$id = $name;
		}
		$this->asset	= $asset;
		$this->author	= $author;
		$this->_loadIndicator($params,$id); //,$type);

		// Check whether indicator is already loaded
		if (is_null(($this->_indicator))) {
			return;
		}

		// Backwards compatibility. Width and height should be passed without a semicolon from now on.
		// If indicator plugins need a unit like "px" for CSS styling, they need to take care of that
		$width	= str_replace(';', '', $width);
		$height	= str_replace(';', '', $height);

		// Initialise variables.
		$return = null;

		$args['name']		= $name;
		$args['content']	= $html;
		$args['width']		= $width;
		$args['height']		= $height;
		$args['col']		= $col;
		$args['row']		= $row;
		$args['buttons']	= $buttons;
		$args['id']			= $id ? $id : $name;
		$args['asset']		= $asset;
		$args['author']		= $author;
		$args['params']		= $params ? $params : $this->_params;
		$args['event']		= 'onDisplay';

		$results[] = $this->_indicator->update($args);
		foreach ($results as $result)
		{
			if (trim($result)) {
				$return .= $result;
			}
		}
		return $return;
	}

	/**
	 * Save the indicator content
	 *
	 * @param   string  The name of the indicator control
	 *
	 * @return  string
	 * @since   11.1
	 */
	public function save($indicator)
	{
		$this->_loadIndicator();

		// Check whether indicator is already loaded
		if (is_null(($this->_indicator))) {
			return;
		}

		$args[] = $indicator;
		$args['event'] = 'onSave';

		$return = '';
		$results[] = $this->_indicator->update($args);

		foreach ($results as $result)
		{
			if (trim($result)) {
				$return .= $result;
			}
		}

		return $return;
	}

	/**
	 * Get the indicator contents
	 *
	 * @param   string  $indicator  The name of the indicator control
	 *
	 * @return  string
	 */
	public function getContent($indicator)
	{
		$this->_loadIndicator();

		$args['name'] = $indicator;
		$args['event'] = 'onGetContent';

		$return = '';
		$results[] = $this->_indicator->update($args);

		foreach ($results as $result)
		{
			if (trim($result)) {
				$return .= $result;
			}
		}

		return $return;
	}

	/**
	 * Set the indicator contents
	 *
	 * @param   string  $indicator  The name of the indicator control
	 * @param   string  $html    The contents of the text area
	 *
	 * @return  string
	 */
	/*
	public function getSetContent($indicator)//, $html)
	{
		$this->_loadIndicator();

		$args['name'] = $indicator;
		//$args['html'] = $html;
		//$args['fire'] = $fire;
		$args['event'] = 'onGetSetContent';

		$return = '';
		$results[] = $this->_indicator->update($args);

		foreach ($results as $result)
		{
			if (trim($result)) {
				$return .= $result;
			}
		}

		return $return;
	}
	*/
	/**
	 * Set the indicator contents
	 *
	 * @param   string  $indicator  The name of the indicator control
	 * @param   string  $html    The contents of the text area
	 *
	 * @return  string
	 */
	public function setContent($indicator, $html) //,$fire = true)
	{
		$this->_loadIndicator();

		$args['name'] = $indicator;
		$args['html'] = $html;
		//$args['fire'] = $fire;
		$args['event'] = 'onSetContent';

		$return = '';
		$results[] = $this->_indicator->update($args);

		foreach ($results as $result)
		{
			if (trim($result)) {
				$return .= $result;
			}
		}

		return $return;
	}
/*
	public function initContent($indicator, $html)
	{
		$this->_loadIndicator();

		$args['name'] = $indicator;
		$args['html'] = $html;
		$args['event'] = 'onInitContent';

		$return = '';
		$results[] = $this->_indicator->update($args);

		foreach ($results as $result)
		{
			if (trim($result)) {
				$return .= $result;
			}
		}

		return $return;
	}
*/
	/**
	 * Get the indicator buttons
	 *
	 * @param   string  $indicator     The name of the indicator.
	 * @param   mixed   $buttons    Can be boolean or array, if boolean defines if the buttons are
	 *                              displayed, if array defines a list of buttons not to show.
	 *
	 * @since   11.1
	 */
	public function getButtons($indicator, $buttons = array())
	{
		$result = array();
		$id = $indicator.'_button_';
		
		//BEGIN new	
		//$buttons = $this->_params->get('buttons',array());
		$editorsXtd = array();
		$indicatorsXtd = array();
		$othersXtd = array();
		foreach($buttons as $key => $value)
		{
			$buttonType = null;
			if(is_array($value))// && isset($buttonType[0]) && is_bool($buttonType[0]))
			{
				foreach($value as $buttonType => $button)
				{
					if(is_bool($button) && $button)
					{
						$load = true;
					}
					else
					{
						$load = false;
					}
					switch($buttonType)
					{
						case 'editors-xtd':
							$loadEditorsXtd = $load;
						break;
						
						case 'indicators-xtd':
							$loadIndicatorsXtd = $load;
						break;
					}
				}
				unset($buttons[$key]);
			}
		}
		foreach($buttons as $key => $button)
		{
			$buttonType = null;
			if(strpos($button, '.') !== false)
			{
				list($buttonType,$buttonName) = explode('.',$button);
			}
			
			if($buttonType == 'editors-xtd' && !isset($loadEditorsXtd))
			{
				$editorsXtd[$key] = $buttonName;
			}
			elseif($buttonType == 'indicators-xtd' && !isset($loadIndicatorsXtd))
			{
				$indicatorsXtd[$key] = $buttonName;
			}
			elseif($buttonType)
			{
				$othersXtd[$key] = $buttonName;
			}
			else
			{
				unset($buttons[$key]);
			}
		}

		$result = array();
		if(count($editorsXtd) || ( isset($loadEditorsXtd) && $loadEditorsXtd) )// || $loadEditorsXtd)
		{
			// Get plugins
			$plugins = JPluginHelper::getPlugin('editors-xtd');
			foreach($plugins as $plugin)
			{
				if ( (is_array($editorsXtd) && !in_array($plugin->name, $editorsXtd)) && !isset($loadEditorsXtd))
				{
					continue;
				}
				$isLoaded = JPluginHelper::importPlugin('editors-xtd', $plugin->name, false);
				$className = 'plgButton'.$plugin->name;
				if (class_exists($className)) {
					$plugin = new $className($this, (array)$plugin);
				}
				// Try to authenticate
				if ($temp = $plugin->onDisplay($indicator, $this->asset, $this->author)) 
				{
					if(isset($temp->name))
					{
						$temp->id = $id.$temp->name;
						$temp->buttonType = 'editors-xtd';
						if(count($editorsXtd)) // || ( isset($loadEditorsXtd) && $loadEditorsXtd) )// || $loadEditorsXtd)
						{
							foreach($editorsXtd as $key => $button)
							{
								if($temp->name != $button) 
								{
									continue;
								}
								$result[$key] = $temp;
							}
						}
						else
						{
							$result[] = $temp;
						}
					}
					else
					{
						//the plugin is avaible but return an unknow result
					}
				}
			}
		}
		$countEditorsXtd = count($result);
		if(count($indicatorsXtd) || ( isset($loadIndicatorsXtd) && $loadIndicatorsXtd) )
		{
			// Get piplugins
			$path = JPATH_ADMINISTRATOR.'/components/com_pagesanditems/includes';
			require_once($path.DS.'extensions'.DS.'pipluginhelper.php');
			$piplugins = ExtensionPiPluginHelper::getExtension('indicators-xtd');//JPluginHelper::getPlugin('editors-xtd');
			if($piplugins)
			foreach($piplugins as $piplugin)
			{
				if (is_array($indicatorsXtd) && !in_array($piplugin->name, $indicatorsXtd) && !isset($loadIndicatorsXtd))
				{
					continue;
				}
				
				$isLoaded = ExtensionPiPluginHelper::importExtension('indicators-xtd', $piplugin->name);//, false);
				$className = 'PagesAndItemsExtensionPiPluginIndicatorXtd'.$piplugin->name;
				if (class_exists($className)) {
					$piplugin = new $className($this, (array)$piplugin);
				}
				// Try to authenticate
				if ($temp = $piplugin->onDisplay($indicator, $this->asset, $this->author)) 
				{
					if(isset($temp->name))
					{
						$temp->id = $id.$temp->name;
						$temp->buttonType = 'indicators-xtd';
						if(count($indicatorsXtd))
						{
							foreach($indicatorsXtd as $key => $button)
							{
								if($temp->name != $button) 
								{
									continue;
								}
								
								if(!count($countEditorsXtd))
								{
									$result[$key] = $temp;
								}
								else
								{
									$result[($key+$countEditorsXtd)] = $temp;
								}
							}
						}
						else
						{
							if(count($countEditorsXtd))
							{
								//we need the max
								$max = max(array_keys($editorsXtd));
								$result[($max+count($result))] = $temp;
							}
							else
							{
								$result[] = $temp;
							}
						}
					}
					else
					{
						//the piplugin is avaible but return an unknow result
					}
				}
			}
		}
		
		ksort($result);
		return $result;
		
		//END new
		
		//Begin old
		//is_bool we do not display all???
		if (is_bool($buttons))// && !$buttons)
		{
			return $result;
		}
		$type = $this->_params->get('buttonType');
		if($type == 'editors-xtd')
		{
			// Get plugins
			$plugins = JPluginHelper::getPlugin('editors-xtd');

			foreach($plugins as $plugin)
			{
				//if (is_array($buttons) || (is_bool($buttons) && $buttons))
				if (is_array($buttons) && !in_array($plugin->name, $buttons)) {
					continue;
				}
	
				$isLoaded = JPluginHelper::importPlugin('editors-xtd', $plugin->name, false);
				$className = 'plgButton'.$plugin->name;

				if (class_exists($className)) {
					$plugin = new $className($this, (array)$plugin);
				}
	
				// Try to authenticate
				if ($temp = $plugin->onDisplay($indicator, $this->asset, $this->author)) {
					if(isset($temp->name))
					{
						$temp->id = $id.$temp->name;
						//$temp->buttonOutput = $buttonOutput;
						$temp->buttonType = $type;
						$result[] = $temp;
					}
					else
					{
						//the plugin is avaible but return an unknow result
					}
				}
			}
		}
		elseif($type == 'indicators-xtd')
		{
			// Get plugins
			// Get the plugin
			$path = JPATH_ADMINISTRATOR.'/components/com_pagesanditems/includes';
			require_once($path.DS.'extensions'.DS.'pipluginhelper.php');
			$piplugins = ExtensionPiPluginHelper::getExtension('indicators-xtd');//JPluginHelper::getPlugin('editors-xtd');
			if($piplugins)
			foreach($piplugins as $piplugin)
			{
				if (is_array($buttons) && !in_array($piplugin->name, $buttons)) {
					continue;
				}
	
				$isLoaded = ExtensionPiPluginHelper::importExtension('indicators-xtd', $piplugin->name);//, false);
				$className = 'PagesAndItemsExtensionPiPluginIndicatorXtd'.$piplugin->name;

				if (class_exists($className)) {
					$plugin = new $className($this, (array)$piplugin);
				}
	
				// Try to authenticate
				if ($temp = $piplugin->onDisplay($indicator, $this->asset, $this->author)) 
				{
					//$temp->buttonOutput = $buttonOutput;
					$temp->buttonType = $type;
					$temp->id = $id.$temp->name;
					$result[] = $temp;
				}
			}
		
		}
		else
		{
			$temp->buttonType = $type;
			//$temp->buttonOutput = $buttonOutput;
			$temp->name = 'Test Name';
			$temp->text = 'Test Text';
			$temp->id = $id.$temp->name;
			$result[] = $temp;
		}
		
		
		return $result;
		//END old
	}

	public function getDeleteButton($id)
	{
		//add delete Button
		$buttonDelete = PagesAndItemsHelper::getButtonMaker();
		$buttonDelete->imageName = PagesAndItemsHelper::getDirIcons().'base/icon-16-delete_minus.png';
		$buttonDelete->style = 'float:right;margin-top:0;margin-right:0;margin-left:6px;';
		$buttonDelete->onclick = $this->setContent($id, '\'\'');//,0);
		//$this->onSetContent($button->get('id'), '') $content .= "var html = $getContent $setContent";
		return $buttonDelete->makeButton();
	}

	public function getEditButton($id,$buttons)
	{
		//add editor Button
		$type = $this->_params->get('buttonType');
		$indicatorName = $this->_name;
		//we have only one button but array and the array can be empty
		$buttonName = '';
		if(count($buttons))
		{
			$button = $buttons[0];
			$buttonName = $button->get('name');
		}
		
		$buttonEditor = PagesAndItemsHelper::getButtonMaker();
		$buttonEditor->imageName = PagesAndItemsHelper::getDirIcons().'ui/ui-editor.png';
		$buttonEditor->style = 'float:right;margin-top:0;margin-right:0;margin-left:6px;';
		
		$buttonEditor->modal = true;
		$buttonEditor->id = $id.'_editor_button';
		//$buttonEditor->text = '123';
		$size_x = '600';
		$size_y = '450';
		$size = 'size: { x: '.$size_x.' , y: '.$size_y.'}';
		//$options = "{handler: 'iframe', ".$size."}"; //GENERALQUESTION with "" we can use {} with '' not why?
		$options = "handler: 'iframe', ".$size;

		$buttonEditor->rel = $options;
				
		$link = 'index.php?option=com_pagesanditems&view=indicator&layout=editor&editorLayout=1&popup=1&button='.$buttonName.'&buttonType='.$type.'&tmpl=component&field_id='.$id.'&indicator='.$indicatorName;
		$buttonEditor->href = $link;
		/*
		$buttonEditor->class = 'blank ';
		$buttonEditor->buttonType = 'joomla';
		$options = "{handler: 'iframe', ".$size."}";
		//$link = 'index.php?option=com_pagesanditems&amp;view=indicator&amp;layout=editor&amp;editorLayout=1&amp;popup=1&amp;button='.$buttonName.'&amp;buttonType='.$type.'&amp;tmpl=component&amp;field_id='.$id.'&amp;indicator='.$indicatorName;
		$buttonEditor->text = '&nbsp;';
		//$buttonEditor->onclick = $this->setContent($id, '\'\'');//,0);

		
		$buttonEditor->style = 'background-position: left center;background-repeat: no-repeat;float: right;margin-left: 8px;padding-right: 0;';
		$buttonEditor->paddingLeft = '20';
		//$this->onSetContent($button->get('id'), '') $content .= "var html = $getContent $setContent";
		$html = '';
		$html .= '<div style="float:right;">';
		$html .= $buttonEditor->makeButton();
		$html .= '</div>';
		*/
		$html = '';
		$html .= $buttonEditor->makeButton();
		
		return $html;
	}

	public function getButton($name, $button) //, $content)
	{
		$popup = (bool)JRequest::getVar('popup',0);
		$return = '';
		$type = $button->get('buttonType');
		$test = false;//
		//$test = true;
		$indicatorName = $this->_name;
		// Results should be an object
		if ($button->get('name')) 
		{
			switch($type)
			{
				case 'editors-xtd':
					$id 		= ($button->get('id')) ? 'id="'.$button->get('id').'"' : 'id="'.$button->get('name').'_a"';
					$idString 	= ($button->get('id')) ? $button->get('id') : $button->get('name').'_a';
					$modal		= ($button->get('modal')) ? 'class="modal-button"' : null;
					$href		= ($button->get('link')) ? 'href="'.JURI::base().$button->get('link').'"' : null;
					$onclick	= ($button->get('onclick')) ? 'onclick="'.$button->get('onclick').'"' : null;
					$title		= ($button->get('title')) ? $button->get('title') : $button->get('text');
					$rel		= ($button->get('options')) ? 'rel="'.$button->get('options').'"' : null;
					if($popup)
					{
						//make nothing
					}
					elseif($button->get('modal'))
					{
						$options = $button->get('options');
						$options = str_replace('\'','',$options);
						$options = preg_replace('/\s/i', '', $options);
						$options = preg_replace('/(\w+)/i', '"${1}"', $options);
						$options = json_decode($options);
						$size_x = '100';
						$size_y = '100';
						if(isset($options->size))
						{
							$size_x = $options->size->x;
							$size_y = $options->size->y;
						}
						//we make an own link
						$link = 'index.php?option=com_pagesanditems&amp;view=indicator&amp;layout=indicator&amp;button='.$button->get('name').'&amp;buttonType='.$type.'&amp;tmpl=component&amp;field_id='.$name.'&amp;indicator='.$indicatorName.'&amp;popup=1&amp;size_x='.$size_x.'&amp;size_y='.$size_y.($button->get('link') ? '&amp;link='.base64_encode(JURI::base().$button->get('link')).'' : null);
						
						//'&amp;field_value='.$content.
						$href		= 'href="'.JURI::base().$link.'"';
					}
					elseif($button->get('onclick') && !$test)
					{
						$options = $button->get('options');
						$options = str_replace('\'','',$options);
						$options = preg_replace('/\s/i', '', $options);
						$options = preg_replace('/(\w+)/i', '"${1}"', $options);
						$options = json_decode($options);
						$size_x = '100';
						$size_y = '100';
						if(isset($options->size))
						{
							$size_x = $options->size->x;
							$size_y = $options->size->y;
						}
						
						$modal		= 'class="modal-button-onclick"';
						$modal		= 'class="modal-button"';
						$size_x = '0';
						$size_y = '0';
						
						
						$rel = "{handler: 'iframe', size: {x: ".$size_x.", y: ".$size_y."}}";
						$rel	= 'rel="'.$rel.'"';
						$link = 'index.php?option=com_pagesanditems&amp;view=indicator&amp;layout=indicator&amp;button='.$button->get('name').'&amp;buttonType='.$type.'&amp;tmpl=component&amp;field_id='.$name.'&amp;indicator='.$indicatorName.'&amp;popup=1&amp;size_x='.$size_x.'&amp;size_y='.$size_y.($button->get('onclick') ? '&amp;onclick='.base64_encode($button->get('onclick')).'' : null);
						$href	= 'href="'.JURI::base().$link.'"';
						
						$onclick = '';
						//&amp;field_value='.$content.'
						
						$doc = JFactory::getDocument();
						$contentCss = "";
						$contentCss .= "#sbox-window.onclick{background-color:transparent;}";
						$contentCss .= "#sbox-content.onclick{background-color:transparent;}";
						$contentCss .= "#sbox-btn-close.onclick{display:none;}";
						$contentCss .= "#sbox-overlay.onclick{opacity:0;background-color:transparent;}";

						$contentCss .= "#sbox-window.onclick {box-shadow: none;}";

						$doc->addStyleDeclaration($contentCss);
						
						
						$contentJs = "";
						/*
						$contentJs .= "function addStyles(){";
						//$contentJs .= "alert('add');";
						$contentJs .= "};";
						$contentJs .= "function removeClasses(){";
						//$contentJs .= "alert('remove');";
						$contentJs .= "};";
						*/
						//$options = "{ closeBtn: false, shadow: 0, overlayOpacity: 0}";
						$contentJs .= "window.addEvent('domready',function(){";
							$contentJs .= "document.id('$idString').addEvent('click',function(){";
								//$contentJs .= "alert(SqueezeBox.closeButton);";
								//$contentJs .= "alert(SqueezeBox.overlay);";
								//$contentJs .= "alert(SqueezeBox.options.overlayOpacity);";
								//$contentJs .= "SqueezeBox.options.overlayOpacity = 0;";
								//$contentJs .= "SqueezeBox.presets.overlayOpacity = 0;";
								
								
								//$contentJs .= "SqueezeBox.initialize(".$options.");";
								//$contentJs .= "SqueezeBox.presets(".$options.");";
								//TODO get all the sytels?
								//$contentJs .= "document.id('$idString').store('styleSetting',document.id('sbox-overlay').getStyle('opacity'));";
								//$contentJs .= "alert('click');";
								$contentJs .= "document.id('sbox-window').addClass('onclick');";
								$contentJs .= "document.id('sbox-content').addClass('onclick');";
								$contentJs .= "document.id('sbox-overlay').addClass('onclick');";
								$contentJs .= "document.id('sbox-btn-close').addClass('onclick');";
								
								//$contentJs .= "document.id('sbox-window').setStyle('background-color', 'transparent');";
								//$contentJs .= "document.id('sbox-window').setStyle('box-shadow', 'none');";
								//$contentJs .= "document.id('sbox-content').setStyle('background-color', 'transparent');";
								//$contentJs .= "document.id('sbox-overlay').setStyle('opacity', '0');";
								
								//$contentJs .= "document.id('sbox-btn-close').setStyle('display', 'none');";
							$contentJs .= "});";
							
							$contentJs .= "document.id('$idString').addEvent('removeClasses',function(){";
								//TODO get all the sytels?
								//$contentJs .= "alert('removeClasses');";
								$contentJs .= "document.id('sbox-window').removeClass('onclick');";
								$contentJs .= "document.id('sbox-content').removeClass('onclick');";
								$contentJs .= "document.id('sbox-overlay').removeClass('onclick');";
								$contentJs .= "document.id('sbox-btn-close').removeClass('onclick');";
								//$contentJs .= "document.id('sbox-overlay').setStyle('opacity',document.id('$idString').retrieve('styleSetting'));";
							//retrieve('styleSetting');
							$contentJs .= "});";
							
						$contentJs .= "});";
						
						$doc->addScriptDeclaration($contentJs);
						
						
						//#sbox-window {background-color:none}
						//#sbox-btn-close{display:none;}
						
						
						
						
					}
					elseif($button->get('onclick'))
					{
						$options = $button->get('options');
						$options = str_replace('\'','',$options);
						$options = preg_replace('/\s/i', '', $options);
						$options = preg_replace('/(\w+)/i', '"${1}"', $options);
						$options = json_decode($options);
						$size_x = '100';
						$size_y = '100';
						if(isset($options->size))
						{
							$size_x = $options->size->x;
							$size_y = $options->size->y;
						}
						$modal		= 'class="modal-button"';
						$rel = "{handler: 'iframe', size: {x: ".$size_x.", y: ".$size_y."}}";
						$rel = 'rel="'.$rel.'"';
						$link = 'index.php?option=com_pagesanditems&view=indicator&layout=indicator&button='.$button->get('name').'&buttonType='.$type.'&tmpl=component&field_id='.$name.'&field_value='.$content.'&indicator='.$indicatorName.'&size_x='.$size_x.'&size_y='.$size_y.($button->get('onclick') ? '&onclick='.base64_encode($button->get('onclick')).'' : null); //&popup=1
						$href = 'href="'.JURI::base().$link.'"';
							
						$modal = '';
						$rel = '';
						$href = '';
						$onclick = '';
					
						
						$doc = JFactory::getDocument();
						
						$contentJs = "";
						$contentJs .= "window.addEvent('domready',function(){";
						$contentJs .= "	var result = new Element('div');";//$('".$name."_result');";
						$contentJs .= "	result.set('id', '".$name."_result');";
						$contentJs .= "	result.inject(document.id('".$name."_Div'), 'before');";
						//$contentJs .= "	var result = $('".$name."_result');";
						//We can use one Request object many times.
						$contentJs .= "	var req = new Request.HTML({";
						$contentJs .= "		url: '".JURI::base().$link."',";
						//$contentJs .= "		method: 'get',";
						$contentJs .= "		evalScripts : true,";
						$contentJs .= "		onRequest: function(){";
						$contentJs .= "			result.set('text', 'Loading...');";
						$contentJs .= "		},";
						$contentJs .= "		onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript){";
						//$contentJs .= "			alert(responseJavaScript);";
						//$contentJs .= "			alert(responseHTML);";
						$contentJs .= "			alert(responseJavaScript);";
						$contentJs .= "			result.set('text', 'The request was successful!'+responseHTML);";
						$contentJs .= "		},";
						// Our request will most likely succeed, but just in case, we'll add an
						// onFailure method which will let the user know what happened.
						$contentJs .= "		onFailure: function(){";
						$contentJs .= "			result.set('text', 'The request failed.');";
						$contentJs .= "		}";
						$contentJs .= "	});";
						$contentJs .= "	document.id('".$button->get('id')."').addEvent('click', function(event){";
						$contentJs .= "		event.stop();";
						$contentJs .= "		req.send(";
						//$contentJs .= "			{data: {alert(data);}}";
						//$contentJs .= "			{data: {";
						//$contentJs .= "				html: 'The request was successful!'";
						//$contentJs .= "			}}";
						$contentJs .= "		);";
						$contentJs .= "	});";
						$contentJs .= "});";
						$doc->addScriptDeclaration($contentJs);
					}
					else
					{
						//get href and check for string javascript:
					}
					$return .= "<div class=\"button2-left\">";
					$return .= "<div class=\"".$button->get('name')."\">";
					$return .= "<a ".$modal." title=\"".$title."\" ".$href." ".$onclick." ".$rel." ".$id.">";
					$return .= $button->get('text') ? $button->get('text') : '$nbsp;';
					$return .= "</a>";
					$return .= "</div>";
					$return .= "</div>\n";
					
					
				break;
				
				case 'indicators-xtd':
					$id 		= ($button->get('id')) ? 'id="'.$button->get('id').'"' : 'id="'.$button->get('name').'_a"';
					$idString 	= ($button->get('id')) ? $button->get('id') : $button->get('name').'_a';
					$modal		= ($button->get('modal')) ? 'class="modal-button"' : null;
					$href		= ($button->get('link')) ? 'href="'.JURI::base().$button->get('link').'"' : null;
					$onclick	= ($button->get('onclick')) ? 'onclick="'.$button->get('onclick').'"' : null;
					$title		= ($button->get('title')) ? $button->get('title') : $button->get('text');
					$rel		= ($button->get('options')) ? 'rel="'.$button->get('options').'"' : null;
					
					// ?? $outputStyle = ($button->get('outputStyle')) ? $button->get('outputStyle') : 'standard';
					//$image		= ($button->get('image')) ? $button->get('image') : null;
					
					$return .= "<div class=\"button2-left\">";
						$return .= "<div class=\"".$button->get('name')."\">";
							$return .= "<a ".$modal." title=\"".$title."\" ".$href." ".$onclick." ".$rel." ".$id.">";
								$return .= $button->get('text') ? $button->get('text') : '$nbsp;';
							$return .= "</a>";
						$return .= "</div>";
					$return .= "</div>\n";
				
				break;
			}
		}
		return $return;
	}



	/**
	 * Load the indicator
	 *
	 * @param   array  $config  Associative array of indicator config paramaters
	 *
	 * @return  mixed
	 * @since   11.1
	 */
	protected function _loadIndicator($config = array(),$id = '')//,$type = 'editors-xtd')
	{
		// Check whether indicator is already loaded
		if (!is_null(($this->_indicator))) {
			return;
		}

		jimport('joomla.filesystem.file');

		// Build the path to the needed indicator plugin
		$indicatorName = JFilterInput::getInstance()->clean($this->_name, 'cmd');
		//
		$path = JPATH_ADMINISTRATOR.'/components/com_pagesanditems/extensions/piplugins/indicators/'.$indicatorName.'/'.$indicatorName.'.php';

		if (!JFile::exists($path)) {
				$message = JText::_('JLIB_HTML_EDITOR_CANNOT_LOAD');
				JError::raiseWarning(500, $message);
				return false;
		}

		// Require plugin file
		require_once $path;

		// Get the plugin
		$path = JPATH_ADMINISTRATOR.'/components/com_pagesanditems/includes';
		require_once($path.DS.'extensions'.DS.'pipluginhelper.php');
	
		$plugin		= ExtensionPiPluginHelper::getExtension('indicators', $this->_name);
		$params = new JRegistry;
		$params->loadString($plugin->params);
		$params->loadArray($config);
		$plugin->params = $params;
		$this->_params = $plugin->params;
		// Build indicator plugin classname
		$indicatorName = 'PagesAndItemsExtensionPiPluginIndicator'.$this->_name;

		if ($this->_indicator = new $indicatorName ($this, (array)$plugin)) {
			// Load plugin parameters
			$this->initialise($id);
			if($plugin->params->get('buttonType','editors-xtd') == 'editors-xtd')
			{
				JPluginHelper::importPlugin('editors-xtd');
			}
			elseif($plugin->params->get('buttonType','editors-xtd') == 'indicators-xtd')
			{
				ExtensionPiPluginHelper::importExtension('indicators-xtd');
			}
		}
	}
}
