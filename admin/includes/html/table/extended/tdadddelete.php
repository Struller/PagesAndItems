<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die( 'Restricted access' );
require_once(realpath(dirname(__FILE__).DS.'..').DS.'td.php');
/**
 * Utility class for 
 *
 * @package     
 * @subpackage  
 * @since       
 */
class htmlElementTdAddDelete extends htmlElementTd
{

	function __construct($config = array())
	{
		parent::__construct($config);
		//$this->_name = isset($config['name']) ? $config['name'] : null;
		//$this->_name = isset($config['attributes']['name']) ? $config['attributes']['name'] : null;
	}

	function tdAddDelete($content = '',$countRows = null, $currentRow = null)
	{
		$content = $content.' countRows:'.$countRows.' currentRow '.$currentRow;
		
		$html = parent::start($content);
		$html .= parent::end();
		return $html;
	}
/*
				//start add/delete columns
				if($addDelete)
				{
					$html .= '<td class="td_reorder_rows_add_delete">'; // style="width:50px;">';
						$button = PagesandItemsHelper::getButtonMaker();
						$button->imagePath = PagesandItemsHelper::getDirIcons();
						$button->buttonType = 'input';
						$button->class = 'fltlft button button_icon'; // button_transparent';
						//$button->style = 'border:0;background-color:transparent;';
						$button->onclick = 'addRow(\''.$name.'\','.$i.');';
						$button->imageName = 'base/icon-16-plus-small.png';
						$html .= $button->makeButton();

						$button = PagesandItemsHelper::getButtonMaker();
						$button->imagePath = PagesandItemsHelper::getDirIcons();
						$button->buttonType = 'input';
						$button->class = 'fltlft  button button_icon';
						//$button->style = 'border:0;background-color:transparent;';
						$button->onclick = 'deleteRow(\''.$name.'\','.$i.');';
						$button->imageName = 'base/icon-16-minus-small.png';
						$html .= $button->makeButton();
					$html .= '</td>';
				}
				//end add/delete columns



*/

}