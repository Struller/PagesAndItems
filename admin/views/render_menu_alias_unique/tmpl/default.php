<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC')){
	die('Restricted access');
}
//if($this->controller->user_type!='Super Administrator'){
if(!PagesAndItemsHelper::getIsSuperAdmin())
{
	echo "<script> alert('you need to be logged in as a super administrator to do this.'); window.history.go(-1); </script>";
	exit();
}

// TODO CHECK 
echo '<link rel="stylesheet" type="text/css" href="components/com_pagesanditems/css/pagesanditems2.css" />';

echo '<div style="width: 600px; margin: 0 auto;">';
echo '<h1>';
echo JText::_('COM_PAGESANDITEMS_MENU_ALIASSES_MAKING_UNIQUE');
echo ' <img src="components/com_pagesanditems/images/processing.gif" alt="'.JText::_('COM_PAGESANDITEMS_MENU_ALIASSES_MAKING_UNIQUE').'" />';
echo '</h1>';
//get menu items which need updating
$db = JFactory::getDBO();
$db->setQuery( "SELECT id, name, alias "
. "\nFROM #__menu"
. "\nORDER BY name ASC"
);
$menuitems_array = $db->loadObjectList();
$total_to_render = count($menuitems_array);
echo '<p>';
echo 'total menu-items to render = '.$total_to_render;
echo '</p>';
if($total_to_render==0){
	PagesAndItemsHelper::redirect_to_url('index.php?option=com_pagesanditems&view=config', JText::_('COM_PAGESANDITEMS_MENU_ALIASSES_MADE_UNIQUE'));
}
//make javascript array of item id's
$javascript_array_menuitems = 'var pi_array_menuitems = new Array(';
$first = true;
foreach($menuitems_array as $item){
	if($first){
		$first = false;
	}else{
		$javascript_array_menuitems .= ',';
	}
	$javascript_array_menuitems .= "'".$item->id."'";
}
$javascript_array_menuitems .= ');';
//make sure mootools is loaded
	JHTML::_('behavior.mootools');


?>
<script language="javascript" type="text/javascript">
<?php echo $javascript_array_menuitems."\n"; ?>

//var ajax_url = 'index.php?option=com_pagesanditems&task=ajax.ajax_make_menu_alias_unique&format=raw';
window.addEvent('domready', function() {
	var delay = 0;
	for (i = 0; i < pi_array_menuitems.length; i++){
		//add ajax. to ajax_make_menu_alias_unique
		ajax_url = 'index.php?option=com_pagesanditems&task=ajax.ajax_make_menu_alias_unique&format=raw&menu_item_id='+pi_array_menuitems[i];
		var req = new Ajax(ajax_url,{ update:'item_'+pi_array_menuitems[i], onComplete:progress_bar });
		delay += 500; // 0.5 seconds between each call
		req.request.delay(delay,req);
	}
});
var rendered = 0;
var total_to_render = '<?php echo $total_to_render; ?>';
var percent;
function progress_bar(){
	rendered = rendered+1;
	ready = total_to_render/rendered;
	percent = 100/ready;
	percent = Math.floor(percent);
	document.getElementById('percent').innerHTML = percent+'%';
	progress_width = percent*4;
	document.getElementById('progress').style.width = progress_width+'px';
	if(ready==1){
		//alert('ready');
		document.location.href = 'index.php?option=com_pagesanditems&view=config';
	}
}
</script>
<div id="percent">
0%
</div>
<div style="width: 400px; height: 20px; border: 1px solid #ccc">
	<div id="progress" style="background: #000033; height: 20px; width: 0px;">
		&nbsp;
	</div>
</div>
<?php
echo '<table cellpadding="5">';
echo '<tr>';
echo '<th>';
echo 'id';
echo '</th>';
echo '<th>';
echo 'name';
echo '</th>';
echo '<th>';
echo 'status';
echo '</th>';
echo '</tr>';
foreach($menuitems_array as $menu_item){
	echo '<tr>';
	echo '<td>';
	echo $menu_item->id;
	echo '</td>';
	echo '<td>';
	echo $menu_item->name;
	echo '</td>';
	echo '<td id="item_'.$menu_item->id.'">&nbsp;';
	echo '</td>';
	echo '</tr>';
}
echo '</table>';
echo '<div>';
?>