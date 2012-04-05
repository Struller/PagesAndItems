<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/


defined('_JEXEC') or die('Restricted access');?>
<?php
$sub_task = JRequest::getVar( 'sub_task', '');
			if($sub_task == 'edit')
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
						var check = false;
						if(pressbutton == 'category.category_create')
						{
							//alert('new');
							document.getElementById('sub_task').value = 'new';
							document.getElementById('parentCategoryId').value = document.getElementById('categoryId').value;
							document.getElementById('categoryId').value = 0;
							<?php
							if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
							{
								echo 'document.adminForm.submit();'."\n";
							}
							else
							{
								echo 'Joomla.submitform(pressbutton);'."\n";
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
						if(document.formvalidator.isValid(document.id('adminForm')))
						{
							var check = true;
						}
						<?php
						}
						?>
						if (pressbutton == 'category.cancel')
						{
							//document.getElementById('sub_task').value = 'edit';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							return;
						}
						
						//TODO 
						if (pressbutton == 'category.category_delete')
						{
							//return;
							are_you_sure = '<?php
							$confirm_delete = addslashes(JText::_('COM_PAGESANDITEMS_SURE_DELETE_CATEGORY')).'?';
							echo $confirm_delete;
							?>';
							
							if(confirm(are_you_sure)){
								
								//categories_state
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
							}
						}
						if (pressbutton == 'category.category_trash'){
							return;
							are_you_sure = '<?php
							$confirm_trash = addslashes(JText::_('COM_PAGESANDITEMS_SURE_TRASH_CATEGORY')).'?';
							echo $confirm_trash;
							?>';
							if(confirm(are_you_sure)){
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
							}
						}
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
						//}
						<!-- ms: add -->
							
						//}
						<!-- ms: end add -->
						if (pressbutton == 'category.category_checkin')
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
						
						
					//TODO
						if (pressbutton == 'category.category_save')
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
						//TODO
						if (pressbutton == 'category.category_apply')
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
								document.getElementById('task').value = 'category.category_save';
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

						document.getElementById('task').value = 'item.item_new';
						
						document.adminForm.submit();
						
						itemtype = document.getElementById('select_itemtype').value;
						categoryId = document.getElementById('categoryId').value;
						document.location.href = 'index.php?option=com_pagesanditems&view=item&sub_task=new&item_type='+itemtype+'&categoryId='+categoryId;

					}
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
					*/
					/*
					function publish_unpublish_categorie(page_id, new_state){
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
						document.getElementById('task').value = 'categorie.categories_state';
						document.adminForm.submit();
					}
					*/
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
						//alert(pressbutton);
						if(pressbutton == 'category.root_save')
						{
							//document.getElementById('sub_task').value = 'new';
							<?php
							if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
							{
								echo 'document.getElementById(\'task\').value = pressbutton;'."\n";
								echo 'document.adminForm.submit();'."\n";
							}
							else
							{
								echo 'document.getElementById(\'task\').value = pressbutton;'."\n";
								echo 'document.adminForm.submit();'."\n";
								echo 'Joomla.submitform();'."\n";
							}
							?>
							return;
						}
						
						if(pressbutton == 'category.category_create')
						{
							document.getElementById('sub_task').value = 'new';
							document.getElementById('task').value = pressbutton;
							
							<?php
							if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
							{
								echo 'document.adminForm.submit();'."\n";
							}
							else
							{
								echo 'Joomla.submitform();'."\n";
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
						//alert(pressbutton);
						if (pressbutton == 'category.category_edit')
						{
							document.getElementById('sub_task').value = 'edit';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
							return;
						}
						
						if (pressbutton == 'category.reorder_save')
						{
							document.getElementById('subsub_task').value = 'save';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
						}
						if (pressbutton == 'category.reorder_apply')
						{
							//alert('apply');
							document.getElementById('subsub_task').value = 'apply';
							document.getElementById('task').value = pressbutton;
							document.adminForm.submit();
						}
						
						
						if (pressbutton == 'category.cancel')
						{
							<?php echo $joomlaSubmit;?>submitform( pressbutton, document.getElementById('adminForm' ));
							return;
						}
						if(document.formvalidator.isValid(document.id('adminForm')))
						{
							var check = true;
						}
						//TODO
						if (pressbutton == 'category.category_save')
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
								document.getElementById('subsub_task').value = 'save';
								document.getElementById('task').value = pressbutton;
								document.adminForm.submit();
							}
						}
						//TODO
						if (pressbutton == 'category.category_apply')
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
								document.getElementById('task').value = 'category.category_save';
								document.adminForm.submit();
							}
						}
					}
					function new_item(page_id){
						document.getElementById('task').value = 'item.item_new';
						document.adminForm.submit();
							
						itemtype = document.getElementById('select_itemtype').value;
						categoryId = document.getElementById('categoryId').value;
						document.location.href = 'index.php?option=com_pagesanditems&view=item&sub_task=new&item_type='+itemtype+'&categoryId='+categoryId;
					}

				</script>
			<?php
			}
			?>
<script language="javascript" type="text/javascript">
function change_extension()
{
	document.getElementById('task').value = 'category.change_extension';
	document.adminForm.submit();
}
</script>

<div id="page_content">
<!-- begin id="form_content" need for css-->
<div id="form_content">
<form action="" method="post" id="adminForm" name="adminForm">
<?php
$html = '';
	$html .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
		$html .= '<tbody>';
			$html .= '<tr>';
				$html .= '<td valign="top" class="treeList">'; //width="20%" valign="top">';
				$style = '';
				if($this->hideTree)
				{
					$style = 'style="display:none;"';
				}
				
					$html .= '<div class="page_tree" '.$style.'>';
					
					
			
						
						

						$html .= '<div class="dtree dtree_container">';
							$html .= '<div class="dtree">';
								$html .= $this->languageSelect;
							$html .= '</div>';
							$html .= '<div class="dtree">';
								$html .= $this->inputCategoryExtension ? $this->inputCategoryExtension : '';
							$html .= '</div>';
							$html .= '<div class="dtree">';
								$html .= '<div id="tree_container">';
								$html .= '</div>';
							$html .= '</div>';
						$html .= '</div>';
					
					$html .= '</div>';
				
				$html .= '</td>';
				
				$html .= '<td valign="top">';
					//$html .= '<form action="" method="post" id="adminForm" name="adminForm">';
					if($sub_task != 'new')
					{
						$html .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
							$html .= '<tbody>';
								$html .= '<tr>';
									$html .= '<td valign="top" class="pagesList">'; //width="49%" class="pagesList">-->width="49%" valign="top">';
										$html .= '<table width="98%" class="piadminform xadminform">';
											$html .= $this->childs;
										$html .= '</table>';
									$html .= '</td>';
									//$html .= '<td width="2%">&nbsp;';
									//$html .= '</td>';
									$html .= '<td valign="top" class="itemsList" id="itemsPage">'; //width="49%" valign="top">';
										$html .= '<div id="item_container"></div>';
										if($this->categoryItems )
										{
											$html .= '<table width="98%" class="piadminform xadminform">';
												$html .= '<thead class="piheader">';
													$html .= '<tr>';
														$html .= '<th>';// class="piheader">';//style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
															//$html .= '<img style="vertical-align: middle;position: relative;" alt="" src="'.PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png">';
															//$html .= '&nbsp;';
															//$html .= JText::_('COM_PAGESANDITEMS_ITEMS_ON_CATEGORY');
															$image = isset($icons->default->imageUrl) ? $icons->default->imageUrl : PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
															$html .= PagesAndItemsHelper::getThImageTitle($image,JText::_('COM_PAGESANDITEMS_ITEMS_ON_CATEGORY'));
															
														$html .= '</th>';
													$html .= '</tr>';
												$html .= '</thead>';
												$html .= '<tbody>';
													$html .= '<tr>';
														$html .= '<td>';
															$html .= $this->categoryItems;
														$html .= '</td>';
													$html .= '</tr>';
												$html .= '</tbody>';
											$html .= '</table>';
										}
										$html .= '</div>';

									$html .= '</td>';
								$html .= '</tr>';
							$html .= '</tbody>';
						$html .= '</table>';
						}
						echo $html;

								$categoryId = JRequest::getVar('categoryId',1);
								
								$sub_task = JRequest::getVar('sub_task',($this->useCheckedOut ? '' :'edit'));

		$checkedOutText = '';
		if(($categoryId > 1 ) && $this->useCheckedOut && ( !$this->canCheckin || !$this->canEdit || $sub_task ==''))// ($sub_task !='new')))// && $sub_task !=='edit')))
		{
			//$user		= JFactory::getUser();

			
			// Join over the users.
			$db		= JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->select('a.*');
			$query->from('`#__categories` AS a');
			$query->select('u.name AS editor');
			$query->join('LEFT', '`#__users` AS u ON u.id = a.checked_out'); //'.$userId); //a.checked_out');
			$query->where("a.id = '".$this->item->id."'");
			$db->setQuery($query);
			$result = $db->loadObject();
			if ($this->item->checked_out)
			{
				$checkedOutText .= '<input type="hidden" value="0" id="boxchecked" >';
				$checkedOutText .= '<input type="hidden" title="" onclick="isChecked(this.checked);" value="'.$this->item->id.'" name="cid[]" id="cb0" >';
				$checkedOutText .= JHtml::_('jgrid.checkedout', 0, $result->editor, $this->item->checked_out_time, 'category.', $this->canCheckin);
			//$checkedOutText
			};
			
			echo '<script>'."\n";
			echo 'window.addEvent(\'domready\', function() {'."\n";
					echo 'document.id(\'category_options\').addClass(\'display_none\');'."\n";
			echo '});'."\n";
			echo '</script>'."\n";


			//$this->form->setFieldAttribute('type','type','text');
			//$this->form->setFieldAttribute('type','class','readonly');
			//$this->form->setFieldAttribute('type','readonly','true');

			$this->form->setFieldAttribute('title','class','readonly');
			$this->form->setFieldAttribute('title','readonly','true');

			$this->form->setFieldAttribute('alias','class','readonly');
			$this->form->setFieldAttribute('alias','readonly','true');

			$this->form->setFieldAttribute('note','class','readonly');
			$this->form->setFieldAttribute('note','readonly','true');

			$this->form->setFieldAttribute('link','class','readonly');
			$this->form->setFieldAttribute('link','readonly','true');

			$this->form->setFieldAttribute('published','readonly','true');

			//$this->form->setFieldAttribute('access','readonly','true');
			$this->form->setFieldAttribute('access','disabled','true');
		
			//$this->form->setFieldAttribute('menutype','readonly','true');
			$this->form->setFieldAttribute('parent_id','readonly','true');
			//$this->form->setFieldAttribute('browserNav','readonly','true');
			
			$this->form->setFieldAttribute('language','readonly','true');
			
			$this->form->setFieldAttribute('description','type','textarea');
			$this->form->setFieldAttribute('description','disabled','true');
			//$this->form->setFieldAttribute('description','readonly','true');
		}


								
								if($categoryId > 1 || $sub_task == 'new')
								{
								?>
								<!-- the category propertys -->
								<table width="98%" class="piadminform xadminform">
									<thead class="piheader">
										<tr>
											<th> <!-- class="piheader"> style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">-->
												<?php 
												/*	
												<div style="margin-left: 4px;float: left;left: 0;position: relative;vertical-align: middle;">
													<?php echo '<img style="vertical-align: middle;position: relative;" alt="" src="'.PagesAndItemsHelper::getDirIcons().'category/icon-16-category_edit.png">'; ?>
													<?php echo '&nbsp;'; ?>
												</div>
												<?php 
													echo JText::_('COM_PAGESANDITEMS_CATEGORY_PROPERTIES'); 
												*/
												?>
												<?php 
												$image2 = '';
												if($sub_task == 'new')
												{
													if(isset($this->icons->new->imageUrl))
													{
														$image = $this->icons->new->imageUrl;
													}
													else
													{
														$image = isset($this->icons->default->imageUrl) ? $this->icons->default->imageUrl : PagesAndItemsHelper::getDirIcons().'category/icon-16-category_new.png';
														$image2 = PagesAndItemsHelper::getDirIcons().'base/bullets/icon-16-bullet_new.png';
													}
													//$image = isset($this->icons->new->imageUrl) ? $this->icons->new->imageUrl : PagesAndItemsHelper::getDirIcons().'category/icon-16-category_new.png';
													
													//$image = PagesAndItemsHelper::getDirIcons().'category/icon-16-category_new.png';
												}
												elseif($sub_task == 'edit')
												{
													//$image = isset($this->icons->edit->imageUrl) ? $this->icons->edit->imageUrl : PagesAndItemsHelper::getDirIcons().'category/icon-16-category_edit.png';
													//$image = PagesAndItemsHelper::getDirIcons().'category/icon-16-category_edit.png';
													if(isset($this->icons->edit->imageUrl))
													{
														$image = $this->icons->edit->imageUrl;
													}
													else
													{
														$image = isset($this->icons->default->imageUrl) ? $this->icons->default->imageUrl : PagesAndItemsHelper::getDirIcons().'category/icon-16-category_new.png';
														$image2 = PagesAndItemsHelper::getDirIcons().'base/bullets/icon-16-bullet_edit.png';
													}
												}
												else
												{
													$image = isset($this->icons->default->imageUrl) ? $this->icons->default->imageUrl : PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
													//$image = PagesAndItemsHelper::getDirIcons().'category/icon-16-category_edit.png';
												}
												echo PagesAndItemsHelper::getThImageTitle($image,$checkedOutText.JText::_('COM_PAGESANDITEMS_CATEGORY_PROPERTIES'),$image2); ?>
											</th>
										</tr>
										</thead>
										<tbody>
										
										<tr>
										<td>

										<div class="width-100 fltlft">
											<fieldset class="adminform">
												<legend><?php echo JText::_('COM_CATEGORIES_FIELDSET_DETAILS');?></legend>
												<ul class="adminformlist">
													<li><?php echo $this->form->getLabel('title'); ?>
													<?php echo $this->form->getInput('title'); ?></li>

													<li><?php echo $this->form->getLabel('alias'); ?>
													<?php echo $this->form->getInput('alias'); ?></li>
													
													
													
													<li><?php echo $this->form->getLabel('extension'); ?>
													<?php //$this->form->setValue('extension',null,'com_content'); ?>
													<?php echo $this->form->getInput('extension'); ?></li>
													<?php //need for J2.5 ?>
													<?php $this->form->setFieldAttribute('parent_id','extension',(strpos($this->categoryExtension,'com_') !== false) ? strtolower($this->categoryExtension) : 'com_'.strtolower($this->categoryExtension)); ?>
													<li><?php echo $this->form->getLabel('parent_id'); ?>
													<?php echo $this->form->getInput('parent_id'); ?></li>

													<li><?php echo $this->form->getLabel('published'); ?>
													<?php echo $this->form->getInput('published'); ?></li>

													<li><?php echo $this->form->getLabel('access'); ?>
													<?php echo $this->form->getInput('access'); ?></li>

													<?php if($this->useCheckedOut && ( !$this->canCheckin || !$this->canEdit || $sub_task =='')) : ?>
													<?php //else; ?>
													<?php elseif ($this->canDo->get('core.admin') ): ?>
													<li><span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
														<div class="button2-left">
															<div class="blank">
																<button type="button" onclick="document.location.href='#access-rules';">
																<?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?></button>
															</div>
														</div>
													</li>
													<?php endif; ?>
													

													<li><?php echo $this->form->getLabel('language'); ?>
													<?php echo $this->form->getInput('language'); ?></li>

													<li><?php echo $this->form->getLabel('id'); ?>
													<?php echo $this->form->getInput('id'); ?></li>
												</ul>
												<?php //if($this->useCheckedOut && ( !$this->canCheckin || !$this->canEdit || $sub_task =='')) : ?>
												<?php //else : ?>
												<div class="clr"></div>
												<?php echo $this->form->getLabel('description'); ?>
												<div class="clr"></div>
												<?php echo $this->form->getInput('description'); ?>
												<?php //endif; ?>
											</fieldset>
										</div>
										<?php
										$html = '<!-- Manager Section-->';
										//if($categoryId)
										//{
											$managerOtherItemEdit = new JObject();
											$managerOtherItemEdit->text = '';
											$new_or_edit = ($sub_task == 'new') ? 0 : 1;
											$params = null;
											$dispatcher = &JDispatcher::getInstance();
											//$dispatcher->trigger('onGetParams',array(&$params, $item_type));
											$path = JPATH_COMPONENT_ADMINISTRATOR;//realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
											require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
											$extensions = ExtensionManagerHelper::importExtension(null,null, true,null,true);
											$dispatcher->trigger('onManagerOtherItemEdit', array (&$managerOtherItemEdit,'categories',$categoryId,$params,$new_or_edit));
											if($managerOtherItemEdit->text != '')
											{
												$html .= '<div class="width-100 fltrt">'; //width-100 fltlft">';
													$html .= $managerOtherItemEdit->text;
												$html .= '</div>';
											}
											echo $html;
										//}
										
										?>
										<!-- Options Section-->
										<div id="category_options" class="width-100 fltlft">
										<fieldset class="adminform">
										<legend><?php echo JText::_('JFIELD_PARAMS_LABEL');?></legend>
										<div class="width-100 fltlft">
										
											<?php echo JHtml::_('sliders.start','categories-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
											<?php echo $this->loadTemplate('options'); ?>
											<div class="clr"></div>
									
												<?php echo JHtml::_('sliders.panel',JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options'); ?>
													<fieldset class="panelform">
														<?php echo $this->loadTemplate('metadata'); ?>
												</fieldset>

											<?php echo JHtml::_('sliders.end'); ?>
										</div>
										<div class="clr"></div>
										<?php if ($this->canDo->get('core.admin')): ?>
										
										<div  class="width-100 fltlft">

											<?php echo JHtml::_('sliders.start','permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

											<?php echo JHtml::_('sliders.panel',JText::_('COM_CATEGORIES_FIELDSET_RULES'), 'access-rules'); ?>
											<fieldset class="panelform">
												<?php echo $this->form->getLabel('rules'); ?>
												<?php echo $this->form->getInput('rules'); ?>
											</fieldset>
											<?php echo JHtml::_('sliders.end'); ?>
										</div>
										<?php endif; ?>
										</fieldset>
										</div>
										
									</td>
								</tr>
							</tbody>
						</table>
						<?php
						}
						?>

						<input type="hidden" name="option" value="com_pagesanditems" />
						<input type="hidden" id="task" name="task" value="" />
						<input type="hidden" id="sub_task" name="sub_task" value="<?php echo $sub_task; ?>" />
						<input type="hidden" id="subsub_task" name="subsub_task" value="" />
						<input type="hidden" id="view" name="view" value="category" />
						<input type="hidden" id="categoryId" name="categoryId" value="<?php echo $this->categoryId; ?>" />
						<input type="hidden" id="parentCategoryId" name="parentCategoryId" value="<?php echo $this->parentCategoryId; ?>" />
						
						<input type="hidden" id="return" name="return" value="<?php echo $this->return; ?>" />
						<!--<input type="hidden" id="categoryExtension" name="categoryExtension" value="<?php echo $this->categoryExtension; ?>" />-->
						<input type="hidden" id="hideTree" name="hideTree" value="<?php echo $this->hideTree; ?>" />
						
						<?php echo JHtml::_('form.token'); ?>
					<!--</form>-->
				</td>
			</tr>
		</tbody>
	</table>
</form>
<!-- end id="form_content" need for css-->
</div>
<!-- end id="page_content"-->
</div>


<?php

	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>