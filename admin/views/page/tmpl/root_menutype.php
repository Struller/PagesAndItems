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

JHTML::_('behavior.tooltip');

$sub_task = JRequest::getVar( 'sub_task', 'edit');
$menutype = JRequest::getVar('menutype', '');
$view = JRequest::getVar( 'view', 'page');
$layout = JRequest::getVar( 'layout', null);

$user		= JFactory::getUser();
$canCreateModule	= $user->authorise('core.create',		'com_modules'); //'com_menus');
$canEditModule	= $user->authorise('core.edit',			'com_modules'); //'com_menus');
$canChangeModule	= $user->authorise('core.edit.state',	'com_modules'); //'com_menus');

$canAdminMenu	= $user->authorise('core.admin',		'com_menus');
$canCreateMenu	= $user->authorise('core.create',		'com_menus');
$canEditMenu	= $user->authorise('core.edit',			'com_menus');
$canChangeMenu	= $user->authorise('core.edit.state',	'com_menus');

$lang		= JFactory::getLanguage();
$lang->load('com_modules', JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load('com_modules', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);




//no checked_out for menutype
		if(!$this->canDoMenutype->get('core.edit') )
		{
			//$this->form->setFieldAttribute('title','type','text');
			$this->form->setFieldAttribute('title','class','readonly');
			$this->form->setFieldAttribute('title','readonly','true');

			//$this->form->setFieldAttribute('menutype','type','text');
			$this->form->setFieldAttribute('menutype','class','readonly');
			$this->form->setFieldAttribute('menutype','readonly','true');


			//$this->form->setFieldAttribute('description','type','text');
			$this->form->setFieldAttribute('description','class','readonly');
			$this->form->setFieldAttribute('description','readonly','true');
		}






// && PagesAndItemsHelper::getIsSuperAdmin()
//Todo edit only for superadmin?
?>


	<table class="piadminform xadminform" width="98%">
		<thead class="piheader">
		<tr>
			<th> <!-- class="piheader">--><!--style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">-->
				<!--<img style="vertical-align: middle;" alt="" src="/administrator/components/com_pagesanditems/media/images/icons/icon-16-menu_edit.png">-->
				<img style="vertical-align: middle;" alt="" src="<?php echo PagesAndItemsHelper::getDirIcons();?>/icon-16-menu_edit.png">
				Menutype Properties
			</th>
		</tr>
		</thead>
		<tbody>	
		<tr>
			<td>
				<fieldset class="adminform">
					<legend><?php echo JText::_('COM_MENUS_MENU_DETAILS');?></legend>
						<ul class="adminformlist">
							<li><?php echo $this->form->getLabel('title'); ?>
							<?php echo $this->form->getInput('title'); ?></li>

							<li><?php echo $this->form->getLabel('menutype'); ?>

							
							<?php 
							if($this->menutypeItem->id)
							{
							?>
								<?php //$this->form->setFieldAttribute('menutype','size','1'); ?>
								<?php //$this->form->setFieldAttribute('menutype','type','rootmenutype'); ?>
							<?php
							}
							/*
							*/
							?>
							
							<?php echo $this->form->getInput('menutype'); ?></li>

							<li><?php echo $this->form->getLabel('description'); ?>
							<?php echo $this->form->getInput('description'); ?></li>

							<?php
							//TODO add enabled only if not id
							
							if(!$this->form->getValue('menutype') && PagesAndItemsHelper::getIsSuperAdmin())
							{
									$element = new JXMLElement('<field ></field>');
									$element->addAttribute('name', 'enabled');
									$element->addAttribute('type', 'radio');
									$element->addAttribute('label', 'Enabled in PI');
									$element->addAttribute('default', '0');
									$option = $element->addChild('option','JNO');
									$option->addAttribute('value', '0');
									$option = $element->addChild('option','JYES');
									$option->addAttribute('value', '1');
									//<option value="0">No</option>
									//<option value="1">Yes</option>


									$this->form->setField($element);
								?>
								<li><?php echo $this->form->getLabel('enabled'); ?>
								<?php echo $this->form->getInput('enabled'); ?></li>
							<?php
							}
							?>

						</ul>
				</fieldset>

				<?php
				$uri = JFactory::getUri();
				$return = base64_encode($uri);
				$addModule = '';
				if($canCreateModule)
				{
					/*
					$addModule .= '<a class="modal" ';
					$addModule .= 'href="'.JRoute::_('index.php?option=com_modules&task=module.add&return='.$return.'&tmpl=component&layout=modal').'" ';
					$addModule .= 'rel="{handler: \'iframe\', size: {x: 1024, y: 450}, onClose: function() {window.location.reload()}}" ';
					$addModule .= 'title="'.JText::_('COM_MODULES').'">';
					$addModule .= JText::_('COM_MODULES');
					$addModule .= '</a>';
					*/
					/*
					$addModule .= '<a rel="{handler: \'iframe\', size: {x: 850, y: 400}, onClose: function() {}}" href="'.JRoute::_('index.php?option=com_modules&amp;view=select&amp;tmpl=component').'" class="modal">';
					$addModule .= '<span class="icon-32-new">';
					$addModule .= '</span>';
					$addModule .=	JText::_('COM_MODULES').' '.JText::_('JTOOLBAR_NEW');
					$addModule .= '</a>';
					
					TODO add with an own extensions/htmls/menutype/select/
					
					
					*/
					
				}
				
				$modMenuId = 0;
				if(version_compare(JVERSION, '2.5', 'ge'))
				{
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'menus.php');
					$modelMenutypes = new MenusModelMenus();
					$modMenuId = (int) $modelMenutypes->getModMenuId();
				}
				
				if (isset($this->modules[$this->menutypeItem->menutype]) || $modMenuId) :
				echo '<fieldset class="adminform">';
						echo '<legend>';
							echo JText::_('COM_MENUS_HEADING_LINKED_MODULES');
						echo '</legend>';
				endif;
				
				if (isset($this->modules[$this->menutypeItem->menutype])) :

					$link = 'index.php?option=com_pagesanditems'; //.$option;
					$link .= '&amp;task=extension.doExecute';
					$link .= '&amp;extension=menutype';
					$link .= '&amp;extensionType=html';
					$link .= '&amp;extensionFolder=page_tree';
					$link .= '&amp;view=module';
					$link .= '&amp;tmpl=component';
					/*
					echo '<fieldset class="adminform">';
						echo '<legend>';
							echo JText::_('COM_MENUS_HEADING_LINKED_MODULES');
						echo '</legend>';
					*/
						echo '<ul>';
							if($addModule)
							{
								echo '<li>';
									echo $addModule;
								echo '</li>';
							}

							foreach ($this->modules[$this->menutypeItem->menutype] as &$module) :
							?>
								<li>
								<?php if ($canEditModule) : ?>
								<a class="modal" href="<?php echo JRoute::_('index.php?option=com_modules&task=module.edit&id='.$module->id.'&return='.$return.'&tmpl=component&layout=modal');?>" rel="{handler: 'iframe', size: {x: 1024, y: 450}, onClose: function() {window.location.reload()}}"  title="<?php echo JText::_('COM_MENUS_EDIT_MODULE_SETTINGS');?>">
								<?php echo JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position)); ?></a>
								<?php else :?>
								<?php echo JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position)); ?>
								<?php endif; ?>
								</li>
							<?php
							/*
					$button = PagesAndItemsHelper::getButtonMaker();
					$button->text = JText::_('PI_EXTENSION_HTML_PAGE_TREE_MENUTYPE_EDIT_MODULE');
					$link .= '&amp;id='.$module->id;
					$link .= '&amp;eid='.$this->menutypeItem->id;

					$size_x = '850';
					$size_y = '600';

					$link .= '&amp;size_x='.$size_x;
					$link .= '&amp;size_y='.$size_y;
					$size = 'size: { x: \''.$size_x.'\' , y: \''.$size_y.'\'}';
					$options = "handler: 'iframe', ".$size;
					$button->imageName = 'class:icon-16-module';
					$button->rel = $options;
					$button->href = $link;
					$button->modal = true;
					//echo $button->makeButton();
					echo '<li>';
						if ($canEditModule) :
						//echo '<a class="modal" href="'.$link.'" rel="{'.$options.'}" title="'.JText::_('COM_MENUS_EDIT_MODULE_SETTINGS').'">';
						echo '<a title="'.JText::_('COM_MENUS_EDIT_MODULE_SETTINGS').'">';
							echo JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position));
						echo '</a>';
						else :
						echo JText::sprintf('COM_MENUS_MODULE_ACCESS_POSITION', $this->escape($module->title), $this->escape($module->access_title), $this->escape($module->position));
						endif;
					echo '</li>';
					*/
							endforeach;
						echo '</ul>';
					//echo '</fieldset>';
				//endif;
				elseif ($modMenuId) : 
					
					//TODO as 
					/*
					<a class="modal" href="<?php echo JRoute::_('index.php?option=com_modules&task=module.edit&id='.$module->id.'&return='.$return.'&tmpl=component&layout=modal');?>" rel="{handler: 'iframe', size: {x: 1024, y: 450}, onClose: function() {window.location.reload()}}"  title="<?php echo JText::_('COM_MENUS_EDIT_MODULE_SETTINGS');?>">
					
					*/
					?>
					<a class="modal" href="<?php echo JRoute::_('index.php?option=com_modules&task=module.add&eid=' . $modMenuId . '&params[menutype]='.$this->menutypeItem->menutype.'&return='.$return.'&tmpl=component&layout=modal');?>" rel="{handler: 'iframe', size: {x: 1024, y: 450}, onClose: function() {window.location.reload()}}">
						<?php echo JText::_('COM_MENUS_ADD_MENU_MODULE'); ?></a>
					<?php 
				endif;
				
				if (isset($this->modules[$this->menutypeItem->menutype]) || $modMenuId) :
					echo '</fieldset>';
				endif;
				
				/*
				if(version_compare(JVERSION, '2.5', 'ge'))
				{
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'menus.php');
					$modelMenutypes = new MenusModelMenus();
					$modMenuId = (int) $modelMenutypes->getModMenuId();
					elseif ($modMenuId) : 
					<a href="<?php echo JRoute::_('index.php?option=com_modules&task=module.add&eid=' . $modMenuId . '&params[menutype]='.$item->menutype); ?>">
						<?php echo JText::_('COM_MENUS_ADD_MENU_MODULE'); ?></a>
					<?php endif; ?>
				}
				else
				{
				
				}
				
				$modMenuId = (int) $this->get('ModMenuId');
				<?php elseif ($modMenuId) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_modules&task=module.add&eid=' . $modMenuId . '&params[menutype]='.$item->menutype); ?>">
						<?php echo JText::_('COM_MENUS_ADD_MENU_MODULE'); ?></a>
					<?php endif; ?>
				
				
				*/
				/*
				echo '<fieldset class="adminform">';
					echo '<legend>';
						echo JText::_('PI_EXTENSION_HTML_PAGE_TREE_MENUTYPE_AVAILABLE').' '.JText::_($this->form->getFieldAttribute('menutype','label'));
					echo '</legend>';
				//TODO display all menutypes here with is enabled in pi config
				$db = JFactory::getDBO();
					$config = PagesAndItemsHelper::getConfig();
					//loop through menutypes from config
					$counter = 1;
					$menus_from_config = $config['menus'];
					$temp_menus = explode(',',$config['menus']);
					if($temp_menus[0]==''){
						$temp_menus = array();
					}
					$menus_on_page = array();

					echo '<ul class="adminformlist" style="display:inline-block;">';

					?>
					<li>
						<span class="sidestep2 b"><?php echo JText::_($this->form->getFieldAttribute('title','label')).' ('.JText::_($this->form->getFieldAttribute('menutype','label')).')'; ?></span><span class="b"><?php echo JText::_('COM_PAGESANDITEMS_ORDER'); ?></span>
					</li>

					<?php
					$isSuperAdmin = PagesAndItemsHelper::getIsSuperAdmin();
					$disabled = '';
					if(!$canAdminMenu) //PagesAndItemsHelper::getIsSuperAdmin())
					{
						$disabled = ' disabled="disabled"';
					}
					for($m = 0; $m < count($temp_menus); $m++){
						$menu_temp = explode(';',$temp_menus[$m]);
						//echo '<tr>';
						echo '<li>';
							//echo '<td>&nbsp;</td>';
							//echo '<td>';
								echo '<span class="sidestep2">';
									echo '<label>';
										if(!$canAdminMenu) //PagesAndItemsHelper::getIsSuperAdmin())
										{
											echo '<input type="checkbox" class="checkbox" name="menus[m'.$m.'][menutype]" value="'.$menu_temp[0].'"';
											echo ' checked="checked" style="display:none;"';
											echo ' />';
											echo '<input type="checkbox" class="checkbox" value="'.$menu_temp[0].'"';
											echo ' checked="checked"';
											echo $disabled;
											echo ' />';
										}
										else
										{
											echo '<input type="checkbox" class="checkbox" name="menus[m'.$m.'][menutype]" value="'.$menu_temp[0].'"';
											echo ' checked="checked"';
											echo ' />';
										}
										echo $menu_temp[1].' ('.$menu_temp[0].')';
									echo '</label>';
								echo '</span>';
								echo '<input type="hidden" name="menus[m'.$m.'][title]" value="'.$menu_temp[1].'" />';
								echo '<input type="text" '.$disabled.' name="menus[m'.$m.'][order]" size="2" value="'.$counter.'"';
								echo ' />';
							//echo '</td>';
						//echo '</tr>';
						echo '</li>';
						array_push($menus_on_page, $menu_temp[0]);
						$counter = $counter + 1;
					}

					//get all menutypes
					$menutypes_db = array();
					//joomla 1.5
					$db->setQuery("SELECT title, menutype FROM #__menu_types ORDER BY title ASC"  );
					$rows = $db-> loadObjectList();
					foreach($rows as $row)
					{
						$new_menutype = array(strtolower($row->menutype),$row->title);
						array_push($menutypes_db, $new_menutype);
					}

					//loop through menutypes from database
					for($m = 0; $m < count($menutypes_db); $m++){
						if(!in_array($menutypes_db[$m][0], $menus_on_page)){
							//echo '<tr>';
							//	echo '<td>&nbsp;</td>';
							//	echo '<td>';
							echo '<li>';
									echo '<span class="sidestep2">';
										echo '<label>';
											echo '<input type="checkbox" class="checkbox" name="menus[m'.($counter-1).'][menutype]" value="'.$menutypes_db[$m][0].'"';
											echo $disabled;
											echo ' />';
											echo $menutypes_db[$m][1].' ('.$menutypes_db[$m][0].')';
										echo '</label>';
									echo '</span>';
									echo '<input type="hidden" name="menus[m'.($counter-1).'][title]" value="'.$menutypes_db[$m][1].'" />';
									echo '<input type="text" '.$disabled.' name="menus[m'.($counter-1).'][order]" size="2" value="'.$counter.'"';
									echo ' />';
							//	echo '</td>';
							//echo '</tr>';
							echo '</li>';
							$counter = $counter + 1;
						}
					}
					echo '</ul>';
				echo '</fieldset>';
				*/
				
?>
			</td>
		</tr>
		</tbody>
	</table>


<?php
/*
END add menutype edit
*/
?>