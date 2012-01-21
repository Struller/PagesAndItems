<?php
/**
* @version		2.1.1
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
$popup = JRequest::getVar('popup', 0 );
$button = JRequest::getVar('button', 0 );
$buttonType = JRequest::getVar('buttonType', 'editors-xtd' );
$indicator = JRequest::getVar('indicator', 'none' );
$field_id = JRequest::getVar('field_id', 0 );

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'html'.DS.'popupmaker.php');
$popupMaker = new PopupMaker();


JHTML::_('behavior.mootools');
?>
<?php echo $popupMaker->start(''); ?>

<?php echo $popupMaker->top(); ?>
<?php echo $popupMaker->startContent(); ?>
<!--<div id="theContent">-->
<!-- here we set the content -->
<?php


	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'html'.DS.'indicator.php');
	$indicator = Indicator::getInstance($indicator);
	
	$html = '<div class="xdisplay_none" style="height:0px;width:0px;overflow:hidden;">';
		//$html .= $indicator->display($field_id, '', '98%', '100px', '75', '20', array($button),null, null, null, array('buttonOutput'=>'button','buttonType' => $buttonType,'button'=>array($button)));
		$html .= $indicator->display($field_id, '', '98%', '100px', '75', '20', array($button),null, null, null, array('buttonType' => $buttonType));
	$html .= '</div>';
	echo $html;
	
	//$editor =& JFactory::getEditor('none');
	$editorType = 'codemirror';
	$editor = & JFactory::getEditor($editorType);
	echo $editor->display('text', '', '100%', '98%', '75', '20', false,null, null, null, array('linenumbers'=>1));
	
	$getContent = $indicator->getContent($field_id);
	$setContent = $indicator->setContent($field_id,"html");

	//$getSetContent = $indicator->getSetContent($field_id);

	$getContentEditor = $editor->getContent('text');
	$setContentEditor = $editor->setContent('text',"html");
	$html = array();
	//$contentJs = "";
	//$html[]= "window.addEvent('domready',function(){";
	$html[] = "window.addEvent('load',function(){";	
		$html[] = "	var myFunction = function(){";
		$html[] = "	var html = $getContent ";
		//$html[] = "	alert(html);";
		//$html[] = "	".str_replace('xhtml','html',str_replace('\'xhtml\'','html',$setContent));
	
		//$html[] = "	var indicator = '$field_id';";
		$html[] = "	".$setContent;
		
		//$html[] = "	setContentIndicatorText$field_id(html, indicator);";
		
		
		//$html[] = "	".str_replace('xhtml','html',str_replace('\'xhtml\'','html',$setContentEditor));
		$html[] = "	".$setContentEditor;
		
		//$html[] = "	alert(document.id('$field_id').value)";
		
		if($editorType == 'codemirror')
		{
			//TODO adjust size
		$html[] = "	var editorCodemirror = Joomla.editors.instances['text'];";
		//$html[] = "alert(editorCodemirror.wrapping);";
			
			
	$html[] = "	var fieldIndicator = document.id('contentcontainer');";
	$html[] = "	var wrapper = editorCodemirror.wrapping;";

	$html[] = "	if(wrapper)";
	$html[] = "	{";
				
	$html[] = "		var sizeIndicator = fieldIndicator.getSize()";
				//$html[] = "alert('The element is ' + sizeIndicator.x + ' pixels wide and ' + sizeIndicator.y + 'pixels high.');";
				//$html[] = "alert(contentLinenumbers + ' ' + contentIframe)";
	$html[] = "		var contentIframe = wrapper.getElement('iframe');";
				//$html[] = "var contentLinenumbers = wrapper.getElement('div');";
	$html[] = "		if(contentIframe)";
	$html[] = "		{";
				
	$html[] = "			var wrapperComputedSize = wrapper.getComputedSize();";
	$html[] = "			var wrapperWidthBorders = wrapperComputedSize.totalWidth - wrapperComputedSize.width;";
					//$html[] = "var wrapperHeightBorders = wrapperComputedSize.totalHeight - wrapperComputedSize.height;";
					//$html[] = "alert('The element is ' + wrapperWidthBorders + ' pixels wide ');";
					//$html[] = "if(contentLinenumbers)";
					//$html[] = "{";
					//	$html[] = "var sizeLinenumbers = contentLinenumbers.getSize();";
						//$html[] = "alert('The element is ' + sizeLinenumbers.x + ' pixels wide ');";
					//	$html[] = "var wrapperWidth = (sizeIndicator.x - sizeLinenumbers.x - wrapperWidthBorders) + 'px';";
					//$html[] = "}";
					//$html[] = "else";
					//$html[] = "{";
	$html[] = "			var wrapperWidth = (sizeIndicator.x - wrapperWidthBorders) + 'px';";
	//$html[] = "			var wrapperHeight = (sizeIndicator.y - wrapperHeightBorders) + 'px';";
					//$html[] = "}";
	$html[] = "			wrapper.setStyle('width', wrapperWidth);";
//					$html[] = "wrapper.setStyle('width', wrapperWidth);";
	//				$html[] = "wrapper.setStyle('margin-bottom','5px');";
	$html[] = "			contentIframe.setStyle('width', wrapperWidth);";//(sizeIndicator.x - sizeLinenumbers.x) + 'px');";
	$html[] = "		}";
				
	$html[] = "	}";
//	$html[] = implode("\n", $html);
			
			
			/*
			
			*/
		}
	$html[] = "	};";
	$html[] = "	myFunction.delay(1000);";
	$html[] = "});";


	$html[] = "function applyContent(){";
	$html[] = "	var html = $getContentEditor ";
	//$html[] = "	var indicator = '$field_id';";
	$html[] = "	".$setContent;
	//$html[] = "	setContentIndicatorText$field_id(html, indicator);";
	//$html[] = 	str_replace('xhtml','html',str_replace('\'xhtml\'','html',$setContent));
	$html[] = "	window.parent.document.getElementById('sbox-window').close();";
	$html[] = "};";


	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration(implode("\n", $html));



$buttons = '';
$buttonApply = PagesAndItemsHelper::getButtonMaker('save');
//$buttonApply->onclick = 'var html = '.$getContentEditor.';'.str_replace('xhtml','html',str_replace('\'xhtml\'','html',$setContent)).';window.parent.document.getElementById(\'sbox-window\').close();';
$buttonApply->onclick = 'javascript:applyContent();';

$buttons .= $buttonApply->makeButton();

$buttonClose = PagesAndItemsHelper::getButtonMaker('cancel');
$buttonClose->onclick = 'window.parent.document.getElementById(\'sbox-window\').close();';
$buttons .= $buttonClose->makeButton();

?>

<!--</div>-->
<?php echo $popupMaker->endContent(); ?>
<?php echo $popupMaker->bottom($buttons); ?>
