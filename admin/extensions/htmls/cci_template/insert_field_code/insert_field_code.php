<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}

require_once(dirname(__FILE__).'/../../../../includes/extensions/html.php');

/**
*********************************
* Html insert_field_code             *
********************************
*/
class PagesAndItemsExtensionHtmlCci_templateInsert_field_code extends PagesAndItemsExtensionHtml
{

	//function onGetHtmlelement(&$htmlelement,$htmlelementVars=null,$name = null, $template = 'intro', $fields = array())
	//function onGetHtmlelement(&$htmlelement,$name = null, $template = 'intro', $fields = array())
	function onGetHtmlelement(&$htmlelement,$name = null, $htmlOptions = null)
	{
		if($name != 'cci_template')
		{
			return false;
		}
		$template = 'intro';
		if(isset($htmlOptions->template))
		{
			$template = $htmlOptions->template;
		}

		$fields = $htmlOptions->fields;
		$html = '';
		$html .= '<select name="pi_fields_'.$template.'" onchange="insert_in_textarea( this.value , \'\',\'template_'.$template.'\' );this.options[0].selected=true;return false;">';
		$html .= '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_FIELD_CODE').' - </option>';
		foreach($fields as $field)
		{
			if($field->plugin=='image_multisize' && $field->installed)
			{
				for ($n = 1; $n <= 5; $n++)
				{
					$option_Field = '{field_'.$field->name.'_'.$field->id.' size='.$n.'}'."\n";
					$_option = '<option value="'.$option_Field.'">';
					$_option .= $field->name.' size='.$n;
					$_option .= '</option>';
					$html .= $_option;
				}
			}
			elseif($field->installed)
			{
				$option_Field = '{field_'.$field->name.'_'.$field->id.'}'."\n";
				$_option = '<option value="'.$option_Field.'">';
				$_option .= $field->name;
				$_option .= '</option>';
				$html .= $_option;
			}
		}
		$html .= '</select>';
		$show_in = $this->params->get('show_in');
		if(!$show_in || ($show_in == '1' && $template == 'intro') || ($show_in == '2' && $template == 'full') )
		{
			$htmlelement->html = $htmlelement->html.$html.' ';
		}
		return true;
	}
}

?>