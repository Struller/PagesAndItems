<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );
require_once(realpath(dirname(__FILE__).DS.'..').DS.'td.php');

class htmlElementTdOrdering extends htmlElementTd
{
	protected $_loadJs = true;
	
	function __construct($config = array())
	{
		$config['attributes']['class'] = isset($config['attributes']['class']) ? $config['attributes']['class'].' td_reorder_rows_arrows' : 'td_reorder_rows_arrows';
		parent::__construct($config);
		$this->_loadJs = isset($config['loadJs']) ? $config['loadJs'] : $this->_loadJs = true;
		$this->_addJs = isset($config['addJs']) ? $config['addJs'] : $this->_addJs = false;
	}

	
	function tdOrdering($content = '',$countRows = null, $countColumns = null,$i = null, $name)
	{
		$html = '';
		if($this->_loadJs)
		{
			//$loaded = 
			$this->_loadBehavior($name);
			//$loaded = 
			$this->_loadBehaviorDomready($name);
		}
		
		if(!$loaded = $this->_loadInputItemsReorder($name))
		{
			$html .= '<input name="items_'.$name.'_are_reordered" id="items_'.$name.'_are_reordered" type="hidden" value="0" />';
			$html .= '<input name="items_'.$name.'_total" id="items_'.$name.'_total" type="hidden" value="'.$countRows.'" />';
		}
		
		
		$addJs1 = (isset($this->_addJs) && $this->_addJs) ? $this->_addJs.'('.$i.','.($i-1).',\''.$name.'\','.$countColumns.');': '';
		$addJs2 = (isset($this->_addJs) && $this->_addJs) ? $this->_addJs.'('.$i.','.($i+1).',\''.$name.'\','.$countColumns.');': '';
		$html .= '<div class="div_reorder_rows_arrows">';
			$html .= '<span>';
			if($i!=1)
			{
				$html .= '<a title="'.JText::_('JLIB_HTML_MOVE_UP').'" class="jgrid" href="javascript: reorderItemsRows('.$i.','.($i-1).',\''.$name.'\','.$countColumns.');'.$addJs1.'">';
					$html .= '<span class="state uparrow">';
						$html .= '<span class="text">';
							$html .= JText::_('JLIB_HTML_MOVE_UP');
						$html .= '</span>';
					$html .= '</span>';
				$html .= '</a>';
			}
			else
			{
				$html .= '&nbsp;';
			}
			$html .= '</span>';
		
			$html .= '<span>';
			if($i != $countRows)
			{
				$html .= '<a title="'.JText::_('JLIB_HTML_MOVE_DOWN').'" class="jgrid" href="javascript: reorderItemsRows('.$i.','.($i+1).',\''.$name.'\','.$countColumns.');'.$addJs2.'">';
					$html .= '<span class="state downarrow">';
						$html .= '<span class="text">';
							$html .= JText::_('JLIB_HTML_MOVE_DOWN');
						$html .= '</span>';
					$html .= '</span>';
				$html .= '</a>';
			}
			else
			{
				$html .= '&nbsp;';
			}
			$html .= '</span>';
		$html .= '</div>';

		$html = parent::start($content.$html);
		$html .= parent::end();
		return $html;
	}


	/**
	 * Load the JavaScript behavior.
	 *
	 * @param   string  $group   The pane identifier.
	 * @param   array   $params  Array of options.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected static function _loadBehavior($name)
	{
		static $loaded;
		if ($loaded)
		{
			return $loaded;
		}
		$loaded = true;
		JHTML::script('reorder_rows.js', PagesAndItemsHelper::getDirJS().'/',false);
		return false;
	}

	protected static function _loadBehaviorDomready($name)
	{
		static $loadedDomready = array();
		if (isset($loadedDomready[$name]))
		{
			return $loadedDomready[$name];
		}
		$loadedDomready[$name] = true;
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("\n".
		"window.addEvent('domready', function()"."\n".
		"{"."\n".
		"	var reordered =document.id('items_".$name."_are_reordered');"."\n".
		"	if(reordered){"."\n".
		"	reordered.value = 0;}"."\n".
		"});");
		
		
		return false;
	}


	protected static function _loadInputItemsReorder($name)
	{
		static $loadedInputItemsReorder = array();
		if (isset($loadedInputItemsReorder[$name]))
		{
			return $loadedInputItemsReorder[$name];
		}
		$loadedInputItemsReorder[$name] = true;
		return false;
	}
}