<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;
/**
For all Popup we will get the same style
*/


class PopupMaker
{

	protected function loadCss()
	{
		//$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../')));
		$path = PagesAndItemsHelper::getDirCSS();
		JHTML::stylesheet('popup.css', $path.'/');
		JHTML::stylesheet('pagesanditems2.css', $path.'/');
	}
	
	protected function loadJs($content)
	{
		$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../')));
		$path = PagesAndItemsHelper::getDirJS();
		JHTML::script('popupmanager.js', $path.'/'); //,false);
	
		$document =& JFactory::getDocument();

		//$size_x = JRequest::getVar('size_x');
		//$size_y = JRequest::getVar('size_y');

		// Load the magic for system messages in popup
		$document->addScriptDeclaration("
			window.addEvent('domready', function()
			{
				var system_message = document.id('system-message');
				var message = document.id('message');
				if(system_message)
				{
					system_message.inject(message);
				}
				if(popupManager)
				{
					popupManager.setSize();
				}
				
				var content = document.id('".$content."');
				if(content)
				{
					var form = document.id('formPopup').getElement('form');
					//alert(form);
					form.grab(content);
					//form.set('html',content.get('html'));
				}
			});
		");
	}


	function start($content = '')
	{
		$this->loadCss();
		$this->loadJs($content);
		$html = '';
		$html .= '<div id="form_content">';
		$html .= '	<div id="formPopup" name="formPopup" >';
		return $html;
	}
	
	function top($image = 'class:icon-32-pi',$title = null,$class = 'headerIcon32')
	{
		$title = $title ? $title : JText::_('COM_PAGESANDITEMS');
		$html = '';
		$html .= '<fieldset class="poup_fieldset content" id="fieldset_top">';
			$html .= PagesAndItemsHelper::getHeaderImageTitle($image,$title,$class);
		$html .= '</fieldset>';
		return $html;
	}
	
	function startContent($form = null)
	{
		$html = '';
				$html .= '<fieldset class="poup_fieldset content" id="fieldset_content">';
				$html .= '<div id="contentcontainer" name="contentcontainer" class="contentcontainer" >';
					
					if($form)
					{
						$html .= '<form '.json_encode($form).'>';
					}
					else
					{
						$html .= '<form action="" method="post" name="adminForm" id="adminForm" class="form-validate">';
					}
						$html .= '<div id="message" style="display:block;">';
						$html .= '</div>';
		return $html;
	}
	
	function endContent()
	{
		$html = '';
							$size_x = JRequest::getVar('size_x');
						$size_y = JRequest::getVar('size_y');
						$html .= '<input type="hidden" id="size_x" name="size_x" value="'.$size_x.'" />';
						$html .= '<input type="hidden" id="size_y" name="size_y" value="'.$size_y.'" />';
					$html .= '</form>';


				$html .= '</div>';
			$html .= '</fieldset>';
		return $html;
	}
	function bottom($buttons = null, $left = '')
	{
		$html = '';
		$html .= '<fieldset class="poup_fieldset content bottom" id="fieldset_bottom" >';
				$html .= '<div class="div_left_bottom_path" >';
				$html .= $left;
				$html .= '</div>';
					$html .= '<div class="clr_right">';
					$html .= '</div>';
					$html .= '<div class="div_buttony" style="float:right">';
					if(!$buttons)
					{
						$buttonClose = PagesAndItemsHelper::getButtonMaker('cancel');
						$buttonClose->onclick = 'window.parent.document.getElementById(\'sbox-window\').close();';
						$buttons = $buttonClose->makeButton();
					}
					$html .= $buttons;
				$html .= '</div>';
			$html .= '</fieldset>';
		return $html;
	}
	
	
	function end()
	{
		$html = '';
		$html .= '	</div>';
		$html .= '</div>';
		return $html;
	}
		
	function popup($content = '', $title = null, $buttons = null, $form = null, $image = 'class:icon-32-pi',$class = 'headerIcon32')
	{
		/*
		<form action="<?php //echo JRoute::_('index.php?option=com_categories&extension='.JRequest::getCmd('extension', 'com_content').'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
		*/
		/*
		PopupMaker::loadCss();
		PopupMaker::loadJs($content);
		$html = '';
		$html .= '<div id="form_content">';
		$html .= '<div id="formPopup" name="formPopup" >';
		*/
		$html = '';
		$html .= $this->start($content);
		$html .= $this->top($image,$title,$class);
		/*
			$html .= '<fieldset class="poup_fieldset content" id="fieldset_top">';
				$html .= PagesAndItemsHelper::getHeaderImageTitle($image,$title);
			$html .= '</fieldset>';
		*/
		$html .= $this->startContent($form);
		$html .= $this->endContent();
		/*
			$html .= '<fieldset class="poup_fieldset content" id="fieldset_content">';
				$html .= '<div id="contentcontainer" name="contentcontainer" class="contentcontainer" >';
					
					if($form)
					{
						$html .= '<form '.json_encode($form).'>';
					}
					else
					{
						$html .= '<form action="" method="post" name="adminForm" id="adminForm" class="form-validate">';
					}
						$html .= '<div id="message" style="display:block;">';
						$html .= '</div>';


						$size_x = JRequest::getVar('size_x');
						$size_y = JRequest::getVar('size_y');
						$html .= '<input type="hidden" id="size_x" name="size_x" value="'.$size_x.'" />';
						$html .= '<input type="hidden" id="size_y" name="size_y" value="'.$size_y.'" />';
					$html .= '</form>';


				$html .= '</div>';
			$html .= '</fieldset>';
		*/
		
		$html .= $this->bottom($buttons);
		/*
			$html .= '<fieldset class="poup_fieldset content bottom" id="fieldset_bottom" >';
				$html .= '<div class="div_left_bottom_path" >';
				$html .= '</div>';
					$html .= '<div class="clr_right">';
					$html .= '</div>';
					$html .= '<div class="div_buttony" style="float:right">';
					if(!$buttons)
					{
						$buttonClose = PagesAndItemsHelper::getButtonMaker('cancel');
						$buttonClose->onclick = 'window.parent.document.getElementById(\'sbox-window\').close();';
						$buttons = $buttonClose->makeButton();
					}
					$html .= $buttons;
				$html .= '</div>';
			$html .= '</fieldset>';
		*/
		$html .= $this->end();
		//$html .= '</div>';
		//$html .= '</div>';
		
		return $html;
	}
}

?>