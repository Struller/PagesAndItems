<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );
require_once(realpath(dirname(__FILE__)).DS.'table.php');
/**
 * Utility class for 
 *
 * @package     
 * @subpackage  
 * @since       
 */
class htmlTableItems extends htmlTable
{
	protected $_countRows = null;
	protected $_countColumns = null;
	protected $_itemName = null;
	protected $_itemTask = null;
	
	protected $_current = null;
	protected $_currentBody = null;
	protected $_currentRow = 0;
	protected $_currentColumn = 0;
	
	protected $_colored = 1;
	
	function __construct($config = array())
	{
		$noAddClass = (isset($config['noAddClass']) && $config['noAddClass']) ? $config['noAddClass'] : false;
		
		$config['attributesTable']['class'] = isset($config['attributesTable']['class']) ? $config['attributesTable']['class'].($noAddClass ? '' : ' reorder_rows reorder_rows_arrows' ) : ($noAddClass ? '' : 'reorder_rows reorder_rows_arrows' );
		parent::__construct($config);
		
		$this->_countRows = isset($config['countRows']) ? $config['countRows'] : null;
		$this->_countColumns = isset($config['countColumns']) ? $config['countColumns'] : null;
		$this->_itemName = isset($config['itemName']) ? $config['itemName'] : null;
		$this->_itemTask = isset($config['itemTask']) ? $config['itemTask'] : null;
		
	}
	
	function getName() {
		return $this->_itemName;
	}
	
	function getCountRows() {
		return $this->_countRows;
	}
	
	function getCountColumns() {
		return $this->_countColumns;
	}
	
	protected function startThead($config) {
		$this->_current = 'thead';
		return parent::startThead($config);
	}
	
	protected function endThead() {
		return parent::endThead();
	}

	protected function startTfoot($config) {
		$this->_current = 'tfoot';
		return parent::startTfoot($config);
	}
	
	protected function endTfoot() {
		return parent::endTfoot();
	}

	protected function startTbody($config) {
		$this->_current = 'tbody';
		$this->_currentBody = true;
		return parent::startTbody($config);
	}
	
	protected function endTbody() {
		return parent::endTbody();
	}
	
	protected function startTr($config) {
		$currentRow = ($this->_current == 'tbody') ? $this->_currentRow : 0;
		if($this->_current == 'tbody')
		{
			$currentRow++;
			$this->_currentRow = $currentRow;
		}
		return parent::startTr($config);
	}

	protected function endTr() {
		$this->_currentRow = $this->_currentBody ? $this->_currentRow : 0;
		$this->_currentColumn = 0;
		return parent::endTr();
	}

	function trColored($config = array())
	{
		$this->_colored = !$this->_colored ? 1 : 0; //1 - $this->_colored;
		$config['attributes']['class'] = isset($config['attributes']['class']) ? $config['attributes']['class'].' row'.$this->_colored : 'row'.$this->_colored;
		return parent::tr($config);
	}

	function td($content = '',$config = array())
	{
		$currentColumn = $this->_currentRow ? $this->_currentColumn : 0;
		
		if($this->_currentRow)
		{
			$currentColumn++;
			$this->_currentColumn = $currentColumn;
		}
		if($this->_currentColumn && $this->_currentColumn <= $this->_countColumns)
		{
			$config['attributes']['id'] = 'items_'.$this->_itemName.'_column_'.$this->_currentColumn.'_'.$this->_currentRow;
		}
		return parent::td($content,$config);
	}
	
	function tdState($content = '',$config = array())
	{
		$currentColumn = $this->_currentRow ? $this->_currentColumn : 0;
		if($this->_currentRow)
		{
			$currentColumn++;
			$this->_currentColumn = $currentColumn;
		}
		
		if($this->_currentColumn && $this->_currentColumn <= $this->_countColumns)
		{
			$config['attributes']['id'] = 'items_'.$this->_itemName.'_column_'.$this->_currentColumn.'_'.$this->_currentRow;
		}
		
		require_once('table'.DS.'extended'.DS.'tdstate.php');
		$state = new htmlElementTdState($config);
		$published = isset($config['rowPublished']) ? $config['rowPublished'] : 0;
		$id = isset($config['rowId']) ? $config['rowId'] : 0;
		$canDo = isset($config['canDo']) ? $config['canDo'] : 0;
		$unpublish = isset($config['unpublish']) ? $config['unpublish'] : 1;
		$unpublishTip = isset($config['unpublishTip']) ? $config['unpublishTip'] : 0;
		$html = $state->tdState($content,$published, $id, $canDo, $this->_itemName,$unpublish,$unpublishTip);
		$html = $this->_store ? $this->setOutput($html) : $html;
		return $html;
	}
	
	function tdOrdering($content = '', $config = array())
	{
		require_once('table'.DS.'extended'.DS.'tdordering.php');
		$ordering = new htmlElementTdOrdering($config);
		
		$countRows = isset($config['countRows']) ? $config['countRows'] : null;
		$currentRow = isset($config['currentRow']) ? $config['currentRow'] : null;

		$html = $ordering->tdOrdering($content,$this->_countRows,$this->_countColumns,$currentRow, $this->_itemName);
		$html = $this->_store ? $this->setOutput($html) : $html;
		return $html;
		
	}
	
	
	function thOrderingIcon($content = '', $config = array())
	{
		require_once('table'.DS.'extended'.DS.'thorderingicon.php');
		$ordering = new htmlElementThOrderingIcon($config);
		$html = $ordering->thOrderingIcon($content, $this->_itemName,$this->_itemTask);
		$html = $this->_store ? $this->setOutput($html) : $html;
		return $html;
	}
	
	
	function thOrdering($content = '', $config = array())
	{
		
		
		//$content .= '<a onclick="alert(\'saveorder\')">'.
		//$content .= '<a class="saveorder" onclick="javascript:alert(\'banners.saveorder\')" title="Reihenfolge speichern">';

		//$content .= '<a onclick="javascript:alert(\'banners.saveorder\')" title="Reihenfolge speichern">';

		$content .= JText::_('COM_PAGESANDITEMS_ORDERING');

		//$content .='<img src="'.PagesAndItemsHelper::getDirIcons().'base/icon-16-save.png">';
		//$content .= '</a>';
		
		$this->th($content, $config);
	}
	
	function thState($content = '', $config = array())
	{
		$content .= JText::_('COM_PAGESANDITEMS_PUBLISHED');
		$this->th($content, $config);
	}
	
	function thTitle($content = '', $config = array())
	{
		$content .= JText::_('COM_PAGESANDITEMS_TITLE');
		$this->th($content, $config);
	}
	
	function thType($content = '', $config = array())
	{
		$content .= JText::_('COM_PAGESANDITEMS_TYPE');
		$this->th($content, $config);
	}

	function thCountItemsIcon($content = '', $config = array())
	{
		require_once('table'.DS.'extended'.DS.'thcountitemsicon.php');
		$ordering = new htmlElementThCountItemsIcon($config);
		$html = $ordering->thCountItemsIcon($content); //, $this->_itemName,$this->_itemTask);
		$html = $this->_store ? $this->setOutput($html) : $html;
		return $html;
	}

	function thCountItems($content = '', $config = array())
	{
		require_once('table'.DS.'extended'.DS.'thcountitems.php');
		$ordering = new htmlElementThCountItems($config);
		$html = $ordering->thCountItems($content); //, $this->_itemName,$this->_itemTask);
		$html = $this->_store ? $this->setOutput($html) : $html;
		return $html;
	}


	function header($columns = array())
	{
		$this->thead();
		$rows = array();
		if(!isset($columns['rows']))
		{
			$rows[] = $columns;
		}
		else
		{
			$rows = $columns['rows'];
		}
		foreach($rows as $key => $columns)
		{
			$this->tr();
			foreach($columns as $column)
			{
				$type = 'th';
				$config = array();
				$content = '';
				foreach($column as $key => $value)
				{
					
					switch($key)
					{
						case 'type':
							$type = ($value != 'th') ? 'th'.ucfirst($value) : 'th';
						break;
						
						case 'content':
							$content = $value;
						break;
						
						case 'config':
							$config = $value;
						break;
					}
				}
				$this->$type($content, $config);
			}
		}
	}

	function footer($columns = array())
	{
		$this->thead();
		$rows = array();
		if(!isset($columns['rows']))
		{
			$rows[] = $columns;
		}
		else
		{
			$rows = $columns['rows'];
		}
		foreach($rows as $key => $columns)
		{
			$this->tr();
			foreach($columns as $column)
			{
				$type = 'td';
				$config = array();
				$content = '';
				foreach($column as $key => $value)
				{
					switch($key)
					{
						case 'type':
							$type = ($value != 'td') ? 'td'.ucfirst($value) : 'td';
						break;
						
						case 'content':
							$content = $value;
						break;
						
						case 'config':
							$config = $value;
						break;
					}
				}
				$this->$type($content, $config);
			}
		}
	}
	
	function tdAdd($content = '', $config = array())
	{
		
	}
	
	function tdDelete($content = '', $config = array())
	{
		
	}
	
	function tdAddDelete($content = '', $config = array())
	{
		
	}

	function thAdd($content = '', $config = array())
	{
		
	}
	
	function thDelete($content = '', $config = array())
	{
		
	}
	
	function thAddDelete($content = '', $config = array())
	{
		
	}


}