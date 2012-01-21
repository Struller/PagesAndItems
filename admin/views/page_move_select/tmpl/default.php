<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC')){
	die('Restricted access');
}


$pageId = JRequest::getVar('pageId', '' );
$categoryId = JRequest::getVar('categoryId', '' );
$menutype = JRequest::getVar('menutype', '' );
$menuitems = PagesAndItemsHelper::getMenuitems();
foreach($menuitems as $row)
{
	if($row->id==$pageId)
	{
		$name = $row->name;
		$parent = $row->parent;
		$menutype = $row->menutype;
	}
}
/*
echo "<link href=\"components/com_pagesanditems/css/pagesanditems2.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
echo "<link href=\"components/com_pagesanditems/css/dtree.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
echo "<script src=\"components/com_pagesanditems/javascript/dtree.js\" language=\"JavaScript\" type=\"text/javascript\"></script>\n";
echo "<script src=\"../includes/js/overlib_mini.js\" language=\"JavaScript\" type=\"text/javascript\"></script>\n";
*/
//give headers in Joomla 1.5 a bit more spunk
//$this->controller->spunk_up_headers_1_5(); //is in css
?>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<script language="JavaScript" type="text/javascript">
<!--
function select_parent(parent_id, new_menutype){
	document.getElementById('new_parent_id').value = parent_id;
	document.getElementById('new_menutype').value = new_menutype;
}
function dont_move_here(){
	alert('<?php echo JText::_('COM_PAGESANDITEMS_NOMOVEUNDERSAMEPAGE'); ?>');
	document.getElementById('new_parent_id').value = '';
}

//function submitbutton
Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'cancel') {
		document.id('menutype').value = document.id('old_menutype').value;
		submitform( pressbutton );
		<?php
			//$live_path = $mainframe->getCfg('live_site');
		?>
		/*
		old_parent_id = document.getElementById('old_parent_id').value;
		document.href = 'index.php?option=com_pagesanditems&view=item&sub_item=edit&pageId='+old_parent_id;
		*/
		return;
	}
	if (pressbutton == 'page.page_move_save') {
		if (document.adminForm.new_parent_id.value == '' ) {
			alert( '<?php echo JText::_('COM_PAGESANDITEMS_HAVETOSELECTPARENTPAGE'); ?>' );
			return;
		} else {
			submitform(pressbutton);
		}
	}
}

-->
</script>


	<table cellspacing="0" cellpadding="0" border="0" width="100%">
	</tbody>
	<tr><td  valign="top" width="20%">
	</td><td valign="top">
	<div id="pi_breadcrumb">
			<?php
			$url = PagesanditemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit&amp;pageId='.$pageId.'&amp;categoryId='.$categoryId,'&amp;');
			//$url = 'index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit&amp;pageId='.$pageId.'&amp;categoryId='.$categoryId;
			?>
			<a href="<?php echo $url; ?>"><?php echo JText::_('COM_PAGESANDITEMS_PAGE'); ?></a> ><?php echo JText::_('COM_PAGESANDITEMS_PAGEMOVE'); ?>
	</div>
	</td></tr>
	</tbody>
	</table>

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
				<input type="hidden" name="task" value="page_move_save" />
				<input type="hidden" name="pageId" id="pageId" value="<?php echo $pageId; ?>">
				<input type="hidden" name="categoryId" value="<?php echo $categoryId; ?>">
				<input type="hidden" name="new_menutype" id="new_menutype" value="">
				<input type="hidden" name="old_menutype" id="old_menutype" value="<?php echo $menutype; ?>">
				<input type="hidden" name="old_parent_id" id="old_parent_id" value="<?php echo $parent; ?>" />
				
				<input type="hidden" name="menutype" id="menutype" value="">
				<?php
				/*
				<div id="pi_breadcrumb">
					<a href="index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit&amp;pageId=<?php echo $pageId; ?>"><?php echo JText::_('COM_PAGESANDITEMS_PAGE'); ?></a> >  <?php echo JText::_('COM_PAGESANDITEMS_PAGEMOVE'); ?>
				</div>
				*/
				?>
				<table class="piadminform xadminform">
				<thead class="piheader">
					<tr>
						<th> <!-- class="piheader">-->
							<?php echo JText::_('COM_PAGESANDITEMS_MOVEPAGE'); ?>: "<?php echo $name ?>"
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<p><?php echo JText::_('COM_PAGESANDITEMS_WHENMOVINGPAGE'); ?></p>
							<p><?php echo JText::_('COM_PAGESANDITEMS_SELECTPAGEUNDER'); ?></p>
							<p>
							<input type="text" name="new_parent_id" id="new_parent_id"  value="" style="width: 50px;" />
							</p>
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

								echo '<div class="dtree">';
								echo '<p><a href="javascript: pages_tree'.$m.'.openAll();">'.JText::_('COM_PAGESANDITEMS_OPEN_ALL').'</a> | <a href="javascript: pages_tree'.$m.'.closeAll();">'.JText::_('COM_PAGESANDITEMS_CLOSE_ALL').'</a></p>';

								//open javascript
								echo "<script type=\"text/javascript\"  type=\"text/javascript\">\n";
								echo "<!--\n";
								echo "pages_tree$m = new dTree('pages_tree$m');\n";

								echo PagesAndItemsHelper::getdTreeIcons("pages_tree".$m);
								
								if (PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
								{
									echo "pages_tree$m.add(0,-1,'";
								}
								else
								{
									echo "pages_tree$m.add(1,-1,'";
								}
								echo '&nbsp;'.PagesAndItemsHelper::getMenutypeTitle($menutypes[$m]);
								//echo "','','','','','',true);\n";
								echo "','javascript:select_parent(1,\'".$menutypes[$m]."\');','','','','',true);\n";
								//make javascript-array from main-menu-items
								$menuitems = PagesAndItemsHelper::getMenutypeMenuitems($menutypes[$m]);

								//make javascript-array from main-menu-items
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
											//TODO select not the page childs
											if( ( (strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type != 'url') || $row->type=='content_category_blog') && $row->id !=$pageId && $row->parent!=$pageId ) //&& PagesAndItemsHelper::check_section_access($section_id))
											{
	
											}
											elseif( ( (strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type != 'url') || $row->type=='content_category_blog') && $row->id == $pageId ) //&& $row->parent == $pageId
											{
												$title = JText::_('COM_PAGESANDITEMS_PAGE_MOVE_SELECT_SAME_CATEGORY_BLOG');
											}
											else
											{
												//$title = JText::_('COM_PAGESANDITEMS_NO_CATEGORY_BLOG');
											}
										}
										//echo "pages_tree$m.add(".$row->id.",".$row->parent.",'".(addslashes($row->name))."','";
										echo "pages_tree$m.add(".$row->id.",".$row->parent.",";
										echo "'".(addslashes($menuName))."','";

										if($row->id!=$pageId && $row->parent!=$pageId)
										//if(((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url') || $row->type=='content_blog_category') && $row->id!=$pageId && $row->parent!=$pageId)
										//why only do this when the menu-item is of type category blog?
										{
											echo "javascript: select_parent(".$row->id.",\'".$menutypes[$m]."\');";
										}
										else
										{
											echo '';
										}
										/*
										if(((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type=='url') || !strstr($row->link, 'index.php?option=com_content&view=category&layout=blog')) && $row->type!='content_blog_category'){
											echo "','','','components/com_pagesanditems/images/link.gif','components/com_pagesanditems/images/link.gif";
										}else{
											echo "','','','components/com_pagesanditems/images/page.gif','components/com_pagesanditems/images/page.gif";
										}
										*/

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
							}//end menutypes loop
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
// $this->controller->display_footer();
?>