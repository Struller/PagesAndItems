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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'html'.DS.'popupmaker.php');
$popupMaker = new PopupMaker();

$buttons = '';
$buttonApply = PagesAndItemsHelper::getButtonMaker('save');
$buttonApply->onclick = 'alert(\'test\');';
//$buttonApply->class = 'button_action_disabled';
//$buttonApply->id = 'button_translate_apply';
//$buttonApply->name = 'button_translate_apply';
//$buttonApply->disabled = true; //"disabled"
$buttons .= $buttonApply->makeButton();

$buttonClose = PagesAndItemsHelper::getButtonMaker('cancel');
$buttonClose->onclick = 'window.parent.document.getElementById(\'sbox-window\').close();';
$buttons .= $buttonClose->makeButton();
JHTML::_('behavior.mootools');
?>
<?php echo $popupMaker->start(''); ?>

<?php echo $popupMaker->top(); ?>
<?php echo $popupMaker->startContent(); ?>
<div id="theContent">
	<!-- here we set the content -->
</div>
<?php echo $popupMaker->endContent(); ?>
<?php echo $popupMaker->bottom($buttons); ?>
