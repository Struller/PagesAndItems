<?php
/**
* @version		2.1.0
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
* Html insert_own_code             *
********************************
*/
class PagesAndItemsExtensionHtmlCci_templateInsert_own_code extends PagesAndItemsExtensionHtml
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
		$html = '';
		$html .= '<select name="pi_fields_own_'.$template.'" onchange="insert_in_textarea( this.value , this.form.pi_fields_own_'.$template.'.options[this.form.pi_fields_own_'.$template.'.selectedIndex].label,\'template_'.$template.'\' );this.options[0].selected=true;return false;">';
			$html .= '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_OWN_CODE').' - </option>';
			$own = $this->params->get('own');
			if($own && $own != '')
			{
				$owns = explode("\n",$own);
				foreach($owns as $own)
				{
					if($own != '')
					{
						$values = explode(";",$own);
						if(count($values) > 1)
						{
							$_option = '<option ';
							$title = '';
							foreach($values as $value)
							{
								list($key,$val) = explode("=",$value);
								if($key == 'value' || $key == 'label')
								{
									$_option .= $key.'="'.$val.'" ';
								}
								if($key == 'title')
								{
									$title = $val;
								}
							}
							$_option .= '>';
							$_option .= htmlspecialchars($title);
							$_option .= '</option>';
							$html .= $_option;
						}
						else
						{
							$_option = '<option ';
							list($key,$val) = explode("=",$value);
							$_option .= 'value="'.$val.'" ';
							$_option .= '>';
							$_option .= '</option>';
							$html .= $_option;
						}
					}
				}

			}
			/*
			value=<div>;label=</div>

			$this->params
			*/
			/*
			$option_Field_begin = '{if-not-empty_field_'.$field->name.'_'.$field->id.'}'."\n";
				$option_Field_end = '{/if-not-empty_field_'.$field->name.'_'.$field->id.'}'."\n";
					$_option = '<option value="'.$option_Field_begin.'" ';
					$_option .= 'label="'.$option_Field_end.'" >';
					$_option .= 'if not empty '.$field->name;
					$_option .= '</option>';
				$html .= $_option;
			*/
		$html .= '</select>';
		$show_in = $this->params->get('show_in');
		if(!$show_in || ($show_in == '1' && $template == 'intro') || ($show_in == '2' && $template == 'full') )
		{
			$htmlelement->html = $htmlelement->html.$html.' ';
		}
		/*
		elseif($show_in == '1' && $template == 'intro')
		{
			$htmlelement->html = $htmlelement->html.$html.' ';
		}
		elseif($show_in == '2' && $template == 'full')
		{
			$htmlelement->html = $htmlelement->html.$html.' ';
		}
		*/

		return true;
	}
}

?>