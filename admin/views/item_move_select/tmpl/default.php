<?php
/**
* @version		2.1.2
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC')){
	die('Restricted access');
}

//get data of item
$item_id = JRequest::getVar('item_id', '' );

$pageId = JRequest::getVar('pageId', '' );
$categoryId = JRequest::getVar('categoryId', '' );
$this->db->setQuery("SELECT catid, title FROM #__content WHERE id=$item_id LIMIT 1");
$rows = $this->db->loadObjectList();
$row = $rows[0];
$old_cat_id = $row->catid;
$title = $row->title;
//get itemtype from item_index
$this->db->setQuery("SELECT itemtype FROM #__pi_item_index WHERE item_id=$item_id LIMIT 1");
$rows = $this->db->loadObjectList();
$item_type = null;
if(count($rows))
{
	$row = $rows[0];
	$item_type = $row->itemtype;
}
if(!$item_type)
{
	$item_type = 'text';
	$item_type = 'content';
}
/*
echo "<script src=\"components/com_pagesanditems/javascript/dtree.js\" language=\"JavaScript\" type=\"text/javascript\"></script>\n";
echo "<script src=\"../includes/js/overlib_mini.js\" language=\"JavaScript\" type=\"text/javascript\"></script>\n";
*/
//give headers in Joomla 1.5 a bit more spunk
//$this->model->spunk_up_headers_1_5(); //is in css
?>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<script language="JavaScript" type="text/javascript">
<!--
function select_parent(parent_id){
	document.getElementById('new_parent_id').value = parent_id;
}

Joomla.submitbutton = function(pressbutton) {

	if (pressbutton == 'cancel')
	{
		<?php
		/*
		//document.href
		//document.location.href = 'index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='+<?php echo $pageId; ?>+'&itemId='+<?php echo $item_id; ?>+'&categoryId='+<?php echo $categoryId; ?>;
		*/
		?>
		document.location.href = "<?php echo PagesAndItemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='.$pageId.'&itemId='.$item_id.'&categoryId='.$categoryId); ?>";
	}	
	if (pressbutton == 'item.item_move_save')
	{
		
		if (document.adminForm.new_parent_id.value == '' )
		{
			alert( '<?php echo JText::_('COM_PAGESANDITEMS_NEED_SELECT_PAGE'); ?>' );
			return;
		} else {
			//document.getElementById('task').value = pressbutton;
			document.adminForm.submit();
			//submitform(pressbutton);
		}
	}
}
-->
</script>
<?php

/*
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	</tbody>
	<tr><td  valign="top" width="20%">
	</td><td valign="top">
		<div id="pi_breadcrumb">
			<?php
			$url = PagesanditemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit&amp;pageId='.$pageId.'&amp;categoryId='.$categoryId);
			//$url = 'index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit&amp;pageId='.$pageId.'&amp;categoryId='.$categoryId;
			?>
			
			<a href="<?php echo $url; ?>"><?php echo JText::_('COM_PAGESANDITEMS_PAGE'); ?></a> >  <a href="index.php?option=com_pagesanditems&amp;view=item&amp;sub_task=edit&amp;pageId=<?php echo $pageId; ?>&amp;categoryId=<?php echo $categoryId; ?>&amp;itemId=<?php echo $item_id; ?>">
			<?php echo JText::_('COM_PAGESANDITEMS_ITEM').' ['.$this->model->translate_item_type($item_type).']'; ?></a> > <?php echo JText::_('COM_PAGESANDITEMS_ITEMMOVE'); ?>
		</div>
	</td></tr>
	</tbody>
</table>
*/
	//$link = PagesanditemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit&amp;pageId='.$pageId.'&amp;categoryId='.$categoryId,'&amp;');
	$link = PagesanditemsHelper::toogleViewPageCategories('index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit'.($pageId ? '&amp;pageId='.$pageId : '').($categoryId ? '&amp;categoryId='.$categoryId : ''));
	$html = '<a href="'.$link.'">'; //<a href="index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit&amp;pageId='.$pageId.'&amp;categoryId='.$categoryId.'">';
		$html .= PagesanditemsHelper::toogleTextPageCategories('COM_PAGESANDITEMS_PAGE');
	$html .= '</a>';
	$html .= ' &gt; ';
	$html .= '<a href="index.php?option=com_pagesanditems&amp;view=item&amp;sub_task=edit&amp;pageId='.$pageId.'&amp;categoryId='.$categoryId.'&amp;itemId='.$item_id.'">';
			$html .= JText::_('COM_PAGESANDITEMS_ITEM').' ['.PagesAndItemsHelper::translate_item_type($item_type).']';
		$html .= '</a>';
		$html .= ' &gt; ';
		$html .= JText::_('COM_PAGESANDITEMS_ITEMMOVE');

	echo PagesAndItemsHelper::breadcrumb($html);


?>
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
			<input type="hidden" name="task" value="item.item_move_save" />
			<input type="hidden" name="pageId" value="<?php echo $pageId; ?>">
			<input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
			<input type="hidden" name="old_cat_id" value="<?php echo $old_cat_id; ?>" />			
			<input type="hidden" name="categoryId" value="<?php echo $categoryId; ?>">
			<?php
			/*
			<div id="pi_breadcrumb">
				<a href="index.php?option=com_pagesanditems&amp;view=page&amp;sub_task=edit&amp;pageId=<?php echo $pageId; ?>"><?php echo JText::_('COM_PAGESANDITEMS_PAGE'); ?></a> >  <a href="index.php?option=com_pagesanditems&amp;view=item&amp;sub_task=edit&amp;pageId=<?php echo $pageId; ?>&amp;itemId=<?php echo $item_id; ?>"><?php echo JText::_('COM_PAGESANDITEMS_ITEM').' ['.PagesAndItemsHelper::translate_item_type($item_type).']'; ?></a> > <?php echo JText::_('COM_PAGESANDITEMS_ITEMMOVE'); ?>
			</div>
			*/
			?>
		<table class="piadminform xadminform">
			<thead class="piheader">
				<tr>
					<th> <!-- class="piheader">-->
						<?php echo JText::_('COM_PAGESANDITEMS_MOVE_ITEM'); ?>: "<?php echo $title; ?>"
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<p><br /><?php 
							echo JText::_('COM_PAGESANDITEMS_SELECT_PAGE').'. '.JText::_('COM_PAGESANDITEMS_MENUTYPE'); ?>
							 'Category Blog'.
						</p>
						<p>
							<input type="text" name="new_parent_id" id="new_parent_id"  value="" style="width: 50px;" />
						</p>
					</td>
				</tr>
				<tr>
					<td>
						<?php
						//TODO same as in page/item only other link
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

									//check section access, only get all data if we realy need to

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
										if( ( (strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type != 'url') || $row->type=='content_category_blog') && $row->id !=$pageId ) //&& PagesAndItemsHelper::check_section_access($section_id))
										{

										}
										elseif( ( (strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type != 'url') || $row->type=='content_category_blog') && $row->id ==$pageId ) //&& PagesAndItemsHelper::check_section_access($section_id))
										{
											$title = JText::_('COM_PAGESANDITEMS_ITEM_MOVE_SELECT_SAME_CATEGORY_BLOG');
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
									if(((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type!='url') || $row->type=='content_blog_category') && $row->id!=$pageId ) //&& PagesAndItemsHelper::check_section_access($section_id))
									{
										echo 'javascript: select_parent('.$row->id.');';
									}
									else
									{
										//echo '#'; //echo 'XX';
									}

									//if(((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type=='url') || !strstr($row->link, 'index.php?option=com_content&view=category&layout=blog')) && $row->type!='content_blog_category')
									//{
									//	echo "','','','components/com_pagesanditems/images/link.gif','components/com_pagesanditems/images/link.gif";
									//}
									//else
									//{
									//	echo "','','','components/com_pagesanditems/images/page.gif','components/com_pagesanditems/images/page.gif";
									//}


									//we will always set an title
									//but dTree will only set an title to an <a>

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
