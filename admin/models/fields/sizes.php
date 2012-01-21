<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;


/**
 * Supports an HTML select list of plugins
 *
 * @package		Joomla.Administrator
 * @subpackage	com_newsfeeds
 * @since		1.6
 */
//class JElementSizes extends JElement
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldSizes extends JFormField //JElementSpacerjtext extends JElement
{
	protected $type = 'Sizes';


	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';


		$sizes = array ();
		$sizes = json_decode(str_replace('\'','"',$this->value));
		$value = str_replace('"','\'',json_encode($sizes));


		//return JHtml::_('access.level', $this->name, $this->value, $attr, $options, $this->id);
		return $this->sizesHtml($sizes,$this->name, $this->id,$this->value);



		return implode($html);
	}


	function sizesHtml($sizes,$name, $id, $value)
	{
		//$this->name.'" id="'.$this->id
		$html = '';
		//hide the data on the page
		$html .= '<input type="hidden" id="'.$id.'" name="'.$name.'"  value="'.$value.'">';
		$html .= '<div class="paddingList">';
			$html .= '<table border="0" cellpadding="0" cellspacing="0" width="100%">'; // style="display: none;">';
				$html .= '<tbody>';
				//headers
					$html .= '<tr>';
						$html .= '<td id="pagesheader_column_1">';
							$html .= '<strong>'.JText::_('visible').'</strong>';
						$html .= '</td>';

						$html .= '<td id="pagesheader_column_2">';
							$html .= '<strong>'.JText::_('Name').'</strong>';
						$html .= '</td>';

						$html .= '<td colspan="21" id="pagesheader_column_3">';
							$html .= '<strong>'.JText::_('Ordering').'</strong>';
						$html .= '</td>';
					$html .= '</tr>';
					//loop through items and echo data to hidden fields
				$count = count($sizes);
				$counter = 0;
				foreach($sizes as $row)
				{
					$html .= '<tr>';
						//column 1
						$html .= '<td id="'.$name.'_column_1_'.$counter.'">';
							$html .= '<input name="'.$name.'_reorder_ordering_id_'.$counter.'" id="'.$name.'_reorder_ordering_id_'.$counter.'" type="hidden" value="'.$row->value.'" />';
							$checked = '';
							if($row->visible)
							{
								$checked = 'checked="checked"';
							}
							$html .= '<input type="checkbox" id="'.$name.'_visible_'.$counter.'" name="'.$name.'_visible" '.$checked.' value="'.$row->visible.'" onclick="this.checked ? this.value=1 : this.value=0;newVisible(\''.$name.'_visible_'.$counter.'\','.$counter.');">';
						$html .= '</td>';
						//column 2
						$html .= '<td id="'.$name.'_column_2_'.$counter.'">';
							$html .= htmlspecialchars($row->displayName);
						$html .= '</td>';
						$html .= '<td width="12">';
						if(!$counter)
						{
							$html .= '&nbsp;';
						}
						else
						{
							$html .= '<a href="javascript: newOrder('.($counter).','.($counter-1).');">';
								$html .= '<img src="images/uparrow.png" alt="move up" border="0">';
							$html .= '</a>';
						}
						$html .= '</td>';
						$html .= '<td width="12">';
						if($counter == $count-1)
						{
							$html .= '&nbsp;';
						}
						else
						{
							$html .= '<a href="javascript: newOrder('.($counter).','.($counter+1).');">';
								$html .= '<img src="images/downarrow.png" alt="move down" border="0">';
							$html .= '</a>';
						}

						$html .= '</td>';
						$html .= '<td width="8">';
							$html .= '&nbsp;';
						$html .= '</td>';
					$html .= '</tr>';
					$counter = $counter + 1;
				}
				$html .= '</tbody>';
			$html .= '</table>';

			//2 hidden fields which are usefull for updating the ordering when submitted
			$html .= '<input name="pages_are_reordered" id="pages_are_reordered" type="hidden" value="false" />';
			$html .= '<input name="pages_total" id="pages_total" type="hidden" value="'.$counter.'" />';

			$html .= '<div id="target_pages"></div>';
			//ok here we need another path
			$html .= '<script src="ordering.js" language="JavaScript" type="text/javascript"></script>';
			$html .= '<script language="JavaScript"  type="text/javascript">';
			$html .= "<!--\n";
			//$html .= "var pages_total = ".$counter.";\n";
			$html .= "var namePrefix = '".$name."';\n";
			$html .= "var controlNamePrefix = '".$id."';\n";
			//$html .= "var number_of_columns_pages = '2';\n";
			//$html .= "var ordering = '".JText::_('ordering')."';\n";
			//$html .= "var no_pages = '".'_pi_lang_thispagenounderlyingpages'."';\n";
			//$html .= "document.onload = print_ordering();\n";
			$html .= "-->\n";
			$html .= "</script>\n";
		$html .= '</div>';
		return $html;
	}
}