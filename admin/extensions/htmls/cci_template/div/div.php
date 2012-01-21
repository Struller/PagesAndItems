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
* Html div             *
********************************
*/
class PagesAndItemsExtensionHtmlCci_templateDiv extends PagesAndItemsExtensionHtml
{

	//function onGetHtmlelement(&$htmlelement,$htmlelementVars=null,$name = null, $template = 'intro', $fields)
	//function onGetHtmlelement(&$htmlelement,$name = null, $template = 'intro', $fields)
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

		$htmlelementVars->text = JText::_('COM_PAGESANDITEMS_ADD_DIV');
		$htmlelementVars->onclick = 'insert_in_textarea(\'<div>\', \'</div>\',\'template_'.$template.'\');';
		$htmlelementVars->class = 'buttonText';
		$show_in = $this->params->get('show_in');
		if(!$show_in || ($show_in == '1' && $template == 'intro') || ($show_in == '2' && $template == 'full') )
		{
			$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name); //parent::onGetButton(&$htmlelement,$htmlelementVars,$name);
		}
		return true;
	}
}

?>