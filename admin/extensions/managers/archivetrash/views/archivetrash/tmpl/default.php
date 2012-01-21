<?php
/**
* @version		1.6.0
* @package		PagesAndItems
* @copyright	Copyright (C) 2006-2010 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/


defined('_JEXEC') or die('Restricted access');?>
<?php
$table = $this->tables[$this->table_id];
//dump('X');
?>
<script language="JavaScript" type="text/javascript">

<?php


if($this->model->joomlaVersion < '1.6')
{
?>
function submitbutton(pressbutton) 
{
	//alert(pressbutton);
	if (pressbutton == 'delete') 
	{
		document.getElementById('extension_task').value = 'delete';
		document.adminForm.submit();
		//submitform();
		return;
	}
	if (pressbutton == 'restore') 
	{
		document.getElementById('extension_task').value = 'restore';
		document.adminForm.submit();
		//submitform();
		return;
	}
	if (pressbutton == 'managers.cancel') 
	{
		document.getElementById('task').value = pressbutton;
		document.adminForm.submit();
		//submitform();
		return;
	}
return;
}
<?php
}
else
{
//TODO 
/*
if (pressbutton == 'page.page_delete')
						{
							
							are_you_sure = '<?php 
							$confirm_delete = addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE2')).'?'.'\n\n'; 
							$confirm_delete .= addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE1')).':\n'; 
							$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4')).'\n'; 
							if($this->helper->config['page_delete_cat']){
								$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5')).'\n'; 
							}
							if($this->helper->config['page_delete_items']){
								$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6')).'\n'; 
							}
							echo $confirm_delete;						
							?>';
							if(confirm(are_you_sure)){
								
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
							}
						}
						if (pressbutton == 'page.page_trash'){
							are_you_sure = '<?php 
							$confirm_trash = addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH2')).'?'.'\n\n'; 
							$confirm_trash .= addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH3')).':\n'; 
							$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4')).'\n'; 
							if($this->helper->config['page_trash_cat']){
								$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5')).'\n'; 
							}
							if($this->helper->config['page_trash_items']){
								$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6')).'\n'; 
							}
							echo $confirm_trash;						
							?>';
							if(confirm(are_you_sure)){	
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
							}
						}


*/
?>
Joomla.submitbutton = function(pressbutton)
{



	
	if (pressbutton == 'trash') 
	{
		<?php
		if($table->tableName == 'menu')
		{
		?>
			are_you_sure = '<?php 
			$confirm_trash = addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH2')).'?'.'\n\n'; 
			$confirm_trash .= addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH3')).':\n'; 
			$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4')).'\n'; 
			if($this->config['page_trash_cat']){
				$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5')).'\n'; 
			}
			if($this->config['page_trash_items']){
				$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6')).'\n'; 
			}
			echo $confirm_trash;
			?>';
			if(confirm(are_you_sure)){
				document.getElementById('extension_task').value = 'trash';
				document.adminForm.submit();
			}
		<?php
		}
		elseif($table->tableName == 'content')
		{
		?>
			are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_ITEMS_TRASH')).'.'; ?>';
			if(confirm(are_you_sure)){	
				document.getElementById('extension_task').value = 'trash';
				document.adminForm.submit();
			}
		<?php
		}
		else
		{
		?>
		document.getElementById('extension_task').value = 'trash';
		document.adminForm.submit();
		<?php
		}
		?>
		return;
	}
	if (pressbutton == 'delete') 
	{
		<?php
		if($table->tableName == 'menu')
		{
		?>
			are_you_sure = '<?php 
			$confirm_delete = addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE2')).'?'.'\n\n'; 
			$confirm_delete .= addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE1')).':\n'; 
			$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4')).'\n'; 
			if($this->config['page_delete_cat']){
				$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5')).'\n'; 
			}
			if($this->config['page_delete_items']){
				$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6')).'\n'; 
			}
				echo $confirm_delete;
			?>';
			if(confirm(are_you_sure)){
				document.getElementById('extension_task').value = 'delete';
				document.adminForm.submit();
			}
		<?php
		}
		elseif($table->tableName == 'content')
		{
		?>
			are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_ITEMS_DELETE')).'.'; ?>';
			if(confirm(are_you_sure)){
				document.getElementById('extension_task').value = 'delete';
				document.adminForm.submit();
			}
		<?php
		}
		else
		{
		?>
			document.getElementById('extension_task').value = 'delete';
			document.adminForm.submit();
		<?php
		}
		?>
		return;
	}
	if (pressbutton == 'restore') 
	{
		document.getElementById('extension_task').value = 'restore';
		document.adminForm.submit();
		return;
	}
	if (pressbutton == 'archive') 
	{
		document.getElementById('extension_task').value = 'archive';
		document.adminForm.submit();
		return;
	}
	if (pressbutton == 'publish') 
	{
		document.getElementById('extension_task').value = 'publish';
		document.adminForm.submit();
		return;
	}
	if (pressbutton == 'unpublish') 
	{
		document.getElementById('extension_task').value = 'unpublish';
		document.adminForm.submit();
		return;
	}
	if (pressbutton == 'managers.cancel') 
	{
		Joomla.submitform( pressbutton, document.getElementById('adminForm' ));
		return;
	}
	return;
}
<?php
}

?>

<!-- if we change the state over the state button in the tree we need to set the cid[] but we must set all other to 0 -->
function setCid(id) 
{
	if (id) 
	{
		//alert(id);
		var form = document.id('adminForm');
		form.getElements('input[name^=cid]').each(function(el) {
			//el.destroy();
			el.set('value','');
			el.removeProperty('checked');
			el.checked = false;

		}); 
		
		form.getElements('span[class*=mif-tree-node-checked]').each(function(el) {
			el.removeClass('mif-tree-node-checked');
			el.addClass('mif-tree-node-unchecked');
		});
		
		
		var input = new Element('input', {
			'type': 'hidden',
			'name': 'cid[]',
			'id': 'cb'+ id,
			'value': id
		});
		form.grab(input);
	}
}
</script>


<form action="index.php" method="post" name="adminForm" id="adminForm">

	<fieldset id="filter-bar">
		<div class="fltlft">
			<?php
				//foreach($this->lists['buttons'] as $button)
				foreach($this->buttons as $button)
				{
					echo $button;
				}
			?>
		</div>
		
		<div class="filter-select fltrt">
			<?php
			//echo JText::_('JSTATUS').' ';
			echo $this->lists['types'];
			?>
			<?php
			/*
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->lists['filter_state'], true);?>
			</select>
			*/
			?>
		</div>
	</fieldset>
				
				
<?php
/*
				<table class="adminform">
					<tbody>
			
						<tr>
							<th>
							<div class="paddingList">
							<?php
							//TODO as JText
							//echo '<div>the avaible item-types</div>';
								foreach($this->lists->buttons as $button)
								{
									echo $button;
								}
							?>
							</div>
							<div class="paddingList">
								<?php
								echo '<div >';
								echo JText::_('JSTATUS').' ';
								echo $this->lists->types;
								echo '</div>';
								?>
							</div>
							</th>
						</tr>
						<?php
						
						?>
					</tbody>
				</table>
*/
?>
					<!-- 	<tr>
							<td> -->
	<?php 
	if($table->tableName == 'menu')
	{
		//here we set the tree(s) all content over javascript
		//see view.html.php
		echo '<div id="tree_container" class="tree_container"></div>';
	}
	elseif(!isset($table->output) || $table->tableName == 'content')
	{

		if(count($this->rows))
		{
	
			echo '<table class="adminlist">';
			
			echo '<thead>';
				echo '<tr>';
					echo '<th width="20">';
						echo JText::_( 'Num' );
					echo '</th>';
					echo '<th width="20">';
						echo '<input type="checkbox" id="toggle" name="toggle" value="0" onclick="checkAll('.count( $this->rows ).');" />';
					echo '</th>';
					echo '<th>';
						//JGLOBAL_TITLE
						echo JHTML::_('grid.sort', $table->referenceDisplay, $table->referenceName, @$this->lists['filter_order_Dir'], @$this->lists['filter_order'] );
						//echo $table->reference_display;
					echo '</th>';
					echo '<th>';
						//echo JHTML::_('grid.sort', $table->state->Name, $table->state->Name, @$this->lists['filter_order_Dir'], @$this->lists['filter_order'] );
						//'JGRID_HEADING_ID'
						echo JHTML::_('grid.sort', $table->state->display, $table->state->name, @$this->lists['filter_order_Dir'], @$this->lists['filter_order'] );
						//echo 'state';
					echo '</th>';
					echo '<th>';
						echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', $table->referenceId, @$this->lists['filter_order_Dir'], @$this->lists['filter_order'] );
						//echo $table->reference_id;
					echo '</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tfoot>';
				echo '<tr>';
						//echo '<td colspan="5">';
					echo '<td colspan="5">';
						echo $this->pagination->getListFooter();
					echo '</td>';
				echo '</tr>';
			echo '</tfoot>';
			echo '<tbody>';
				$k = 0;
				$i = 0;
				$k = 0;
				for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
				{
					$row 	= $this->rows[$i];
				
				//foreach($this->rows as $row)
				//{
					//echo '<tr>';
					//$i++;
					/*
					echo '<tr class="row'.$k.'">';
					echo '<td align="right">';
						echo $i;
					echo '</td>';
					*/
					?>
					<tr class="<?php echo "row$k"; ?>">
					<td align="right">
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<?php
					
					
					echo '<td>';
						$id = $table->referenceId;
						//echo '<input type="checkbox" id="cb'.($i-1).'" name="cid[]" value="'.$row->$id.'" onclick="isChecked(this.checked);" />';
						echo '<input type="checkbox" id="cb'.($i).'" name="cid[]" value="'.$row->$id.'" onclick="isChecked(this.checked);" />';
					echo '</td>';
					echo '<td>';
						$name = $table->referenceName;
						echo $row->$name;
					echo '</td>';

					echo '<td>';
					$stateName = 'state';
					if(isset($table->state->Name) )
					{
						$stateName = $table->state->name;
					}
					$state = $row->$stateName;
					
					switch($state)
					{
						case '1':
							//$state = 'published';
							$state = '<span class="state publish"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_UNPUBLISH_ITEM').'"';
							$onclick = 'onclick="setCid(\''.$row->$id.'\');Joomla.submitbutton(\'unpublish\');"';
						break;

						case '0':
							//$state = 'unpublished';
							$state = '<span class="state unpublish"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$onclick = 'onclick="setCid(\''.$row->$id.'\');Joomla.submitbutton(\'restore\');"';
						break;
					
						case '2':
							//$state = 'archive';
							$state = '<span class="state archive"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$onclick = 'onclick="setCid(\''.$row->$id.'\');Joomla.submitbutton(\'restore\');"';
						break;
		
						case '-1':
							//$state = 'archive';
							$state = '<span class="state archive"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$onclick = 'onclick="setCid(\''.$row->$id.'\');Joomla.submitbutton(\'restore\');"';
						break;
						case '-2':
							//$state = 'trash';
							$state = '<span class="state trash"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$onclick = 'onclick="setCid(\''.$row->$id.'\');Joomla.submitbutton(\'restore\');"';
						break;
					}
					echo ' <a class="jgrid hasTip" '.$title.' '.$onclick.'> '.$state.'</a>';
					echo '</td>';

					echo '<td>';
						echo $row->$id;
					echo '</td>';
					echo '</tr>';
					$k = 1 - $k;
				}
				echo '</tbody>';
			echo '</table>';
		}
	}
	else
	{
		//here we need the output from the extension
		$dispatcher = &JDispatcher::getInstance();
		$output = '';
		/*
		in the model we have load all extensions and here we trigger on all extensions
		but only one can output here
		so the extension must check 
		$table->extension 
		$table->extensionType
		$table->extensionFolder
		or we detach all extensions and load the single here
		*/		
		
		$query = "SELECT *"
			. " FROM #__pi_extensions"
			. " WHERE type <> 'language' "
			. " GROUP BY type"
			. " ORDER BY type"
			;
		$this->db->setQuery( $query );
		$types = $this->db->loadObjectList();
		$dispatcher = &JDispatcher::getInstance();
		foreach($types as $type)
		{
			$dispatcher->trigger('onDetach', array ( $type->type));
		}
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..');
		if(isset($table->extension) && isset($table->extensionType))
		{
			if(!isset($table->extensionFolder))
			{
				$table->extensionFolder = '';
			}
			require_once($path.DS.'includes'.DS.'extensions'.DS.$table->extensionType.'helper.php');
			$typeName = 'Extension'.ucfirst($table->extensionType).'Helper';
			$typeName::importExtension($table->extensionFolder, $table->extension,true,null,true);
			$dispatcher->trigger('onArchiveTrashOutput', array ( &$output,$table,$this->lists));
			echo $output;
		}

		//$dispatcher->trigger('onArchiveTrashOutput', array ( &$output,$table,$this->lists));
		//echo $output;
	}
	?>
	<input type="hidden" id="boxchecked" name="boxchecked" value="0" />
	<input type="hidden" id="filter_order" name="filter_order" value="<?php echo $this->lists['filter_order'];?>" >
	<input type="hidden" id="filter_order_Dir" name="filter_order_Dir" value="asc" >
	<input type="hidden" name="option" value="com_pagesanditems" />
	<input type="hidden" id="task" name="task" value="extension.doExecute" />
	<input type="hidden" id="extension_task" name="extension_task" value="" />
	<input type="hidden" id="extension" name="extension" value="archivetrash" />
	<input type="hidden" id="extensionType" name="extensionType" value="manager" />
	<input type="hidden" id="extensionFolder" name="extensionFolder" value="" />
	
	
	<input type="hidden" id="view" name="view" value="archivetrash" />
	<input type="hidden" id="table_id" name="table_id" value="<?php echo $this->table_id;?>" />
	<input type="hidden" id="table_name" name="table_name" value="<?php echo $table->tableName;?>" />
	
	
	<?php
	/*
	<input type="hidden" id="extension_sub_task" name="extension_sub_task" value="display" />
	<input type="hidden" id="filter_state" name="filter_state" value="<?php echo $this->filter_state;?>" />
	<input type="hidden" id="table_id" name="table_id" value="<?php echo $this->table;?>" />
	<input type="hidden" id="table_table" name="table[table]" value="<?php echo $table->tableName;?>" />
	<input type="hidden" id="table_extension" name="table[extension]" value="<?php echo $table->extension;?>" />
	<input type="hidden" id="table_extensionType" name="table[extensionType]" value="<?php echo $table->extensionType;?>" />
	*/
	?>


	<?php
	/*
	<input type="hidden" name="filter_client" value="<?php echo $this->client;?>" />
	*/
	?>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	
</form>
<?php
	//echo PagesAndItemsHelper::loadIconsCss();
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>