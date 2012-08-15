<?php
/**
* @version		2.1.6
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
//$path = str_replace(DS,'/',str_replace(JPATH_SITE,'',JPATH_COMPONENT_ADMINISTRATOR));
	JHTML::_('behavior.tooltip');
	$ordering = ($this->lists['order'] == 'p.type,p.folder' || $this->lists['order'] == 'p.folder' || $this->lists['order'] == 'p.ordering');
	$rows = & $this->items;

?>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php
if(PagesAndItemsHelper::getIsJoomlaVersion('>=','1.6'))
{
?>
<script language="javascript" type="text/javascript">
	
	// needed for Table Column ordering
	/**
	 * USED IN: libraries/joomla/html/html/grid.php
	 *
	 * @deprecated	12.1 This function will be removed in a future version. Use Joomla.tableOrdering() instead.
	 */
	Joomla.tableOrdering = function(order, dir, task, form) 
	{
		tableOrdering(order, dir, task);
	}
	
	function tableOrdering(order, dir, task) {
		var form = document.adminForm;

		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		document.getElementById('extensionTask').value = task;
		Joomla.submitform( 'extension.doExecute', document.getElementById('adminForm' ));
	}
	
	function checkAll_button(n, task) {
	if (!task) {
		task = 'saveorder';
	}

	for (var j = 0; j <= n; j++) {
		var box = document.adminForm['cb'+j];
		if (box) {
			if (box.checked == false) {
				box.checked = true;
			}
		} else {
			alert("You cannot change the order of items, as an item in the list is `Checked Out`");
			return;
		}
	}
	document.getElementById('extensionTask').value = task;
	Joomla.submitform( 'extension.doExecute', document.getElementById('adminForm' ));
	return;
	}
	
	Joomla.submitbutton = function(pressbutton)
	{
		//alert(pressbutton);
		if(pressbutton == 'managers.cancel')
		{
			Joomla.submitform( pressbutton, document.getElementById('adminForm' ));
			return;
		}
			document.getElementById('extensionTask').value = pressbutton;
			Joomla.submitform( 'extension.doExecute', document.getElementById('adminForm' ));
			return;
	}
	
	function submitbutton(pressbutton)
	{
		if(pressbutton == 'managers.cancel')
		{
			submitform( pressbutton);
			return;
		}
		
		document.getElementById('extensionTask').value = pressbutton;
		Joomla.submitform( 'extension.doExecute', document.getElementById('adminForm' ));
		return;
	}
</script>
<?php
}
?>

<?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP');?>:
<ol>
	<li>
		<?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_PAGETYPES');?>: <?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_PAGETYPE_2');?>
	</li>
	<li>
		<?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_ITEMTYPES');?>: <?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_ITEMTYPE_2');?>
	</li>
	<li>
		<?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_FIELDTYPES');?>: <?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_FIELDTYPE_2');?>
	</li>
	<li>
		<?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_HTMLS');?>: <?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_HTML_2');?>
	</li>
	<li>
		<?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_MANAGERS');?>: <?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_MANAGER_2');?>
	</li>
	<li>
		<?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_LANGUAGES');?>: <?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_LANGUAGE_2');?>
	</li>
</ol>

<table>
	<tr>

		<td align="left" width="100%">
		<?php
		/*	<?php echo JText::_( 'Filter' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
		*/
		?>
		</td>
		<td nowrap="nowrap">
			<?php
			echo $this->lists['type'];
			echo $this->lists['folder'];
			echo $this->lists['state'];
			echo $this->lists['language'];
			echo $this->lists['client'];
			?>
		</td>
	</tr>
</table>
<table class="adminlist">
<thead>
	<tr>
		<th width="20">
			#
		</th>
		<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort', 'COM_PAGESANDITEMS_EXTENSIONS_NAME', 'p.element', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>


		<th nowrap="nowrap" width="5%">
			<?php echo JHTML::_('grid.sort', 'JSTATUS', 'p.enabled', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
		<?php
		if($this->filter_type != 'language' )
		{
		?>
		<th width="8%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ORDERING', 'p.ordering', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			<?php if ($ordering) echo JHTML::_('grid.order',  $rows, null,'piextensions.saveorder' ); ?>
		</th>
		<?php
		}
		?>
		<th nowrap="nowrap"  width="10%" class="title">
			<?php echo JHTML::_('grid.sort', 'COM_PAGESANDITEMS_EXTENSIONS_TYPE', 'p.type', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort', 'COM_PAGESANDITEMS_EXTENSIONS_FOLDER', 'p.folder', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>

		<th nowrap="nowrap" width="10%">
			<?php
				echo JHTML::_('grid.sort', 'JVERSION', 'p.version', @$this->lists['order_Dir'], @$this->lists['order'] );
				//echo JHTML::_('grid.sort',   'Access', 'groupname', @$this->lists['order_Dir'], @$this->lists['order'] );
			?>
		</th>


		<th nowrap="nowrap"  width="1%" class="title">
			<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'p.extension_id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
		</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<td colspan="12">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $rows ); $i < $n; $i++)
{
	$row 	= $rows[$i];
	$item = $this->loadItem($row);

	//$link = JRoute::_( 'index.php?option=com_pagesanditems&view=manageextension&client='. $this->client .'&sub_task=edit&cid[]='. $row->extension_id.'&extension_id='. $row->extension_id );
	$link = JRoute::_( 'index.php?option=com_pagesanditems&task=extension.doExecute&extensionName=extensions&extensionType=manager&layout=edit&extensionTask=display&view=piextension&client='. $this->client .'&sub_task=edit&cid[]='. $row->extension_id.'&extension_id='. $row->extension_id );
	//$access 	= JHTML::_('grid.access', $row, $i );

	$protected = 0;
	//$published 	= JHTML::_('grid.published', $row, $i,'tick.png', 'publish_x.png','extensions.publish'); //,'extension_id' );
	if( ($row->type == 'itemtype' && ($row->element == 'content' || $row->element == 'text')) || ($row->type == 'pagetype' && $row->version == 'integrated') || ($row->type == 'manager' && $row->element == 'extensions' && $row->version == 'integrated') || $row->type == 'language')
	{
		/*
		//JHtml::_('jgrid.published', $item->enabled, $i, 'extensions.', false);
		$published = '
		<a href="javascript:void(0);" title="'. JText::_('COM_PAGESANDITEMS_EXTENSION_ITEM_CAN_NOT_UNPUBLISH') .'">
		<img src="images/'. $item->img .'" border="0" alt="'. JText::_('COM_PAGESANDITEMS_EXTENSION_ITEM_CAN_NOT_UNPUBLISH') .'" /></a>'
		;
		*/
		//action($i, $task, $prefix='', $text='', $active_title='', $inactive_title='', $tip=false, $active_class='', $inactive_class='', $enabled = true, $translate=true, $checkbox='cb')
		//JHtml::_('jgrid.action($i, 'publish', 'extensions.', $text='', $active_title='', $inactive_title='', $tip=false, $active_class='', $inactive_class='', $enabled = true, $translate=true, $checkbox='cb')
		$more = '<span class="jgrid" title="'.JText::_('COM_PAGESANDITEMS_EXTENSION_ITEM_CAN_NOT_UNPUBLISH').'">';
			$more .= '<span class="state checkedout">';
				//$more .= '<span class="text">';
				//	$more .= 'Ausgecheckt';
				//$more .= '</span>';
			$more .= '</span>';
		$more .= '</span>';
		$published = $more.JHtml::_('jgrid.published', $item->enabled, $i, 'piextensions.',false);
		$protected++;
	}
	else
	{
		/*
		$published = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $item->task .'\')" title="'. $item->action .'">
		<img src="images/'. $item->img .'" border="0" alt="'. $item->alt .'" /></a>'
		;
		*/
		$published = JHtml::_('jgrid.published', $item->enabled, $i, 'piextensions.');
		//$published .= JHtml::_('jgrid.published', $item->state, $i, 'piextensions.');
	}

	if($row->version == 'integrated' || ($row->type == 'language' && ($row->protected || $row->element == 'en-GB')) )
	{
		$protected++;
	}

	$checked 	= JHTML::_('grid.checkedout', $row, $i,'extension_id' );
	$user		= JFactory::getUser();
	$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
	$checked_out = '';
	if ($item->checked_out)
	{
		$checked_out = JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'piextensions.', $canCheckin);
		/*
		if($protected >= 2)
		{

		}
		<a class="jgrid hasTip" title="" onclick="return listItemTask('cb0','piextensions.checkin')" href="javascript:void(0);">
			<span class="state checkedout">
				<span class="text">Ausgecheckt</span>
			</span>
		</a>

		*/
	//icon-16-lock.png
	}


?>
	<tr class="<?php echo "row$k"; ?>">
		<td align="right">
			<?php

			echo $this->pagination->getRowOffset( $i );

			?>
		</td>
		<td>
			<?php
			if( $row->installed)
			{
				if($protected < 2)
				{

			//name="cid[]"
			?>
					<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $item->extension_id; ?>" onclick="isChecked(this.checked);" <?php echo $item->cbd; ?> />
				<?php
				}
				else
				{
				//name="cid[]"
				/*
				protected items can not checkin
				*/
				?>
					<input type="hidden" name="xcid[]" id="cb<?php echo $i;?>" value="<?php echo $item->extension_id; ?>" onclick="isChecked(this.checked);" <?php echo $item->cbd; ?> />
					<input type="checkbox" disabled="disabled" value="<?php echo $item->extension_id; ?>" <?php echo $item->cbd; ?> />
				<?php
				}
			}
			else
			{
				echo '<img src="'.PagesAndItemsHelper::getDirIcons().'base/icon-16-no_access_slash_button.png">';
			}
			//echo $checked;
			?>
		</td>
		<td>
			<?php
			$name = JText::_(strtoupper ($row->name)) <> strtoupper ($row->name) ? JText::_(strtoupper ($row->name)) : $row->name;
			
			echo $checked_out;
			if($row->type == 'language' && ($row->name <> $row->element ) )
			{
				echo '<span class="gi">|&mdash;</span>';
			}
			if($row->version == 'integrated' || $row->protected || ($row->type == 'language' && ($row->protected || $row->element == 'en-GB')))
			{
				$more = '<span class="jgrid" title="'.JText::_('COM_PAGESANDITEMS_EXTENSION_ITEM_CAN_NOT_UNINSTALL').'">';
					$more .= '<span class="state checkedout">';
					$more .= '</span>';
				$more .= '</span>';
				echo $more;
			}
			if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out ) || !$row->installed || $row->type == 'language')
			{
				if(!$row->installed)
				{
					echo '<span class="editlinktip hasTip" title="'.JText::_( 'COM_PAGESANDITEMS_EXTENSION_NOT_INSTALLED' ).'::'.htmlspecialchars($row->element).'">';
					echo '<img src="'.PagesAndItemsHelper::getDirIcons().'base/icon-16-no_access_slash_button.png">';
					echo htmlspecialchars($row->element).'</span>';
					?>
					<p class="smallsub">
					<span></span>
						<?php echo htmlspecialchars($name); //$row->name); ?>
					</p>
					<?php
				}
				else
				{
					$addSpan = false;
					//$row->type == 'language' && $row->element != 'en-GB'
					if($row->type == 'language' && $row->name == $row->element && (!$row->protected )) //|| $row->version != 'integrated'))
					{
						echo '<span class="editlinktip hasTip" title="'.JText::_( 'COM_PAGESANDITEMS_LANGUAGE_UNINSTALL' ).'::'.htmlspecialchars($row->element).'<br />'.JText::sprintf( 'COM_PAGESANDITEMS_LANGUAGE_UNINSTALL_TIP',$row->element ).'">';
						$addSpan = true;
					}
					//$row->type == 'language' && $row->element != 'en-GB'
					elseif($row->type == 'language' && $row->name <> $row->element && (!$row->protected )) //|| $row->version != 'integrated'  ))
					{
						echo '<span class="editlinktip hasTip" title="'.JText::_( 'COM_PAGESANDITEMS_LANGUAGE_UNINSTALL' ).'::'.htmlspecialchars($row->element).'<br />'.JText::sprintf( 'COM_PAGESANDITEMS_LANGUAGE_UNINSTALL_SELECTED_TIP',$row->name ).'">';
						$addSpan = true;
					}
					?>

					<?php echo htmlspecialchars($row->element);

					if($addSpan) // && $row->type == 'language') // && $row->element != 'en-GB')
					{
						echo '</span>';
					}
					?>

					<p class="smallsub">
					<?php
					if($row->type == 'language' && ($row->name <> $row->element ) )
					{
					echo '<span class="gtr">|&mdash;</span>';
					}
					else
					{
					echo '<span></span>';
					}
					?>
						<?php echo htmlspecialchars($name); //$row->name); ?>
						<?php
						if($row->type == 'language')
						{
							$client = JApplicationHelper::getClientInfo($row->client_id);
							echo '('.$client->name.')';
						}
						?>
					</p>
					<?php
				}
			}
			else
			{
			?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_PAGESANDITEMS_EXTENSION_EDIT' );?>::<?php echo htmlspecialchars($row->element); ?>">
					<a href="<?php echo $link; ?>">
						<?php echo htmlspecialchars($row->element); ?>
					</a>
				</span>
				<p class="smallsub">
					<span></span>
						<?php echo htmlspecialchars($name); //$row->name); ?>
				</p>

			<?php } ?>
		</td>
		<?php
		/*
		<td>
			<?php
			//TODO $row->installed
			if (  JTable::isCheckedOut($this->user->get ('id'), $row->checked_out )  || !$row->installed)
			{
				echo $row->name;
			} else {
			?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_PAGESANDITEMS_EXTENSION_EDIT' );?>::<?php echo htmlspecialchars($row->name); ?>">
				<a href="<?php echo $link; ?>">
					<?php echo htmlspecialchars($row->name); ?></a></span>
			<?php } ?>
		</td>
		*/
		?>


		<td align="center">
			<?php

				echo $published;
			?>
		</td>
		<?php
		if($row->type != 'language' )
		{
		?>
		<td class="order">
			<span><?php
				//orderUpIcon($i, $condition = true, $task = 'orderup', $alt = 'Move Up', $enabled = true)
				echo $this->pagination->orderUpIcon( $i, ($row->type == @$rows[$i-1]->type && $row->folder == @$rows[$i-1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'piextensions.orderup', 'JLIB_HTML_MOVE_UP', $ordering ); ?></span>
			<span><?php echo $this->pagination->orderDownIcon( $i, $n, ($row->type == @$rows[$i+1]->type && $row->folder == @$rows[$i+1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'piextensions.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering ); ?></span>
			<?php

			$disabled = $ordering ?  '' : 'disabled="disabled"';

			?>

			<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>"  <?php echo $disabled ?> class="text_area" style="text-align: center" />
		</td>
		<?php
		}
		?>

		<td nowrap="nowrap">
			<?php echo $row->type;?>
		</td>
		<td>
			<?php
			if ( $row->folder && $row->folder != '' )
			{

				$view = '';
				if(strpos($row->folder, '_') !== false)
				{
					list($view,$type) = explode('_',$row->folder);
				}
				
				$image = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'images'.DS.'view_'.$view.'.png');
				if($image)
				{
					JHTML::_( 'behavior.modal' );
					$image = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',$image));
					$value = '<span class="editlinktip"><a class="modal hasTip editlinktip" title="'.JText::_('COM_PAGESANDITEMS_EXTENSION_FOLDER_TIP').'" href="'.$image.'">';
						$value .= $row->folder;
					$value .= '</a></span>';
					echo $value;
				}
				else
				{
					echo $row->folder;
				}
			}
			else
			{
				echo JText::_('JOPTION_UNASSIGNED'); //'N/A';
			}
			 ?>
		</td>


		<td align="center">
			<?php
				echo $row->version;
				//echo $access;
			?>
		</td>
		<?php
		/*
		<td nowrap="nowrap">
			<?php echo $row->element;?>
		</td>
		*/
		?>
		<td align="center">
			<?php echo $row->extension_id;?>
		</td>
	</tr>
	<?php
	$k = 1 - $k;
}
?>
</tbody>
</table>
	<input type="hidden" name="option" value="com_pagesanditems" />
	<input type="hidden" id="task" name="task" value="extension.doExecute" />
	<input type="hidden" id="extensionTask" name="extensionTask" value="" />
	<input type="hidden" id="extensionName" name="extensionName" value="extensions" />
	<input type="hidden" id="extensionType" name="extensionType" value="manager" />
	<input type="hidden" id="extensionFolder" name="extensionFolder" value="" />
	<input type="hidden" id="view" name="view" value="piextensions" />




	<!--<input type="hidden" name="filter_client" value="<?php echo $this->client;?>" />-->


	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<!-- end id="form_content" need for css-->
</div>
<?php
//echo $this->loadTemplate('footer');
/*
$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..')));
echo '<link href="'.JURI::root(true).'/'.$path.'/css/pagesanditems_icons.css" rel="stylesheet" type="text/css" />'."\n";
*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>
