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
/**
 * Utility class for 
 *
 * @package     
 * @subpackage  
 * @since       
 */
class htmlElementTdState extends htmlElementTd
{
	function __construct($config = array())
	{
		$config['attributes']['class'] = isset($config['attributes']['class']) ? $config['attributes']['class'].' td_reorder_rows_state' : 'td_reorder_rows_state';
		if(isset($config['attributes']['style'])) $config['attributes']['style'] = $config['attributes']['style'];
		parent::__construct($config);
	}

	function tdState($content = '',$published = null, $id = null, $canDo = 0, $name = '',$unpublish = 1,$unpublishTip = 0)
	{
		$hand = 'pi_hand';
		switch($published)
		{
			case '1':
				//$state = 'published';
				$state = '<span class="state publish"></span>';
				$title = (!$unpublish && $unpublishTip) ? JText::_('COM_PAGESANDITEMS_PUBLISHED').' '.$unpublishTip : JText::_('COM_PAGESANDITEMS_PUBLISHED');
				$title = 'title="'.$title.'"';
				
				$alt = JText::_('COM_PAGESANDITEMS_PUBLISHED');
				$image = 'tick';
				$new_state = '0';
				if($unpublish)
				{
					$onclick = 'onclick="publish_unpublish_'.$name.'('.$id.','.$new_state.');"';
				}
				else
				{
					$onclick = '';
					$hand = '';
				}
			break;
			case '0':
				//$state = 'unpublished';
				$state = '<span class="state unpublish"></span>';
				$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
				$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
				$image = 'cross';
				$new_state = '1';
				$onclick = 'onclick="publish_unpublish_'.$name.'('.$id.','.$new_state.');"';
			break;

			case '2':
				//$state = 'archive';
				$state = '<span class="state archive"></span>';
				$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
				$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
				$image = 'archive';
				$new_state = '1';
				$onclick = 'onclick="publish_unpublish_'.$name.'('.$id.','.$new_state.');"';
			break;

			case '-1':
				//$state = 'archive';
				$state = '<span class="state archive"></span>';
				$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
				$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
				$image = 'archive';
				$new_state = '1';
				$onclick = 'onclick="publish_unpublish_'.$name.'('.$id.','.$new_state.');"';
			break;
			case '-2':
				//$state = 'trash';
				$state = '<span class="state trash"></span>';
				$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
				$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
				$image = 'trash';
				$new_state = '1';
				$onclick = 'onclick="publish_unpublish_'.$name.'('.$id.','.$new_state.');"';
			break;
		}
		
		if(!$canDo){
			$onclick = '';
			$title = 'title="'.JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THE_STATE').'"';
			$hand = '';
		}

		$content .= '<div class="div_reorder_rows_state">';
		$content .= ' <a class="jgrid hasTip'.($hand ? ' '.$hand : '').'" '.$title.' '.$onclick.' > '.$state.'</a>';
		$content .= '</div>';
		
		$html = parent::start($content);
		$html .= parent::end();
		return $html;
	}
}