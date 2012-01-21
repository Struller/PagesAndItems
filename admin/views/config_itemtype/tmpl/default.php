<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC')){
	die('Restricted access');
}

//if($this->model->user_type!='Super Administrator' && !$this->pagesAndItemsModel->isSuperAdmin)
if(!PagesAndItemsHelper::getIsSuperAdmin())
{
	echo "<script> alert('you need to be logged in as a super administrator to edit the Pages-and-Items config.'); window.history.go(-1); </script>";
	exit();
}
//<link rel="stylesheet" type="text/css" href="components/com_pagesanditems/css/pagesanditems.css" />
// TODO CHECK 	echo '<link href<script src="../includes/js/overlib_mini.js" language="JavaScript" type="text/javascript"></script>
?>


<script language="JavaScript" type="text/javascript">

//function submitbutton(pressbutton)
<?php
if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
{
?>
function submitbutton(pressbutton) {
	if (pressbutton == 'itemtype.config_itemtype_save') {
		submitform('itemtype.config_itemtype_save');
	}
	if (pressbutton == 'itemtype.config_itemtype_apply') {
		document.getElementById('sub_task').value = 'apply';
		submitform('itemtype.config_itemtype_save');
	}
	if (pressbutton == 'itemtype.cancel') {
		document.location.href = 'index.php?option=com_pagesanditems&view=config';
	}
}

<?php
}
else
{
?>
Joomla.submitbutton = function(pressbutton)
	if (pressbutton == 'itemtype.config_itemtype_save') {
		Joomla.submitform('itemtype.config_itemtype_save',document.getElementById('adminForm')
	}
	if (pressbutton == 'itemtype.config_itemtype_apply') {
		document.getElementById('sub_task').value = 'apply';
		Joomla.submitform('itemtype.config_itemtype_save',document.getElementById('adminForm')
	}
	if (pressbutton == 'itemtype.cancel') {
		document.location.href = 'index.php?option=com_pagesanditems&view=config';
	}
}

<?php
}
?>
</script>
<?php
//give headers in Joomla 1.5 a bit more spunk
//$this->model->spunk_up_headers_1_5(); //is in css
$item_type = JRequest::getVar('item_type');
?>
<form name="adminForm" method="post" action="">
	<input type="hidden" name="option" value="com_pagesanditems" />
	<input type="hidden" name="task" value="itemtype.config_itemtype_save" />
	<input type="hidden" name="sub_task" id="sub_task" value="" />
	<input type="hidden" name="item_type" value="<?php echo $item_type; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<div style="margin: 0 auto; width: 700px; text-align: left;">
		<?php
		/*
		<a href="index.php?option=com_pagesanditems">pages and items</a> >
		*/
		?>
		<a href="index.php?option=com_pagesanditems&view=config&tab=itemtypes"><?php echo JText::_('COM_PAGESANDITEMS_CONFIG'); ?></a>
		<?php echo PagesAndItemsHelper::translate_item_type($item_type).' '.JText::_('COM_PAGESANDITEMS_CONFIG'); ?>
		<h2><?php echo PagesAndItemsHelper::translate_item_type($item_type).' '.JText::_('COM_PAGESANDITEMS_CONFIG'); ?></h2>
		<?php
		/*

		//COMMENT this moment only the integrated itemtypes will display
		*/
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$itemtype = ExtensionHelper::importExtension('itemtype',null, $item_type,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		$itemtype = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);

		$dispatcher = &JDispatcher::getInstance();
		$itemtypeHtml = & new JObject();
		$itemtypeHtml->text = '';

		$results = $dispatcher->trigger('onItemtypeDisplay_config_form', array(&$itemtypeHtml,$item_type));
		echo $itemtypeHtml->text;


		if($item_type=='text')
		{

			//$plugin_name = JText::_('COM_PAGESANDITEMS_ITEMTYPETEXT');
		}
		elseif($item_type=='html')
		{
			//$plugin_name = 'HTML';
		}
		elseif($item_type=='content')
		{

			//$plugin_name = 'content'; //ADD to see if not an pi item
		}
		elseif($item_type=='other_item')
		{
			//$plugin_name = JText::_('COM_PAGESANDITEMS_ITEMTYPE_OTHER_ITEM');
		}
		else
		{
			//include itemtype plugin language
			//$pi_lang_plugin = $this->controller->get_itemtype_language($item_type);
			//get pluginspecific configuration
			/*

			TODO rewrite ?
			$pi_plugin_config = $this->controller->get_itemtype_config($item_type);
			include($this->controller->pathPluginsItemtypes.'/'.$item_type.'/admin/config.php');

			*/
		}
		?>
	</div>
</form>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
//  $this->model->display_footer();
?>