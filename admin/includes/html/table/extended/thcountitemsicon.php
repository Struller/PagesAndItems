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

class htmlElementThCountItemsIcon extends htmlElementTh
{
	protected $_loadJs = true;
	
	function __construct($config = array())
	{
		//$config['attributes']['class'] = isset($config['attributes']['class']) ? $config['attributes']['class'].' th_reorder_rows_arrows_icon' : 'th_reorder_rows_arrows_icon';
		parent::__construct($config);
		//$this->_loadJs = isset($config['loadJs']) ? $config['loadJs'] : $this->_loadJs = true;
	}

	
	function thCountItemsIcon($content)
	{
		$html = '';
		
		//$html .= '<span class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'"> # </span>';
		$html .= '<div class="th_icon">';
			$html .= '<span>';
				$html .= '<a class="countItems no_underline" >';
					$html .= '<span class="state countItemsIcon" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'">';
						//$html .= '<span class="text">';
						$html .= '&nbsp;';
						//	$html .= JText::_('COM_PAGESANDITEMS_ORDERING');
						//$html .= '</span>';
					$html .= '</span>';
				$html .= '</a>';
			$html .= '</span>';
		$html .= '</div>';

		$html = parent::start($content.$html);
		$html .= parent::end();
		return $html;
	}
}