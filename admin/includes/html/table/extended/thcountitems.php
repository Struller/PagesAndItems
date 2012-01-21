<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );
require_once(realpath(dirname(__FILE__).DS.'..').DS.'td.php');

class htmlElementThCountItems extends htmlElementTh
{
	protected $_loadJs = true;
	
	function __construct($config = array())
	{
		//$config['attributes']['class'] = isset($config['attributes']['class']) ? $config['attributes']['class'].' th_reorder_rows_arrows_icon' : 'th_reorder_rows_arrows_icon';
		parent::__construct($config);
		//$this->_loadJs = isset($config['loadJs']) ? $config['loadJs'] : $this->_loadJs = true;
	}

	
	function thCountItems($content)
	{
		$html = '';
		$html .= '<span class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'"> # </span>';
		$html = parent::start($content.$html);
		$html .= parent::end();
		return $html;
	}
}