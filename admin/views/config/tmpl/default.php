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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$checked = ' checked="checked"';

if(!PagesAndItemsHelper::getIsSuperAdmin())
{
	echo "<script> alert('you need to be logged in as a super administrator to edit the Pages-and-Items config'); window.history.go(-1); </script>";
	exit();
}
$config = PagesAndItemsHelper::getConfig();
?>

<script src="components/com_pagesanditems/javascript/tab_cookies.js" language="JavaScript" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript">

<?php
if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
{
?>
	function submitbutton(pressbutton)
	{
		if (pressbutton == 'config.config_save') {
			submitform('config.config_save');
		}
		if (pressbutton == 'config.config_apply') {
			document.getElementById('sub_task').value = 'apply';
			submitform('config.config_save');
		}
		if (pressbutton == 'config.cancel') {
			document.location.href = 'index.php?option=com_pagesanditems';
		}
	}
<?php
}
else
{
?>
	Joomla.submitbutton = function(pressbutton)
	{
		if (pressbutton == 'config.config_save') {
			Joomla.submitform('config.config_save',document.getElementById('adminForm'));
		}
		if (pressbutton == 'config.config_apply')
		{
			document.getElementById('sub_task').value = 'apply';
			Joomla.submitform('config.config_save',document.getElementById('adminForm'));
		}
		if (pressbutton == 'config.cancel')
		{
			document.location.href = 'index.php?option=com_pagesanditems';
		}
	}
<?php
}

?>
function config_itemtype(itemtype){
	document.id('sub_task').value = itemtype;
	document.id('task').value = 'config.config_itemtype';
	document.adminForm.submit();
	//Joomla.submitform('config.config_itemtype',document.getElementById('adminForm'));
}
<?php
$tab = JRequest::getVar('tab', false);
if(!$tab){
	echo "cookie_value = getCookie('pi_tabs');"."\n";
	echo "if(cookie_value!=null){"."\n";
		echo "current_tab = cookie_value;"."\n";
	echo "}else{"."\n";
		echo "setCookie('pi_tabs', 'general', '', '', '', '');"."\n";
		echo "current_tab = 'general';"."\n";
	echo "}"."\n";
}else{
	echo "setCookie('pi_tabs', '".$tab."', '', '', '', '');"."\n";
	echo "current_tab = '".$tab."';"."\n";
}
?>
function get_tab(tab){
	if(tab!=current_tab){
		new_tab = 'tab_'+tab;
		document.getElementById(new_tab).className = 'on';
		old_tab = 'tab_'+current_tab;
		document.getElementById(old_tab).className = 'none';
		document.getElementById(tab).style.display = 'block';
		document.getElementById(current_tab).style.display = 'none';
		current_tab = tab;
		setCookie('pi_tabs', tab, '', '', '', '');
	}
}
function pi_config_menu_init(){
	current_tab_name = 'tab_'+current_tab;
	document.getElementById(current_tab_name).className = 'on';
	document.getElementById(current_tab).style.display = 'block';
}
if(window.addEventListener)window.addEventListener("load",pi_config_menu_init,false);else if(window.attachEvent)window.attachEvent("onload",pi_config_menu_init);


function check_latest_version(){
	document.getElementById('version_checker_target').innerHTML = document.getElementById('version_checker_spinner').innerHTML;
	ajax_url = 'index.php?option=com_pagesanditems&controller=config&task=ajax_version_checker&format=raw';
	var req = new Request.HTML({url:ajax_url, update:'version_checker_target' });
	req.send();
}

</script>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<div style="text-align: left;">
	<div style="float:right;width: 100px;">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
			<img alt="" border="0" src="https://www.paypal.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">

		</form>
	</div>
	<form id="adminForm" name="adminForm" method="post" action="">
		<input type="hidden" name="option" value="com_pagesanditems" />
		<input type="hidden" name="task" id="task" value="config_save" />
		<input type="hidden" name="sub_task" id="sub_task" value="" />
		<?php echo JHtml::_('form.token'); ?>

		<ul id="pi_menu">
			<li><a id="tab_general" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('general');"><span><?php echo JText::_('COM_PAGESANDITEMS_GENERAL'); ?></span></a></li>
			<li><a id="tab_permissions" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('permissions');"><span><?php echo JText::_('COM_PAGESANDITEMS_PERMISSIONS'); ?></span></a></li>
			<li><a id="tab_itemtypes" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('itemtypes');"><span><?php echo JText::_('COM_PAGESANDITEMS_ITEMTYPES'); ?></span></a></li>
			<li><a id="tab_items" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('items');"><span><?php echo JText::_('COM_PAGESANDITEMS_ITEMS'); ?></span></a></li>
			<li><a id="tab_new_item" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('new_item');"><span><?php echo JText::_('COM_PAGESANDITEMS_NEW_ITEM'); ?></span></a></li>
			<li><a id="tab_menus" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('menus');"><span><?php echo JText::_('COM_PAGESANDITEMS_MENUS'); ?></span></a></li>
			<li><a id="tab_pages" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('pages');"><span><?php echo JText::_('COM_PAGESANDITEMS_PAGES'); ?></span></a></li>
			<li><a id="tab_credits" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('credits');"><span><?php echo JText::_('COM_PAGESANDITEMS_CREDITS'); ?></span></a></li>
			<li><a id="tab_joomfish" onfocus="if(this.blur)this.blur();" href="javascript:get_tab('joomfish');"><span>Joom!Fish</span></a></li>
		</ul>
<!--
general
-->

		<div id="general">
		<table class="adminlist" width="100%">
			<tr>
				<th colspan="2">
					<?php echo JText::_('COM_PAGESANDITEMS_GENERAL'); ?>
				</th>
			</tr>
			<tr>
				<td width="30%">
					<?php echo JText::_('COM_PAGESANDITEMS_STATUS_PLUGIN_CONTENT'); ?>
				</td>
				<td>
					<?php
					$check = PagesAndItemsHelper::checkPlugin('content');
					if($check == 2 || $check == 1)
					{
						echo '<span style="color: #5F9E30;">'.JText::_('COM_PAGESANDITEMS_PLUGIN_CONTENT_INSTALLED').'</span>';
					}
					else
					{
						echo '<span style="color: red;">'.JText::_('COM_PAGESANDITEMS_PLUGIN_CONTENT_NOT_INSTALLED').'</span>';
					}
					if($check == 2)
					{
						echo '<div style="color: #5F9E30;">'.JText::_('COM_PAGESANDITEMS_PLUGIN_CONTENT_ENABLED').'</div>';
					}
					else
					{
						echo '<div style="color: red;">'.JText::_('COM_PAGESANDITEMS_PLUGIN_CONTENT_NOT_ENABLED').'</div>';
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_STATUS_PLUGIN_SYSTEM'); ?>
				</td>
				<td>
					<?php
					$check = PagesAndItemsHelper::checkPlugin('system');
					if($check == 2 || $check == 1)
					{
						echo '<span style="color: #5F9E30;">'.JText::_('COM_PAGESANDITEMS_PLUGIN_SYSTEM_INSTALLED').'</span>';
					}
					else
					{
						echo '<span style="color: red;">'.JText::_('COM_PAGESANDITEMS_PLUGIN_SYSTEM_NOT_INSTALLED').'</span>';
					}
					if($check == 2)
					{
						echo '<div style="color: #5F9E30;">'.JText::_('COM_PAGESANDITEMS_PLUGIN_SYSTEM_ENABLED').'</div>';
					}
					else
					{
						echo '<div style="color: red;">'.JText::_('COM_PAGESANDITEMS_PLUGIN_SYSTEM_NOT_ENABLED').'</div>';
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_USE_PI_FRONTEND_EDITTING'); ?>
				</td>
				<td>
					<label>
						<input name="use_pi_frontend_editting" type="radio" value="0" class="radio" <?php if($config['use_pi_frontend_editting']=='0'){echo ' checked="checked"';} ?> /><?php echo JText::_('JNO'); ?>
					</label>
					<br />
					<label>
						<input name="use_pi_frontend_editting" type="radio" value="1" class="radio" <?php if($config['use_pi_frontend_editting']=='1' || $config['use_pi_frontend_editting']=='true'){echo ' checked="checked"';} ?> /><?php echo JText::_('COM_PAGESANDITEMS_USE_PI_FRONTEND_EDITTING_TIP'); ?>
					</label>
					<br />
					<label>
						<input name="use_pi_frontend_editting" type="radio" value="2" class="radio" <?php if($config['use_pi_frontend_editting']=='2'){echo ' checked="checked"';} ?> /><?php echo JText::_('COM_PAGESANDITEMS_USE_PI_FRONTEND_EDITTING_TIP_ONLY_PI').'<br />'.JText::_('COM_PAGESANDITEMS_USE_PI_FRONTEND_EDITTING_TIP'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_SYSTEM_PLUGIN_OPTIONS'); ?>
				</td>
				<td>
					<label>
					<input type="checkbox" name="plugin_system_add_button" value="true" <?php if(isset($config['plugin_system_add_button']) && $config['plugin_system_add_button']){echo 'checked="checked"';} ?> /> <?php echo JText::_('COM_PAGESANDITEMS_SYSTEM_PLUGIN_EDIT_BUTTON'); ?> Pages-and-Items.
					</label>
					<br />
					<label>
					<input type="checkbox" name="plugin_system_hidde_button" value="true" <?php if(isset($config['plugin_system_hidde_button']) && $config['plugin_system_hidde_button']){echo 'checked="checked"';} ?> /> <?php echo JText::_('COM_PAGESANDITEMS_SYSTEM_PLUGIN_HIDDE_BUTTON'); ?>
					</label>
				</td>

			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_ENABLED_VIEW_CATEGORY'); ?>
				</td>
				<td>
					<label>
					<input type="checkbox" name="enabled_view_category" value="true" <?php if(isset($config['enabled_view_category']) && $config['enabled_view_category']){echo 'checked="checked"';} ?> /> <?php echo JText::_('COM_PAGESANDITEMS_ENABLE'); ?>
					</label>
					<span style="padding-left: 20px;">
					<?php echo JText::_('COM_PAGESANDITEMS_ENABLED_VIEW_CATEGORY_TIP'); ?>
					</span>
				</td>

			</tr>

			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_SHOW_SLIDER_IN_LISTS'); ?>
				</td>
				<td>
					<select name="showSlider">
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'-1'){echo 'selected="selected"';} ?> value="-1"><?php echo JText::_('JYES'); ?></option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'2'){echo 'selected="selected"';} ?> value="1"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 1</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'2'){echo 'selected="selected"';} ?> value="2"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 2</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'3'){echo 'selected="selected"';} ?> value="3"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 3</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'4'){echo 'selected="selected"';} ?> value="4"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 4</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'5'){echo 'selected="selected"';} ?> value="5"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 5</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'6'){echo 'selected="selected"';} ?> value="6"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 6</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'7'){echo 'selected="selected"';} ?> value="7"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 7</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'8'){echo 'selected="selected"';} ?> value="8"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 8</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'9'){echo 'selected="selected"';} ?> value="9"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 9</option>
						<option <?php if(isset($config['showSlider']) && $config['showSlider'] == (int)'10'){echo 'selected="selected"';} ?> value="10"><?php echo JText::_('COM_PAGESANDITEMS_GREATER'); ?> 10</option>
						<option <?php if(isset($config['showSlider']) && !$config['showSlider']){echo 'selected="selected"';} ?> value="0"><?php echo JText::_('JNEVER'); ?></option>
					</select>
					<span style="padding-left: 20px;">
					<?php echo JText::_('COM_PAGESANDITEMS_SHOW_SLIDER_IN_LISTS_TIP'); ?>
					</span>
				</td>

			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_USE_CHECKEDOUT'); ?>
				</td>
				<td>
					<label>
						<input name="useCheckedOut" type="radio" value="0" class="radio" <?php if(!isset($config['useCheckedOut']) || (isset($config['useCheckedOut']) && ($config['useCheckedOut']=='0' || $config['useCheckedOut']==''))){echo ' checked="checked"';} ?> /><?php echo JText::_('JNO'); ?>
					</label>
					<label>
						<input name="useCheckedOut" type="radio" value="1" class="radio" <?php if(isset($config['useCheckedOut']) && ($config['useCheckedOut']=='1' || $config['useCheckedOut']=='true')){echo ' checked="checked"';} ?> /><?php echo JText::_('JYES'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_VERSION'); ?>
				</td>
				<td>
					<?php echo PagesAndItemsHelper::getPagesAndItemsVersion();//$this->model->version; ?>
					<input type="button" class="button input_button  noicon" value="<?php echo JText::_('COM_PAGESANDITEMS_CHECK_LATEST_VERSION'); ?>" onclick="check_latest_version();" style="margin-left: 20px; padding: 3px 3px 3px 3px" />
					<div id="version_checker_target"></div>
					<span id="version_checker_spinner"><img src="components/com_pagesanditems/images/processing.gif" alt="processing" /></span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_VERSION_CHECKER'); ?>
				</td>
				<td>
					<label><input type="checkbox" class="checkbox" name="version_checker" value="true" <?php if($config['version_checker']){echo 'checked="checked"';} ?> /> <?php echo JText::_('COM_PAGESANDITEMS_ENABLE'); ?></label>
					<span style="padding-left: 20px;">
					<?php echo JText::_('COM_PAGESANDITEMS_VERSION_CHECKER_INFO'); ?>.
					</span>
				</td>
			</tr>
			<?php

			$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
			require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
			ExtensionHtmlHelper::importExtension('config_base', null,true,null,true);
			$dispatcher = &JDispatcher::getInstance();
			$htmlelementTable->html = '';
			$dispatcher->trigger('onGetHtmlelementTable',array(&$htmlelementTable,'config_base'));
			echo $htmlelementTable->html;
			?>
			</table>
			<?php
			echo '<br />';
			echo '<div>';
			$htmlelement->html = '';
			$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'config_base'));
			echo $htmlelement->html;
			echo '</div>';
			?>
			</div>

			<div id="permissions">
			<table  class="adminlist" width="100%">
			<tr>
				<th>
					<?php echo JText::_('COM_PAGESANDITEMS_PERMISSIONS'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<p>
						Pages-and-Items <?php echo JText::_('COM_PAGESANDITEMS_ACL_A'); ?>.
					</p>
					<p>
						<?php echo JText::_('COM_PAGESANDITEMS_ACL_F').' com_menus '.JText::_('COM_PAGESANDITEMS_ACL_B'); ?>:
						<br />
						<a class="modal" href="index.php?option=com_config&amp;view=component&amp;component=com_menus&amp;path=&amp;tmpl=component" rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}">
							<span class="icon-32-options" style="width: 32px; height: 32px; display: block; float: left; ">
							</span>
							<span style="display: block; clear: both;">
								<?php echo JText::_('JOPTIONS'); ?> com_menus
							</span>
						</a>
					</p>
					<p>
						<?php echo JText::_('COM_PAGESANDITEMS_ACL_C').' com_content '.JText::_('COM_PAGESANDITEMS_ACL_B'); ?>:
						<br />
						<a class="modal" href="index.php?option=com_config&view=component&component=com_content&path=&tmpl=component" rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}">
							<span class="icon-32-options" style="width: 32px; height: 32px; display: block; float: left;">
							</span>
							<span style="display: block; clear: both;">
								<?php echo JText::_('JOPTIONS'); ?> com_content
							</span>
						</a>
					</p>
					</td>
				</tr>
			</table>
			</div>

<!--
itemtypes
-->
			<div id="itemtypes">
			<table  class="adminlist" width="100%">
			<tr>
				<th colspan="2">
					<?php echo JText::_('COM_PAGESANDITEMS_ITEMTYPES'); ?>
				</th>
			</tr>
			<tr>
				<td>&nbsp;
				</td>
				<td style="text-align: right;"><a href="http://www.pages-and-items.com/extensions/pages-and-items-plugins" target="_blank"><?php echo JText::_('COM_PAGESANDITEMS_GET_MORE_ITEMTYPES'); ?></a>&nbsp;&nbsp;&nbsp;<input type="button" class="button input_button noicon" value="<?php echo JText::_('COM_PAGESANDITEMS_CREATE_NEW_ITEMTYPE'); ?>" onclick="document.location.href='index.php?option=com_pagesanditems&view=config_custom_itemtype&sub_task=new';" />
				</td>
			</tr>
			<tr>
				<td class="b">
					<?php echo JText::_('COM_PAGESANDITEMS_ALIAS'); ?>
				</td>
				<td class="b">
					<span class="sidestep">
						<?php echo JText::_('COM_PAGESANDITEMS_PUBLISHED'); ?>
					</span>
					<span class="sidestep">
					<?php echo JText::_('COM_PAGESANDITEMS_NAME'); ?>
					</span>
					<span class="sidestep">
					<?php echo JText::_('COM_PAGESANDITEMS_TYPE'); ?>
					</span>
					<?php echo JText::_('COM_PAGESANDITEMS_CONFIG'); ?>
				</td>
			</tr>
					<?php

						//make a new array from installed itemtypes
						//TODO search the db?
						$installed_itemtypes = array();

						$query = 'SELECT element ';
						$query .='FROM #__pi_extensions ';
						$query .='WHERE type='.$this->db->Quote('itemtype').' ';
						//$query .='AND version <>'.$this->db->Quote('integrated');
						$query .='AND element <>'.$this->db->Quote('custom');
						$this->db->setQuery( $query );
						$itemtypeRows = $this->db->loadResultArray();

						if($itemtypeRows)
						{
							foreach($itemtypeRows as $itemtype)
							{
								array_push($installed_itemtypes, $itemtype);
							}
						}
						//add 'text' and 'html' and 'other_item' to array as those are itemtypes which are embedded in Pages-and-Items
						/*
						array_push($installed_itemtypes, 'content');
						array_push($installed_itemtypes, 'text');
						array_push($installed_itemtypes, 'html');
						array_push($installed_itemtypes, 'other_item');
						*/
						$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
						require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
						$itemtype = ExtensionItemtypeHelper::importExtension(null, null,true,null,true);

						//make array with extra column for alias and custom-or-not
						$all_itemtypes = array();
						foreach ($installed_itemtypes as $itemtype)
						{
							//$itemtype_array = array($itemtype, $this->controller->translate_item_type($itemtype), 0);
							$itemtype_array = array($itemtype, PagesAndItemsHelper::translate_item_type($itemtype), 0);
							array_push($all_itemtypes, $itemtype_array);
						}
						//get customitemtypes
						$this->db->setQuery("SELECT id, name FROM #__pi_customitemtypes"  );
						$custom_itemypes = $this->db->loadObjectList();
						foreach($custom_itemypes as $custom_itemype)
						{
							//echo $custom_itemype->name.'<br>';
							$itemtype_array = array($custom_itemype->name, $custom_itemype->name, $custom_itemype->id);
							array_push($all_itemtypes, $itemtype_array);
						}

						//order itemtype-array on language-specific alias
						foreach ($all_itemtypes as $key => $row)
						{
							$order[$key]  = strtolower($row[1]);
						}
						array_multisort($order, SORT_ASC, $all_itemtypes);
/*
						$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
						require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
						$itemtype = ExtensionItemtypeHelper::importExtension(null, null,true,null,true);
*/
						$dispatcher = &JDispatcher::getInstance();
						//display itemtype list
						foreach($all_itemtypes as $itemtype)
						{
							echo '<tr>';
							echo '<td>'.$itemtype[1].'</td>';
							$checked2 = '';
							$custom_itemtype_name = 'custom_'.$itemtype[2];
							$itemtypes = PagesAndItemsHelper::getItemtypes();
							if(in_array($itemtype[0], $itemtypes) || in_array($custom_itemtype_name, $itemtypes) || $itemtype[0]=='content' || $itemtype[0]=='text')
							{
								$checked2 = ' checked="checked"';
							}
							/*
							$config = '-';
							if($itemtype[2])
							{
								$config = '<a href="index.php?option=com_pagesanditems&view=config_custom_itemtype&type_id='.$itemtype[2].'">'.JText::_('COM_PAGESANDITEMS_CONFIG').'</a>';
								//}else if(file_exists(dirname(__FILE__).'/../../com_pi_itemtype_'.$itemtype[0].'/admin/config.php')){
							}
							else if(file_exists($this->controller->pathPluginsItemtypes.'/'.$itemtype[0].'/admin/config.php'))
							{
								$config = '<a href="index.php?option=com_pagesanditems&view=config_itemtype&item_type='.$itemtype[0].'">'.JText::_('COM_PAGESANDITEMS_CONFIG').'</a>';
							}
							elseif($itemtype[1])
							{
								$config = '<a href="index.php?option=com_pagesanditems&view=config_itemtype&item_type='.$itemtype[0].'">'.JText::_('COM_PAGESANDITEMS_CONFIG').'</a>';
							}
							*/
							echo '<td><span class="sidestep">';
							echo '<input type="checkbox" name="itemtypes[';
							if($itemtype[2])
							{
								echo 'custom_'.$itemtype[2];
							}else{
								echo $itemtype[0];
							}
							echo ']" value="1"'.$checked2.' ';
							if($itemtype[0]=='content' || $itemtype[0]=='text')
							{
								echo 'disabled="disabled" ';
							}
							echo '/>';

							if($itemtype[0]=='content' || $itemtype[0]=='text')
							{
								echo '<input type="checkbox" name="itemtypes[';
								echo $itemtype[0];
								echo ']" value="1"'.$checked2.' ';
								echo 'style="display:none" ';
								echo '/>';
							}

							echo '</span><span class="sidestep">';
							if($itemtype[2])
							{
								echo 'custom_'.$itemtype[2];
							}else{
								echo $itemtype[0];
							}
							echo '</span>';
							echo '<span class="sidestep">';
							if($itemtype[2]){
								echo JText::_('COM_PAGESANDITEMS_CUSTOMITEMTYPE');
							}
							else if($itemtype[0]=='content' || $itemtype[0]=='html' || $itemtype[0]=='text' || $itemtype[0]=='other_item')
							{
								echo JText::_('COM_PAGESANDITEMS_COREITEMTYPE');
							}
							else
							{
								echo JText::_('COM_PAGESANDITEMS_ITEMTYPE_PLUGIN');
							}
							echo '</span>';
							$itemtypeHtmlIsConfig = new JObject();
							$itemtypeHtmlIsConfig->text = '-';

							if($itemtype[2])
							{
								//onItemtypeDisplay_config_form
								$results = $dispatcher->trigger('onItemtypeIs_config_form', array(&$itemtypeHtmlIsConfig,'custom',$itemtype[2]));
							}
							else
							{
								$results = $dispatcher->trigger('onItemtypeIs_config_form', array(&$itemtypeHtmlIsConfig,$itemtype[0]));
								if($itemtypeHtmlIsConfig->text == '-')
								{
									//$dispatcher->trigger('onItemtypeDisplay_config_form_tip', array(&$itemtypeHtmlIsConfig,$itemtype[0]));
								}
								//echo <a onclick="" $itemtype[0]
								if($itemtypeHtmlIsConfig->text != '-')
								$itemtypeHtmlIsConfig->text = '<a href="#" onclick="javascript: config_itemtype(\''.$itemtype[0].'\');">'.JText::_('COM_PAGESANDITEMS_CONFIG').'</a>';
								//http://127.0.0.1:4001/administrator/index.php?option=com_pagesanditems&task=extension.doExecute&extensionName=extensions&extensionType=manager&layout=edit&extensionTask=display&view=piextension&client=both&sub_task=edit&cid[]=1&extension_id=1
							}
							echo $itemtypeHtmlIsConfig->text;

							echo '</td>';
							echo '</tr>';
						}

					?>
			</table>
			</div>
<!--
*********
* items *
*********
-->
			<div id="items">
			<table  class="adminlist" width="100%">
			<tr>
				<th colspan="3">
					<?php echo JText::_('COM_PAGESANDITEMS_ITEMS'); ?>
				</th>
			</tr>
			<tr>
				<td width="250">
					<?php echo JText::_('COM_PAGESANDITEMS_PLUGIN_SYNTAX_CHEATCHEAT'); ?>
				</td>
				<td width="150">
					<textarea name="plugin_syntax_cheatcheat" cols="60" rows="3" style="width: 400px;"><?php echo $config['plugin_syntax_cheatcheat']; ?></textarea>
				</td>
				<td><?php echo JText::_('COM_PAGESANDITEMS_PLUGIN_SYNTAX_CHEATCHEAT_INFO'); ?><br /><?php echo JText::_('COM_PAGESANDITEMS_EXAMPLE'); ?><br />
				<img src="components/com_pagesanditems/images/syntax.gif" alt="example syntax cheatsheet" style="border: 1px solid #000;" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_MAKE_ARTICLE_ALIAS_UNIQUE'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="make_article_alias_unique" value="true" <?php if($config['make_article_alias_unique']){echo $checked;} ?> /> <?php echo JText::_('COM_PAGESANDITEMS_MAKE_ARTICLE_ALIAS_UNIQUE_INFO').'.'; ?>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;

				</td>
			</tr>
			<tr>
				<th>
					<?php echo JText::_('COM_PAGESANDITEMS_ITEMS_PROPERTIES'); ?>
				</th>
				<th colspan="2">
					<?php echo JText::_('COM_PAGESANDITEMS_DISPLAY_PROPERTY_WHEN_EDITTING_2'); ?>
				</th>
			</tr>
			<tr>
				<td class="lowercase">&nbsp;

				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_hideforsuperadmin" value="true" <?php if($config['item_props_hideforsuperadmin']){echo $checked;} ?> />
					<?php echo JText::_('COM_PAGESANDITEMS_HIDE_FOR_SUPER_ADMIN'); ?>
				</td>
			</tr>
			<tr>
				<td class="b">
					<legend><?php echo JText::_('JDETAILS'); ?></legend>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_details" value="true" <?php if($config['item_props_details']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_TITLE'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_title" value="true" <?php if($config['item_props_title']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('COM_PAGESANDITEMS_ALIAS'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_alias" value="true" <?php if($config['item_props_alias']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JCATEGORY'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_category" value="true" <?php if($config['item_props_category']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JSTATUS'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_status" value="true" <?php if($config['item_props_status']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_access" value="true" <?php if($config['item_props_access']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JFEATURED'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_featured" value="true" <?php if($config['item_props_featured']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_language" value="true" <?php if($config['item_props_language']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGRID_HEADING_ID'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_id" value="true" <?php if($config['item_props_id']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="b">
					<legend><?php echo JText::_('COM_CONTENT_FIELD_ARTICLETEXT_LABEL'); ?></legend>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_articletext" value="true" <?php if($config['item_props_articletext']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="b">
					<legend><?php echo JText::_('COM_CONTENT_FIELDSET_PUBLISHING'); ?></legend>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_publishingoptions" value="true" <?php if($config['item_props_publishingoptions']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGRID_HEADING_CREATED_BY'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_createdby" value="true" <?php if($config['item_props_createdby']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('COM_CONTENT_FIELD_CREATED_BY_ALIAS_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_createdbyalias" value="true" <?php if($config['item_props_createdbyalias']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('COM_CONTENT_FIELD_CREATED_BY_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_createddate" value="true" <?php if($config['item_props_createddate']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_FIELD_PUBLISH_UP_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_start" value="true" <?php if($config['item_props_start']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_FIELD_PUBLISH_DOWN_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_finish" value="true" <?php if($config['item_props_finish']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_FIELD_MODIFIED_BY_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_modified_by" value="true" <?php if($config['item_props_modified_by']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_FIELD_MODIFIED_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_modified" value="true" <?php if($config['item_props_modified']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JVERSION'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_revision" value="true" <?php if($config['item_props_revision']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_HITS'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_hits" value="true" <?php if($config['item_props_hits']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="b">
					<legend><?php echo JText::_('COM_CONTENT_ATTRIBS_FIELDSET_LABEL'); ?></legend>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_articleoptions" value="true" <?php if($config['item_props_articleoptions']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_TITLE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_title" value="true" <?php if($config['item_props_show_title']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_LINKED_TITLES_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_link_titles" value="true" <?php if($config['item_props_link_titles']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_INTRO_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_intro" value="true" <?php if($config['item_props_show_intro']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_CATEGORY_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_category" value="true" <?php if($config['item_props_show_category']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_LINK_CATEGORY_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_link_category" value="true" <?php if($config['item_props_link_category']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_PARENT_CATEGORY_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_parent_category" value="true" <?php if($config['item_props_show_parent_category']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_LINK_PARENT_CATEGORY_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_link_parent_category" value="true" <?php if($config['item_props_link_parent_category']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_AUTHOR_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_author" value="true" <?php if($config['item_props_show_author']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_LINK_AUTHOR_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_link_author" value="true" <?php if($config['item_props_link_author']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_CREATE_DATE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_create_date" value="true" <?php if($config['item_props_show_create_date']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_MODIFY_DATE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_modify_date" value="true" <?php if($config['item_props_show_modify_date']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_PUBLISH_DATE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_publish_date" value="true" <?php if($config['item_props_show_publish_date']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_NAVIGATION_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_item_navigation" value="true" <?php if($config['item_props_show_item_navigation']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_ICONS_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_icons" value="true" <?php if($config['item_props_show_icons']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_PRINT_ICON_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_print_icon" value="true" <?php if($config['item_props_show_print_icon']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_EMAIL_ICON_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_email_icon" value="true" <?php if($config['item_props_show_email_icon']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_VOTE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_vote" value="true" <?php if($config['item_props_show_vote']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_HITS_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_hits" value="true" <?php if($config['item_props_show_hits']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_UNAUTH_LINKS_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_show_noauth" value="true" <?php if($config['item_props_show_noauth']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_READMORE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_alternative_readmore" value="true" <?php if($config['item_props_alternative_readmore']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JFIELD_ALT_LAYOUT_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_article_layout" value="true" <?php if($config['item_props_article_layout']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="b">
					<legend><?php echo JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'); ?></legend>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_metadataoptions" value="true" <?php if($config['item_props_metadataoptions']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JFIELD_META_DESCRIPTION_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_desc" value="true" <?php if($config['item_props_desc']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JFIELD_META_KEYWORDS_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_keywords" value="true" <?php if($config['item_props_keywords']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JFIELD_METADATA_ROBOTS_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_robots" value="true" <?php if($config['item_props_robots']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JAUTHOR'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_author" value="true" <?php if($config['item_props_author']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JFIELD_META_RIGHTS_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_rights" value="true" <?php if($config['item_props_rights']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JFIELD_XREFERENCE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_xreference" value="true" <?php if($config['item_props_xreference']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="b">
					<legend><?php echo JText::_('COM_PAGESANDITEMS_ITEM_OPTIONS'); ?></legend>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_pioptions" value="true" <?php if($config['item_props_pioptions']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('COM_PAGESANDITEMS_DISPLAY_OTHER_ITEMS_INSTANCES'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_instance" value="true" <?php if($config['item_props_instance']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="pi_padleft">
					<?php echo JText::_('JGLOBAL_SHOW_TITLE_LABEL'); ?>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_pishowtitle" value="true" <?php if($config['item_props_pishowtitle']){echo $checked;} ?> />
				</td>
			</tr>
			<tr>
				<td class="b">
					<legend><?php echo JText::_('COM_CONTENT_FIELDSET_RULES'); ?></legend>
				</td>
				<td colspan="2">
					<input type="checkbox" name="item_props_permissions" value="true" <?php if($config['item_props_permissions']){echo $checked;} ?> />
				</td>
			</tr>
			</table>
			</div>
			<div id="new_item">
			<table  class="adminlist" width="100%">
			<tr>
				<th colspan="2">
					<?php echo JText::_('COM_PAGESANDITEMS_NEW_ITEM'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_ITEM_SAVE_REDIRECT'); ?>
				</td>
				<td colspan="2">
					<label>
						<input name="item_save_redirect" type="radio" value="item" class="radio" <?php if($config['item_save_redirect']=='item'){echo ' checked="checked"';} ?> /><?php echo JText::_('COM_PAGESANDITEMS_ITEM_SAVE_REDIRECT_ITEM'); ?>
					</label>
					<br />
					<label>
						<input name="item_save_redirect" type="radio" value="category_blog" class="radio" <?php if($config['item_save_redirect']=='category_blog'){echo ' checked="checked"';} ?> /><?php echo JText::_('COM_PAGESANDITEMS_ITEM_SAVE_REDIRECT_CATEGORY_BLOG'); ?>
					</label>
					<br />
					<label>
						<input name="item_save_redirect" type="radio" value="url" class="radio" <?php if($config['item_save_redirect']=='url'){echo ' checked="checked"';} ?> /><?php echo JText::_('COM_PAGESANDITEMS_ITEM_SAVE_REDIRECT_URL'); ?>.
						<input type="text" name="item_save_redirect_url" style="width: 300px;" value="<?php echo $config['item_save_redirect_url']; ?>" /><?php echo ' '.JText::_('COM_PAGESANDITEMS_EXAMPLE'); ?>:  'index.php?option=com_content&view=frontpage'
					</label>
					<br />
					<label>
						<input name="item_save_redirect" type="radio" value="current" class="radio" <?php if($config['item_save_redirect']=='current'){echo ' checked="checked"';} ?> /><?php echo JText::_('COM_PAGESANDITEMS_ITEM_SAVE_REDIRECT_CURRENT'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<span class="lowercase"><?php echo JText::_('COM_PAGESANDITEMS_SHOW_TITLE'); ?></span>
				</td>
				<td>
					<input type="checkbox" name="item_new_show_title" value="true" <?php if($config['item_new_show_title']){echo 'checked="checked"';} ?> />
					<?php echo JText::_('COM_PAGESANDITEMS_SHOW_TITLE_INFO'); ?>.
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_NEW_ITEM_LINK_FRONTEND'); ?>
				</td>
				<td>
					<div>
						<?php echo JText::_('COM_PAGESANDITEMS_NEW_ITEM_LINK_FRONTEND_A').' \''.JText::_('COM_PAGESANDITEMS_ITEM_SUBMISSION_LAYOUT').'\'.'; ?>
					</div>
					<div><br />
						<?php echo JText::_('COM_PAGESANDITEMS_NEW_ITEM_LINK_FRONTEND_B').' \''.JText::_('COM_PAGESANDITEMS_SELECT_ITEMTYPE_FOR_NEW_ITEM').'\'.'; ?>
					</div>
					<div>
						<input type="checkbox" name="item_type_select_frontend" value="true" <?php if($config['item_type_select_frontend']){echo 'checked="checked"';} ?> /> <?php echo JText::_('COM_PAGESANDITEMS_ENABLED_ITEMTYPE_SELECT_FRONTEND'); ?>
					</div>
					<div>
					<br />
						<?php echo JText::_('COM_PAGESANDITEMS_NEW_ITEM_LINK_FRONTEND4'); ?>
					</div>
					index.php?option=com_pagesanditems&amp;view=item&amp;sub_task=new&amp;pageId=888&amp;item_type=html
				</td>
			</tr>
			</table>
			</div>

<!--
*********
* menus *
*********
-->
			<div id="menus">
			<table class="adminlist" width="100%">
			<tr>
				<th>
					<?php echo JText::_('COM_PAGESANDITEMS_MENUS'); ?>
				</th>
			</tr>
			</table>
			<table class="adminlist" width="100%">
			<tbody>
			<tr>
				<td style="width: 100px;border:none;">
					<img src="components/com_pagesanditems/images/moremenus2.gif" style="border: 1px solid #ccc;" />
				</td>
				<?php
				/*
				here the old
				?>
				<td>
				
					<table class="adminlist" width="100%">
						<tr>
							<td colspan="2">
								<?php echo JText::_('COM_PAGESANDITEMS_MENUS_TIP_B'); ?>. 
							</td>
						</tr>
						<tr>
							<td>&nbsp;

							</td>
							<td>
								<span class="sidestep2 b"><?php echo JText::_('COM_PAGESANDITEMS_NAME'); ?></span><span class="b"><?php echo JText::_('COM_PAGESANDITEMS_ORDER'); ?></span>
							</td>
						</tr>
				<?php

						//loop through menutypes from config
						$counter = 1;
						$menus_from_config = $config['menus'];
						$temp_menus = explode(',',$config['menus']);
						if($temp_menus[0]==''){
							$temp_menus = array();
						}

						//get all menutypes
						$menutypes_db = array();
						//joomla 1.5
						$this->db->setQuery("SELECT title, menutype FROM #__menu_types ORDER BY title ASC"  );
						//$this->db->setQuery("SELECT title, menutype FROM #__menu_types ORDER BY id ASC"  );
						$rows = $this->db-> loadObjectList();
						foreach($rows as $row)
						{
							$new_menutype = array(strtolower($row->menutype),$row->title);
							array_push($menutypes_db, $new_menutype);
						}

						$menus_on_page = array();

						for($m = 0; $m < count($temp_menus); $m++){
							$menu_temp = explode(';',$temp_menus[$m]);
						echo '<tr>';
							echo '<td>&nbsp;</td>';
							echo '<td>';
							echo '<span class="sidestep2">';
							echo '<label>';
							echo '<input type="checkbox" class="checkbox" name="menus[m'.$m.'][menutype]" value="'.$menu_temp[0].'"';
							echo ' checked="checked"';
							echo ' />';
							echo $menu_temp[1];

							echo '</label>';
							echo '</span>';
							echo '<input type="hidden" name="menus[m'.$m.'][title]" value="'.$menu_temp[1].'" />';
							echo '<input type="text" name="menus[m'.$m.'][order]" size="2" value="'.$counter.'"';
							echo ' />';
							echo '</td>';
						echo '</tr>';
							array_push($menus_on_page, $menu_temp[0]);
							$counter = $counter + 1;
						}


						//loop through menutypes from database
						for($m = 0; $m < count($menutypes_db); $m++){
							if(!in_array($menutypes_db[$m][0], $menus_on_page)){
						echo '<tr>';
							echo '<td>&nbsp;</td>';
							echo '<td>';
								echo '<span class="sidestep2">';
								echo '<label>';
								echo '<input type="checkbox" class="checkbox" name="menus[m'.($counter-1).'][menutype]" value="'.$menutypes_db[$m][0].'"';
								echo ' />';
								echo $menutypes_db[$m][1];
								echo '</label>';
								echo '</span>';
								echo '<input type="hidden" name="menus[m'.($counter-1).'][title]" value="'.$menutypes_db[$m][1].'" />';
								echo '<input type="text" name="menus[m'.($counter-1).'][order]" size="2" value="'.$counter.'"';
								echo ' />';
							echo '</td>';
						echo '</tr>';
						$counter = $counter + 1;
						}
					}
					
				?>
					</table>
				</td>
				here the old end
				<?php
				here the new
				*/
				?>
				<td style="width: 500px;border:none;">
				
				<?php
					require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'lists'.DS.'menuslist.php');
					$menusList = new MenusList();
					$output = $menusList->renderItems();
					echo $output;
				?>
				</td>
				<td style="border:none;">
				</td>
				
				<?php
				//here the new end
				?>
			</tr>
			</tbody>
			</table>
			</div>

<!--
*********
* Pages *
*********
COMMENT
if we use pagetypes as extension
move this to the params from pagetypes?
-->
			<div id="pages">
			<table class="adminlist" width="100%">
			<tr>
				<th colspan="3">
					<?php echo JText::_('COM_PAGESANDITEMS_PAGES'); ?>
				</th>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_MAKE_PAGE_ALIAS_UNIQUE'); ?>
				</td>
				<td colspan="2">
					<?php
					$make_page_alias_unique = '0';
					if($config['make_page_alias_unique']){
						$make_page_alias_unique = $config['make_page_alias_unique'];
					}
					?>
					<input type="checkbox" name="make_page_alias_unique" value="true" <?php if($make_page_alias_unique){echo 'checked="checked"';} ?> /> <?php echo JText::_('COM_PAGESANDITEMS_MAKE_PAGE_ALIAS_UNIQUE_INFO').'.'; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_MAKE_MENU_ALIAS_UNIQUE_TOOL'); ?>
				</td>
				<td colspan="2">
					<input type="button" class="button input_button noicon" value="<?php echo JText::_('COM_PAGESANDITEMS_MAKE_MENU_ALIAS_BUTTON').'.'; ?>" onclick="if(confirm('<?php echo addslashes(JText::_('COM_PAGESANDITEMS_ARE_YOU_SURE_MENU_ALIASSES_UNIQUE')); ?>')){document.location.href = 'index.php?option=com_pagesanditems&view=render_menu_alias_unique';}" /> <?php echo JText::_('COM_PAGESANDITEMS_MAKE_MENU_ALIAS_UNIQUE_TOOL_INFO').'.'; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_TRUNCATE_ITEM_TITLES'); ?>
				</td>
				<td colspan="2">
					<?php
					$truncate_item_title = '0';
					if($config['truncate_item_title']){
						$truncate_item_title = $config['truncate_item_title'];
					}
					?>
					<input type="text" value="<?php echo $truncate_item_title; ?>" name="truncate_item_title" /><?php echo ' '.JText::_('COM_PAGESANDITEMS_TRUNCATE_ITEM_TITLES2'); ?>.
				</td>

			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_PAGE_TRASH'); ?>
				</td>
				<td colspan="2">
					<?php echo JText::_('COM_PAGESANDITEMS_PAGE_TRASH_INFO').', '.JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH3'); ?>:<br />
					<label><input type="checkbox" name="bogus" value="1" checked="checked" disabled="disabled" /><?php echo JText::_('COM_PAGESANDITEMS_TRASH_MENU_CAT_ITEMS'); ?>.</label><br />
					<label><input type="checkbox" name="page_trash_cat" value="1" <?php if($config['page_trash_cat']){ echo $checked; }?>  /><?php echo JText::_('COM_PAGESANDITEMS_IF_PAGE').', '.JText::_('COM_PAGESANDITEMS_TRASH_CAT'); ?>.</label><br />
					<label><input type="checkbox" name="page_trash_items" value="1" <?php if($config['page_trash_items']){ echo $checked; }?>  /><?php echo JText::_('COM_PAGESANDITEMS_IF_PAGE').', '.JText::_('COM_PAGESANDITEMS_TRASH_ITEMS'); ?>.</label>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_PAGESANDITEMS_PAGE_DELETE'); ?>
				</td>
				<td colspan="2">
					<?php echo JText::_('COM_PAGESANDITEMS_PAGE_DELETE_INFO').', '.JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE1'); ?>:<br />
					<label><input type="checkbox" name="bogus2" value="1" checked="checked" disabled="disabled" /><?php echo JText::_('COM_PAGESANDITEMS_TRASH_MENU_CAT_ITEMS'); ?>.</label><br />
					<label><input type="checkbox" name="page_delete_cat" value="1" <?php if($config['page_delete_cat']){ echo $checked; }?>  /><?php echo JText::_('COM_PAGESANDITEMS_IF_PAGE').', '.JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5'); ?>.</label><br />
					<label><input type="checkbox" name="page_delete_items" value="1" <?php if($config['page_delete_items']){ echo $checked; }?>  /><?php echo JText::_('COM_PAGESANDITEMS_IF_PAGE').', '.JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6'); ?>.</label>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;
				</td>
			</tr>
			<tr>
				<th>
					<?php echo JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES'); ?>
				</th>
				<th colspan="2">
				 	<?php echo JText::_('COM_PAGESANDITEMS_DISPLAY_PAGE_PROPERTY_WHEN_EDITTING_2'); ?>
				</th>
			</tr>
			<tr>
				<td class="lowercase">&nbsp;

				</td>
				<td colspan="2">
					<input type="checkbox" name="page_props_hideforsuperadmin" value="true" <?php if($config['page_props_hideforsuperadmin']){echo 'checked="checked"';} ?> />
					<?php echo JText::_('COM_PAGESANDITEMS_HIDE_FOR_SUPER_ADMIN'); ?>
				</td>
			</tr>
			<tr>
				<td class="b" colspan="3">
					<?php echo JText::_('COM_MENUS_MENU_MENUTYPE_LABEL').': '.JText::_('JALL'); ?>
				</td>
			</tr>
			<tr>
				<td class="pi_padleft_panel">
					<legend><?php echo JText::_('JDETAILS'); ?></legend>
				</td>
				<td colspan="2">&nbsp;

				</td>
			</tr>

			<?php
			$page_fields = PagesAndItemsHelper::get_all_page_fields();
			foreach($page_fields as $field){
				$field_right = $field[0];
				$field_label = $field[2];
				$field_type = $field[4];
				?>
				<tr>
					<td<?php if($field_type=='field'){echo ' class="pi_padleft"';}elseif($field_type=='menutype'){echo ' class="b"';}else{echo ' class="pi_padleft_panel"';}?>>
						<?php
						if($field_type=='panel'){
							echo '<legend>';
						}
						if($field_type=='menutype'){
							echo JText::_('COM_MENUS_MENU_MENUTYPE_LABEL').': ';
						}
						echo JText::_($field_label);
						if($field_type=='panel'){
							echo '</legend>';
						}
						?>
					</td>
					<td colspan="2">
						<?php
						if($field_type!='menutype'){
						?>
						<input type="checkbox" name="<?php echo $field_right; ?>" value="true" <?php if($config[$field_right]){echo $checked;} ?> />
						<?php
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
			</div>
			<div id="credits">
			<table class="adminlist" width="100%">
				<tr>
					<th colspan="2">
						<?php echo JText::_('COM_PAGESANDITEMS_CREDITS'); ?>
					</th>
				</tr>
				<tr>
					<td>
						<a href="http://gecko.struller.de" target="_blank">Michael Struller</a>
					</td>
					<td>
						rebuild the fieldtype and itemtype framework<br />
						created the pagetype framework<br />
						created the plugin installer and managers<br />
						Joomfish compatibility<br />
						fixed bugs<br />
						update German language file
					</td>
				</tr>
				<tr>
					<td>
						Lars Vonnahme
					</td>
					<td>
						added show/hide page properties<br />
						update German language file
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.cullenlogan.com" target="_blank">Cullen Logan</a>
					</td>
					<td>
						fix bugs<br />
						the PHP itemtype-code
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.easyteneweb.com/" target="_blank">Per Kersoe</a>
					</td>
					<td>
						Danish translation
					</td>
				</tr>
				<tr>
					<td>
						Matthias Dwidjosiswojo
					</td>
					<td>
						German translation
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.re-media.ro" target="_blank">Razvan Rosca</a>
					</td>
					<td>
						Romanian translation
					</td>
				</tr>
				<tr>
					<td>
						Athena Maliora
					</td>
					<td>
						Greek translation
					</td>
				</tr>
				<tr>
					<td>
						Rogelio Herrer&iacute;as Hern&aacute;ndez
					</td>
					<td>
						Spanish translation
					</td>
				</tr>
				<tr>
					<td>
						Kiril Pamporov (nf1)
					</td>
					<td>
						Bulgarian translation
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.dazzleweb.com.br" target="_blank">Thiago Estrela</a>
					</td>
					<td>
						Brazilian-portuguese translation
					</td>
				</tr>
				<tr>
					<td>
						Michael Lasevich
					</td>
					<td>
						code improvement render engine
					</td>
				</tr>
				<tr>
					<td>
						Huang Wei
					</td>
					<td>
						Chinese translation
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.picoseuropa.info" target="_blank">Juan C. Iglesias</a>
					</td>
					<td>
						update Spanish translation
					</td>
				</tr>
				<tr>
					<td>
						<a href="http://www.system34.com" target="_blank">Lidija Ajranovic</a>
					</td>
					<td>
						Serbian translation
					</td>
				</tr>


				<tr>
					<td>
						Carsten Engel
					</td>
					<td>
						main developer
					</td>
				</tr>
			</table>
			<tr>
			<br />
			<p>
				Some Icons are Copyright &copy; Yusuke Kamiyamane. All rights reserved. Licensed under a Creative Commons Attribution 3.0 license.
			</p>
		</div>
		<div id="joomfish">
			<table class="adminlist" width="100%">
				<tr>
					<th colspan="2">
						Joom!Fish
					</th>
				</tr>
				<tr>
					<td colspan="2">
						<p>
							<?php echo JText::_('COM_PAGESANDITEMS_JOOMFISH_INFO1'); ?>
							<br />
							<a href="http://extensions.joomla.org/extensions/languages/multi-lingual-content/460" target="_blank"><?php echo JText::_('COM_PAGESANDITEMS_JOOMFISH_JED'); ?></a>
							<br />
							<a href="http://www.joomfish.net/" target="_blank">www.joomfish.net</a>
						</p>
						<p>
							<?php echo JText::_('COM_PAGESANDITEMS_JOOMFISH_INFO_A'); ?>.
						</p>
						<p>
							<?php echo JText::_('COM_PAGESANDITEMS_JOOMFISH_INFO3'); ?>
							<br />
							<a href="http://gecko.struller.de/" target="_blank">gecko.struller.de</a>
						</p>

					</td>
				</tr>
			</table>
		</div>
	</form>
</div>
	</div>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>