<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );
require_once(realpath(dirname(__FILE__)).DS.'attributes.php');
/**
 * Utility class for create Table 
 *
 * @package     
 * @subpackage  
 * @since       
 */
class htmlTable extends htmlAttribute
{
	protected $_table = array();
	
	protected $_currentTableChild = ''; //'thead'|'tfoot'|'tbody'|0
	protected $_currentTableBody = 0;
	protected $_output = '';
	protected $_store = true;
	

	protected $_thead = null;
	protected $_tfoot = null;
	protected $_tbody = null;
	protected $_tbodies = array(); //???
	protected $_tr = null;
	protected $_td = null;
	protected $_th = null;
	protected $_elementTable = null;

	protected $_attributtesTable = null;
	
	function __construct($config = array())
	{
		/*
		W3C: W3C Standard.
		Collection 	Description 	W3C
		cells 	Returns a collection of all <td> or <th> elements in a table 	No
		rows 	Returns a collection of all <tr> elements in a table 	Yes
		tBodies 	Returns a collection of all <tbody> elements in a table 	Yes

		Table Object Properties Deprecated:
		Property	Description
		align		Deprecated. Sets or returns the alignment of a table according to surrounding text. Use style.textAlign instead
		background	Deprecated. Sets or returns the background image of a table. Use style.background instead
		bgColor		Deprecated. Sets or returns the background color of a table. Use style.backgroundColor instead
		border		Deprecated. Sets or returns the width of the table border. Use style.border instead
		height		Deprecated. Sets or returns the height of a table. Use style.height instead
		width		Deprecated. Sets or returns the width of the table. Use style.width instead
		
		Table Object Properties WC3:
		Property	Description
		caption		Returns the caption of a table
		
		cellPadding	Sets or returns the amount of space between the cell border and cell content
		cellSpacing	Sets or returns the amount of space between the cells in a table
		frame		Sets or returns which outer-borders (of a table) that should be displayed
		rules		Sets or returns which inner-borders (between the cells) that should be displayed in a table
		summary		Sets or returns a description of the data in a table
		*/
		parent::__construct($config);
		$this->_attributesTable = isset($config['attributesTable']) ? $config['attributesTable'] : array();
		$this->_store = isset($config['output']) ? $config['output'] : true;
	}

	function setOutput($html,$type = null)
	{
		if(count($this->_table))
		{
			switch($type)
			{
				case 'table':
				case 'thead':
				case 'tfoot':
				case 'tbody':
				case 'tr':
					return $html;
				break;
				
				default:
					switch($this->_currentTableChild)
					{
						case 'thead':
							
							$countRows = count($this->_table['table']['thead']['rows']) ? count($this->_table['table']['thead']['rows']) -1 : 0;
							$countColumns = (isset($this->_table['table']['thead']['rows'][$countRows]['columns']) && count($this->_table['table']['thead']['rows'][$countRows]['columns'])) ? count($this->_table['table']['thead']['rows'][$countRows]['columns']) : 0;
							$this->_table['table']['thead']['rows'][$countRows]['columns'][$countColumns] = $html;
						break;
						
						case 'tfoot':
							$countRows = count($this->_table['table']['tfoot']['rows']) ? count($this->_table['table']['tfoot']['rows']) -1 : 0;
							$countColumns = (isset($this->_table['table']['tfoot']['rows'][$countRows]['columns']) && count($this->_table['table']['tfoot']['rows'][$countRows]['columns'])) ? count($this->_table['table']['tfoot']['rows'][$countRows]['columns']) : 0;
							$this->_table['table']['tfoot']['rows'][$countRows]['columns'][$countColumns] = $html;
						break;
						
						case 'tbody':
							$countBody = isset($config['body']) ? isset($config['body']) : (count($this->_table['table']['tbodies']) ? (count($this->_table['table']['tbodies']) -1) : 0);
							$countRows = count($this->_table['table']['tbodies'][$countBody]['rows']) ? count($this->_table['table']['tbodies'][$countBody]['rows']) -1 : 0;
							$countColumns = (isset($this->_table['table']['tbodies'][$countBody]['rows'][$countRows]['columns']) && count($this->_table['table']['tbodies'][$countBody]['rows'][$countRows]['columns'])) ? count($this->_table['table']['tbodies'][$countBody]['rows'][$countRows]['columns']) : 0;
							$this->_table['table']['tbodies'][$countBody]['rows'][$countRows]['columns'][$countColumns] = $html;
						break;
					}
				break;
			}
		}
		else
		{
			$output = $this->_output.$html;
			$this->_output = $output;
		}
		return true;
	}

	function getOutput()
	{
		if(count($this->_table))
		{
			$output = array();
			$output[] = $this->_table['table']['start'];
			if(isset($this->_table['table']['thead']) && count($this->_table['table']['thead']) )
			{
				$output[] = $this->_table['table']['thead']['start'];
				if(isset($this->_table['table']['thead']['rows']) && count($this->_table['table']['thead']['rows']))
				{
					foreach($this->_table['table']['thead']['rows'] as $rowKey => $rowValue)
					{
						$output[] = $this->_table['table']['thead']['rows'][$rowKey]['start'];
						if(isset($this->_table['table']['thead']['rows'][$rowKey]['columns']) && count($this->_table['table']['thead']['rows'][$rowKey]['columns']))
						{
							foreach($this->_table['table']['thead']['rows'][$rowKey]['columns'] as $column)
							{
								$output[] = $column;
							}
						}
						$output[] = $this->_table['table']['thead']['rows'][$rowKey]['end'];
					}
				}
				$output[] = $this->_table['table']['thead']['end'];
			}
			
			if(isset($this->_table['table']['tfoot']) && count($this->_table['table']['tfoot']) && $this->_table['table']['tfoot'])
			{
				$output[] = $this->_table['table']['tfoot']['start'];
				if(isset($this->_table['table']['tfoot']['rows']) && count($this->_table['table']['tfoot']['rows']))
				{
					foreach($this->_table['table']['tfoot']['rows'] as $rowKey => $rowValue)
					{
						$output[] = $this->_table['table']['tfoot']['rows'][$rowKey]['start'];
						if(isset($this->_table['table']['tfoot']['rows'][$rowKey]['columns']) && count($this->_table['table']['tfoot']['rows'][$rowKey]['columns']))
						{
							foreach($this->_table['table']['tfoot']['rows'][$rowKey]['columns'] as $column)
							{
								$output[] = $column;
							}
						}
						$output[] = $this->_table['table']['tfoot']['rows'][$rowKey]['end'];
					}
				}
				$output[] = $this->_table['table']['tfoot']['end'];
			}
			
			if(isset($this->_table['table']['tbodies']) && count($this->_table['table']['tbodies'])  && $this->_table['table']['tbodies'])
			{
				foreach($this->_table['table']['tbodies'] as $bodyKey => $bodyValue)
				{
					$output[] = $this->_table['table']['tbodies'][$bodyKey]['start'];
					if(isset($this->_table['table']['tbodies'][$bodyKey]['rows']) && count($this->_table['table']['tbodies'][$bodyKey]['rows']))
					{
						foreach($this->_table['table']['tbodies'][$bodyKey]['rows'] as $rowKey => $rowValue)
						{
							$output[] = $this->_table['table']['tbodies'][$bodyKey]['rows'][$rowKey]['start'];
							if(isset($this->_table['table']['tbodies'][$bodyKey]['rows'][$rowKey]['columns']) && count($this->_table['table']['tbodies'][$bodyKey]['rows'][$rowKey]['columns']))
							{
								foreach($this->_table['table']['tbodies'][$bodyKey]['rows'][$rowKey]['columns'] as $column)
								{
									$output[] = $column;
								}
							}
							$output[] = $rowValue['end'];
						}
					}
					$output[] = $this->_table['table']['tbodies'][$bodyKey]['end'];
				}
			}
			$output[] = $this->_table['table']['end'];
			return implode($output);
		}
		else
		{
			return $this->_output;
		}
	}
	
	function table($captionConfig = array())
	{
		$this->_store = true;
		$this->_table['table'] = '';
		$this->_table['table']['start'] = $this->startTable($captionConfig);
		$this->_table['table']['end'] = $this->endTable();
	}
	
	protected function startTable($captionConfig = array()) {
		require_once('table'.DS.'table.php');
		$attributes = array();
		foreach($this->_attributesTable as $key => $value)
		{
			if($value || $value == 0)
			$attributes[$key] = htmlspecialchars($value);
		}
		$config =array('attributes'=>$attributes);
		$this->_elementTable = new htmlElementTable($config);
		$html = $this->_elementTable->start($captionConfig);
		$html = $this->_store ? $this->setOutput($html,'table') : $html;
		return $html;
	}

	protected function endTable() {
		$html = (!$this->_elementTable) ? '</table>' : $this->_elementTable->end();
		$this->_elementTable =  null;
		$html = $this->_store ? $this->setOutput($html,'table') : $html;
		return $html;
	}
	
	function thead($config = array())
	{
		$this->_store = true;
		$this->_currentTableChild = 'thead';
		$this->_table['table']['thead']['start'] = $this->startThead($config);
		$this->_table['table']['thead']['end'] = $this->endThead();
	}
	
	protected function startThead($config) {
		require_once('table'.DS.'thead.php');
		$this->_thead = new htmlElementThead($config);
		$html = $this->_thead->start();
		$html = $this->_store ? $this->setOutput($html,'thead') : $html;
		return $html;
	}
	
	protected function endThead() {
		$html = (!$this->_thead) ? '</thead>' : $this->_thead->end();
		$this->_thead =  null;
		$html = $this->_store ? $this->setOutput($html,'thead') : $html;
		return $html;
	}
	
	
	function tfoot($config = array())
	{
		$this->_store = true;
		$this->_currentTableChild = 'tfoot';
		$this->_table['table']['tfoot']['start'] = $this->startTfoot($config);
		$this->_table['table']['tfoot']['end'] = $this->endTfoot();
	}
	
	protected function startTfoot($config) {
		require_once('table'.DS.'tfoot.php');
		$this->_tfoot = new htmlElementTfoot($config);
		$html = $this->_tfoot->start();
		$html = $this->_store ? $this->setOutput($html,'tfoot') : $html;
		return $html;
	}
	
	protected function endTfoot() {
		$html = (!$this->_tfoot) ? '</thead>' : $this->_tfoot->end();
		$this->_tfoot =  null;
		$html = $this->_store ? $this->setOutput($html,'tfoot') : $html;
		return $html;
	}
	
	
	function tbody($config = array())
	{
		$this->_store = true;
		$this->_currentTableChild = 'tbody';
		$count = isset($config['body']) ? $config['body'] : (isset($this->_table['table']['tbodies']) && count($this->_table['table']['tbodies']) ? count($this->_table['table']['tbodies']) : 0);
		$this->_currentTableBody = $count;
		$this->_table['table']['tbodies'][$count]['start'] = $this->startTbody($config);
		$this->_table['table']['tbodies'][$count]['end'] = $this->endTbody();
	}
	
	protected function startTbody($config) {
		require_once('table'.DS.'tbody.php');
		$this->_tbody = new htmlElementTbody($config);
		$html = $this->_tbody->start();
		$html = $this->_store ? $this->setOutput($html,'tbody') : $html;
		return $html;
	}
	
	protected function endTbody() {
		$html = (!$this->_tbody) ? '</tbody>' : $this->_tbody->end();
		$this->_tbody =  null;
		$html = $this->_store ? $this->setOutput($html,'tbody') : $html;
		return $html;
	}
	
	function tr($config = array())
	{
		switch($this->_currentTableChild)
		{
			case 'thead':
				$count = (isset($this->_table['table']['thead']['rows']) && count($this->_table['table']['thead']['rows'])) ? count($this->_table['table']['thead']['rows']) : 0;
				$this->_table['table']['thead']['rows'][$count]['start'] = $this->startTr($config);
				$this->_table['table']['thead']['rows'][$count]['end'] = $this->endTr();
			break;
			
			case 'tfoot':
				$count = (isset($this->_table['table']['tfoot']['rows']) && count($this->_table['table']['tfoot']['rows'])) ? count($this->_table['table']['tfoot']['rows']) : 0;
				$this->_table['table']['tfoot']['rows'][$count]['start'] = $this->startTr($config);
				$this->_table['table']['tfoot']['rows'][$count]['end'] = $this->endTr();

			break;
			
			case 'tbody':
				$countBody = isset($config['body']) ? isset($config['body']) : ( count($this->_table['table']['tbodies']) ? (count($this->_table['table']['tbodies']) -1) : 0);
				$count = (isset($this->_table['table']['tbodies'][$countBody]['rows']) && count($this->_table['table']['tbodies'][$countBody]['rows'])) ? count($this->_table['table']['tbodies'][$countBody]['rows']) : 0;
				$this->_table['table']['tbodies'][$countBody]['rows'][$count]['start'] = $this->startTr($config);
				$this->_table['table']['tbodies'][$countBody]['rows'][$count]['end'] = $this->endTr();
			break;
			
			default:
				$count = -1;
			break;
		}
	}
	
	protected function startTr($config) {
		require_once('table'.DS.'tr.php');
		$this->_tr = new htmlElementTr($config);
		$html = $this->_tr->start();
		$html = $this->_store ? $this->setOutput($html,'tr') : $html;
		return $html;
	}
	
	protected function endTr() {
		$html = (!$this->_tr) ? '</tr>' : $this->_tr->end();
		$this->_tr =  null;
		$html = $this->_store ? $this->setOutput($html,'tr') : $html;
		return $html;
	}
	
	function td($content = '',$config = array())
	{
		$htmlStart = $this->startTd($content,$config);
		$htmlEnd = $this->endTd();
		$html = $this->_store ? $this->setOutput($htmlStart.$htmlEnd,'td') : $htmlStart.$htmlEnd;
		return $html;
	}
	
	protected function startTd($content = '',$config = array())
	{
		require_once('table'.DS.'td.php');
		$this->_td = new htmlElementTd($config);
		$html = $this->_td->start($content);
		return $html;
	}
	
	protected function endTd()
	{
		$html = (!$this->_td) ? '</td>' : $this->_td->end();
		$this->_td = null;
		return $html;
	}
	
		
	function th($content = '',$config = array())
	{
		$htmlStart = $this->startTh($content,$config);
		$htmlEnd = $this->endTh();
		$html = $this->_store ? $this->setOutput($htmlStart.$htmlEnd,'th') : ($htmlStart.$htmlEnd);
		return $html;
	}
	
	protected function startTh($content = '',$config = array())
	{
		require_once('table'.DS.'th.php');
		$this->_th = new htmlElementTh($config);
		$html = $this->_th->start($content);
		return $html;
	}
	
	protected function endTh()
	{
		$html = (!$this->_th) ? '</th>' : $this->_th->end();
		$this->_th = null;
		return $html;
	}
}