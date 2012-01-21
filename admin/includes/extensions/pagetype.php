<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;

require_once(dirname(__FILE__).DS.'extension.php');

abstract class PagesAndItemsExtensionPagetype extends PagesAndItemsExtension
{
	var $componentId = null;
	var $subject = null;


	//move the next 4 to an own class underlayingCategories
	private $_parent = null;
	private $_items = null;
	private $_item = null;
	private $_maxLevelcat = null;

	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config = array())
	{
		//$this->subject = $subject;
		parent::__construct($subject, $config);
	}


	function getName()
	{
		return $this->_name;
	}

	function onDetach($pageType)
	{
		if($this->_name != $pageType)
		{
			$this->_subject->detach($this);
		}
		return true;
	}

	function onGetPagetype($name,$pageType)
	{
		$name = $this->_name;
		return true;
	}

	//function getUnknowIcon($identifier)
	function getUnknowIcon($component,$section = null)
	{
		$identifier = '';
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if($joomlaVersion < '1.6')
		{
			$identifier = $component->admin_menu_img;
		}
		else
		{
			$query = 'SELECT `img`';
			$query .= ' FROM `#__menu`';
			$query .=' WHERE `component_id` = "'.$this->db->getEscaped($component->id).'"';
			$query .=' AND (`menutype` = \'main\' OR `menutype` = \'menu\')';
			$query .= ' AND `level` = 1';
			$query1 = $query;
			
			if($section)
			{
				$query = 'SELECT `img`';
				$query .= ' FROM `#__menu`';
				$query .=' WHERE `title` = "'.$this->db->getEscaped($component->option).'_'.$this->db->getEscaped($section).'"';
				$query .=' AND (`menutype` = \'main\' OR `menutype` = \'menu\')';
			}
			
			$this->db->setQuery( $query );
			if(!$identifier = $this->db->loadResult())
			{
				if($section)
				{
					$this->db->setQuery( $query1 );
					if(!$identifier = $this->db->loadResult())
					{
					
					}
				}
			}

		}
		//$css = null;
		if (substr($identifier, 0, 6) == 'class:')
		{
			// We were passed a class name
			$class = substr($identifier, 6);

			$class = 'class:icon-16-'.$class;
			return $class;
		}
		else
		{
			// We were passed an image path... is it a themeoffice one?
			//if in J1.6 also?
			if (substr($identifier, 0, 15) == 'js/ThemeOffice/')
			{

				return null;
			}
			else
			{
				if ($identifier == null)
				{
					return null;
				}
				return $identifier;
			}
		}
		return null;
	}

	function onGetPageTypeIcons($icons,$pageType,$dirIcons, $option)
	{

		/*
		here we get the icons from the pagetypes
		we can work with path and images or class:

		and we can make in config an option for use the core icons?
		so we ignore the icons from the pagetype config.xml?

		ore we make as theme?
		like
		option PagesAndItems
		option Joomla Core
		*/
		$path = realpath(dirname(__FILE__).'/../../extensions'.DS.'pagetypes');
		//realpath
		$componentId = 0;
		$component = 0;
		$section = 0;
		if($option)
		{
			$parts = explode('.', $option);
			$option = (strpos($parts[0],'com_') !== false) ? strtolower($parts[0]) : 'com_'.strtolower($parts[0]);
			$section = count($parts) > 1 ? $parts[1] : '';
			
			$componentId = $this->getComponentId($option);
			$component = $this->getComponent($option);
			
		}

		if(file_exists($path.DS.$this->_name.DS.'config.xml') && $this->_name != 'component')
		{
			$dom =  new DOMDocument();
			$dom->load($path.DS.$this->_name.DS.'config.xml');
			$items = $dom->getElementsByTagName('icon');
			foreach($items as $item)
			{
				$name = $item->getAttribute('name');
				$class = false;
				foreach ($item->attributes as $attrName => $attrNode)
				{
					$icons->$name->$attrName = $item->getAttribute($attrName);
				}
				
				if(isset($icons->$name->pi_icons) && $icons->$name->pi_icons == '1')
				{
					if(isset($icons->$name->folder) && $icons->$name->folder != '')
					{
						$icons->$name->imageUrl = $dirIcons.'/'.$icons->$name->folder;
					}
					else
					{
						$icons->$name->imageUrl = $dirIcons.'/';
					}
				}
				elseif(isset($icons->$name->folder) && $icons->$name->folder != '')
				{
					$icons->$name->imageUrl = $icons->$name->folder;
				}
				elseif(isset($icons->$name->pi_icons) && $icons->$name->pi_icons == 'class')
				{
					$icons->$name->imageUrl = $icons->$name->image;
					$class= true;
				}
				else
				{
					$icons->$name->imageUrl = '';
				}
				
				if(isset($icons->$name->image) && $icons->$name->image != '' && !$class)
				{
					$icons->$name->imageUrl .= '/'.$icons->$name->image;
				}
				/*
				elseif(isset($icons->$name->image) && $icons->$name->image != '' && $class)
				{
					$icons->$name->imageUrl .= $icons->$name->image;
				}
				*/
				
				$icons->$name->imageUrl = str_replace('//','/',str_replace(DS,'/',$icons->$name->imageUrl));
			}
		}
		elseif(file_exists($path.DS.$this->_name.DS.'config.xml') && $componentId) //elseif(file_exists($path.DS.$this->_name.DS.'config.xml') && $this->getComponentId($component))
		{
			//set Standard


			//$component =& JTable::getInstance( 'component');
			//$component->load($componentId);

			if(file_exists($path.DS.'component'.DS.'config.xml'))
			{
				$dom =  new DOMDocument();
				$dom->load($path.DS.'component'.DS.'config.xml');
				$items = $dom->getElementsByTagName('icon');
				foreach($items as $item)
				{
					$name = $item->getAttribute('name');
					foreach ($item->attributes as $attrName => $attrNode)
					{
						$icons->componentDefault->$name->$attrName = $item->getAttribute($attrName);
					}
					if(isset($icons->componentDefault->$name->pi_icons) && $icons->componentDefault->$name->pi_icons == '1')
					{
						if(isset($icons->componentDefault->$name->folder) && $icons->componentDefault->$name->folder != '')
						{
							$icons->componentDefault->$name->imageUrl = $dirIcons.'/'.$icons->componentDefault->$name->folder;
						}
						else
						{
							$icons->componentDefault->$name->imageUrl = $dirIcons.'/';
						}
					}
					elseif(isset($icons->componentDefault->$name->folder) && $icons->componentDefault->$name->folder != '')
					{
						$icons->componentDefault->$name->imageUrl = $icons->componentDefault->$name->folder;
					}
					else
					{
						$icons->componentDefault->$name->imageUrl = '';
					}
					if(isset($icons->componentDefault->$name->image) && $icons->componentDefault->$name->image != '')
					{
						$icons->componentDefault->$name->imageUrl .= '/'.$icons->componentDefault->$name->image;
					}
					$icons->componentDefault->$name->imageUrl = str_replace('//','/',str_replace(DS,'/',$icons->componentDefault->$name->imageUrl));
				}
			}



			//if($this->getUnknowIcon($component->admin_menu_img))
			if($menu_img = $this->getUnknowIcon($component,$section))
			{
				if($menu_img != 'class:icon-16-component')
				{
					//we have an component-icon
					if(isset($icons->componentDefault))
					{
						$icons->componentDefault = $icons->componentDefault;
					}
					/*
					$icons->default->imageUrl = $component->admin_menu_img;
					$icons->items->imageUrl = $component->admin_menu_img;
					$icons->pagepropertys->imageUrl = $component->admin_menu_img;
					*/
					$icons->default->imageUrl = $menu_img;
					$icons->items->imageUrl = $menu_img;
					$icons->pagepropertys->imageUrl = $menu_img;
					//$icons->new->imageUrl = $menu_img;
					//$icons->edit->imageUrl = $menu_img;
				}
			}
			//ADD m: 22.03.2011
			elseif($option == 'com_users')
			{
				/* com_users have not defined an menu img
				so we create here the standard icon for com_users

				but i think we will make an pagetype for com_useres with own icons
				but then we must create each for each view
				*/
				$menu_img = 'class:icon-16-user';
				$icons->default->imageUrl = $menu_img;
				$icons->items->imageUrl = $menu_img;
				$icons->pagepropertys->imageUrl = $menu_img;
			}
			//END ADD m: 22.03.2011
			else
			{
				$icons = $icons->componentDefault;
				unset($icons->componentDefault);
			}
		}
		else //($this->itemType == 'not_installed_no_access')
		{
			if(file_exists($path.DS.$this->_name.DS.'config.xml'))
			{

				$dom =  new DOMDocument();
				$dom->load($path.DS.$this->_name.DS.'config.xml');
				$items = $dom->getElementsByTagName('icon');
				foreach($items as $item)
				{
					$name = $item->getAttribute('name');
					foreach ($item->attributes as $attrName => $attrNode)
					{
						$icons->$name->$attrName = $item->getAttribute($attrName);
					}
					if(isset($icons->$name->pi_icons) && $icons->$name->pi_icons == '1')
					{
						if(isset($icons->$name->folder) && $icons->$name->folder != '')
						{
							$icons->$name->imageUrl = $dirIcons.'/'.$icons->$name->folder;
						}
						else
						{
							$icons->$name->imageUrl = $dirIcons.'/';
						}
					}
					elseif(isset($icons->$name->folder) && $icons->$name->folder != '')
					{
						$icons->$name->imageUrl = $icons->$name->folder;
					}
					else
					{
						$icons->$name->imageUrl = '';
					}
					if(isset($icons->$name->image) && $icons->$name->image != '')
					{
						$icons->$name->imageUrl .= '/'.$icons->$name->image;
					}
					$icons->$name->imageUrl = str_replace('//','/',str_replace(DS,'/',$icons->$name->imageUrl));
				}

			}
			/*
			else
			{
				$icons = 'no xml';
			}
			*/
		}
		//$icons;

		return $icons;
	}

	function setComponentId($component)
	{
		$query = 'SELECT `id`' .
			' FROM `#__components`' .
			' WHERE `link` <> \'\'' .
			' AND `parent` = 0' .
			' AND `option` = "'.$this->db->getEscaped($component).'"';
		$this->db->setQuery( $query );
		$this->componentId = $this->db->loadResult();
	}

	function getComponentId($option)
	{
		//$version = new JVersion();
		//$joomlaVersion = $version->getShortVersion();
		//if($joomlaVersion < '1.6')
		/*
		$query = 'SELECT `id`' .
			' FROM `#__components`' .
			' WHERE `link` <> \'\'' .
			' AND `parent` = 0' .
			' AND `option` = "'.$this->db->getEscaped($option).'"';
		$this->db->setQuery( $query );

		$component_id = $this->db->loadResult();
		*/

		// Determine the component id.
		/*
		$query	= $this->db->getQuery(true);
		$query->select('extension_id AS "id" ');
		$query->from('#__extensions');
		$query->where('`type` = '.$this->db->quote('component'));
		$query->where('`element` = '.$this->db->quote($component));
		$this->db->setQuery($query);
		$component = $this->db->loadObject();
		*/
		$component = JComponentHelper::getComponent($option);

		if (isset($component->id))
		{
			//$component_id = $component->id;
			return $component->id;
		}

		return null;



		//if(!$this->componentId)
		//{
		$this->setComponentId($component);
		//}
		return $this->componentId;
	}
	function getComponent($option)
	{
		$component = JComponentHelper::getComponent($option);
		return $component;
		
		
		if (isset($component->id))
		{
			//$component_id = $component->id;
			return $component->id;
		}

		return false;
		return $component_id;
	}
}
