<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );
require_once(realpath(dirname(__FILE__).DS.'..').DS.'td.php');

class htmlElementThOrderingIcon extends htmlElementTh
{
	protected $_loadJs = true;
	
	function __construct($config = array())
	{
		$config['attributes']['class'] = isset($config['attributes']['class']) ? $config['attributes']['class'].' th_reorder_rows_arrows_icon' : 'th_reorder_rows_arrows_icon';
		parent::__construct($config);
		$this->_loadJs = isset($config['loadJs']) ? $config['loadJs'] : $this->_loadJs = true;
	}

	
	function thOrderingIcon($content = '', $name='',$task='')
	{
		$html = '';
		if($this->_loadJs)
		{
			//$loaded = 
			$this->_loadBehavior($name);
			$this->_loadBehaviorDomready($name);
		}
		$html .= '<div class="div_reorder_rows_arrows_th_icon">';
			$html .= '<span id="reorderSave'.$name.'" class="display_none">';
				$html .= '<a class="saveReorder no_underline" href="javascript: saveReorderItemsRows(\''.$name.'\',\''.$task.'\');">';
					$html .= '<span class="state saveReorderIcon" title="'.JText::_('JLIB_HTML_SAVE_ORDER').'">';
						//$html .= '<span class="text">';
							$html .= '&nbsp;';//JText::_('COM_PAGESANDITEMS_ORDERING');
						//$html .= '</span>';
					$html .= '</span>';
				$html .= '</a>';
			$html .= '</span>';
			$html .= '<span id="reorderSaveIcon'.$name.'" class="">';
				$html .= '<a  class="reorder no_underline" >';
					$html .= '<span title="'.JText::_('JGRID_HEADING_ORDERING').'" class="state reorderIcon">';
						//$html .= '<span class="text">';
							$html .= '&nbsp;';//JText::_('COM_PAGESANDITEMS_ORDERING');
						//$html .= '</span>';
					$html .= '</span>';
				$html .= '</a>';
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
		JHTML::script('save_reorder_rows.js', PagesAndItemsHelper::getDirJS().'/',false);
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
		"	var reordered = document.id('items_".$name."_are_reordered');"."\n".
		"	if(reordered){"."\n".
		"	reordered.addEvent('change', function(){"."\n".
		"		document.id('reorderSave".$name."').removeClass('display_none');"."\n".
		"		document.id('reorderSaveIcon".$name."').addClass('display_none');"."\n".
		"	});"."\n".
		"	}"."\n".
		"});");
		return false;
	}

}