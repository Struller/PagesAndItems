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

?>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<link href="components/com_pagesanditems/css/popup.css" rel="stylesheet" type="text/css" />
<?php

$pageId = JRequest::getVar('itemsPageId');
$selectedPageId = JRequest::getVar('selectedPageId');
//get link from menuitems
$menuitems = PagesAndItemsHelper::getMenutypeMenuitems();

//foreach($this->controller->menuitems as $row){
foreach($menuitems as $row){
	if($row->id==$pageId){
		$link = $row->link;
	}
}
?>
<style type="text/css">
#minwidth{
	min-width: 700px;
}
</style>
<script language="JavaScript" type="text/javascript">
function selectUrl(selectUrl, menuid){
	window.parent.document.getElementById('href').value = selectUrl;
	window.parent.document.getElementById('targetlist').options[0].selected = true;
	window.parent.document.getElementById('ispopup').checked = false;
	window.parent.document.getElementById('title').value = '';
}
</script>
<?php
// TODO CHECK 
echo "<link href=\"components/com_pagesanditems/css/pagesanditems2.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
// TODO CHECK 
echo "<link href=\"components/com_pagesanditems/css/dtree.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
// TODO CHECK 
echo "<script src=\"components/com_pagesanditems/javascript/dtree.js\" language=\"JavaScript\" type=\"text/javascript\"></script>\n";
//echo "<script src=\"../includes/js/overlib_mini.js\" language=\"JavaScript\" type=\"text/javascript\"></script>\n";
//give headers in Joomla 1.5 a bit more spunk
//$this->controller->spunk_up_headers_1_5(); //is in css

//see how many loops we need
//$loops = count($this->controller->menutypes);
$menutypes = PagesAndItemsHelper::getMenutypes();
$loops = count($menutypes);
//loop menutypes
for($m = 0; $m < $loops; $m++){
	echo '<div class="dtree">';
	echo '<p><a href="javascript: d'.$m.'.openAll();">'.JText::_('COM_PAGESANDITEMS_OPEN_ALL').'</a> | <a href="javascript: d'.$m.'.closeAll();">'.JText::_('COM_PAGESANDITEMS_CLOSE_ALL').'</a></p>';
	//open javascript
	echo "<script type=\"text/javascript\"  type=\"text/javascript\">\n";
	echo "<!--\n";
	//echo "d".$m."_array = new array('d".$m."_array');\n";
	echo "var d".$m."_array = new Array('d".$m."_array');\n";
	echo "d$m = new dTree('d$m');\n";
	echo PagesAndItemsHelper::getdTreeIcons("d".$m);
			/*
				COMMENT
				in Joomla 1.6
				we have one parent_id=0 in table #__menus
				but more parent_id=1 in table #__menus

				parent_id=1 in table #__menus = menutype:'', title:Menu_Item_Root, alias:root

			*/
			if (PagesAndItemsHelper::getIsJoomlaVersion('<','1.6'))
			{
				echo "d$m.add(0,-1,'";
			}
			else
			{
				echo "d$m.add(1,-1,'";
			}


	echo PagesAndItemsHelper::getMenutypeTitle($menutypes[$m]);
	echo "','','','','','',true);\n";
	//make javascript-array from main-menu-items
	//foreach($this->controller->menuitems as $row){
	$menuitems = PagesAndItemsHelper::getMenuitems();
	foreach($menuitems as $row){
		//if($row->menutype==$this->controller->menutypes[$m]){
		if($row->menutype==$menutypes[$m]){
			echo "d$m.add(".$row->id.",".$row->parent.",'".(addslashes($row->name))."','";
			if($row->id!=$pageId){
				//?? Itemid not itemId ?
				if(strpos($row->link, "&Itemid=")){
					$stringItemId = "";
				}else{
					$stringItemId = "&Itemid=".$row->id;
				}
				echo "javascript: selectUrl(\'".$row->link.$stringItemId."\',\'d$m\');";
			}
			if(((strstr($row->link, 'index.php?option=com_content&view=category&layout=blog') && $row->type=='url') || !strstr($row->link, 'index.php?option=com_content&view=category&layout=blog')) && $row->type!='content_blog_category'){
									echo "','','','components/com_pagesanditems/images/link.gif','components/com_pagesanditems/images/link.gif";
								}else{
									echo "','','','components/com_pagesanditems/images/page.gif','components/com_pagesanditems/images/page.gif";
								}
			echo "');\n";
			echo "d".$m."_array.push($row->id);\n";
		}
	}

	echo "document.write(d$m);\n";

	//if a page was already selected, make tree-menu-button selected
	//foreach($this->controller->menuitems as $row){
		foreach($menuitems as $row){
			//if($row->id==$selectedPageId && $row->menutype==$this->controller->menutypes[$m]){
			if($row->id==$selectedPageId && $row->menutype==$menutypes[$m]){
			echo "d$m.openTo(";
			echo $row->id;
			echo ", true);\n";
		}
	}
	//close javascript
	echo "//-->\n";
	echo "</script>\n";

	echo '</div>';

}//end loop menutypes
?>
</td>
</tr>
</table> 
<!-- end id="form_content" need for css-->
</div>