<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::_('behavior.tooltip');

$sub_task = JRequest::getVar( 'sub_task', ''); //'edit');
$menutype = JRequest::getVar('menutype', '');
$pageType = JRequest::getVar('pageType', '');
$view = JRequest::getVar( 'view', 'page');
$layout = JRequest::getVar( 'layout', null);
$subsub_task = JRequest::getVar('subsub_task', '');
JHtml::_('behavior.formvalidation');
?>
<?php
	echo $this->reload;
?>
<div id="page_content">
<!-- begin id="form_content" need for css-->
<div id="form_content">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>

		<td valign="top"  class="treeList"> <!--width="20%">-->
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
						if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
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
							document.getElementById('task').value = 'page.page_new'; //pressbutton;
							
							
							//alert('new');
							document.adminForm.submit();
							<?php
							//echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
							//document.adminForm.submit();
						}
						/*
						if (pressbutton == 'page.root_underlayingpages')
						{
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
						}
						*/
						if (pressbutton == 'menutype.root_save')
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
						
						
						if (pressbutton == 'menutype.root_menutype_new')
						{
							//document.getElementById('subsub_task').value = 'menutype';
							document.getElementById('sub_task').value = 'newMenutype';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php
							//echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
						}
						if (pressbutton == 'menutype.root_cancel')
						{
							//document.getElementById('subsub_task').value = 'cancel';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php
							//	echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
						}
						if(document.formvalidator.isValid(document.id('adminForm')))
						{
							var check = true;
						}
						
						if (pressbutton == 'page.root_apply' && check)
						{
							document.getElementById('sub_task').value = 'apply';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
						}
						else if(pressbutton == 'page.root_apply' && !check)
						{
							return false
						}
						
						if (pressbutton == 'menutype.root_menutype_save' && check)
						{
							//document.getElementById('subsub_task').value = 'menutype';
							document.getElementById('sub_task').value = 'save';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php
							//echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
						}
						else if(pressbutton == 'menutype.root_menutype_save' && !check)
						{
							return false
						}
						if (pressbutton == 'menutype.root_menutype_apply' && check)
						{
							//document.getElementById('subsub_task').value = 'menutype';
							document.getElementById('sub_task').value = 'apply';
							document.getElementById('task').value = 'menutype.root_menutype_save';
							document.adminForm.submit();
							<?php
							//echo $joomlaSubmit;
							?>
							//submitform(pressbutton);
						}
						else if(pressbutton == 'menutype.root_menutype_apply' && !check)
						{
							return false
						}
						
						

						<?php
						/*
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
					*/
					?>
					}
					<?php
					/*
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
					*/
					?>
				</script>
			<?php
			//$this->getMenuItem();
			/*
			if($sub_task == 'edit' && $this->pageId)
			{
				//$this->getMenuItem();
			}
			*/
			if($sub_task != 'new' && $sub_task != 'newMenutype')
			{
			?>
			
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td valign="top"class="pagesList"> <!--width="49%" class="pagesList">-->
						<table class="piadminform xadminform" width="98%">

							<?php
							if($sub_task == 'edit' || $sub_task == '')
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
					<!--<td width="2%">&nbsp;
					</td>
					-->
					<td valign="top" class="itemsList" id="itemsPage"> <!-- width="49%" class="itemsList" id="itemsPage">-->
					<?php
					//$dispatcher = &JDispatcher::getInstance();
					//$dispatcher->trigger('onSubLayoutItems',array()); //??
					?>
					<?php
					?>
					</td>
				</tr>
			</table>

			<?php
			}
			//$this->getMenuItem();
			
			if($this->menuItem)
			{
				echo $this->loadTemplate('properties');
			}
			/*
			if($sub_task == 'new' && $subsub_task == '')
			{
				//$this->getMenuItem();
				//echo $this->pagePropertys;
				if($this->isPagePropertys)
				{
					echo $this->loadTemplate('properties');
				}
			}
			*/
			/*
			if(($this->pageId || $sub_task == 'new') && ($this->pageMenuItem || $this->menu_item))
			{

				echo $this->pagePropertys;
				//at this moment in J1.6 to many errors
				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
				{
					//echo $this->loadTemplate('pagepropertys');
				}
			}
			*/

			/*
			add menutype edit
			//TODO RootMenutype
			*/
			if($this->menutypeItem ) //&& PagesAndItemsHelper::getIsSuperAdmin()) // && ($sub_task == 'newMenuitem' || $sub_task != 'new')) )
			{
				echo $this->loadTemplate('menutype');
			}
			/*
			END add menutype edit
			*/






			?>
			<input type="hidden" id="option" 		name="option" value="com_pagesanditems" />
			<input type="hidden" id="view" 		name="view" value="<?php echo $view; ?>" />
			<input type="hidden" id="sub_task" 		name="sub_task" value="<?php echo $sub_task; ?>" />
			<input type="hidden" id="task" 		name="task" value="" />
			<input type="hidden" id="subsub_task" 	name="subsub_task" value="" />
			<input type="hidden" id="menutype" 		name="menutype" value="<?php echo $menutype; ?>">
			<?php
				if($layout)
				{
					echo '<input type="hidden" id="layout" name="layout" value="'.$layout.'">';
				}
			?>
			<?php
			/*
			<input type="hidden" id="urloption" name="url[option]" value="" />
			<input type="hidden" id="urlview" name="url[view]" value="" />
			<input type="hidden" id="urllayout" name="url[layout]" value="" />

			<input type="hidden" id="pageTypeType" name="pageTypeType" value="<?php echo $this->pageTypeType; ?>" />

			*/

			?>

			<input type="hidden" id="pageTypeType" name="pageTypeType" value="<?php echo $this->pageTypeType; ?>" />
			<input type="hidden" id="pageType" name="pageType" value="<?php echo $this->pageType; ?>" />
			<input type="hidden" id="type" name="type" value="<?php echo $this->type; ?>" />
			<input type="hidden" id="pageId" name="pageId" value="<?php echo $this->pageId; ?>">
			<input type="hidden" id="menutypeId" name="menutypeId" value="<?php echo isset($this->menutypeItem->id) ? $this->menutypeItem->id : 0; ?>">
			<?php echo JHtml::_('form.token'); ?>
			</form>
		</td>
	</tr>
</table>
<!-- end id="form_content" need for css-->
</div>
<!-- end id="page_content"-->
</div>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>