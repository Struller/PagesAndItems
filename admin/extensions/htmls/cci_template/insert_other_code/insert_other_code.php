<?php
/**
* @version		2.1.6
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
* Html insert_other_code             *
********************************
*/
class PagesAndItemsExtensionHtmlCci_templateInsert_other_code extends PagesAndItemsExtensionHtml
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
		$html .= '<select name="pi_fields_other_'.$template.'" onchange="insert_in_textarea( this.value , this.form.pi_fields_other_'.$template.'.options[this.form.pi_fields_other_'.$template.'.selectedIndex].label,\'template_'.$template.'\' );this.options[0].selected=true;return false;" >';

			$html .= '<option value="" selected="selected" >- '.JText::_('COM_PAGESANDITEMS_INSERT_OTHER_CODE').' - </option>';
			$html .= '<option value="{article_id}" label="">{article_id}</option>';
			$html .= '<option value="{article_title}" label="">{article_title}</option>';
			$html .= '<option value="{article_created}" label="">{article_created}</option>';
			$html .= '<option value="{article_modified}" label="">{article_modified}</option>';
			$html .= '<option value="{article_publish_up}" label="">{article_publish_up}</option>';
			$html .= '<option value="{article_hits}" label="">{article_hits}</option>';
			$html .= '<option value="{article_rating}" label="">{article_rating}</option>';
			$html .= '<option value="<p>" label="</p>">&lt;p&gt;&lt;/p&gt;</option>';
			$html .= '<option value="<a href=&quot;&quot;>" label="</a>">&lt;a&gt;&lt;/a&gt;</option>';
			$html .= '<option value="<br />" label="">&lt;br /&gt;</option>';
			$html .= '<option value="<strong>" label="</strong>">&lt;strong&gt;&lt;/strong&gt;</option>';
			$html .= '<option value="<h1>" label="</h1>">&lt;h1&gt;&lt;/h1&gt;</option>';
			$html .= '<option value="<h2>" label="</h2>">&lt;h2&gt;&lt;/h2&gt;</option>';
			$html .= '<option value="<h3>" label="</h3>">&lt;h3&gt;&lt;/h3&gt;</option>';
			$html .= '<option value="<h4>" label="</h4>">&lt;h4&gt;&lt;/h4&gt;</option>';
			$html .= '<option value="<h5>" label="</h5>">&lt;h5&gt;&lt;/h5&gt;</option>';
			$html .= '<option value="<h6>" label="</h6>">&lt;h6&gt;&lt;/h6&gt;</option>';
			$html .= '<option value="<em>" label="</em>">&lt;em&gt;&lt;/em&gt;</option>';
			$html .= '<option value="<span style=&quot;text-decoration: underline;&quot;>" label="</span>">underline</option>';
			$html .= '<option value="<pre>" label="</pre>">&lt;pre&gt;&lt;/pre&gt;</option>';
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