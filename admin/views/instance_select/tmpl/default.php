<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC')){
	die('Restricted access');
}


?>
<script language="JavaScript" type="text/javascript">
<!--
function select_parent(parent_id){
	document.getElementById('new_parent_id').value = parent_id;
}
<?php
if(PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
{
?>
function submitbutton(pressbutton) {
	if (pressbutton == 'cancel') {
		history.back();
	}
	if (pressbutton == 'create_instance') {
		if (document.adminForm.new_parent_id.value == '' ) {
			alert( '<?php echo JText::_('COM_PAGESANDITEMS_SELECT_PAGE_FOR_NEW_INSTANCE'); ?>' );
			return;
		} else {
			//submitform(pressbutton);
			new_parent_page = document.adminForm.new_parent_id.value;
			other_item_id = document.adminForm.other_item_id.value;
			document.location.href = 'index.php?option=com_pagesanditems&type=content_blog_category&view=item&sub_task=new&pageId='+new_parent_page+'&item_type=other_item&other_item_id='+other_item_id;
		}
	}
}
<?php
}
else
{
?>
Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'cancel')
	{
		//ms: not run
		history.back();
	}
	if (pressbutton == 'create_instance') {
		if (document.adminForm.new_parent_id.value == '' ) {
			alert( '<?php echo JText::_('COM_PAGESANDITEMS_SELECT_PAGE_FOR_NEW_INSTANCE'); ?>' );
			return;
		} else {
			//submitform(pressbutton);
			new_parent_page = document.adminForm.new_parent_id.value;
			other_item_id = document.adminForm.other_item_id.value;
			document.location.href = 'index.php?option=com_pagesanditems&type=content_blog_category&view=item&sub_task=new&pageId='+new_parent_page+'&item_type=other_item&other_item_id='+other_item_id;
		}
	}
}
<?php

}
?>
-->
</script>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td valign="top" width="20%">
		<?php
			echo $this->pageTree;
		?>
		</td>
		<td valign="top">
			<form name="adminForm" method="post" action="">
				<input type="hidden" name="option" value="com_pagesanditems" />
				<input type="hidden" name="new_parent_id" id="new_parent_id" value="" />
				<input type="hidden" name="other_item_id" value="<?php echo JRequest::getVar('other_item_id'); ?>" />
		<table class="piadminform xadminform" width="100%">
			<thead class="piheader">
			<tr>
				<th> <!-- class="piheader">-->
					 <?php echo JText::_('COM_PAGESANDITEMS_NEW_INSTANCE'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			
				<tr>
					<td>
						<p><?php echo JText::_('COM_PAGESANDITEMS_SELECT_PAGE_FOR_NEW_INSTANCE'); ?>.</p>
					</td>
				</tr>
				<tr>
					<td>
						<?php

						//see how many loops we need
						$modelMenutypes = new PagesAndItemsModelMenutypes();
						$menutypes = PagesAndItemsHelper::getMenutypes();
						$loops = count($menutypes);
						$config = PagesAndItemsHelper::getConfig();
						//loop menutypes
						for($m = 0; $m < $loops; $m++)
						{
							echo '<div class="dtree pi_instance_select">';
							echo '<p><a href="javascript: pages_tree'.$m.'.openAll();">'.JText::_('COM_PAGESANDITEMS_OPEN_ALL').'</a> | <a href="javascript: pages_tree'.$m.'.closeAll();">'.JText::_('COM_PAGESANDITEMS_CLOSE_ALL').'</a></p>';

							//open javascript
							echo "<script type=\"text/javascript\" type=\"text/javascript\">\n";
							echo "<!--\n";
							echo "pages_tree$m = new dTree('pages_tree$m');\n";
							echo PagesAndItemsHelper::getdTreeIcons("pages_tree".$m);
/*							$script = "pages_tree$m.icon = {";
			$script .= "root		: '".PagesAndItemsHelper::getDirIcons()."icon-16-menu.png',
			folder	: '".PagesAndItemsHelper::getDirIcons()."folder.gif',
			folderOpen	: '".PagesAndItemsHelper::getDirIcons()."folderopen.gif',
			node		: '".PagesAndItemsHelper::getDirIcons()."page.gif',
			empty		: '".PagesAndItemsHelper::getDirIcons()."empty.gif',
			line		: '".PagesAndItemsHelper::getDirIcons()."line.gif',
			join		: '".PagesAndItemsHelper::getDirIcons()."join.gif',
			joinBottom	: '".PagesAndItemsHelper::getDirIcons()."joinbottom.gif',
			plus		: '".PagesAndItemsHelper::getDirIcons()."plus.gif',
			plusBottom	: '".PagesAndItemsHelper::getDirIcons()."plusbottom.gif',
			minus		: '".PagesAndItemsHelper::getDirIcons()."minus.gif',
			minusBottom	: '".PagesAndItemsHelper::getDirIcons()."minusbottom.gif',
			nlPlus	: '".PagesAndItemsHelper::getDirIcons()."nolines_plus.gif',
			nlMinus	: '".PagesAndItemsHelper::getDirIcons()."nolines_minus.gif'
			};\n";
							echo $script;
*/
							if (PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
							{
								echo "pages_tree$m.add(0,-1,'";
							}
							else
							{
								echo "pages_tree$m.add(1,-1,'";
							}
							//echo "pages_tree$m.add(0,-1,'";
							//$script .= $this->getMenutypeTitle($menutypes[$m]);
							echo PagesAndItemsHelper::getMenutypeTitle($menutypes[$m]);
							echo "','','','','','',true);\n";

							//make javascript-array from main-menu-items
							$menuitems = PagesAndItemsHelper::getMenutypeMenuitems($menutypes[$m]);

							foreach($menuitems as $row)
							{


								$show_item = false;
								if($row->menutype == $menutypes[$m])
								{
									$show_item = true;
								}
								if($show_item)
								{
									$image = '';
									$imageNoAccess = '';
									$itemtype_no_access = array();
									$not_installed_no_access = false;
									$pageType = null;
									if($row->type == 'components'){
										//backward compatibility for site which were migrated from Joomla 1.5
										$row->type = 'component';
									}

									if($row->type != 'component')
									{
										$pageType = $row->type;
									}
									else
									{
										$pageType =$modelMenutypes->buildPageType($row->link);
										if(!isset($this->menuItemsTypes[$pageType]))
										{
											$pageType = null;
										}
									}
									if(!$pageType)
									{
										//we have an component without option???
										//i think is an unistallet component
										//we set the image to component_no_access
										//we need an $this->menuItemsTypes->not_installed_no_access
										$pageType = 'not_installed_no_access';
										$not_installed_no_access = true;
									}
									$menuItemsType = $this->menuItemsTypes[$pageType];

									if(isset($menuItemsType->icons->default->imageUrl))
									{
										$image = $menuItemsType->icons->default->imageUrl;
									}
									else
									{
										if(isset($menuItemsType->icons->componentDefault->default->imageUrl))
										{
											$image = $menuItemsType->icons->componentDefault->default->imageUrl;
										}
									}
									if(isset($menuItemsType->icons->no_access->imageUrl))
									{
										$imageNoAccess = $menuItemsType->icons->no_access->imageUrl;
									}
									else
									{
										if(isset($menuItemsType->icons->componentDefault->no_access->imageUrl))
										{
											$imageNoAccess = $menuItemsType->icons->componentDefault->no_access->imageUrl;
										}
									}
									if($not_installed_no_access)
									{
										$image = $imageNoAccess;
										$itemtype_no_access = addslashes(JText::_('COM_PAGESANDITEMS_COMPONENT_NOT_INSTALLED_NO_ACCESS'));
									}

									if($row->type == 'separator' )
									{
										$name = JText::_('COM_PAGESANDITEMS_MENU_ITEM_TYPE').': '.JText::_('SEPARATOR');
										if($row->name != '')
										{
											$name .= ' ('.$row->name.')';
										}
										else
										{
											$name .= ' (empty)';
										}
										$menuName = $name;
									}
									else
									{
										$menuName = $row->name;
									}
									$section_id = '';

									$title = '';
									if($itemtype_no_access != '' && !is_array($itemtype_no_access))
									{
										$title = $itemtype_no_access;
										//style="color: grey;"
										//$menuName = '<a title="'.$itemtype_no_access.'" class="node noaccess" >'.$menuName.'</a>';
										//$script .= "d$m.add(".$row->id.",".$row->parent.",'".$menuName."','";
									}
									else
									{
										//TODO get this over the pagetype
										//if(((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url') || $row->type=='content_blog_category') && $class_pi->check_section_access($section_id)

										if( (strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type != 'url') || $row->type=='content_category_blog')
										{

										}
										else
										{
											//$itemtype_no_access = JText::_('COM_PAGESANDITEMS_NO_CATEGORY_BLOG');
											$title = JText::_('COM_PAGESANDITEMS_NO_CATEGORY_BLOG');
											//$menuName = '<a title="'.JText::_('COM_PAGESANDITEMS_NO_CATEGORY_BLOG').'" class="node nocategoryblog" >'.$menuName.'</a>';
										}


										//$menuName = addslashes($menuName);
										//$script .= "d$m.add(".$row->id;//." id
										//$script .= ",".$row->parent;//." , pid
										//$script .= ",'".($menuName)."'"; //, name
										//$script .= ",'index.php?option=com_pagesanditems&view=page&menutype=".$row->menutype."&pageId=".$row-	>id."&sub_task=edit&pageType=".$pageType; //."&currentLevel=".$row->sublevel; //, url
									}

									//$menuName
									//echo "pages_tree$m.add(".$row->id.",".$row->parent.",'".(addslashes($row->name))."','";
									echo "pages_tree$m.add(".$row->id.",".$row->parent.",";

									echo "'".(addslashes($menuName))."','";

									//if content page (content category blog) and not in the category it came from, make selectable link
									//TODO get this over the pagetype
									if((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url') || $row->type=='content_blog_category')
									{
										echo 'javascript: select_parent('.$row->id.');';
									}
									else
									{
										//echo '#'; //echo 'XX';
									}
									/*
									if(((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type=='url') || !strstr($row->link, 'index.php?option=com_content&view=category&layout=blog')) && $row->type!='content_blog_category')
									{
										echo "','','','components/com_pagesanditems/images/link.gif','components/com_pagesanditems/images/link.gif";
									}
									else
									{
										echo "','','','components/com_pagesanditems/images/page.gif','components/com_pagesanditems/images/page.gif";
									}
									*/

									/*
									we will always set an title
									but dTree will only set an title to an <a>
									*/
									//echo "','".$itemtype_no_access;
									echo "','".$title;
									echo "','','".$image."','".$image;
									echo "');\n";
								}
							}
							echo "document.write(pages_tree$m);\n";

							//close javascript
							echo "//-->\n";
							echo "</script>\n";
							echo '</div>';

						}//end loops menutypes
						?>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</td>
	</tr>
</table>
<!-- end id="form_content" need for css-->
</div>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
// $this->model->display_footer();
?>