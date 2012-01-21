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
* Html hide_in_intro_view             *
********************************
*/
class PagesAndItemsExtensionHtmlCci_templateHide_in_intro_view extends PagesAndItemsExtensionHtml
{

	//function onGetHtmlelement(&$htmlelement,$htmlelementVars=null,$name = null, $template = 'intro', $fields)
	//function onGetHtmlelement(&$htmlelement,$name = null, $template = 'intro', $fields)
	function onGetHtmlelement(&$htmlelement,$name = null, $htmlOptions = null)
	{
		$template = 'intro';
		if(isset($htmlOptions->template))
		{
			$template = $htmlOptions->template;
		}
		if($name != 'cci_template' || $template != 'intro')
		{
			return false;
		}
		$htmlelementVars->text = JText::_('COM_PAGESANDITEMS_ADD_HIDE_INTRO_VIEW');
		$htmlelementVars->class = 'buttonText';
		$htmlelementVars->onclick = 'insert_in_textarea(\'{hide_in_intro_view}\', \'{/hide_in_intro_view}\',\'template_'.$template.'\');';
		$htmlelement->html = $htmlelement->html.parent::onGetButton($htmlelement,$htmlelementVars,$name); //parent::onGetButton(&$htmlelement,$htmlelementVars,$name);
		return true;
	}
}

?>