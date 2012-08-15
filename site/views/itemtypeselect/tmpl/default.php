<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
defined('_JEXEC') or die();
$config = PagesAndItemsHelper::getConfig();
//if(!$this->helper->config['item_type_select_frontend']){
if(!$config['item_type_select_frontend']){
	exit('itemtype selection from the frontend is disabled');
}

$js = 'function new_article(){'."\n";
$js .= 'itemtype = document.getElementById(\'select_itemtype\').value;'."\n";
$js .= 'document.location.href=\'index.php?option=com_pagesanditems&view=item&sub_task=new&item_type=\'+itemtype;'."\n";
$js .= '}'."\n";

//FB::dump(JRequest::get());
$Itemid = JRequest::getVar('Itemid');


$js = 'function new_item(){'."\n";
$js .= 'itemtype = document.getElementById(\'select_itemtype\').value;'."\n";
$js .= 'document.location.href=\'index.php?option=com_pagesanditems&view=item&sub_task=new&item_type=\'+itemtype+\'&Itemid='.$Itemid.'\';'."\n";
$js .= '}'."\n";

$doc =&JFactory::getDocument();
$doc->addScriptdeclaration($js);

?>
<form id="adminForm" name="adminForm">
<id="form_content">
<div id="com_pagesanditems">
	<h2 class="componentheading" id="header_item_type_select">
		<?php echo JText::_('COM_PAGESANDITEMS_SELECT_ITEMTYPE_FOR_NEW_ITEM'); ?>
	</h2>
	<div class="item">
		<?php 
		//echo $this->helper->itemtype_select(0); 
		echo PagesAndItemsHelper::itemtype_select(0); 
		?>
		<!--&nbsp;&nbsp;
		<input type="button" class="button" value="<?php echo JText::_('COM_PAGESANDITEMS_CREATE_NEW_ITEM'); ?>" onclick="new_article()" />-->
	</div>
</div>
</div>
</form>