<?php
/**
* @version		2.1.2
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
TODO this is for itemtypes

we need another way to integrate
*/
$categoryId = JRequest::getVar('categoryId', '' );
$item_type = JRequest::getVar('item_type', '' );
$sub_task = JRequest::getVar('sub_task', '' );
$extensionSubTask = JRequest::getVar('extensionSubTask', '' );
$pageId = JRequest::getVar('pageId', '' );
$item_id = JRequest::getVar('itemId', '' );
//check if user has permission to this itemtype

$config = PagesAndItemsHelper::getConfig();
//check if plugin actually exists
/*

TODO rewrite for extensions
if (!file_exists($this->controller->pathPluginsItemtypes.'/'.$item_type.'/'.$item_type.'.php'))
{
	echo '<script> alert(\''.JText::_('COM_PAGESANDITEMS_ITEMTYPENOTINSTALLED').$item_type.'\'); window.history.go(-1); </script>';
	exit();
}
*/
//if itemtype is not published, throw error
/*
if (!strpos($config['itemtypes'], $item_type))
{
	echo '<script> alert(\''.JText::_('COM_PAGESANDITEMS_ITEMTYPENOTPUBLISHED').$item_type.'\'); window.history.go(-1); </script>';
	exit();
}
*/
//get pluginspecific configuration
/*
TODO rewrite for extensions
if (file_exists($this->controller->pathPluginsItemtypes.'/'.$item_type.'/'.$item_type.'.php'))
{
	$pi_plugin_config = $this->controller->get_itemtype_config($item_type);
}
*/

if(!PagesAndItemsHelper::getIsAdmin())
{
	$frontend = 1;
}
else
{
	$frontend = 0;
}

//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
//$itemtype = ExtensionHelper::importExtension('itemtype',null, $item_type,true,null,true);
$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
$itemtype = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);



//$itemtype = ExtensionHelper::importExtension('itemtype',null, null,true,null,true);
$dispatcher = &JDispatcher::getInstance();


$breadcrumb = '';

if(!$frontend)
$dispatcher->trigger('onItemtypeSubItemBreadcrumb', array(&$breadcrumb,$item_type,$sub_task,$extensionSubTask)); //,$this->model));

if($breadcrumb != '')
{
/*
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	</tbody>
	<tr><td  valign="top" width="20%">
	</td><td valign="top">
		<?php
*/

	//only if we use useCheckedOut
	//if(JRequest::getVar('hidemainmenu',false))
	$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
	//$sub_task = JRequest::getVar('sub_task', '');
	if($useCheckedOut && $sub_task == 'edit')
	{
		//eliminate href and add class
		preg_match_all('|<a\s+(href=[\"\'][^\'\"]*[\'\"])\s*>.*</a>|Ui', $breadcrumb, $targets, PREG_SET_ORDER);
		//preg_match('|<a\s+(href=[\"\'][^\'\"]*[\'\"])\s*>.*</a>|Ui', $test, $target); //, PREG_SET_ORDER);
		foreach($targets as $target)
		{
			if(isset($target[1]))
			{
				$breadcrumb = str_replace($target[1],'class="no_underline"',$breadcrumb);
			}
		}
	}

	
	echo $breadcrumb;
/*
	</td></tr>
	</tbody>
</table>
*/
}
?>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<?php
if(!$frontend)
{
?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top" width="20%">
		<?php
			if(PagesAndItemsHelper::getIsAdmin())
			{
				echo $this->pageTree;
			}
		?>
		</td>
		<td valign="top">
<?php
}
?>
		<form name="adminForm" method="post" action="" enctype="multipart/form-data">

			<input type="hidden" name="option" id="option" value="com_pagesanditems" />
			<input type="hidden" name="task" value="extension." />
			<input type="hidden" name="pageId" id="pageId" value="<?php echo $pageId; ?>">
			<input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
			<input type="hidden" name="item_type" value="<?php echo $item_type; ?>">

			<input type="hidden" name="extensionName" id="extensionName" value="<?php echo $item_type; ?>">
			<input type="hidden" name="extensionType" id="extensionType" value="itemtype">
			<input type="hidden" name="extensionSubTask" id="extensionSubTask" value="">
			<input type="hidden" name="sub_task" id="sub_task" value="" />
			<input type="hidden" name="categoryId" value="<?php echo $categoryId; ?>">
			
			
			<?php
			if($frontend)
			{
			?>
			<input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
			
			<?php
			}
			
			
			$database = JFactory::getDBO();

			//plugin-specific things



			/*
			TODO rewrite for extensions
			*/
			//if($this->model->isAdmin)
			if(PagesAndItemsHelper::getIsAdmin())
			{
				//we need to add buttons
				//$results = $dispatcher->trigger('onItemtypeToolbar', array($item_type,$sub_task));
			}
			if(!PagesAndItemsHelper::getIsAdmin())
			{
				echo '<div class="paddingList" style="margin-top: 40px;">';
					echo '<div>';
						echo '<div class="right_align">';
							$image= PagesAndItemsHelper::getDirIcons().'icon-32-pi.png';
							echo '<img src="'.$image.'" alt="" style="float:left;" />&nbsp;';
			}
			$toolbarHtml = '';
			$results = $dispatcher->trigger('onItemtypeToolbar', array(&$toolbarHtml,$item_type,$sub_task,$extensionSubTask));
			echo $toolbarHtml;

			if(!PagesAndItemsHelper::getIsAdmin())
			{
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}

			$itemtypeHtml = & new JObject();
			$itemtypeHtml->text = '';
			$results = $dispatcher->trigger('onItemtypeDisplay_item_subtask', array(&$itemtypeHtml,$item_type,$sub_task,$extensionSubTask)); //,$this->model));
			echo $itemtypeHtml->text;

			/*
			*/
			//require_once($this->controller->pathPluginsItemtypes.'/'.$item_type.'/admin/'.$sub_task.'.php');




			?>
		</form>
<?php
if(!$frontend)
{
?>
	</td>
  </tr>
</table>
<?php
}
?>
<!-- end id="form_content" need for css-->
</div>
<?php

if($frontend)
{
echo '<link type="text/css" rel="stylesheet" href="administrator/components/com_pagesanditems/css/pagesanditems2.css" />';
}
else
{
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
}
// $this->controller->display_footer();
?>