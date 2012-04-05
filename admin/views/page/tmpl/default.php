<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
$view = JRequest::getVar( 'view', 'page');
$sub_task = JRequest::getVar( 'sub_task', ''); //'edit');
$menutype = JRequest::getVar('menutype', '');
$pageType = JRequest::getVar('pageType', '');
?>
<?php
	echo $this->reload;
?>
<div id="page_content">
<!-- begin id="form_content" need for css-->
<div id="form_content">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top" class="treeList"> <!--width="20%">-->
		<?php
			//display the page-tree
			//incl. htmls/page_tree
			echo $this->pageTree;
		?>

		</td>
		<td valign="top">
			<form name="adminForm" id="adminForm" method="post" action="">
			<?php
			//if($sub_task == 'edit')
			if($sub_task != 'new')
			{
			?>
				<script language="javascript" type="text/javascript">
				<?php
				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
				{
					$joomlaSubmit = '';
					//echo 'function submitbutton(pressbutton)'."\n";
				?>
					function submitbutton(pressbutton)
				<?php
				}
				else
				{
					$joomlaSubmit = 'Joomla.';
					//echo 'Joomla.submitbutton = function(pressbutton)'."\n";
				?>
					Joomla.submitbutton = function(pressbutton)
				<?php
				}
				?>
					{
						//alert(pressbutton);
						var check = false;
						if(pressbutton == 'newMenuItem')
						{
						<?php
						//TODO also for extensions/htmls/page_childs/menuitemtypeselect
						if($this->useCheckedOut && $sub_task == 'edit')
						{
						?>
							alert('Not in Edit-Mode');
							return;
						<?php
						}
						?>
							
							//document.getElementById('page_reload').style.display  = 'block';
							document.getElementById('sub_task').value = 'new';
							document.getElementById('task').value = 'page.page_new'; //pressbutton;
							document.adminForm.submit();
							<?php
							if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
							{
								echo 'document.adminForm.submit();'."\n";
							}
							else
							{
								echo "Joomla.submitform('page.page_new',document.getElementById('adminForm'));"."\n";
								//echo 'Joomla.submitform();'."\n";
							}
							?>
							return;
						}
						<?php
						if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
						{
							echo 'check=true;'."\n";
						}
						else
						{
						?>
						if (pressbutton == 'page.cancel')
						{
							document.getElementById('sub_task').value = 'edit';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							<?php
							//echo $joomlaSubmit;
							?>
							//submitform( pressbutton );
							return;
						}
						if(document.formvalidator.isValid(document.id('adminForm')))
						{
							var check = true;
						}
						<?php
						}
						?>
						//alert(check);
						
						
						if (pressbutton == 'page_move_select')
						{
							document.location.href = 'index.php?option=com_pagesanditems&view=page_move_select&pageId='+<?php echo $this->pageId; ?>+'&menutype='+'<?php echo $this->menutype; ?>';
							return;
						}
						if (pressbutton == 'page.page_delete')
						{

							are_you_sure = '<?php
							$confirm_delete = addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE2')).'?'.'\n\n';
							$confirm_delete .= addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_DELETE1')).':\n';
							$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4')).'\n';
							//if($this->helper->config['page_delete_cat']){
							if(PagesAndItemsHelper::getConfigAsRegistry()->get('page_delete_cat',0)){
								$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5')).'\n';
							}
							//if($this->helper->config['page_delete_items']){
							if(PagesAndItemsHelper::getConfigAsRegistry()->get('page_delete_items',0)){
								$confirm_delete .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6')).'\n';
							}
							echo $confirm_delete;
							?>';
							if(confirm(are_you_sure)){

								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
								<?php //echo $joomlaSubmit;?>//submitform('page.page_delete');
							}
						}
						if (pressbutton == 'page.page_trash'){
							are_you_sure = '<?php
							$confirm_trash = addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH2')).'?'.'\n\n';
							$confirm_trash .= addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH3')).':\n';
							$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH4')).'\n';
							//if($this->helper->config['page_trash_cat']){
							if(PagesAndItemsHelper::getConfigAsRegistry()->get('page_trash_cat',0)){
								$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH5')).'\n';
							}
							//if($this->helper->config['page_trash_items']){
							if(PagesAndItemsHelper::getConfigAsRegistry()->get('page_trash_items',0)){
								$confirm_trash .= '- '.addslashes(JText::_('COM_PAGESANDITEMS_IF_PAGE')).' '.addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGE_TRASH6')).'\n';
							}
							echo $confirm_trash;
							?>';
							if(confirm(are_you_sure)){
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
							}
						}

						/*
						if (document.adminForm.boxcheckedItem.value==0)
						{
							alert('<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>');
							}else{
						*/
						<?php
						/*
							if (pressbutton == 'item.items_archive'){
								are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_ITEMS_ARCHIVE')).'.'; ?>';
								if(confirm(are_you_sure)){
									document.getElementById('sub_task').value = 'archive';
									document.getElementById('task').value = 'item.items_state';
									document.adminForm.submit();
								}
							}
							if (pressbutton == 'item.items_trash'){
								are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_ITEMS_TRASH')).'.'; ?>';
								if(confirm(are_you_sure)){
									document.getElementById('sub_task').value = 'trash';
									document.getElementById('task').value = 'item.items_state';
									document.adminForm.submit();
								}
							}
							if (pressbutton == 'item.items_delete'){
								are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_ITEMS_DELETE')).'.'; ?>';
								if(confirm(are_you_sure)){
									document.getElementById('sub_task').value = 'delete';
									document.getElementById('task').value = 'item.items_state';
									document.adminForm.submit();
								}
							}
							if (pressbutton == 'item.items_publish'){
								document.getElementById('sub_task').value = 'publish';
								document.getElementById('task').value = 'item.items_state';
								document.adminForm.submit();
							}
							if (pressbutton == 'item.items_unpublish'){
								document.getElementById('sub_task').value = 'unpublish';
								document.getElementById('task').value = 'item.items_state';
								document.adminForm.submit();
							}
						*/
						?>
						//}
						<!-- ms: add -->
						//if (document.adminForm.boxcheckedIPage.value==0)
						/*
						if (document.adminForm.boxcheckedPage.value==0)
						{
							alert('<?php echo JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'); ?>');
							//ce: why did you add this message to all four buttons instead of having that in 1 place?
						}else{
						*/
						<?php
						/*
							if (pressbutton == 'page.pages_archive'){
								are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGES_ARCHIVE')).'.'; ?>';
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
								are_you_sure = '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_CONFIRM_PAGES_DELETE')).'.'; ?>';
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
						//}
						*/
						?>
						<!-- ms: end add -->
						
						if (pressbutton == 'page.page_edit')
						{
							document.getElementById('sub_task').value = 'edit';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							return;
						}
						
						if (pressbutton == 'page.page_checkin')
						{
							if ( document.adminForm.name && document.adminForm.name.value == '' )
							{
								alert( '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_NEED_TITLE')); ?>' );
								return;
							}
							else if(!check)
							{
								return;
							}
							else
							{
								document.getElementById('sub_task').value = 'edit';
								document.getElementById('subsub_task').value = 'apply';
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
							}
						}
						
						if (pressbutton == 'page.reorder_save')
						{
							document.getElementById('subsub_task').value = 'save';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
						}
						if (pressbutton == 'page.reorder_apply')
						{
							//alert('apply');
							document.getElementById('subsub_task').value = 'apply';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
						}
						
						
						
						
						if (pressbutton == 'page.page_save')
						{
							if ( document.adminForm.name && document.adminForm.name.value == '' )
							{
								alert( '<?php echo addslashes(JText::_('COM_PAGESANDITEMS_NEED_TITLE')); ?>' );
								return;
							}
							else if(!check)
							{
								return;
							}
							else
							{
								//alert('save');
								//alert('this function must do rewrite');
								//return;
								document.getElementById('subsub_task').value = 'save';
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
								<?php
								//echo $joomlaSubmit;
								?>
								//submitform(pressbutton);
							}
						}
						if (pressbutton == 'page.page_apply')
						{
							if ( document.adminForm.name && document.adminForm.name.value == '' )
							{
								alert( '<?php echo JText::_('COM_PAGESANDITEMS_NEED_TITLE'); ?>' );
								return;
							}
							else if(!check)
							{
								return;
							}
							else
							{
								//alert('apply');
								document.getElementById('subsub_task').value = 'apply';
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
								//alert('apply1');
								<?php
								//echo $joomlaSubmit;
								?>
								//submitform('page.page_apply');
								//alert('apply1');
							}
						}
					}

					function new_item(page_id){

						/*
						pageType = document.getElementById('pageType').value;
						itemtype = document.getElementById('select_itemtype').value;
						categoryId = document.getElementById('categoryId').value;
						menutype = document.getElementById('menutype').value;
						*/
						
						document.getElementById('task').value = 'item.item_new';
						document.adminForm.submit();
						//document.location.href = 'index.php?option=com_pagesanditems&view=item&sub_task=new&pageType='+pageType+'&pageId='+page_id+'&item_type='+itemtype+'&categoryId='+categoryId+'&menutype='+menutype;
					}
					<?php
					/*
					function publish_unpublish_item(item_id, new_state){

						//unselect all checkboxes
						for (i = 0; i < item_ids.length; i++){
							box_id = 'itemCid_'+item_ids[i];
							if(document.getElementById(box_id)){
								document.getElementById(box_id).checked = false;
							}
						}

						//select the checkbox we need
						box_id = 'itemCid_'+item_id;
						document.getElementById(box_id).checked = 'checked';

						if(new_state=='1'){
							sub_task = 'publish';
						}else{
							sub_task = 'unpublish';
						}

						//submit
						document.getElementById('sub_task').value = sub_task;
						document.getElementById('task').value = 'item.items_state';
						//alert('item');
						document.adminForm.submit();
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
					*/
					?>
				</script>
			<?php
			}
			else
			{
			?>
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
						if(pressbutton == 'newMenuItem')
						{
							//alert('newMenuItemNew');
							document.getElementById('sub_task').value = 'new';
							document.getElementById('task').value = 'page.page_new'; //pressbutton;
							document.adminForm.submit();
							
							<?php
							if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
							{
								echo 'document.adminForm.submit();'."\n";
							}
							else
							{
								echo 'Joomla.submitform(\'page.page_new\');'."\n";
								//echo 'Joomla.submitform();'."\n";
							}
							?>
							return;
						}
						var check = false;
						<?php
						if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
						{
							echo 'check=true;'."\n";
						}
						else
						{
						?>

						<?php
						}
						?>
						if (pressbutton == 'page.cancel')
						{
							document.getElementById('sub_task').value = 'edit';
							//document.getElementById('task').value = pressbutton;
							//document.adminForm.submit();
							<?php echo $joomlaSubmit;?>submitform( pressbutton, document.getElementById('adminForm' ));
							return;
						}
						if(document.formvalidator.isValid(document.id('adminForm')))
						{
							var check = true;
						}
						if (pressbutton == 'page.page_save')
						{
							if ( document.adminForm.name && document.adminForm.name.value == '' )
							{
								alert( '<?php echo JText::_('COM_PAGESANDITEMS_NEED_TITLE'); ?>' );
								return;
							}
							else if(!check)
							{
								return;
							}
							else
							{

								//alert('this function must do rewrite');
								//return;
								document.getElementById('subsub_task').value = 'save';
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
								<?php
								//echo $joomlaSubmit;
								?>
								//submitform(pressbutton);
							}
						}
						if (pressbutton == 'page.page_apply')
						{
							if ( document.adminForm.name && document.adminForm.name.value == '' )
							{
								alert( '<?php echo JText::_('COM_PAGESANDITEMS_NEED_TITLE'); ?>' );
								return;
							}
							else if(!check)
							{
								return;
							}
							else
							{
								document.getElementById('subsub_task').value = 'apply';
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
								<?php
								//echo $joomlaSubmit;
								?>
								//submitform('page.page_apply');
							}
						}
					}


				</script>
			<?php
			}

			//$this->getMenuItem();
			//if($sub_task == 'edit')
			if($sub_task != 'new')
			{
			//	if($this->pageId)
			//	{
			//		$this->getMenuItem();
			//	}
			?>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td valign="top" class="pagesList"> <!--width="49%" class="pagesList">-->
						<table class="piadminform xadminform" width="98%">
						<?php
							//display the underlayingpages/childs
							//incl. htmls/page_childs
							//echo $this->loadTemplate('underlayingpages');
							echo $this->pageChilds;
							//echo $this->loadTemplate('childs');
						?>
						</table>
					</td>
					<!--<td width="2%">&nbsp;
					</td>
					-->
					<td valign="top" class="itemsList" id="itemsPage"> <!-- width="49%" class="itemsList" id="itemsPage">-->

					<?php
						//display the pageItems
						//incl. htmls/page_items
						//$dispatcher = &JDispatcher::getInstance();
						//$dispatcher->trigger('onGetPageItems',array(&$html,$this));
						//echo $html;
						echo $this->pageItems;

					?>
					</td>
				</tr>
			</table>
			<?php
			}

			//if($sub_task == 'new')
			//{
			//	$this->getMenuItem();
			//}


			//mootools script to hide fields and panels as set in PI config
			$fields_to_hide = array();
			
			$all_fields = PagesAndItemsHelper::get_all_page_fields();//$this->helper->get_all_page_fields();
			
			foreach($all_fields as $field){
				$field_right = $field[0];
				$field_id = $field[1];
				$field_type = $field[4];
				//if(!$this->helper->check_display($field_right) && $field_type!='menutype'){
				if(!PagesAndItemsHelper::check_display($field_right) && $field_type!='menutype'){
					$fields_to_hide[] = $field_id;
				}
			}
			if(count($fields_to_hide)){
				echo '<script>'."\n";
				echo 'var fields_array = new Array(';
				$first = 1;
				foreach($fields_to_hide as $field_to_hide){
					if(!$first){
						echo ',';
					}else{
						$first = 0;
					}
					echo '"';
					echo $field_to_hide;
					echo '"';
				}
				echo ');'."\n";
				echo 'window.addEvent(\'domready\', function() {'."\n";
					echo 'for (i = 0; i < fields_array.length; i++){'."\n";
						echo 'var myElement = document.id(fields_array[i]);'."\n";
						echo 'if(myElement){';
						echo 'var parent = myElement.getParent();'."\n";
						//echo 'parent.style.display = \'none\';'."\n";
						echo 'parent.addClass(\'display_none\');'."\n";
						echo'}';

					echo '}'."\n";
				echo '});'."\n";

				echo '</script>'."\n";
			}


			//echo $this->pagePropertys;
			
			if($this->isPagePropertys)
			//if($this->menuItem)
			{
				echo $this->loadTemplate('properties');
			}
			
			/*
			if(($this->pageId || $sub_task == 'new') && ($this->pageMenuItem || $this->menu_item))
			{

				//at this moment in J1.6 to many errors
				if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
				{
					//COMMENT this will subTemplate we use in root to?
					//echo $this->loadTemplate('pagepropertys');
				}

			}
			*/
			?>

			<input type="hidden" id="option" name="option" value="com_pagesanditems" />
			<input type="hidden" id="view" name="view" value="<?php echo $view; ?>" />
			<!--<input type="hidden" id="item_type" name="item_type" value="" />-->
			<input type="hidden" id="sub_task" name="sub_task" value="<?php echo $sub_task; ?>" />
			<input type="hidden" id="task" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" id="subsub_task" name="subsub_task" value="" />
			<input type="hidden" id="menutype" name="menutype" value="<?php echo $menutype; ?>" />

			<input type="hidden" id="pageTypeType" name="pageTypeType" value="<?php echo $this->pageTypeType; ?>" />
			<input type="hidden" id="pageType" name="pageType" value="<?php echo $this->pageType; ?>" />
			<input type="hidden" id="type" name="type" value="<?php echo $this->type; ?>" />


			<input type="hidden" id="pageId" name="pageId" value="<?php echo $this->pageId; ?>" />
			<input type="hidden" id="categoryId" name="categoryId" value="<?php echo JRequest::getVar('categoryId',0); ?>" />
			<?php echo JHtml::_('form.token'); ?>
			<?php
			/*
			<input type="hidden" id="url" name="url" value="" /> ?
			<input type="hidden" name="pageParent" value="<?php echo $pageParent; ?>">

			<input type="hidden" name="mosmsg" value="">

			TODO <input type="hidden" name="cat_id" value="<?php echo $cat_id;?>">
			TODO <input type="hidden" name="section_id_current" value="<?php echo $section_id;?>">
			TODO <input type="hidden" name="default_home_current" value="<?php echo $default_home;?>">
			*/
			?>
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
/*
<div name="display_footer" class="pages_display_footer" style="float: left;position: relative;width: 100%;">
	<?php
		//the stylesheets must load into document not header so no other stylesheets can  override it
		echo "<link href=\"components/com_pagesanditems/css/pagesanditems2.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
		echo "<link href=\"components/com_pagesanditems/css/dtree.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
		echo $this->baseModel->display_footer();
		//$this->loadTemplate('underlayingpages'); loadtemplate
	?>
</div>
*/
?>