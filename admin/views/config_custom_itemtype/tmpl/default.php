<?php
/**
* @version		2.1.5
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

//if(PagesAndItemsHelper::user_type!='Super Administrator' && !$this->pagesAndItemsModel->isSuperAdmin)
if(!PagesAndItemsHelper::getIsSuperAdmin())
{
	exit("you need to be logged in as a super administrator to edit the Pages-and-Items config.");
}
$type_id = JRequest::getVar('type_id', '' );
if(!$type_id){
	//start new

		$type_id = '';
		$name = '';
		$read_more = '4';
		$items_of_this_type = 0;
		$editor_id = 0;
		$html_after = '';
		$html_before = '';

	//end new
}else{
	//start edit

	//get data about this itemtype
	$this->db->setQuery("SELECT * FROM #__pi_customitemtypes WHERE id='$type_id' LIMIT 1");
	$customitemtypes = $this->db->loadObjectList();
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
	$this->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE type_id='$type_id' ORDER BY ordering ASC");
	$fields = $this->db->loadObjectList();

	//get itemtype name
	$itemtype_name = 'custom_'.$type_id;
	//get number of items which need updating
	$this->db->setQuery( "SELECT c.id "
	."FROM #__pi_item_index AS i "
	."LEFT JOIN #__content AS c "
	."ON c.id=i.item_id "
	."WHERE i.itemtype='$itemtype_name' "
	."AND (c.state='0' OR c.state='1') "
	);

	$items_array = $this->db->loadObjectList();
	$items_of_this_type = count($items_array);


	//end edit
}

//first load all avaible fields
$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, null,true,null,true,' ORDER BY element');
$dispatcher = &JDispatcher::getInstance();
$allFieldtypes = ExtensionFieldtypeHelper::getExtension(null, null,' ORDER BY element');


?>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<!-- <link rel="stylesheet" type="text/css" href="components/com_pagesanditems/css/pagesanditems2.css" /> -->
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
			if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
			document.location.href = 'index.php?option=com_pagesanditems&view=render_items_by_custom_itemtype&type_id=<?php echo $type_id; ?>&futuretask=config_custom_itemtype&tmpl=component';
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
			if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
			if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
			if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
		if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
//PagesAndItemsHelper::spunk_up_headers_1_5(); //is in css
?>

<div  style="margin: 0 auto; width: 950px; text-align: left;">
	<form id="adminForm" name="adminForm" method="post" action="">
		<input type="hidden" name="option" value="com_pagesanditems" />
		<input type="hidden" id="task" name="task" value="customitemtype.config_custom_itemtype_save" />
		<input type="hidden" id="view" name="view" value="" />
		<input type="hidden" id="tab" name="tab" value="" />
		<input type="hidden" id="sub_task" name="sub_task" value="" />
		<input type="hidden" id="type_id" name="type_id" value="<?php echo $type_id; ?>" />
		<input type="hidden" name="delete_items" id="delete_items" value="0" />
		<input type="hidden" name="update_items" id="update_items" value="0" />
		<input type="hidden" name="old_read_more_option" id="old_read_more_option" value="<?php echo $read_more; ?>" />
		<input type="hidden" name="items_of_this_type" id="items_of_this_type" value="<?php echo $items_of_this_type; ?>" />

		<?php //ADD MS 18.09.2009 ?>
		<input type="hidden" name="old_editor_id" id="old_editor_id" value="<?php echo $editor_id; ?>" />
		<input type="hidden" name="old_html_after" id="old_html_after" value="<?php echo $html_after; ?>" />
		<input type="hidden" name="old_html_before" id="old_html_before" value="<?php echo $html_before; ?>" />



		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHTML::_( 'form.token' ); ?>
		<?php //ADD MS 18.09.2009 END ?>


		<?php
		/*
		<a href="index.php?option=com_pagesanditems">pages and items</a> >
		*/
		?>
		<a href="index.php?option=com_pagesanditems&view=config&tab=itemtypes"><?php echo JText::_('COM_PAGESANDITEMS_CONFIG'); ?></a>
		 &#8250; 
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
				//echo '<a href="http://www.pages-and-items.com/custom-itemtype-fields/" target="_blank">';
				echo '<a href="http://www.pages-and-items.com/extensions/pages-and-items-plugins/custom-itemtype-fields/" target="_blank">';
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

					if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
			/*
			//old begin
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
				$title = PagesAndItemsHelper::truncate_string(stripslashes($field->name), '50');
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
			echo "var moveUp = '".JText::_('JLIB_HTML_MOVE_UP')."';\n";
			echo "var moveDown = '".JText::_('JLIB_HTML_MOVE_DOWN')."';\n";
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
			
			*/
			
			//old end
			?>
			<?php
			//new start
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'customfieldslist.php');
			$customfieldsList = new CustomFieldsList();
			echo $customfieldsList->renderItems($fields,$dispatcher);
			//new end
			?>
			<?php
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
				ExtensionManagerHelper::importExtension(null,null,true,null,true);

				$managerItemtypeConfigEdit->html = '';
				$dispatcher->trigger('onGetManagerItemtypeConfigEdit',array(&$managerItemtypeConfigEdit,'custom_'.$type_id,$type_id));
				echo $managerItemtypeConfigEdit->html;
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
				<?php
				
				$extension = 'com_plugins';
				$lang = &JFactory::getLanguage();
				//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
				$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
				/*
				<strong>
					<?php echo JText::_('COM_PAGESANDITEMS_PARAMS') ?>:
				</strong>
				*/
				?>
				
				<?php echo JHtml::_('sliders.start','params_sliders', array('useCookie'=>0,'startOffset'=>-1) ); ?>
				<?php //echo JHtml::_('sliders.panel',JText::_('COM_PAGESANDITEMS_PARAMS'), 'customitemtype'); ?>
				<?php
				
				$doc = JFactory::getDocument();
				$contentCss = "";
				$contentCss .= ".radiolong {max-width: 500px;min-width:400px;}";
				$doc->addStyleDeclaration($contentCss);
				
				$this->form->setFieldAttribute('useDefault','type','radio','params');
				$fieldSets = $this->form->getFieldsets('params');
				foreach ($fieldSets as $name => $fieldSet) :
				$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_PLUGINS_'.$name.'_FIELDSET_LABEL';
				if($name != 'hidden')
				{
					
					echo JHtml::_('sliders.panel',JText::_($label), $name.'-options');
					if (isset($fieldSet->description) && trim($fieldSet->description)) :
						echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
					endif;
					
					$hidden = '';
					
				}
				else
				{
					$hidden = 'style="display:none;"';
				}
				?>
				<fieldset class="panelform" <?php echo $hidden; ?>>
					<?php
					$hidden_fields = '';
					$countHiddenFields = 0;
					$countFields = 0;
					?>
					<ul class="adminformlist">
						<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<?php if (!$field->hidden) : ?>
						<li>
							<?php echo $field->label; ?>
							<?php echo $field->input;
							$countFields++;
							?>
						</li>
						<?php else : $hidden_fields.= $field->input;
						$countHiddenFields++;
						?>
						<?php endif; ?>
						<?php endforeach; ?>
					</ul>
					<?php echo $hidden_fields;
					if($countHiddenFields == count($this->form->getFieldset($name)))
					{
						//echo JText::_('COM_PAGESANDITEMS_EXTENSION_ONLY_HIDDEN_PARAMS');
					}
					?>
				</fieldset>
			<?php
			endforeach;
			echo JHtml::_('sliders.end');
			?>
			</div>
			<div class="line_top">
			
			
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

			echo '<div class="line_bottom">';
				$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
				require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
				$htmlelements = ExtensionHtmlHelper::importExtension('cci_template', null,true,null,true);
				$htmlelement->html = '';
				$htmlOptions->template = 'intro';
				$htmlOptions->fields = $fields;
				$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'cci_template',$htmlOptions));

				echo $htmlelement->html;

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

			$htmlelement->html = '';
			$htmlOptions->template = 'full';
			$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'cci_template',$htmlOptions));

			echo $htmlelement->html;

			echo '</div>';
			echo '<div><br /><strong>';

			echo JText::_('COM_PAGESANDITEMS_TEMPLATE_DEFAULT');
			echo '</strong><br />';
			echo JText::_('COM_PAGESANDITEMS_TEMPLATE_DEFAULT_TIP');
			echo '</div>';
			echo '<div>';
			echo '<textarea name="template_default" cols="110" rows="25" style="width: 100%;" readonly="readonly">';
			$template_special = false;
			$template = false;

			if(!$template)
			{
				foreach($fields as $field){
					echo '<div>'."\n";
					echo '{field_'.$field->name.'_'.$field->id.'}'."\n";
					echo '</div>'."\n";
				}
			}
			echo '</textarea>';
			echo '</div>';

			//end edit
		}else{
			//when new field set default
			echo '<input type="hidden" class="radio" name="read_more" value="1" />';
		} ?>

	</form>
</div>
<!-- end id="form_content" need for css-->
</div>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>