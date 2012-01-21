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
/*
this is for Popup

*/

$popup = JRequest::getVar('popup', 0 );
$button = JRequest::getVar('button', 0 );
$buttonType = JRequest::getVar('buttonType', 'editors-xtd' );
$indicator = JRequest::getVar('indicator', 'none' );
$field_id = JRequest::getVar('field_id', 0 );
//$field_value = ''; //base64_decode(JRequest::getVar('field_value', '' ));
//$field_value = html_entity_decode(base64_decode(JRequest::getVar('field_value', '' )));
?>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<form name="adminForm" method="post" action="" enctype="multipart/form-data">
<?php
//	$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../../../')));
//	JHTML::script('popup_extension.js',$path.'/javascript/',false);
//	$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../../../')));
//	
// TODO CHECK 
echo '<link href="'.PagesAndItemsHelper::getDirCSS(true).'/pagesanditems2.css" rel="stylesheet" type="text/css" />'."\n";
$doc = JFactory::getDocument();
$css = "html {overflow: hidden !important;} body.contentpane, body{margin: 0;}";
$doc->addStyleDeclaration($css);

	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'html'.DS.'indicator.php');
	$indicator = Indicator::getInstance($indicator);
	
	$html = '<div class="xdisplay_none" style="height:0px;width:0px;overflow:hidden;">';
		//$html .= $indicator->display($field_id,  $field_value , '98%', '100px', '75', '20', array($button),null, null, null, array('buttonOutput'=>'button','buttonType' => $buttonType,'button'=>array($button)));
		$html .= $indicator->display($field_id, '' , '98%', '100px', '75', '20', array($button),null, null, null, array('buttonType' => $buttonType));
	$html .= '</div>';

	$getContent = $indicator->getContent($field_id);
	//$setContent = $indicator->setContent($field_id,'xhtml');//,0);
	//$getSetContent = $indicator->getSetContent($field_id);
	$setContent = $indicator->setContent($field_id,"html");
	
	$contentJs = "";
	//$contentJs .= "window.addEvent('domready',function(){";
	$contentJs .= "window.addEvent('load',function(){";
	$contentJs .= "	var html = $getContent ";
	//$contentJs .= "	var indicator = '$field_id';";
	//$contentJs .= "	setContentIndicatorText$field_id(html, '$field_id');";
	$contentJs .= "	".$setContent;
	
//	$contentJs .= "myFunction();";
	$contentJs .= "});";
	//$contentJs .= "alert('domready');";
	//$contentJs .= "delay(1000);"; //,alert('domready');";
	//.wait(400)
	//an anonymous function which waits a second and then alerts
	//(function(){ alert('one second later...'); }).delay(1000);
//		$contentJs .= "var myFunction = function(){";
			//$contentJs .= "var html = $getContent ".str_replace('xhtml','html',$setContent);
//		$contentJs .= "};";
//		$contentJs .= "if(document.id('$field_id').get('wait'))";
//		$contentJs .= "{";
			//$contentJs .= "alert('wait');";
//			$contentJs .= "myFunction.delay(document.id('$field_id').get('wait'));";
//		$contentJs .= "}";
//		$contentJs .= "else;";
//		$contentJs .= "{";
			//$contentJs .= "alert('wait not');";
			//must delay and wait for indicator
			//$contentJs .= "myFunction.delay(1000);";
//		$contentJs .= "}";
	//$contentJs .= "});";
	//$contentJs .= "var html = $getContent ".str_replace('xhtml','html',str_replace('\'xhtml\'','html',$setContent));
	//$contentJs .= "var html = $getContent $setContent";
	if($popup)
	{
		$contentJs .= "SqueezeBox.close = function(){parent.SqueezeBox.close();};";
	}
	//$contentJs .= "});";

	$doc->addScriptDeclaration($contentJs);
	
	echo $html;
	$size_x = JRequest::getVar('size_x','100');
	$size_y = JRequest::getVar('size_y','100');
	
	$link = base64_decode(JRequest::getVar('link'));
	if($link)
	{
?>
	<iframe style="overflow-x: hidden;" frameborder="0" width="100%" height="<?php echo $size_y; ?>" src="<?php echo $link ?>"></iframe>
	<?php
	}
	$onclick = base64_decode(JRequest::getVar('onclick'));
	
	if($onclick)
	{
		//echo 'wait';
		$contentJs = "";
		//$contentJs .= "window.addEvent('domready',function(){";
		$contentJs .= "window.addEvent('load',function(){";
		if($popup)
		{
			//$contentJs .= "if(!onclick()){parent.SqueezeBox.close();}";
		}
		$contentJs .= "function onclick(){ ";
		if($popup)
		{
			//$contentJs .= "parent.document.id('".$field_id."_button_".$button."').fireEvent('removeClasses');";
		}
		$contentJs .= "$onclick };";
		//$contentJs .= "onclick.delay(1000);";
		if($popup)
		{
			$contentJs .= "if(!onclick()){";
			$contentJs .= "parent.document.id('".$field_id."_button_".$button."').fireEvent('removeClasses');";
			$contentJs .= "parent.SqueezeBox.close();}";
		}
		$contentJs .= "});";
		$doc->addScriptDeclaration($contentJs);
	}
	
	?>


</form>
<!-- end id="form_content" need for css-->
</div>


