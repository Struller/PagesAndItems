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

//if($this->model->user_type!='Super Administrator' && !$this->pagesAndItemsModel->isSuperAdmin)
if(!$this->model->isSuperAdmin)
{
	exit("you need to be logged in as a super administrator to edit the Pages-and-Items config.");
}
$type_id = JRequest::getVar('type_id', '' );
if(!$type_id){
	//start new
		
		$type_id = '';
		$name = '';
		$read_more = '1';
		$items_of_this_type = 0;
		
	//end new	
}else{
	//start edit
	
	//get data about this itemtype
	$this->model->db->setQuery("SELECT * FROM #__pi_customitemtypes WHERE id='$type_id' LIMIT 1");
	$customitemtypes = $this->model->db->loadObjectList();
	$customitemtype = $customitemtypes[0];
	$name = $customitemtype->name;
	$read_more = $customitemtype->read_more;
	$template_intro = $customitemtype->template_intro;
	//display &nbsp;  ?
	//$template_intro = str_replace('&nbsp;','&amp;nbsp;',$template_intro);
	
	$template_full = $customitemtype->template_full;
	$editor_id = $customitemtype->editor_id;
	if($editor_id==0){
		$editor_id = '';
	}
	$html_after = $customitemtype->html_after;
	$html_before = $customitemtype->html_before;
	
	//get fields
	$this->model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' ORDER BY ordering ASC");
	$fields = $this->model->db->loadObjectList();
	//print_r($fields);
	
	//get itemtype name
	$itemtype_name = 'custom_'.$type_id;
	//get number of items which need updating
	$this->model->db->setQuery( "SELECT c.id "
	."FROM #__pi_item_index AS i "		
	."LEFT JOIN #__content AS c "
	."ON c.id=i.item_id "
	."WHERE i.itemtype='$itemtype_name' "
	."AND (c.state='0' OR c.state='1') "	
	);
				
	$items_array = $this->model->db->loadObjectList();
	$items_of_this_type = count($items_array);
	
					
	//end edit
}

/*
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
$itemtype = ExtensionHelper::importExtension('itemtype',null, 'custom',true,null,true);
$dispatcher = &JDispatcher::getInstance();


first load all avaible fields
*/
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
//$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, null,true,null,true);
$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, null,true,null,true,' ORDER BY element');
$dispatcher = &JDispatcher::getInstance();

//$allFieldtypes = ExtensionHelper::getExtension('fieldtype',null, null);
$allFieldtypes = ExtensionFieldtypeHelper::getExtension(null, null,' ORDER BY element');
//


?>

<!-- <link rel="stylesheet" type="text/css" href="components/com_pagesanditems/css/pagesanditems.css" /> -->
<script language="JavaScript" type="text/javascript">
function do_pre_submit_checks(){
	if (document.adminForm.name.value == '') {
		alert('<?php echo JText::_('COM_PAGESANDITEMS_NO_NAME'); ?>');
		return;
	} else {
		<?php
		if(!$type_id)
		{
			//new custom itemtype
			
			//echo 'submitform(\'customitemtype.config_custom_itemtype_save\');';
			if($this->model->joomlaVersion < '1.6')
			{
			?>
			submitform('customitemtype.config_custom_itemtype_save');
			<?php
			}
			else
			{
			?>
			Joomla.submitform('customitemtype.config_custom_itemtype_save',document.getElementById('adminForm'));
			<?php
			}
		}
		else
		{
		?>
			if(check_name_field()){
				template_intro = document.getElementById('template_intro').value;
				template_intro_original = document.getElementById('template_intro_original').value;
				template_full = document.getElementById('template_full').value;
				template_full_original = document.getElementById('template_full_original').value;
				old_read_more_option = document.getElementById('old_read_more_option').value;
				new_read_more_option = getCheckedValue(document.adminForm.read_more);
				
				//ADD MS 18.09.2009
				editor_id = document.getElementById('editor_id').value;
				old_editor_id = document.getElementById('old_editor_id').value;
				html_after = document.getElementById('html_after').value;
				old_html_after = document.getElementById('old_html_after').value;
				html_before = document.getElementById('html_before').value;
				old_html_before = document.getElementById('old_html_before').value;
				//ADD MS 18.09.2009 END
				
				items_of_this_type = document.getElementById('items_of_this_type').value;
							
				if((template_intro!=template_intro_original || template_full!=template_full_original || old_read_more_option!=new_read_more_option || old_editor_id!=editor_id || old_html_after!=html_after || old_html_before!=html_before) && items_of_this_type!=0)
				{
					if(confirm("<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CHANGE_TO_TEMPLATE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_WANT_TO_UPDATE_ITEMS')); ?>")){
						document.getElementById('update_items').value = '1';
					}
				}
				//submitform('customitemtype.config_custom_itemtype_save');
				<?php
				if($this->model->joomlaVersion < '1.6')
				{
				?>
				submitform('customitemtype.config_custom_itemtype_save');
				<?php
				}
				else
				{
				?>
				Joomla.submitform('customitemtype.config_custom_itemtype_save',document.getElementById('adminForm'));
				<?php
				}
				?>
			}
		<?php
		}
		?>
	}
}
function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

//function submitbutton(pressbutton) 
<?php 
if($this->model->joomlaVersion < '1.6')
{
echo 'function submitbutton(pressbutton)'."\n";
}
else
{
echo 'Joomla.submitbutton = function(pressbutton)'."\n";
}
?>
{
	if (pressbutton == 'customitemtype.config_custom_itemtype_save') 
	{
		do_pre_submit_checks();
	}
	//COMMENT 
	if (pressbutton == 'config_itemtype_render') 
	{
		if(confirm("<?php echo addslashes(JText::_('COM_PAGESANDITEMS_WANT_TO_UPDATE_ITEMS')); ?>"))
		{
			document.location.href = 'index.php?option=com_pagesanditems&view=render_items_by_custom_itemtype&type_id=<?php echo $type_id; ?>&futuretask=config_custom_itemtype';
		}
	}
	if (pressbutton == 'customitemtype.config_custom_itemtype_apply') 
	{
		document.getElementById('sub_task').value = 'apply';
		do_pre_submit_checks();
	}
	
	if (pressbutton == 'customitemtype.config_custom_itemtype_archive') {
		if(confirm("<?php echo JText::_('COM_PAGESANDITEMS_SURE_ITEMTYPE_ARCHIVE'); ?>"))
		{
			if(confirm("<?php echo JText::_('COM_PAGESANDITEMS_SURE_ITEMS_ARCHIVE'); ?>"))
			{
				//document.getElementById('archive_items').value = '1';
			}
			return;
			<?php
			if($this->model->joomlaVersion < '1.6')
			{
			?>
			submitform('customitemtype.config_custom_itemtype_archive');
			<?php
			}
			else
			{
			?>
			Joomla.submitform('customitemtype.config_custom_itemtype_archive',document.getElementById('adminForm'));
			<?php
			}
			?>
		}
	}
	if (pressbutton == 'customitemtype.config_custom_itemtype_trash') 
	{
		if(confirm("<?php echo JText::_('COM_PAGESANDITEMS_SURE_ITEMTYPE_TRASH'); ?>")){
			if(confirm("<?php echo JText::_('COM_PAGESANDITEMS_SURE_ITEMS_TRASH'); ?>"))
			{
				document.getElementById('trash_items').value = '1';
			}
			return;
			//submitform('customitemtype.config_custom_itemtype_trash');
			<?php
			if($this->model->joomlaVersion < '1.6')
			{
			?>
			submitform('customitemtype.config_custom_itemtype_trash');
			<?php
			}
			else
			{
			?>
			Joomla.submitform('customitemtype.config_custom_itemtype_trash',document.getElementById('adminForm'));
			<?php
			}
			?>
		}
	}
	
	if (pressbutton == 'customitemtype.config_custom_itemtype_delete') {
		if(confirm("<?php echo JText::_('COM_PAGESANDITEMS_SURE_ITEMTYPE_DELETE'); ?>"))
		{
			if(confirm("<?php echo JText::_('COM_PAGESANDITEMS_SURE_ITEMS_DELETE'); ?>"))
			{
				document.getElementById('delete_items').value = '1';
			}
			//alert(document.getElementById('delete_items').value);
			//submitform('customitemtype.config_custom_itemtype_delete');
			<?php
			if($this->model->joomlaVersion < '1.6')
			{
			?>
			submitform('customitemtype.config_custom_itemtype_delete');
			<?php
			}
			else
			{
			?>
			Joomla.submitform('customitemtype.config_custom_itemtype_delete',document.getElementById('adminForm'));
			<?php
			}
			?>
		}
	}
	if (pressbutton == 'customitemtype.cancel') 
	{
		document.getElementById('view').value = 'config';
		document.getElementById('tab').value = 'itemtypes';
		<?php
		if($this->model->joomlaVersion < '1.6')
		{
		?>
		submitform('display');
		<?php
		}
		else
		{
		?>
		Joomla.submitform('display',document.getElementById('adminForm'));
		<?php
		}
		?>
		return;
		document.location.href = 'index.php?option=com_pagesanditems&view=config&tab=itemtypes';
	}
}
Array.prototype.in_array = function (element) 
{
  var retur = false;
  for (var values in this) 
  {
    if (this[values] == element) 
    {
      retur = true;
      break;
    }  
  }
  return retur;
};
function check_name_field()
{
	name = document.getElementById('name').value;
	name_format = true;
	if(!name.match(/^[a-zA-z1234567890\u0021\u0023\u002D\u00C0\u00C1\u00C2\u00C3\u00C4\u00C5\u00C6\u00C7\u00C8\u00C9\u00CA\u00CB\u00CC\u00CD\u00CE\u00CF\u00D0\u00D1\u00D2\u00D3\u00D4\u00D5\u00D6\u00D8\u00D9\u00DA\u00DB\u00DC\u00DD\u00DE\u00DF\u00E0\00E1\u00E2\u00E3\u00E4\u00E5\u00E6\u00E7\u00E8\u00E9\u00EA\u00EB\u00EC\u00ED\u00EE\u00EF\u00F0\u00F1\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u00F9\u00FA\u00FB\u00FC\u00FD\u00FE\u00FF]+$/) || name.match(/\u005F/))
	{
		alert('<?php echo addslashes(JText::_('COM_PAGESANDITEMS_ONLY_CHAR')); ?>');
		document.getElementById('name').focus();
		name_format = false;
	}
	return name_format;
}
function add_new_field(){
	plugin = document.getElementById('plugins').value;
	if(plugin==0){
		alert('<?php echo JText::_('COM_PAGESANDITEMS_NO_FIELD_TYPE_SELECTED'); ?>');
	}else{
		document.location.href = 'index.php?option=com_pagesanditems&view=config_custom_itemtype_field&type_id=<?php echo $type_id; ?>&plugin='+plugin;
	}
}
function delete_fields(){
	//check if there is anything checked to delete
	if (document.adminForm.boxchecked.value == '0') {
		alert('<?php echo JText::_('COM_PAGESANDITEMS_NO_FIELDS_SELECTED'); ?>');
		return;
	} else {
		if(confirm("<?php echo JText::_('COM_PAGESANDITEMS_SURE_FIELDS_DELETE'); ?>")){
			submitform('customitemtype.custom_itemtype_fields_delete');
		}
	}
}
// ADD MS
function insert_in_textarea(aTag, eTag, template_element) 
{
	var input = document.forms['adminForm'].elements[template_element];
	input.focus();
	/* for Internet Explorer */
	if(typeof document.selection != 'undefined') 
	{
		/* inseret code */
		var range = document.selection.createRange();
		var insText = range.text;
		range.text = aTag + insText + eTag;
		/* adapt Cursorposition */
		range = document.selection.createRange();
		if (insText.length == 0) 
		{
			range.move('character', -eTag.length);
		} 
		else 
		{
			range.moveStart('character', aTag.length + insText.length + eTag.length);
		}
		range.select();
	}
	/* for newer Gecko based Browsers */
	else if(typeof input.selectionStart != 'undefined')
	{
		/* inseret code */
		var start = input.selectionStart;
		var end = input.selectionEnd;
		var insText = input.value.substring(start, end);
		input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
		/* adapt Cursorposition */
		var pos;
		if (insText.length == 0) 
		{
			pos = start + aTag.length;
		} 
		else 
		{
			pos = start + aTag.length + insText.length + eTag.length;
		}
		input.selectionStart = pos;
		input.selectionEnd = pos;
	}
	/* for other Browsers */
	else
	{
		/* get insertposition */
		var pos;
		var re = new RegExp('^[0-9]{0,3}$');
		while(!re.test(pos)) 
		{
			pos = prompt("insert at position (0.." + input.value.length + "):", "0");
		}
		if(pos > input.value.length) 
		{
			pos = input.value.length;
		}
		/* adapt Cursorposition */
		var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
		input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
	}
}
// ADD MS END
</script>
<?php
//give headers in Joomla 1.5 a bit more spunk
//$this->model->spunk_up_headers_1_5(); //is in css
?>
<div style="margin: 0 auto; width: 950px; text-align: left;">
	<form id="adminForm" name="adminForm" method="post" action="">
		<input type="hidden" name="option" value="com_pagesanditems" />
		<input type="hidden" id="task" name="task" value="customitemtype.config_custom_itemtype_save" />
		<input type="hidden" id="view" name="view" value="" />
		<input type="hidden" id="tab" name="tab" value="" />
		<input type="hidden" id="sub_task" name="sub_task" value="" />
		<input type="hidden" id="type_id" name="type_id" value="<?php echo $type_id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="delete_items" id="delete_items" value="0" />
		<input type="hidden" name="update_items" id="update_items" value="0" />
		<input type="hidden" name="old_read_more_option" id="old_read_more_option" value="<?php echo $read_more; ?>" />
		<input type="hidden" name="items_of_this_type" id="items_of_this_type" value="<?php echo $items_of_this_type; ?>" />
		
		<?php //ADD MS 18.09.2009 ?>
		<input type="hidden" name="old_editor_id" id="old_editor_id" value="<?php echo $editor_id; ?>" />
		<input type="hidden" name="old_html_after" id="old_html_after" value="<?php echo $html_after; ?>" />
		<input type="hidden" name="old_html_before" id="old_html_before" value="<?php echo $html_before; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		<?php //ADD MS 18.09.2009 END ?>
			
		
		<?php
		/*
		<a href="index.php?option=com_pagesanditems">pages and items</a> >
		*/
		?>
		<a href="index.php?option=com_pagesanditems&view=config&tab=itemtypes"><?php echo JText::_('COM_PAGESANDITEMS_CONFIG'); ?></a>
		<?php echo JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_CONFIG'); ?>
		<h2><?php echo JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_CONFIG'); ?></h2>
		<div class="pi_form_wrapper">
			<div class="pi_width15">
				<?php echo JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_NAME'); ?>: <span class="star">&nbsp;*</span>
			</div>
			<div class="pi_width70">
				<input type="text" value="<?php echo $name; ?>" id="name" name="name" onchange="check_name_field();" /> <?php echo JText::_('COM_PAGESANDITEMS_ONLY_CHAR'); ?>
			</div>
		</div>
		<?php if($type_id)
		{
		//start edit 
		//echo '<fieldset class="adminform">';
		//echo '<legend>fields:</legend>';		
		echo '<br />';
		echo '<div class="line_top">';
		echo '<br />';
			echo '<div>';
			echo '<strong>';
				echo JText::_('COM_PAGESANDITEMS_FIELDS');
			echo '</strong>';
			echo '</div>';
			echo '<div class="right_align">';
				echo '<a href="http://www.pages-and-items.com/custom-itemtype-fields/" target="_blank">';
					echo JText::_('COM_PAGESANDITEMS_GET_MORE_FIELDTYPES');
				echo '</a>';
			echo '</div>';
		echo '<br />';
		
		?>

		<div class="right_align">
			<?php
		
				//get fieldtypes
				foreach($allFieldtypes as $key => $fieldtype)
				{
					$required_fieldtype = false;
					$only_once = false;
					$params = null;					
					//here we can not dispatch the fields not load 
					//here we must set registery object 
					
					if($this->model->joomlaVersion < '1.6')
					{
						if($fieldtype->params != '')
						{
							$params = PagesAndItemsHelper::objectToString($fieldtype->params);
							$params = new JParameter($params); //,$path);
						}
						else
						{
							$params = new JParameter(''); //,$path);
						}
					}
					else
					{
						$params = new JRegistry;
						$params->loadJSON($fieldtype->params);
					}

					if($params->get('only_once'))
					{
						foreach($fields as $field)
						{
							if($field->plugin == $fieldtype->name)
							{
								unset($allFieldtypes[$key]);
							}
						}
					}
					if($params->get('required_fieldtype'))
					{
						$required_fieldtype= $params->get('required_fieldtype');
					}
					if($required_fieldtype)
					{
						$required_fieldtype_in_fields = false;
						foreach($fields as $field)
						{
							if($field->plugin == $required_fieldtype)
							{
								$required_fieldtype_in_fields = true;
							}
						}
						if(!$required_fieldtype_in_fields)
						{
							unset($allFieldtypes[$key]);
						}
					}
					if($params->get('no_select'))
					{
						foreach($fields as $field)
						{
							if($field->plugin == $fieldtype->name)
							{
								unset($allFieldtypes[$key]);
							}
						}
					}
				}
				
				echo '<select name="plugins" id="plugins">';
				echo '<option value="0">'.JText::_('COM_PAGESANDITEMS_SELECT_FIELD_TYPE').'</option>';
				foreach($allFieldtypes as $fieldtype)
				{
					echo '<option value="';
					echo $fieldtype->name;
					echo '">';
					echo $fieldtype->name;
					echo '</option>';
				}
				
				
				echo '</select>';
				
			?>
			&nbsp;&nbsp;&nbsp;
			<!-- button with image? -->
			<?php
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->imagePath = PagesAndItemsHelper::getDirIcons();
			$button->buttonType = 'input';
			$button->text = JText::_('COM_PAGESANDITEMS_ADD_NEW_FIELD');
			$button->onclick = 'add_new_field();';
			$button->imageName = 'base/icon-16-add.png';
			echo $button->makeButton();
			
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->imagePath = PagesAndItemsHelper::getDirIcons();
			$button->buttonType = 'input';
			$button->text = JText::_('COM_PAGESANDITEMS_DELETE_SELECTED_FIELD');
			$button->onclick = 'delete_fields();';
			$button->imageName = 'base/icon-16-trash_delete.png';
			echo $button->makeButton();
			
			/*
			<input type="button" value="<?php echo JText::_('COM_PAGESANDITEMS_ADD_NEW_FIELD'); ?>" onclick="add_new_field();" />&nbsp;&nbsp;&nbsp;
			*/
			/*
			<a href="http://www.pages-and-items.com/custom-itemtype-fields/" target="_blank">
				<?php echo JText::_('COM_PAGESANDITEMS_GET_MORE_FIELDTYPES'); ?></a>
			*/
			?>

			<?php
			/*
			<a href="http://www.pages-and-items.com/custom-itemtype-fields/" target="_blank"><?php echo JText::_('COM_PAGESANDITEMS_GET_MORE_FIELDTYPES'); ?></a>&nbsp;&nbsp;&nbsp;
			<input type="button" value="<?php echo JText::_('COM_PAGESANDITEMS_DELETE_SELECTED_FIELD'); ?>" onclick="delete_fields();" />
			*/
			?>
		</div>
		<br />
		<div class="right_align">
			<?php
				//$htmlelements = ExtensionHelper::importExtension('html','cci_fields', null,true,null,true);
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
				$htmlelements = ExtensionHtmlHelper::importExtension('cci_fields', null,true,null,true);
				//$htmlelementVars = null;
				$htmlelement->html = '';
				//$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,$htmlelementVars,'cci_fields'));
				$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'cci_fields'));
				//ms: we will at this moment not display the buttons
				//echo $htmlelement->html;
			
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->imagePath = PagesAndItemsHelper::getDirIcons();
			$button->buttonType = 'input';
			$button->text = JText::_('COM_PAGESANDITEMS_DELETE_SELECTED_FIELD');
			$button->onclick = 'delete_fields();';
			$button->imageName = 'base/icon-16-trash_delete.png';
			//echo $button->makeButton();
			
			/*
			<input type="button" value="<?php echo JText::_('COM_PAGESANDITEMS_DELETE_SELECTED_FIELD'); ?>" onclick="delete_fields();" />
			*/
			?>
		</div>
		<?php
			
			//hide the data on the page
			echo '<div style="display: none;">';
			
			//headers
			echo '<div id="header_column_1"><strong>'.JText::_('COM_PAGESANDITEMS_TITLE').'</strong></div>';
			echo '<div id="header_column_2"><strong>'.JText::_('COM_PAGESANDITEMS_TEMPLATE_CODE').'</strong></div>';
			echo '<div id="header_column_3"><strong>'.JText::_('COM_PAGESANDITEMS_TYPE').'</strong></div>';
		
			//loop through items and echo data to hidden fields
			$counter = 0;
			foreach($fields as $field)
			{
				$areThereItems = true;
				$counter = $counter + 1;
				$title = $this->model->truncate_string(stripslashes($field->name), '50');
				$title = str_replace('"','&quot;',$title);
				
				echo '<input name="reorder_item_id_'.$counter.'" id="reorder_item_id_'.$counter.'" type="hidden" value="'.$field->id.'" />';
				
				//column 1
				echo '<div id="item_column_1_'.$counter.'">';
				echo '<input type="checkbox" class="checkbox" id="items_to_delete_'.$counter.'" name="items_to_delete[]" value="'.$field->id.'"  onclick="isChecked(this.checked);" />';
				$results = $dispatcher->trigger('onGetParams',array(&$params,$field->plugin));

				if(! in_array(true,$results))
				{
					$field->installed = false;
					echo '<a title="not Installed" >'; //href="index.php?option=com_pagesanditems&view=config_custom_itemtype_field&field_id='.$field->id.'">';
				}
				else
				{
					$field->installed = true;
					echo '<a href="index.php?option=com_pagesanditems&view=config_custom_itemtype_field&field_id='.$field->id.'">';
				}
				/*
				if ()
				{
					//unset($installed_fieldtypes[$key]);
				}
				*/
				
				
				echo $title.'</a>';
				echo '</div>';
				
				//column 2
				echo '<div id="item_column_2_'.$counter.'">';
				echo '{field_'.$field->name.'_'.$field->id.'}';
				echo '</div>';
				
				//column 3
				echo '<div id="item_column_3_'.$counter.'">';
				echo $field->plugin;
				echo '</div>';
							
			}
			echo '</div>';
		
			
			//2 hidden fields which are usefull for updating the ordering when submitted
			echo '<input name="items_are_reordered" id="items_are_reordered" type="hidden" value="false" />';
			echo '<input name="items_total" id="items_total" type="hidden" value="'.$counter.'" />';
			
			echo '<div id="target_items" class="more_space"></div>';
			echo '<script src="components/com_pagesanditems/javascript/reorder_items.js" language="JavaScript" type="text/javascript"></script>';
			echo '<script language="JavaScript"  type="text/javascript">';
			echo "<!--\n";
			echo "var joomlaVersion = '1.6';\n";
			echo "var items_total = ".$counter.";\n";
			echo "var number_of_columns = '3';\n";
			echo "var ordering = '".JText::_('COM_PAGESANDITEMS_ORDERING')."';\n";
			echo "var no_items = '".JText::_('COM_PAGESANDITEMS_CUSTOMITEMTYPE_HAS_NO_FIELDS')."';\n";
			
			echo "document.onload = print_items();\n";
			echo "-->\n";
			echo "</script>\n";
			
			
			echo '<br />';
			echo '<div class="line_top">';
			echo '<br />';
			?>
			
			

			<?php
			
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				/*
				require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
				ExtensionHtmlHelper::importExtension('template', null,true,null,true);
				$htmlelementVars = null;
				$htmlelement->html = '';
				$dispatcher->trigger('onGetHtmlelementConfig',array(&$htmlelement,$htmlelementVars,'manager',$type_id));
				*/
				require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
				//ExtensionManagerHelper::importExtension(null,'template',true,null,true);
				ExtensionManagerHelper::importExtension(null,null,true,null,true);
				//$htmlelementVars = null;
				//$htmlelement->html = '';
				
				$managerItemtypeConfigEdit->html = '';
				$dispatcher->trigger('onGetManagerItemtypeConfigEdit',array(&$managerItemtypeConfigEdit,'custom_'.$type_id,$type_id));
				echo $managerItemtypeConfigEdit->html;
				//$dispatcher->trigger('onGetHtmlelementConfig',array(&$htmlelement,$htmlelementVars,'template',$type_id));
				//$html .= $htmlelement->html;
				//echo $htmlelement->html;
				if($managerItemtypeConfigEdit->html != '')
				{
					echo '<br />';
					echo '<br />';
					echo '<div class="line_top">';
						//echo '<br />';
					echo '</div>';
				}
			
			?>
			<div class="pi_form_wrapper">
				<strong>
					<?php echo JText::_('COM_PAGESANDITEMS_DISPLAY_READ_MORE_LINK') ?>: 
				</strong>
				<div>
					<label><input type="radio" class="radio" name="read_more" value="1" <?php if($read_more=='1'){ echo "checked=\"checked\""; }?> /><?php echo JText::_('COM_PAGESANDITEMS_CIT_READMORE_NONE'); ?>.</label><br />
					<label><input type="radio" class="radio" name="read_more" value="4" <?php if($read_more=='4'){ echo "checked=\"checked\""; }?> /><?php echo JText::_('COM_PAGESANDITEMS_CIT_READMORE_ALWAYS'); ?>.</label><br />
					<label><input type="radio" class="radio" name="read_more" value="2" <?php if($read_more=='2'){ echo "checked=\"checked\""; }?>  /><?php echo JText::_('COM_PAGESANDITEMS_CIT_READMORE_WHEN_CONTENT'); ?></label><br />
					<label><input type="radio" class="radio" name="read_more" value="3" <?php if($read_more=='3'){ echo "checked=\"checked\""; }?>  /><?php echo JText::_('COM_PAGESANDITEMS_CIT_READMORE_EDITOR'); ?></label><input type="text" style="width: 20px;" name="editor_id"  id="editor_id" value="<?php echo $editor_id; ?>" />}. <?php echo JText::_('COM_PAGESANDITEMS_CIT_READMORE_HTML_AFTER'); ?>: <input type="text" name="html_after" id="html_after" value="<?php echo $html_after; ?>" />. <?php echo JText::_('COM_PAGESANDITEMS_CIT_READMORE_HTML_BEFORE'); ?>: <input type="text" name="html_before" id="html_before" value="<?php echo $html_before; ?>" />.
				</div>
			</div>
			<?php
			
			//echo '<br />';
			echo '<div class="line_top">';
			echo '<br />';
				echo '<strong>';
					echo JText::_('COM_PAGESANDITEMS_TEMPLATE_INTRO');
				echo '</strong>';
			echo '<br />';
				echo JText::_('COM_PAGESANDITEMS_TEMPLATE_INTRO_TIP');
			echo '</div>';

			echo '<div class="textarea">';
				echo '<textarea name="template_intro" id="template_intro" cols="110" rows="25" style="width: 100%;">'.$template_intro.'</textarea>';
				echo '<textarea name="template_intro_original" id="template_intro_original" cols="110" rows="25" style="display: none;">'.$template_intro.'</textarea>';
			echo '</div>';

//				echo '<div>';
			echo '<div class="line_bottom">';
				/*
				$this->model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' ORDER BY ordering");
				$this->model->db->query();
				$rows_pi_fields = $this->model->db->loadObjectList();
				*/
				//$htmlelements = ExtensionHelper::importExtension('html','cci_template', null,true,null,true);
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
				$htmlelements = ExtensionHtmlHelper::importExtension('cci_template', null,true,null,true);
				//$htmlelementVars = null;
				$htmlelement->html = '';
				//$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,$htmlelementVars,'cci_template','intro',$fields));
				
				$htmlOptions->template = 'intro';
				$htmlOptions->fields = $fields;
				$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'cci_template',$htmlOptions));
				//$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'cci_template','intro',$fields));
				//$html .= $htmlelement->html;

				echo $htmlelement->html;
			
				//TODO template_special_button
				/*
				foreach($fields as $field)
				{	
					$htmlelementVars = null;
					$htmlelement->html = '';
					$dispatcher->trigger('onGetTemplate_special_button',array(&$htmlelement,$htmlelementVars,'cci_template','intro',$fields,$field->plugin));
					//$html .= $htmlelement->html;

					echo $htmlelement->html;
				}
				*/
			/*
				//insert field tags
				
				echo '<select name="pi_fields_intro" onchange="insert_in_textarea( this.value , \'\',\'template_intro\' );this.options[0].selected=true;return false;">';
				echo '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_FIELD_CODE').' - </option>';
				foreach( $rows_pi_fields as $row_pi_fields )
				{
					$results = $dispatcher->trigger('onGetParams',array(&$params,$row_pi_fields->plugin));
					if(in_array(true,$results))
					{
					if($row_pi_fields->plugin=='image_multisize')
					{
						for ($n = 1; $n <= 5; $n++){
							$option_Field = '{field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.' size='.$n.'}'."\n";
							$_option = '<option value="'.$option_Field.'">';
							$_option .= $row_pi_fields->name.' size='.$n;
							$_option .= '</option>';
						echo $_option;
						}
					}else{	
						
						$option_Field = '{field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$_option = '<option value="'.$option_Field.'">';
						$_option .= $row_pi_fields->name;
						$_option .= '</option>';
						echo $_option;
					}
					}
				}
				echo '</select>';
			
			
				//insert if-empty tags
				echo '<select name="pi_fields_if_intro" onchange="insert_in_textarea( this.value , this.form.pi_fields_if_intro.options[this.form.pi_fields_if_intro.selectedIndex].label,\'template_intro\' );this.options[0].selected=true;return false;">';
				echo '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_IF_EMPTY_CODE').' - </option>';
				foreach( $rows_pi_fields as $row_pi_fields )
				{
					$results = $dispatcher->trigger('onGetParams',array(&$params,$row_pi_fields->plugin));
					if($row_pi_fields->plugin!='image_multisize' && in_array(true,$results))
					{
						$option_Field_begin = '{if-empty_field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$option_Field_end = '{/if-empty_field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$_option = '<option value="'.$option_Field_begin.'" ';
						$_option .= 'label="'.$option_Field_end.'" >';
						$_option .= 'if empty '.$row_pi_fields->name;
						$_option .= '</option>';
						echo $_option;
					}
				}
				echo '</select>';
			
			
				//insert if-empty tags
				echo '<select name="pi_fields_ifnot_intro" onchange="insert_in_textarea( this.value , this.form.pi_fields_ifnot_intro.options[this.form.pi_fields_ifnot_intro.selectedIndex].label,\'template_intro\' );this.options[0].selected=true;return false;">';
				echo '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_IF_NOT_EMPTY_CODE').' - </option>';
				foreach( $rows_pi_fields as $row_pi_fields )
				{
					$results = $dispatcher->trigger('onGetParams',array(&$params,$row_pi_fields->plugin));
					if($row_pi_fields->plugin!='image_multisize' && in_array(true,$results))
					{
						$option_Field_begin = '{if-not-empty_field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$option_Field_end = '{/if-not-empty_field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$_option = '<option value="'.$option_Field_begin.'" ';
						$_option .= 'label="'.$option_Field_end.'" >';
						$_option .= 'if not empty '.$row_pi_fields->name;
						$_option .= '</option>';
						echo $_option;
					}
				}
				echo '</select>';
				
				echo ' ';
				echo '<input	 type="button" value="'.JText::_('COM_PAGESANDITEMS_ADD_DIV').'" onClick="insert_in_textarea(\'<div>\', \'</div>\',\'template_intro\')">';
				echo '	 ';
				echo '<input type="button" value="'.JText::_('COM_PAGESANDITEMS_ADD_HIDE_FULL_VIEW').'" onClick="insert_in_textarea(\'{hide_in_full_view}\', \'{/hide_in_full_view}\',\'template_intro\')">';
				echo ' ';
				echo '<input type="button" value="'.JText::_('COM_PAGESANDITEMS_ADD_HIDE_INTRO_VIEW').'" onClick="insert_in_textarea(\'{hide_in_intro_view}\', \'{/hide_in_intro_view}\',\'template_intro\')">';
				echo ' ';
			
				echo '<select name="pi_fields_other_intro" onchange="insert_in_textarea( this.value , 	this.form.pi_fields_other_intro.options[this.form.pi_fields_other_intro.selectedIndex].label,\'template_intro\' );this.options[0].selected=true;return false;">';
					echo '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_OTHER_CODE').' - </option>';
					echo '<option value="{article_id}" label="">{article_id}</option>';
					echo '<option value="{article_title}" label="">{article_title}</option>';
					echo '<option value="{article_created}" label="">{article_created}</option>';
					echo '<option value="{article_modified}" label="">{article_modified}</option>';
					echo '<option value="{article_publish_up}" label="">{article_publish_up}</option>';
					echo '<option value="{article_hits}" label="">{article_hits}</option>';
					echo '<option value="{article_rating}" label="">{article_rating}</option>';
					echo '<option value="<p>" label="</p>">&lt;p&gt;&lt;/p&gt;</option>';
					echo '<option value="<a href=&quot;&quot;>" label="</a>">&lt;a&gt;&lt;/a&gt;</option>';
					echo '<option value="<br />" label="">&lt;br /&gt;</option>';
					echo '<option value="<strong>" label="</strong>">&lt;strong&gt;&lt;/strong&gt;</option>';
					echo '<option value="<h1>" label="</h1>">&lt;h1&gt;&lt;/h1&gt;</option>';
					echo '<option value="<h2>" label="</h2>">&lt;h2&gt;&lt;/h2&gt;</option>';
					echo '<option value="<h3>" label="</h3>">&lt;h3&gt;&lt;/h3&gt;</option>';
					echo '<option value="<h4>" label="</h4>">&lt;h4&gt;&lt;/h4&gt;</option>';
					echo '<option value="<h5>" label="</h5>">&lt;h5&gt;&lt;/h5&gt;</option>';
					echo '<option value="<h6>" label="</h6>">&lt;h6&gt;&lt;/h6&gt;</option>';
					echo '<option value="<em>" label="</em>">&lt;em&gt;&lt;/em&gt;</option>';
					echo '<option value="<span style=&quot;text-decoration: underline;&quot;>" label="</span>">underline</option>';
					echo '<option value="<pre>" label="</pre>">&lt;pre&gt;&lt;/pre&gt;</option>';
				echo '</select>';
				*/	
				
				
				//Carsten 16-9-2009. sorry Micha. Temporarily taken out code.
				//Carsten 17-9-2009. ok Micha, code back in :-)#
				// ADD MS 30.07.2009
				/*
				foreach($fields as $field)
				{
					if($class_object = $this->model->get_field_class_object($field->plugin,'template_special_button'))
					{
						if(in_array($field->plugin, $this->model->fieldtypes_integrated))
						{
							$template_button = $class_object->template_special_button($type_id, 'template_intro');
						}
						else
						{
							$this->model->get_fieldtype_language($fieldtype);
							$template_button = $class_object->template_special_button($type_id, 'template_intro');
						}
						echo $template_button;
					}
				}
				*/


				// ADD MS END 30.07.2009
			
//				echo '</div>';
			echo '</div>';
			echo '<br />';
			echo '<div class="top"><strong>';
			// ADD MS END
			echo JText::_('COM_PAGESANDITEMS_TEMPLATE_FULL');
			echo '</strong><br />';
			echo JText::_('COM_PAGESANDITEMS_TEMPLATE_FULL_TIP');
			echo '</div>';
			echo '<div class="textarea">';
			echo '<textarea name="template_full" id="template_full" cols="110" rows="25" style="width: 100%;">'.$template_full.'</textarea>';
			echo '<textarea name="template_full_original" id="template_full_original" cols="110" rows="25" style="display: none;">'.$template_full.'</textarea>';
			//echo '</div>';
			echo '</div>';//<br /><strong>'; // CHANGE MS
			echo '<div class="line_bottom">';
			
			
			
			//$htmlelementVars = null;
			$htmlelement->html = '';
			//$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,$htmlelementVars,'cci_template','full',$fields));
			$htmlOptions->template = 'full';
			//$htmlOptions->fields = $fields;
			$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'cci_template',$htmlOptions));
			//$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'cci_template','full',$fields));
			//$html .= $htmlelement->html;

			echo $htmlelement->html;
			
			//TODO template_special_button
			/*
			foreach($fields as $field)
			{
				$htmlelementVars = null;
				$htmlelement->html = '';
				$dispatcher->trigger('onGetTemplate_special_button',array(&$htmlelement,$htmlelementVars,'cci_template','full',$fields,$field->plugin));
				//$html .= $htmlelement->html;

				echo $htmlelement->html;
			}
			*/
			/*
			$this->model->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' ");
			$this->model->db->query();
			$rows_pi_fields = $this->model->db->loadObjectList();
			
			//insert field tags
			echo '<select name="pi_fields_intro" onchange="insert_in_textarea( this.value , \'\',\'template_full\' );this.options[0].selected=true;return false;">';
			echo '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_FIELD_CODE').' - </option>';
			foreach( $rows_pi_fields as $row_pi_fields )
			{
				$results = $dispatcher->trigger('onGetParams',array(&$params,$row_pi_fields->plugin));
				if($row_pi_fields->plugin=='image_multisize' && in_array(true,$results))
				{
					for ($n = 1; $n <= 5; $n++){
						$option_Field = '{field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.' size='.$n.'}'."\n";
						$_option = '<option value="'.$option_Field.'">';
						$_option .= $row_pi_fields->name.' size='.$n;
						$_option .= '</option>';
						echo $_option;
					}
				}else{
					$option_Field = '{field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
					$_option = '<option value="'.$option_Field.'">';
					$_option .= $row_pi_fields->name;
					$_option .= '</option>';
					echo $_option;
				}
			}
			echo '</select>';
			
			//insert if-empty tags
			echo '<select name="pi_fields_if_full" onchange="insert_in_textarea( this.value , this.form.pi_fields_if_full.options[this.form.pi_fields_if_full.selectedIndex].label,\'template_full\' );this.options[0].selected=true;return false;">';
				echo '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_IF_EMPTY_CODE').' - </option>';
				foreach( $rows_pi_fields as $row_pi_fields )
				{
					$results = $dispatcher->trigger('onGetParams',array(&$params,$row_pi_fields->plugin));
					if($row_pi_fields->plugin!='image_multisize' && in_array(true,$results))
					{
						$option_Field_begin = '{if-empty_field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$option_Field_end = '{/if-empty_field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$_option = '<option value="'.$option_Field_begin.'" ';
						$_option .= 'label="'.$option_Field_end.'" >';
						$_option .= 'if empty '.$row_pi_fields->name;
						$_option .= '</option>';
						echo $_option;
					}
				}
			echo '</select>';
			
			//insert if-empty tags
			echo '<select name="pi_fields_ifnot_full" onchange="insert_in_textarea( this.value , this.form.pi_fields_ifnot_full.options[this.form.pi_fields_ifnot_full.selectedIndex].label,\'template_full\' );this.options[0].selected=true;return false;">';
				echo '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_IF_NOT_EMPTY_CODE').' - </option>';
				foreach( $rows_pi_fields as $row_pi_fields )
				{
					$results = $dispatcher->trigger('onGetParams',array(&$params,$row_pi_fields->plugin));
					if($row_pi_fields->plugin!='image_multisize' && in_array(true,$results)){
						$option_Field_begin = '{if-not-empty_field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$option_Field_end = '{/if-not-empty_field_'.$row_pi_fields->name.'_'.$row_pi_fields->id.'}'."\n";
						$_option = '<option value="'.$option_Field_begin.'" ';
						$_option .= 'label="'.$option_Field_end.'" >';
						$_option .= 'if not empty '.$row_pi_fields->name;
						$_option .= '</option>';
						echo $_option;
					}
				}
			echo '</select>';
			
			echo ' ';
			echo '<input type="button" value="'.JText::_('COM_PAGESANDITEMS_ADD_DIV').'" onClick="insert_in_textarea(\'<div>\', \'</div>\',\'template_full\')">';
			echo ' ';
			
			
			//ADD CHANGE MS END 13.09.2009
			
			//select other code
			echo '<select name="pi_fields_other_full" onchange="insert_in_textarea( this.value , this.form.pi_fields_other_full.options[this.form.pi_fields_other_full.selectedIndex].label,\'template_full\' );this.options[0].selected=true;return false;">';
				echo '<option value="" selected="selected">- '.JText::_('COM_PAGESANDITEMS_INSERT_OTHER_CODE').' - </option>';
				echo '<option value="{article_id}" label="">{article_id}</option>';
				echo '<option value="{article_title}" label="">{article_title}</option>';
				echo '<option value="{article_created}" label="">{article_created}</option>';
				echo '<option value="{article_modified}" label="">{article_modified}</option>';
				echo '<option value="{article_publish_up}" label="">{article_publish_up}</option>';
				echo '<option value="{article_hits}" label="">{article_hits}</option>';
				echo '<option value="{article_rating}" label="">{article_rating}</option>';
				echo '<option value="<p>" label="</p>">&lt;p&gt;&lt;/p&gt;</option>';
				echo '<option value="<a href=&quot;&quot;>" label="</a>">&lt;a&gt;&lt;/a&gt;</option>';
				echo '<option value="<br />" label="">&lt;br /&gt;</option>';
				echo '<option value="<strong>" label="</strong>">&lt;strong&gt;&lt;/strong&gt;</option>';
				echo '<option value="<h1>" label="</h1>">&lt;h1&gt;&lt;/h1&gt;</option>';
				echo '<option value="<h2>" label="</h2>">&lt;h2&gt;&lt;/h2&gt;</option>';
				echo '<option value="<h3>" label="</h3>">&lt;h3&gt;&lt;/h3&gt;</option>';
				echo '<option value="<h4>" label="</h4>">&lt;h4&gt;&lt;/h4&gt;</option>';
				echo '<option value="<h5>" label="</h5>">&lt;h5&gt;&lt;/h5&gt;</option>';
				echo '<option value="<h6>" label="</h6>">&lt;h6&gt;&lt;/h6&gt;</option>';
				echo '<option value="<em>" label="</em>">&lt;em&gt;&lt;/em&gt;</option>';
				echo '<option value="<span style=&quot;text-decoration: underline;&quot;>" label="</span>">underline</option>';
				echo '<option value="<pre>" label="</pre>">&lt;pre&gt;&lt;/pre&gt;</option>';
			echo '</select>';
			*/
			//Carsten 16-9-2009. sorry Micha. Temporarily taken out code.
			//Carsten 17-9-2009. ok Micha, code back in :-)#
			// ADD MS 30.07.2009
			/*
			foreach($fields as $field)
			{
				if($class_object = $this->model->get_field_class_object($field->plugin,'template_special_button'))
				{
					if(in_array($field->plugin, $this->model->fieldtypes_integrated))
					{
						$template_button = $class_object->template_special_button($type_id, 'template_full');
					}
					else
					{
						$this->model->get_fieldtype_language($fieldtype);
						$template_button = $class_object->template_special_button($type_id, 'template_full');
					}
					echo $template_button;
				}
			}
			*/
			// ADD MS END
			
			echo '</div>';
			echo '<div><br /><strong>';
			
			echo JText::_('COM_PAGESANDITEMS_TEMPLATE_DEFAULT');
			echo '</strong><br />';
			echo JText::_('COM_PAGESANDITEMS_TEMPLATE_DEFAULT_TIP');
			echo '</div>';
			echo '<div>';
			echo '<textarea name="template_default" cols="110" rows="25" style="width: 100%;" readonly="readonly">';
			// CHANGE ADD all fields function spezial_template_output?
			//ADD MS
			$template_special = false;
			$template = false;
			
			/*
			//TODO remove template_special
			
			foreach($fields as $field)
			{
				$htmlelementVars = null;
				$htmlelement->html = '';
				$dispatcher->trigger('onGetTemplate_special',array(&$htmlelement,$htmlelementVars,'cci_template','default',$fields,$field->plugin));
				//$html .= $htmlelement->html;

				echo $htmlelement->html;
			}
			*/
			
			/*
			foreach($fields as $field)
			{
				if($class_object = $this->model->get_field_class_object($field->plugin,'template_special'))
				{
					$template = $class_object->template_special($fields);
					echo $template;
				}
			}
			*/
			//if(!$template_spezial)
			if(!$template)
			{
			//END ADD MS
				foreach($fields as $field){
					echo '<div>'."\n";
					echo '{field_'.$field->name.'_'.$field->id.'}'."\n";
					echo '</div>'."\n";
				}
			//ADD MS
			}
			//END ADD MS
			echo '</textarea>';
			echo '</div>';
			
			//end edit
		}else{
			//when new field set default
			echo '<input type="hidden" class="radio" name="read_more" value="1" />';
		} ?>
		
	</form>
</div>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
// $this->model->display_footer(); 
?>