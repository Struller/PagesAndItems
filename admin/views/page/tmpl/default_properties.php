<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die;
?>
<?php
		$html = '';
		$sub_task = JRequest::getVar( 'sub_task', '');
		$lang = &JFactory::getLanguage();
		//$modelMenu = $this->modelMenu;


		
		$coreEditText = '';
		$checkedOutText = '';
		//TODO 
		//JRequest::setVar('hidemainmenu', true);
		
		$db = JFactory::getDbo();

		if($this->useCheckedOut && ( !$this->canCheckin || !$this->canEdit || $sub_task ==''))// ($sub_task !='new')))// && $sub_task !=='edit')))
		{
			//$user		= JFactory::getUser();
			// Join over the users.
			$db		= JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->select('a.*');
			$query->from('`#__menu` AS a');
			$query->select('u.name AS editor');
			$query->join('LEFT', '`#__users` AS u ON u.id = a.checked_out'); //'.$userId); //a.checked_out');
			$query->where("a.id = '".$this->menuItem->id."'");
			$db->setQuery($query);
			$result = $db->loadObject();
			if ($this->menuItem->checked_out)
			{
				$checkedOutText .= '<input type="hidden" value="0" id="boxchecked" >';
				$checkedOutText .= '<input type="hidden" title="" onclick="isChecked(this.checked);" value="'.$this->menuItem->id.'" name="cid[]" id="cb0" >';
				$checkedOutText .= JHtml::_('jgrid.checkedout', 0, $result->editor, $this->menuItem->checked_out_time, 'page.', $this->canCheckin);
			};
			
			//hide all panels if (!$this->canDoMenu->get('core.edit')) || $this->menuItem->checked_out)
			if(!$this->canDoMenu->get('core.edit'))
			{
				//$coreEditText .= 'not core.edit';
			}
			if(!$this->menuItem->checked_out) //if(!$this->menuItem->checked_out)
			{
				//$checkedOutText .= ' not checked_out';
			}
			//set all to readonly and hide Panels
			//mootools script to hide panels
			$fields_to_hide = array();
			
			$all_fields = PagesAndItemsHelper::get_all_page_fields();//$this->helper->get_all_page_fields();
			
			foreach($all_fields as $field){
				$field_right = $field[0];
				$field_id = $field[1];
				$field_type = $field[4];
				if($field_type =='panel')
				{
					$fields_to_hide[] = $field_id;
				}
			}
			if(count($fields_to_hide)){
				echo '<script>'."\n";
				echo 'var panels_array = new Array(';
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
					echo 'for (i = 0; i < panels_array.length; i++){'."\n";
						echo 'var myElement = document.id(panels_array[i]);'."\n";
						echo 'if(myElement){';
						echo 'var parent = myElement.getParent();'."\n";
						echo 'parent.addClass(\'display_none\');'."\n";
						echo'}';

					echo '}'."\n";
				echo '});'."\n";

				echo '</script>'."\n";
			}

			$this->form->setFieldAttribute('type','type','text');
			$this->form->setFieldAttribute('type','class','readonly');
			$this->form->setFieldAttribute('type','readonly','true');

			$this->form->setFieldAttribute('title','class','readonly');
			$this->form->setFieldAttribute('title','readonly','true');

			$this->form->setFieldAttribute('alias','class','readonly');
			$this->form->setFieldAttribute('alias','readonly','true');

			$this->form->setFieldAttribute('note','class','readonly');
			$this->form->setFieldAttribute('note','readonly','true');

			$this->form->setFieldAttribute('link','class','readonly');
			$this->form->setFieldAttribute('link','readonly','true');

			$this->form->setFieldAttribute('published','readonly','true');

			$this->form->setFieldAttribute('access','disabled','true');
		
			$this->form->setFieldAttribute('menutype','readonly','true');
			$this->form->setFieldAttribute('parent_id','readonly','true');
			
			$this->form->setFieldAttribute('menuordering','readonly','true');

			
			
			
			$this->form->setFieldAttribute('browserNav','readonly','true');
			if ($this->menuItem->type == 'component') :
				$this->form->setFieldAttribute('home','type','radioreadonly');
				$this->form->setFieldAttribute('home','class','readonly');
			endif;
			$this->form->setFieldAttribute('language','readonly','true');
			$this->form->setFieldAttribute('template_style_id','disabled','true');
		}
		/*
		
		
		
		*/


		if(isset($this->menuItem->request['option']))
		{
			//$lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR);
			$lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR, null, false, false) || $lang->load($this->menuItem->request['option'], JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
			
		}
		$menu_item_name = $this->menuItem->title; //must change tothe ???
		$menu_item_description = $this->form->getInput('type');
		$menu_item_parent_id = $this->form->getInput('parent_id');
		
		$buttonLinkMenutype = '';
		$buttonLinkMenutype .= '<div>';
			//here we can set an select type to change se type?
			$buttonLinkMenutype .= $menu_item_description;
		$buttonLinkMenutype .= '</div>';
		//$this->model->getLists();
		$html .= '<script language="JavaScript" type="text/javascript">';
			$html .= '<!--';
			$html .= 'function popupPageBrowser(url)';
			$html .= '{';
				$html .= 'var winl = (screen.width - 400) / 2;';
				$html .= 'var wint = (screen.height - 400) / 2;';
				$html .= "winprops = 'height=400,width=400,top='+wint+',left='+winl+',scrollbars=yes,resizable';";
				$html .= "linkValue = document.getElementById('link').value;";
				$html .= 'linkValue = escape(linkValue);';
				$html .= "urlString = url+'&url='+linkValue;";
				$html .= "win = window.open(urlString, 'pages', winprops);";
				$html .= 'if (parseInt(navigator.appVersion) >= 4)';
				$html .= '{';
					$html .= 'win.window.focus();';
				$html .= '}';
			$html .= '}';
			$html .= '-->';
		$html .= '</script>';

		if($this->menuItem && $this->menuItemsType)
		{
			$menuItemsType = $this->menuItemsType;
			$image = false;
			$imageNew = false;
			$imageEdit = false;
			$imageBulletNew = '';
			$imageBulletEdit = '';
			if(isset($menuItemsType->icons->default->imageUrl))
			{
				$image = $menuItemsType->icons->default->imageUrl;
			}
			if(isset($menuItemsType->icons->new->imageUrl))
			{
				$imageNew = $menuItemsType->icons->new->imageUrl;
			}
			else
			{
				$imageNew = $image;
				$imageBulletNew = PagesAndItemsHelper::getDirIcons().'base/bullets/icon-16-bullet_new.png';
			}
			if(isset($menuItemsType->icons->edit->imageUrl))
			{
				$imageEdit = $menuItemsType->icons->edit->imageUrl;
			}
			else
			{
				$imageEdit = $image;
				$imageBulletEdit = PagesAndItemsHelper::getDirIcons().'base/bullets/icon-16-bullet_edit.png';
			}
			if($sub_task=='new')
			{
				if(!$imageBulletNew && $imageNew)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageNew,JText::_('COM_PAGESANDITEMS_NEW_PAGE'));
				}
				elseif($imageBulletNew && $imageNew)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageNew,JText::_('COM_PAGESANDITEMS_NEW_PAGE'),$imageBulletNew);
				}
				else
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',JText::_('COM_PAGESANDITEMS_NEW_PAGE'));
				}
			}
			elseif($sub_task=='edit')
			{
				if(!$imageBulletEdit && $imageEdit)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageEdit,$checkedOutText.JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )',null,'thIcon16','thText');
				}
				elseif($imageBulletEdit && $imageEdit)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($imageEdit,$checkedOutText.JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )',$imageBulletEdit,'thIcon16','thText');
				}
				else
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',$checkedOutText.JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )',null,'thIcon16','thText');
				}
			}
			else
			{
				if($image)
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle($image,$checkedOutText.JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )',null,'thIcon16','thText');
				}
				else
				{
					$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',$checkedOutText.JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )',null,'thIcon16','thText');
				}
			}
		}
		else
		{
			if($sub_task=='new')
			{
				$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu.png',JText::_('COM_PAGESANDITEMS_NEW_PAGE').' ( '.$menu_item_name.' )');
			}
			elseif($sub_task=='edit')
			{
				$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu_edit.png',JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )');
			}
			else
			{
				$imageDisplay = PagesAndItemsHelper::getThImageTitle(PagesAndItemsHelper::getDirIcons().'icon-16-menu.png',JText::_('COM_PAGESANDITEMS_PAGE_PROPERTIES').' ( '.$menu_item_name.' )');
				
				
			}
		}

		$html .='<table class="piadminform xadminform" width="98%">';
			$html .= '<thead class="piheader">';
			
			$html .='<tr>';
				$html .='<th>'; // class="piheader">';//style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
					$html .= $imageDisplay;

				$html .='</th>';
			$html .='</tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			$html .='<tr>';
				$html .='<td>';
						JHtml::_('behavior.tooltip');
						JHtml::_('behavior.formvalidation');
						JHTML::_('behavior.modal');
						
						//$html .= $coreEditText;
						//$html .= $checkedOutText;
						
						
						$html .='<!-- $this->lists->display-> -->';
						$html .='<div class="width-60 fltlft">';
							$html .='<fieldset class="adminform">';
								$html .='<legend>'.JText::_('COM_MENUS_ITEM_DETAILS').'</legend>';

									$html .='<ul class="adminformlist">';
										//do not display when new
										if($this->menuItem->id){
											$html .='<li '.$this->lists->display->id.'>'.$this->form->getLabel('id');
											$html .= $this->form->getInput('id').'</li>';
										}
										$this->form->setFieldAttribute('type', 'type', 'pimenutype');
										$html .='<li>'.$this->form->getLabel('type');
										$html .= $this->form->getInput('type').'</li>';

										$html .='<li>'.$this->form->getLabel('title');
										$html .= $this->form->getInput('title').'</li>';

										if ($this->menuItem->type =='url'):
											$this->form->setFieldAttribute('link','readonly','false');
											$html .='<li>'.$this->form->getLabel('link');
											$html .= $this->form->getInput('link').'</li>';
										endif;

										if(version_compare(JVERSION, '2.5', 'ge'))
										{
											if ($this->menuItem->type == 'alias'):
												$html .='<li>';
													$html .= $this->form->getLabel('aliastip');
												$html .='</li>';
												endif;
												if ($this->menuItem->type !='url'):
												$html .='<li>';
													$html .= $this->form->getLabel('alias');
													$html .=$this->form->getInput('alias');
												$html .='</li>';
											endif;
										}
										else
										{
										$html .='<li>'.$this->form->getLabel('alias');
										$html .= $this->form->getInput('alias').'</li>';
										}
										/*
										
										
										*/
										$html .='<li>'.$this->form->getLabel('note');
										$html .= $this->form->getInput('note').'</li>';

										if ($this->menuItem->type !=='url'):
											$html .='<li>'.$this->form->getLabel('link');
											$html .= $this->form->getInput('link').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('published');
										$html .= $this->form->getInput('published').'</li>';

										$html .='<li>'.$this->form->getLabel('access');
										$html .= $this->form->getInput('access').'</li>';

										$html .='<li>'.$this->form->getLabel('menutype');
										$html .= $this->form->getInput('menutype').'</li>';
										
										
										$html .='<li>'.$this->form->getLabel('parent_id');
										$html .= $this->form->getInput('parent_id').'</li>';

										if($this->form->getField('menuordering'))
										{
											$html .= '<li>'.$this->form->getLabel('menuordering');
											$html .= $this->form->getInput('menuordering').'</li>';
										}
										
										$html .='<li>'.$this->form->getLabel('browserNav');
										$html .= $this->form->getInput('browserNav').'</li>';
										
										
										
										if ($this->menuItem->type == 'component') :
											$html .='<li>'.$this->form->getLabel('home');
											$html .= $this->form->getInput('home').'</li>';
										endif;

										$html .='<li>'.$this->form->getLabel('language');
										$html .= $this->form->getInput('language').'</li>';

										$html .='<li>'.$this->form->getLabel('template_style_id');
										$html .= $this->form->getInput('template_style_id').'</li>';

								$html .='</ul>';
							$html .='</fieldset>';
						$html .='</div>';

						$html .= '<!-- Menu Item Parameters Section content-->';
						$html .= '<div class="width-40 fltrt">'; //width-100 fltlft">';
							$html .= JHtml::_('sliders.start','menu-sliders-'.$this->menuItem->id);
								/*
									ms:
									check here for pagetype  == 'content_article'
									so we must not add an article here
								*/
								if($this->pageType == 'content_article')
								{
									$this->form->setFieldAttribute('id', 'required', false,'request');
								}

								/*
									ms:
									check here for pagetype  == 'content_category_blog' and sub_tak == 'new'
									so we can make an new category
								*/
								if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new')
								{
									$this->form->setFieldAttribute('id', 'required', false,'request');
								}

								if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' )
								{
									$this->form->setFieldAttribute('id', 'type', 'Picategory','request');
								}
								
								$fieldSets = $this->form->getFieldsets('request');
								if (!empty($fieldSets))
								{
									$fieldSet = array_shift($fieldSets);
									$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$fieldSet->name.'_FIELDSET_LABEL';
									$html .=  JHtml::_('sliders.panel',JText::_($label), 'request-options');
										if (isset($fieldSet->description) && trim($fieldSet->description)) :
											//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
											$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
										endif;

										$html .= '<fieldset class="panelform">';
											$hidden_fields = '';
											$html .= '<ul class="adminformlist">';
											
											foreach ($this->form->getFieldset('request') as $field)
											{
												if (!$field->hidden)
												{
													if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' && strtolower($field->type) == 'picategory')
													{
														$html .= '<li>';
															$html .= $field->input;
														$html .= '</li>';
													}
													else if(($this->pageType == 'content_category_blog' || $this->pageType == 'content_category' || $this->pageType == 'content_categories') && $sub_task == 'new' && strtolower($field->type) == 'category')
													{
														$html .= '<table>';
															$html .= '<tr>';
																$html .= '<td>';
																	$html .= '<input type="radio" name="create_new_category" value="0" id="create_new_category_0" />';
																$html .= '</td>';
																$html .= '<td>';
																	$html .= $field->label;
																$html .= '</td>';
																$html .= '<td>';
																	$html .= $field->input;
																$html .= '</td>';
															$html .= '</tr>';
															$html .= '<tr>';
																$html .= '<td>';
																	//the checked part will be configurable in the pagetype config
																	$html .= '<input type="radio" name="create_new_category" value="1" id="create_new_category_1" checked="checked" />';
																$html .= '</td>';
																$html .= '<td colspan="2">';
																	$html .= '<label class="hasTip" for="create_new_category_1" title="';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
																		$html .= '::';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY_TIP');
																		$html .= '">';
																		$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
																	$html .= '</label>';
																$html .= '</td>';
															$html .= '</tr>';
														$html .= '</table>';
													}
													else
													{
														$html .= '<li>';
															$html .= $field->label;
															$html .= $field->input;
														$html .= '</li>';
													}

												}
												else
												{
													$hidden_fields.= $field->input;
												}
											}
											$html .= '</ul>';
											$html .= $hidden_fields;
										$html .= '</fieldset>';
								}
									$fieldSets = $this->form->getFieldsets('params');
									foreach ($fieldSets as $name => $fieldSet)
									{
										$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_MENUS_'.$name.'_FIELDSET_LABEL';
										$html .= JHtml::_('sliders.panel',JText::_($label), $name.'-options');
											if (isset($fieldSet->description) && trim($fieldSet->description))
											{
												//$html .= '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
												$html .= '<p class="tip">'.JText::_($fieldSet->description).'</p>';
											}
											$html .= '<fieldset class="panelform">';
												$html .= '<ul class="adminformlist">';
												foreach ($this->form->getFieldset($name) as $field)
												{
													$html .= '<li>';
														$html .= $field->label;
														$html .=  $field->input;
													$html .= '</li>';
												}
												$html .= '</ul>';
											$html .= '</fieldset>';
									}

									$html .= '<div class="clr"></div>';
									if (!empty($this->modules))
									{
										$html .=  JHtml::_('sliders.panel',JText::_('COM_MENUS_ITEM_MODULE_ASSIGNMENT'), 'module-options');
											$html .= '<fieldset>';
												$html .= '<table class="adminlist">';
													$html .= '<thead>';
														$html .= '<tr>';
															$html .= '<th class="left">';
																$html .= JText::_('COM_MENUS_HEADING_ASSIGN_MODULE');
															$html .= '</th>';
															$html .= '<th>';
																$html .= JText::_('COM_MENUS_HEADING_DISPLAY');
															$html .= '</th>';
														$html .= '</tr>';
													$html .= '</thead>';
													$html .= '<tbody>';
													foreach ($this->modules as $i => &$module)
													{
														$html .= '<tr class="row<?php echo $i % 2;?>">';
															$html .= '<td>';
																$link = 'index.php?option=com_modules&amp;client_id=0&amp;task=module.edit&amp;id='. $module->id.'&amp;tmpl=component&amp;view=module&amp;layout=modal' ;
																$html .= '<a class="modal" href="'. $link.'" rel="{handler: \'iframe\', size: {x: 900, y: 550}}" title="'.JText::_('COM_MENUS_EDIT_MODULE_SETTINGS').'">';
																	//$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position));
																	$html .= JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $module->title, $module->access_title, $module->position);
																$html .='</a>';

														$html .= '</td>';
														$html .= '<td class="center">';
														if (is_null($module->menuid))
														{
															$html .= JText::_('JNONE');
														}
														elseif ($module->menuid != 0)
														{
															$html .= JText::_('COM_MENUS_MODULE_SHOW_VARIES');
														}
														else
														{
															$html .= JText::_('JALL');
														}
														$html .= '</td>';
													$html .= '</tr>';
													}
													$html .= '</tbody>';
												$html .= '</table>';
											$html .= '</fieldset>';
											}
										$html .= JHtml::_('sliders.end');
										//$html .= '<input type="hidden" name="task" value="" />';
										$html .= $this->form->getInput('component_id');
										$html .=  JHtml::_('form.token');
									$html .= '</div>';
									//$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
								$html .= '<!-- END Menu Item Parameters Section-->';
						//	$html .= '</td>';
						//$html .= '</tr>';
								$html .= '<!-- Manager Section-->';
								$pageId = JRequest::getVar('pageId', null);
								//if($pageId)
								//{
									$new_or_edit = (JRequest::getVar('sub_task','edit') == 'new') ? 0 : 1;
									$managerOtherItemEdit = new JObject();
									$managerOtherItemEdit->text = '';

									$params = null;
									$dispatcher = &JDispatcher::getInstance();
									//$dispatcher->trigger('onGetParams',array(&$params, $item_type));
									$path = JPATH_COMPONENT_ADMINISTRATOR;//realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..');
									require_once($path.DS.'includes'.DS.'extensions'.DS.'managerhelper.php');
									$extensions = ExtensionManagerHelper::importExtension(null,null, true,null,true);
									$dispatcher->trigger('onManagerOtherItemEdit', array (&$managerOtherItemEdit,'menu',$pageId,$params,$new_or_edit));
									if($managerOtherItemEdit->text != '')
									{
									$html .= '<div class="width-100 fltrt">'; //width-100 fltlft">';
										$html .= $managerOtherItemEdit->text;
									$html .= '</div>';
									}
								//}


		
						
						$html .='<table border="0" cellspacing="0" cellpadding="0" class="paramlist" width="98%" >';
						$html .= $this->lists->add->bottom;
						$html .= '</table>';

						//$html .= $this->menuItem->linkfield;
						//replace with
						$html .= $this->lists->pageType->html;
						$html .= '<input type="hidden" name="id" value="'.$this->menuItem->id.'" />';
						$html .= '<input type="hidden" name="component_id" value="'.$this->menuItem->component_id.'" />';

						$html .= $this->form->getInput('component_id');
						$html .= JHtml::_('form.token');
						//$html .= '<input type="hidden" id="fieldtype" name="fieldtype" value="" />';
						//$html .= '<input type="hidden" id="pageType" name="pageType" value="'.$this->pageType.'" />';
						$html .= '<input type="hidden" name="type" value="'.$this->menuItem->type.'" />';
				$html .= '</td>';
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		echo $html;
?>
