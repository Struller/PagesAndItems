<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}
//if($this->controller->user_type!='Super Administrator'){
if(!$this->model->isSuperAdmin)
{
	echo "<script> alert('you need to be logged in as a super administrator to edit the Pages-and-Items config.'); window.history.go(-1); </script>";
	exit();
}
echo '<link rel="stylesheet" type="text/css" href="components/com_pagesanditems/css/pagesanditems.css" />';

$type_id = intval(JRequest::getVar('type_id', ''));
//get url together for after rendering
$futuretask = JRequest::getVar('futuretask', '');
$url_after_processing = 'index.php?option=com_pagesanditems&view=';
$own_futuretask = false;
if($futuretask=='config')
{
	$url_after_processing .= 'config&tab=itemtypes';
}
elseif($futuretask=='config_custom_itemtype')
{
	$url_after_processing .= 'config_custom_itemtype&type_id='.$type_id;
}
elseif($futuretask=='config_custom_itemtype_field')
{
	$field_id = JRequest::getVar('field_id', '');
	$url_after_processing .= 'config_custom_itemtype_field&field_id='.$field_id;
}
elseif($futuretask != '')
{
	$own_futuretask = true;
	$url_after_processing = base64_decode($futuretask);
}


echo '<div style="width: 600px; margin: 0 auto;">';
	echo '<p>';
	//check what message to display
	$from = JRequest::getVar('from', '');
	if(!$own_futuretask)
	{
		if(($futuretask=='config' || $futuretask=='config_custom_itemtype') && $from!='field')
		{
			echo JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_SAVED');
			$message_after_rendering = JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_SAVED');
		}
		else
		{
			echo JText::_('COM_PAGESANDITEMS_FIELD_SAVED');
			$message_after_rendering = JText::_('COM_PAGESANDITEMS_FIELD_SAVED');
		}
	}
	else
	{
		echo JRequest::getVar('message', '');
		$message_after_rendering = JRequest::getVar('message_after_rendering', '');
	}

	echo '</p>';
	echo '<h1>';
		echo 'updating items of this itemtype...';
		echo ' <img src="components/com_pagesanditems/images/processing.gif" alt="updating items of this itemtype" />';
	echo '</h1>';

	$itemtype_name = 'custom_'.$type_id;
	//get items which need updating
	$this->model->db->setQuery( "SELECT c.id, c.title, c.catid"
	. "\nFROM #__content AS c"		
	. "\nLEFT JOIN #__pi_item_index AS i"
	. "\nON c.id=i.item_id"
	. "\nWHERE i.itemtype='$itemtype_name'"
	. "\nAND (c.state='0' OR c.state='1') "
	. "\nORDER BY c.title ASC"
	);
	$items_array = $this->model->db->loadObjectList();
	$total_to_render = count($items_array);
	echo '<p>';
		echo 'total items to render = '.$total_to_render;
	echo '</p>';
	if($total_to_render==0){
		$message_after_rendering .=  '. '.JText::_('COM_PAGESANDITEMS_NO_ITEMS_TO_UPDATE').'.';
		$this->model->redirect_to_url($url_after_processing, $message_after_rendering);
	}
	//make javascript array of item id's
	$javascript_array_items = 'var pi_array_items = new Array(';
	$first = true;
	foreach($items_array as $item){
		if($first){
			$first = false;
		}else{
			$javascript_array_items .= ',';
		}
		$javascript_array_items .= "'".$item->id."'";
	}
	$javascript_array_items .= ');';
	//make sure mootools is loaded
	JHTML::_('behavior.mootools');
?>
	<script language="javascript" type="text/javascript">
	<?php echo $javascript_array_items."\n"; ?>

	var custom_itemtype_id = '<?php echo $type_id; ?>';
	//add ajax. to ajax_update_cit_item
	//does not get triggered in the ajax controller, so moved to the main controller
	window.addEvent('domready', function() 
	{
		var delay = 0;
		for (i = 0; i < pi_array_items.length; i++){			
			//ajax_url = 'index.php?option=com_pagesanditems&task=ajax.ajax_update_cit_item&format=raw&itemtype='+custom_itemtype_id+'&item_id='+pi_array_items[i]+'&<?php echo JUtility::getToken(); ?>=1';
			ajax_url = 'index.php?option=com_pagesanditems&task=ajax_update_cit_item&format=raw&itemtype='+custom_itemtype_id+'&item_id='+pi_array_items[i]+'&<?php echo JUtility::getToken(); ?>=1';
			//alert(ajax_url);
			var req = new Request.HTML({url:ajax_url, update:'item_'+pi_array_items[i], onComplete:progress_bar });
			delay += 500; // 0.5 seconds between each call
			req.send.delay(delay,req); 
		}
	});
	var rendered = 0;
	var total_to_render = '<?php echo $total_to_render; ?>';
	var percent;
	function progress_bar()
	{
		rendered = rendered+1;
		ready = total_to_render/rendered;
		percent = 100/ready;
		percent = Math.floor(percent);
		document.getElementById('percent').innerHTML = percent+'%';
		progress_width = percent*4;
		document.getElementById('progress').style.width = progress_width+'px';
		if(ready==1)
		{
			//alert('ready');
			document.location.href = '<?php echo $url_after_processing; ?>';
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
	<br />
	<?php
	echo '<table cellpadding="5" style="width: 400px;">';
		echo '<tr>';
			echo '<th>';
				echo 'id';
			echo '</th>';
			echo '<th>';
				echo 'title';
			echo '</th>';			
			echo '<th>';
				echo 'category id';
			echo '</th>';	
			echo '<th>';
				echo 'status';
			echo '</th>';		
		echo '</tr>';
		foreach($items_array as $item)
		{
		echo '<tr>';
			echo '<td>';
				echo $item->id;
			echo '</td>';
			echo '<td>';
				echo $item->title;
			echo '</td>';			
			echo '<td>';
				echo $item->catid;
			echo '</td>';	
			echo '<td id="item_'.$item->id.'">&nbsp;';
			echo '</td>';		
		echo '</tr>';
		}
	echo '</table>';
echo '</div>';
?>