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

JHTML::_('behavior.tooltip');


//if($this->model->user_type!='Super Administrator')
if(!PagesAndItemsHelper::getIsSuperAdmin())
{
	echo "<script> alert('you need to be logged in as a super administrator to edit the Pages-and-Items config.'); window.history.go(-1); </script>";
	exit();
}
$field_id = JRequest::getVar('field_id', '' );
if(!$field_id){
	//start new

	$plugin = JRequest::getVar('plugin', '' );
	$type_id = JRequest::getVar('type_id', '' );
	$name = '';
	$custom_field = '';
	$field_params['description'] = '';
	$field_params['alert_message'] = '';
	$field_params['default_value'] = '';
	$field_params['validation'] = 0;

	//end new

}else{
	//start edit

	$this->db->setQuery("SELECT * FROM #__pi_custom_fields WHERE id='$field_id' LIMIT 1");
	$custom_fields = $this->db->loadObjectList();
	$custom_field = $custom_fields[0];
	$name = $custom_field->name;
	$type_id = $custom_field->type_id;
	$plugin = $custom_field->plugin;
	$temp = $custom_field->params;
	//$temp = stripslashes($temp);

	//echo $temp;
	//explode params
	$temp = explode( '[;-)# ]', $temp);
	for($n = 0; $n < count($temp); $n++){
		//list($var,$value) = split("-=-",$temp[$n]);
		//$field_params[$var] = trim($value);
		$temp2 = explode('-=-',$temp[$n]);
		$var = $temp2[0];
		$value = '';
		if(count($temp2)==2){
			$value = $temp2[1];
		}
		$field_params[$var] = trim($value);
	}


	//end edit
}
//ADD MS 08.09.2009
/*
if(defined('_JEXEC'))
{
	$field_type = $plugin;
	if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
	{
		$pluginParams = new JParameter( $this->model->get_plugin_params_base($plugin) );
	}
	else
	{
		//TOOD must be Test
		//JRegistery accept array, object and JSON string
		$pluginParams = new JRegistry($this->model->get_plugin_params_base($plugin));
	}

	if($pluginParams->get('version'))
	{
		$field_type .= '<br />'.JText::_('COM_PAGESANDITEMS_VERSION').': '.$pluginParams->get('version');
	}
	if($pluginParams->get('required_pi_version'))
	{
		if($this->model->version >= $pluginParams->get('required_pi_version'))
		{
			$field_type .= '<br />'.JText::_('COM_PAGESANDITEMS_VERSION_OK');
		}
		else
		{
			$field_type .= '<br />'.JText::_('COM_PAGESANDITEMS_VERSION_NOT_OK_1').$pluginParams->get('required_pi_version').' '.JText::_('COM_PAGESANDITEMS_VERSION_NOT_OK_2').$this->model->version ;
		}
	}
}
*/
$field_type = $plugin;


/*
i have problems in pi_fish on to load the paramsfrom other fieldtypes
so we load here not only pi_fish
*/
//$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $plugin,true,null,true);


/*
TODO check if go withhout:
//get fields plugin
$this->db->setQuery( "SELECT DISTINCT plugin "
. "\nFROM #__pi_custom_fields "
. "\nWHERE type_id='$type_id' "
. "\nORDER BY ordering ASC"
);
$fieldPlugins = $this->db->loadResultArray();
if(!$field_id && !in_array($plugin, $fieldPlugins))
{
	$fieldPlugins[] = $plugin;
}
$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $fieldPlugins,true,null,true);
*/

//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
//$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $plugin,true,null,true);

$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
$fieldtype = ExtensionFieldtypeHelper::importExtension(null, $plugin,true,null,true);

$dispatcher = &JDispatcher::getInstance();

/*
old
$required_pi_version = false;
$params = null;
$dispatcher->trigger('onGetParams',array(&$params,$plugin));
if($params->get('required_pi_version'))
{
	if(PagesAndItemsHelper::getPagesAndItemsVersion() >= $params->get('required_pi_version'))
	{
		$field_type .= '<br />'.JText::_('COM_PAGESANDITEMS_VERSION_OK');
	}
	else
	{
		$field_type .= '<br />'.JText::_('COM_PAGESANDITEMS_VERSION_NOT_OK_1').$pluginParams->get('required_pi_version').' '.JText::_('COM_PAGESANDITEMS_VERSION_NOT_OK_2').PagesAndItemsHelper::getPagesAndItemsVersion() ;
	}
}
*/

/*
new
ms: but i have add in install the check so we can not install if the extension required_version is higher then the pi version
$required_pi_version = $dispatcher->trigger('getRequired_version',array($plugin));
$required_pi_version = isset($required_pi_version[0]) ? $required_pi_version[0] : 0;

$required_pi_version = 0;
$dispatcher->trigger('onGetRequired_version',array(&$required_pi_version,$plugin));

if($required_pi_version )
{
	if(PagesAndItemsHelper::getPagesAndItemsVersion() >= $required_pi_version || $required_pi_version == -1)
	{
		$field_type .= '<br />'.JText::_('COM_PAGESANDITEMS_VERSION_OK');
	}
	else
	{
		$field_type .= '<br />'.JText::_('COM_PAGESANDITEMS_VERSION_NOT_OK_1').$required_pi_version.' '.JText::_('COM_PAGESANDITEMS_VERSION_NOT_OK_2').PagesAndItemsHelper::getPagesAndItemsVersion() ;
	}
}
*/

//$allFieldtypes = ExtensionHelper::getExtension('fieldtype',null, null);



//ADD MS END
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

/*<script src="../includes/js/overlib_mini.js" language="JavaScript" type="text/javascript"></script>*/
?>


<?php
/*
<link rel="stylesheet" type="text/css" href="components/com_pagesanditems/css/pagesanditems2.css" />
*/
?>
<script language="JavaScript" type="text/javascript">
function do_pre_submit_checks(){
	if (document.adminForm.name.value == '')
	{
		alert('<?php echo addslashes(JText::_('COM_PAGESANDITEMS_NO_NAME')); ?>');
		return;
	} else {
		if(check_name_field())
		{
			items_of_this_type = document.getElementById('items_of_this_type').value;
			if(items_of_this_type!=0)
			{
				if(confirm("<?php echo addslashes(JText::_('COM_PAGESANDITEMS_WANT_TO_UPDATE_ITEMS')); ?>"))
				{
					document.getElementById('update_items').value = '1';
				}
			}
			<?php
			if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
			{
			?>
				submitform('customitemtypefield.config_custom_itemtype_field_save');
			<?php
			}
			else
			{
			?>
				Joomla.submitform('customitemtypefield.config_custom_itemtype_field_save',document.getElementById('adminForm'));
			<?php
			}
			?>
			return;
			//submitform('customitemtypefield.config_custom_itemtype_field_save');
		}
	}
}

//function submitbutton(pressbutton)
<?php
if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
{
?>
function submitbutton(pressbutton)
<?php
}
else
{
?>
Joomla.submitbutton = function(pressbutton)
<?php
}
?>
{
	if (pressbutton == 'customitemtypefield.config_custom_itemtype_field_save')
	{
		do_pre_submit_checks();
	}
	if (pressbutton == 'customitemtypefield.config_custom_itemtype_field_apply')
	{
		document.getElementById('sub_task').value = 'apply';
		do_pre_submit_checks();
	}
	if (pressbutton == 'customitemtypefield.cancel')
	{
		document.getElementById('view').value = 'config_custom_itemtype';
		//document.getElementById('tab').value = 'itemtypes';
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
		document.location.href = 'index.php?option=com_pagesanditems&view=config_custom_itemtype&type_id=<?php echo $type_id; ?>';
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

</script>
<?php
//give headers in Joomla 1.5 a bit more spunk
//$this->model->spunk_up_headers_1_5(); //is in css
?>
<div style="margin: 0 auto; width: 950px; text-align: left;">
	<form id="adminForm" name="adminForm" method="post" action="">
		<input type="hidden" name="option" value="com_pagesanditems" />
		<input type="hidden" id="task" name="task" value="customitemtypefield.config_custom_itemtype_field_save" />
		<input type="hidden" id="view" name="view" value="config_custom_itemtype_fiel" />
		<input type="hidden" id="sub_task" name="sub_task" value="" />
		<input type="hidden" id="type_id" name="type_id" value="<?php echo $type_id; ?>" />
		<input type="hidden" name="field_id" value="<?php echo $field_id; ?>" />
		<input type="hidden" name="plugin" value="<?php echo $plugin; ?>" />
		<input type="hidden" name="update_items" id="update_items" value="0" />
		<input type="hidden" name="items_of_this_type" id="items_of_this_type" value="<?php echo $items_of_this_type; ?>" />

		<?php
		/*
		<a href="index.php?option=com_pagesanditems">pages and items</a> >
		*/
		$html = '';
		$results = $dispatcher->trigger('onDisplay_config_form', array (&$html,$plugin, $type_id, $name, $field_params, $field_id));
		?>
		<a href="index.php?option=com_pagesanditems&view=config&tab=itemtypes"><?php echo JText::_('COM_PAGESANDITEMS_CONFIG'); ?></a> > <a href="index.php?option=com_pagesanditems&view=config_custom_itemtype&type_id=<?php echo $type_id; ?>"><?php echo JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_CONFIG'); ?></a> > <?php echo JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_FIELD_CONFIG'); ?>
		<h2><?php echo JText::_('COM_PAGESANDITEMS_CUSTOM_ITEMTYPE_FIELD_CONFIG'); ?></h2>
		<div class="pi_form_wrapper">
			<div class="pi_width20">
				<?php echo JText::_('COM_PAGESANDITEMS_FIELD_TYPE'); ?>:
			</div>
			<div class="pi_width70">
				<?php echo $field_type;
				?>
			</div>
		</div>
		<?php
		if(! in_array(true,$results))
		{
		?>
		<div class="pi_form_wrapper">
			<div class="pi_width20">
				&nbsp;
			</div>
			<div class="pi_width70">
				<h4><?php echo JText::_('not Installed'); ?></h4>
			</div>
		</div>
		<?php
		}
		else
		{
		?>
		<div class="pi_form_wrapper">
			<div class="pi_width20">
				<?php echo JText::_('COM_PAGESANDITEMS_FIELD_NAME'); ?>: <span class="star">&nbsp;*</span>
			</div>
			<div class="pi_width70">
				<input type="text" class="width200" value="<?php echo $name; ?>" id="name" name="name"  onchange="check_name_field();" /> <?php echo JText::_('COM_PAGESANDITEMS_ONLY_CHAR'); ?>
			</div>
		</div>
		<?php

		//get language for fieldtype plugin, defaults to english
		//if(!in_array($plugin, $this->model->fieldtypes_integrated))
		/*
		if(!in_array($plugin, $this->model->fieldtypes_integrated))
		{
			$this->model->get_fieldtype_language($plugin);
		}
		*/
		//include field plugin class
		/*
		if($class_object = $this->model->get_field_class_object($plugin,'display_config_form'))
		{
			echo $class_object->display_config_form($plugin, $type_id, $name, $field_params, $field_id);
		}
		*/

		//$fieldtypeHtml = & new JObject();
		//$filedtypeHtml->text = '';
		//$results = $dispatcher->trigger('onItemtypeDisplay_item_edit', array(&$itemtypeHtml,$item_type,$item_id,$text,$itemIntroText,$itemFullText));
		//echo $itemtypeHtml->text;

		//$field->item_id = $item_id;

		echo $html;
		}
		//onDisplay_config_form(&$html,$plugin, $type_id, $name, $field_params, $field_id)
		/*
		$this->model->include_field_class($plugin);
		$class_name = 'class_fieldtype_'.$plugin;
		$class_plugin = new $class_name();
		echo $class_plugin->display_config_form($plugin, $type_id, $name, $field_params, $field_id);
		*/
		?>
	</form>
</div>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
// $this->model->display_footer();
?>