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
/*
this is for extensions/managers

*/

$item_type = JRequest::getVar('item_type', '' );
$sub_task = JRequest::getVar('sub_task', '' );
$pageId = JRequest::getVar('pageId', '' );
$item_id = JRequest::getVar('itemId', '' );

$showPageTree = JRequest::getVar('showPageTree', 0 );
$showFooter = JRequest::getVar('showFooter', 1 );
$tmpl = JRequest::getVar('tmpl', 0 );
$popup = JRequest::getVar('popup', 0 );

$extensionName = JRequest::getVar('extensionName',JRequest::getVar('extension', '' ));
$extensionType = JRequest::getVar('extensionType', '');
$extensionFolder = JRequest::getVar('extensionFolder', '');
$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');

if($extensionType != '')
{
	require_once($path.DS.'includes'.DS.'extensions'.DS.$extensionType.'helper.php');
	$typeName = 'Extension'.ucfirst($extensionType).'Helper';
	$typeName::importExtension($extensionFolder, $extensionName,true,null,true);
}
$dispatcher = &JDispatcher::getInstance();


if($popup)
{

	/*
	we will set something here like
	header with image ....

	an javascript to set the dimensions

	*/
	$headerTitle = JText::_( 'Pages and Items');
	$headerImage = PagesAndItemsHelper::getDirIcons().'icon-32-pi.png';

	$results = $dispatcher->trigger('onDisplay_HeaderImage', array(&$headerImage,$extensionName); //,$this->model));
	$results = $dispatcher->trigger('onDisplay_HeaderTitle', array(&$headerTitle,$extensionName); //,$this->model));


?>



<!-- begin id="form_content" need for css-->
<div id="form_content">
<form name="adminForm" method="post" action="" enctype="multipart/form-data">
<fieldset id="fieldset_top">
<?php echo PagesAndItemsHelper::getHeaderImageTitle($headerImage,$headerTitle); ?>
		<?php
		/*
<div class="formHeader">

	<h1 class="pi_h1">
		<img src="<?php echo $headerImage; ?>" alt="..." class="pi_icon" />
		<?php echo '&nbsp;'.$headerTitle; ?>
	</h1>
</div>
*/
?>
</fieldset>
<fieldset id="fieldset_content">
<div id="formContent" class="formContent">
<?php

	//$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,JURI::root(),realpath(dirname(__FILE__).'/../../../')));
	//JHTML::script('popup_extension.js', $path.'/javascript/',false);

	//here we need an path to the component dir javascript
	$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../../../')));
	JHTML::script('popup_extension.js',$path.'/javascript/',false);
	//here we get an absolute path like /administrator/components/com_pagesanditems/javascript/popup_extension.js
	//here we need an relative path
	$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../../../')));
// TODO CHECK 
	echo '<link href="'.JURI::root(true).'/'.$path.'/css/pagesanditems2.css" " rel="stylesheet" type="text/css" />'."\n";
// TODO CHECK 
	echo '<link href="'.JURI::root(true).'/'.$path.'/css/dtree.css" rel="stylesheet" type="text/css" />'."\n";
// TODO CHECK 
	echo '<link href="'.JURI::root(true).'/'.$path.'/css/pages_and_items_extension.css" rel="stylesheet" type="text/css" />'."\n";
	/*
	TODO calc content height -90px

	*/
}
?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
	<?php
	if($showPageTree)
	{
		echo '<td valign="top" width="20%">';
			echo $this->pageTree;
		echo '</td>';
	}
	?>
		<td valign="top" style="  font-size: 100% !important;font-family: monospace;">



			<input type="hidden" name="option" id="option" value="com_pagesanditems" />
			<input type="hidden" id="task" name="task" value="extension.display" />
			<input type="hidden" name="pageId" id="pageId" value="<?php echo $pageId; ?>">
			<input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
			<input type="hidden" name="itemId" value="<?php echo $item_id; ?>">
			<input type="hidden" name="item_type" value="<?php echo $item_type; ?>">

			<input type="hidden" name="extensionName" id="extensionName" value="<?php echo $extensionName; ?>">
			<input type="hidden" name="extensionType" id="extensionType" value="<?php echo $extensionType; ?>">
			<input type="hidden" name="extensionFolder" id="extensionFolder" value="<?php echo $extensionFolder; ?>">


			<?php

			$content = & new JObject();
			$content->text = '';
			$results = $dispatcher->trigger('onDisplayContent', array(&$content,$extension,$sub_task); //,$this->model));
			echo $content->text;

			?>
		</td>
	</tr>
</table>
<?php
if($popup)
{
	/*
	we will set something here like
	an cancle button as default
	*/
?>
</div>
</fieldset>
<fieldset id="fieldset_bottom">
<div class="formFooter">
<?php
	/*
	$htmlButton = '<button style="float:right;" class="button_action" name="close-button" id="button_close" type="button" onclick="window.parent.document.getElementById(\'sbox-window\').close();">';
	$htmlButton .= JText::_('Cancel');
	$htmlButton .= '</button>';
	*/
	$button = PagesAndItemsHelper::getButtonMaker('close');
	$button->onclick = 'window.parent.document.getElementById(\'sbox-window\').close();';
	$button->style = 'float:right;';
	$htmlButton = $button->makeButton();

	$results = $dispatcher->trigger('onHtmlDisplay_Button', array(&$htmlButton,$extensionName); //,$this->model));
	echo $htmlButton;
?>

</div>
</fieldset>
</form>
<!-- end id="form_content" need for css-->
</div>
<?php
}
?>

<?php
if($showFooter && !$tmpl)
{
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
}
?>