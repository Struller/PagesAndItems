<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.tooltip');

$sub_task = JRequest::getVar( 'sub_task', 'edit');
$menutype = JRequest::getVar('menutype', '');
$view = JRequest::getVar( 'view', 'page');
$layout = JRequest::getVar( 'layout', null);

?>
<?php
	echo $this->reload;
?>
<div id="page_content">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>

		<td valign="top" width="20%">
		<?php
			//display the page-tree
			//incl. htmls/page_tree
			//TODO 
			echo $this->pageTree;
			//echo $this->getPages();
		?>
		</td> 
		<td valign="top">
			<form name="adminForm" id="adminForm" method="post" action="">
				<script language="javascript" type="text/javascript">
					<?php 
						if($this->model->joomlaVersion < '1.6')
						{
							$joomlaSubmit = '';
							echo 'function submitbutton(pressbutton)'."\n";
						}
						else
						{
							$joomlaSubmit = 'Joomla.';
							echo 'Joomla.submitbutton = function(pressbutton)'."\n";
						}
					?>
					{
						
						//root
						//alert(pressbutton);
						if(pressbutton == 'newMenuItem')
						{
							//document.getElementById('page_reload').style.display  = 'block';
							document.getElementById('sub_task').value = 'new';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php 
							//echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
							//document.adminForm.submit();
						}
						if (pressbutton == 'page.root_underlayingpages')
						{
							//document.getElementById('subsub_task').value = 'apply';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php 
							//echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
						}
						if (pressbutton == 'page.root_apply')
						{
							document.getElementById('subsub_task').value = 'apply';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php 
							//echo $joomlaSubmit;
							?>
							//submitform('page.root_apply');
						}
						if (pressbutton == 'page.root_save')
						{
							//alert('must write');
							document.getElementById('subsub_task').value = 'save';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php 
							//echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
						}
						if (pressbutton == 'page.root_cancel')
						{
							//document.getElementById('subsub_task').value = 'cancel';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php 
							//	echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
						}
						
						
						if (pressbutton == 'page.pages_archive'){
							are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGES_ARCHIVE')); ?>';
							if(confirm(are_you_sure)){
								document.getElementById('sub_task').value = 'archive';
								document.getElementById('task').value = 'page.pages_state';	
								document.adminForm.submit();
							}
						}
						if (pressbutton == 'page.pages_trash'){
							are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGES_TRASH')); ?>';
							if(confirm(are_you_sure)){	
								document.getElementById('sub_task').value = 'trash';
								document.getElementById('task').value = 'page.pages_state';
								document.adminForm.submit();
							}
						}
						if (pressbutton == 'page.pages_delete'){
							are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGES_DELETE')); ?>';
							if(confirm(are_you_sure)){	
								document.getElementById('sub_task').value = 'delete';
								document.getElementById('task').value = 'page.pages_state';
								document.adminForm.submit();
							}
						}
						if (pressbutton == 'page.pages_publish'){
							document.getElementById('sub_task').value = 'publish';
							document.getElementById('task').value = 'page.pages_state';
							document.adminForm.submit();
						}
						if (pressbutton == 'page.pages_unpublish'){
							document.getElementById('sub_task').value = 'unpublish';
							document.getElementById('task').value = 'page.pages_state';
							document.adminForm.submit();
						}
					}
					
					function publish_unpublish_page(page_id, new_state){
						//unselect all checkboxes
						for (i = 0; i < page_ids.length; i++){
							box_id = 'pageCid_'+page_ids[i];
							if(document.getElementById(box_id)){
								document.getElementById(box_id).checked = false;
							}
						}
						
						//select the checkbox we need
						box_id = 'pageCid_'+page_id;
						document.getElementById(box_id).checked = 'checked';
						
						if(new_state=='1'){
							sub_task = 'publish';
						}else{
							sub_task = 'unpublish';
						}
						
						//submit
						document.getElementById('sub_task').value = sub_task;
						document.getElementById('task').value = 'page.pages_state';	
						document.adminForm.submit();
					}
				</script>
			<?php
			//$this->getMenuItem();
			/*
			if($sub_task == 'edit' && $this->pageId)
			{
				//$this->getMenuItem();
			}
			*/
			?>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td valign="top" width="49%">
						<table class="adminform" width="98%">
						
							<?php
							if($sub_task == 'edit')
							{
								//display the underlayingpages/childs
								//incl. htmls/page_childs
								//echo $this->loadTemplate('underlayingpages');
								
								//TODO 
								echo $this->pageChilds;
								//echo $this->loadTemplate('childs');
							}
							?>
						</table>
					</td>
					<td width="2%">&nbsp;
					</td>
					<td valign="top" width="49%">
					<?php
					$dispatcher = &JDispatcher::getInstance();
					$dispatcher->trigger('onSubLayoutItems',array()); //??
					?>
					<?php
					?>
					</td>
				</tr>
			</table>
			<?php
			//$this->getMenuItem();
			if($sub_task == 'new')
			{
				//$this->getMenuItem();
			}
			echo $this->pagePropertys;
			/*
			if(($this->pageId || $sub_task == 'new') && ($this->pageMenuItem || $this->menu_item))
			{
			
				echo $this->pagePropertys;
				//at this moment in J1.6 to many errors
				if($this->joomlaVersion < '1.6')
				{
					//echo $this->loadTemplate('pagepropertys');
				}
			}
			*/
			
			
			
			
			?>
			<input type="hidden" id="option" name="option" value="com_pagesanditems" />
			<input type="hidden" id="view" name="view" value="<?php echo $view; ?>" />
			<input type="hidden" id="sub_task" name="sub_task" value="<?php echo $sub_task; ?>" />
			<input type="hidden" id="task" name="task" value="" />
			<input type="hidden" id="subsub_task" name="subsub_task" value="" />
			<input type="hidden" id="menutype" name="menutype" value="<?php echo $menutype; ?>">
			<?php
				if($layout)
				{
					echo '<input type="hidden" id="layout" name="layout" value="'.$layout.'">';
				}
			?>
			<?php
			//dump($this->pageType);
			/*
			<input type="hidden" id="urloption" name="url[option]" value="" />
			<input type="hidden" id="urlview" name="url[view]" value="" />
			<input type="hidden" id="urllayout" name="url[layout]" value="" />
			*/
			?>
			
			<input type="hidden" id="pageTypeType" name="pageTypeType" value="" />
			
			<input type="hidden" id="pageType" name="pageType" value="<?php echo $this->pageType; ?>" />
			<input type="hidden" id="type" name="type" value="" />
			<input type="hidden" id="pageId" name="pageId" value="">
			</form>
		</td>
	</tr>
</table>
</div>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>