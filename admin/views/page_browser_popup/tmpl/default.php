<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC')){
	die('Restricted access');
}

?>
<link href="components/com_pagesanditems/css/popup.css" rel="stylesheet" type="text/css" />
<?php


if(JRequest::getVar('itemsPageId')){//pagebrowser used in link-pop-up on item level
$pageId = JRequest::getVar('itemsPageId');
$selectedPageId = JRequest::getVar('selectedPageId');

//get link from menuitems
$menuitems = $this->model->getMenutypeMenuitems();
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
//var oldmenu = '';
function selectUrl(selectUrl, menuid){
	//if(oldmenu!=''){
		//alert(oldmenu);
		//oldmenu.openTo(0, true);
	//}
	//oldmenu = menuid;
	window.parent.document.getElementById('href').value = selectUrl;
	window.parent.document.getElementById('targetlist').options[0].selected = true;
	window.parent.document.getElementById('ispopup').checked = false;
	window.parent.document.getElementById('title').value = '';
}
</script>
<?php
}else{
//pagebrowser used in pop-up link-to-page-page
$link = urldecode($_GET['url']); 
?>
<script language="JavaScript" type="text/javascript">
var url = '';

function selectUrl(selectUrl, menutype_int){
	url = selectUrl;
}
function parseUrl(){
	window.opener.document.getElementById('link').value = url;
	window.opener.document.getElementById('browserNav').options[0].selected = true;
	window.close();
}
</script>
<?php
}//end if = page-level-link
?>

<?php 
echo "<link href=\"components/com_pagesanditems/css/pagesanditems.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
echo "<link href=\"components/com_pagesanditems/css/dtree.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
echo "<script src=\"components/com_pagesanditems/javascript/dtree.js\" language=\"JavaScript\" type=\"text/javascript\"></script>\n";
echo "<script src=\"../includes/js/overlib_mini.js\" language=\"JavaScript\" type=\"text/javascript\"></script>\n";
//give headers in Joomla 1.5 a bit more spunk
//$this->controller->spunk_up_headers_1_5(); //is in css
		
if(!JRequest::getVar('itemsPageId')){
//pagebrowser used in link-pop-up on item level
?>
		<table class="adminform">
						<tr>
							<th><?php echo JText::_('COM_PAGESANDITEMS_SELECTPAGE'); ?></th>
						</tr>
						<tr>
						
    <td ><div align="right">
	 <input type="button" value="<?php echo JText::_('COM_PAGESANDITEMS_OK'); ?>" onclick="parseUrl();" />&nbsp;&nbsp;<input type="button" value="<?php echo JText::_('COM_PAGESANDITEMS_CANCEL'); ?>" onclick="window.close();" /></div>
	</td></tr>
	<tr>
	<td> 
	
<?php
}//end if link-pop-up on item-level
//see how many loops we need
//$loops = count($this->controller->menutypes);
$menutypes = $this->model->getMenutypes();
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
			$script = "d$m.icon = {";
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
			/*
				COMMENT
				in Joomla 1.6 
				we have one parent_id=0 in table #__menus
				but more parent_id=1 in table #__menus
				
				parent_id=1 in table #__menus = menutype:'', title:Menu_Item_Root, alias:root
				
			*/
			if ($this->joomlaVersion < '1.6')
			{
				echo "d$m.add(0,-1,'";
			}
			else
			{
				echo "d$m.add(1,-1,'";
			}
	
	
	//echo $this->controller->get_menutype_title($this->controller->menutypes[$m]);
	echo $this->model->get_menutype_title($menutypes[$m]);
	echo "','','','','','',true);\n";
	//make javascript-array from main-menu-items
	//foreach($this->controller->menuitems as $row){
	$menuitems = $this->model->getMenuitems();
	foreach($menuitems as $row){
		//if($row->menutype==$this->controller->menutypes[$m]){
		if($row->menutype==$menutypes[$m]){
			echo "d$m.add(".$row->id.",".$row->parent.",'".(addslashes($row->name))."','";
			if($row->id!=$pageId){
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
	if(JRequest::getVar('itemsPageId', '' )){
		//pagebrowser used in link-pop-up on item level
		//foreach($this->controller->menuitems as $row){
		foreach($menuitems as $row){
			//if($row->id==$selectedPageId && $row->menutype==$this->controller->menutypes[$m]){
			if($row->id==$selectedPageId && $row->menutype==$menutypes[$m]){
				echo "d$m.openTo(";
				echo $row->id;
				echo ", true);\n";
			}
		}
	}else{
	
		echo "if(window.opener){\n";
		echo "option = window.opener.document.getElementById('option').value;\n";
		echo "if(option=='com_pagesanditems'){\n";
		//if($option=='com_pagesanditems'){
			echo "url = window.opener.document.getElementById('link').value;\n";
			echo "posItemId = url.indexOf('&Itemid=');\n";
			echo "if(posItemId!=-1){\n";
				//get the id of the link-page to select
				echo "startItemId = posItemId + 8;\n";
				echo "selectedPageId = url.slice(startItemId);\n";
				//check if id is in current menutype-loop
				echo "for (i=0; i<d".$m."_array.length; i++){\n";
					echo "if(selectedPageId==d".$m."_array[i]){\n";
						echo "d".$m.".openTo(selectedPageId, true);\n";
						//echo "alert('in=".$m." page='+selectedPageId+' array='+d".$m."_array);\n";
						echo "break;\n";
					echo "}\n"; 
				echo "}\n";
			echo "}\n";
		//}
		echo "}\n";
		echo "}\n";
        
	}
	//close javascript
	echo "//-->\n";
	echo "</script>\n";
	
	echo '</div>';
	
}//end loop menutypes
if(!JRequest::getVar('itemsPageId', '' )){
	//pagebrowser used in link-pop-up on item level
	?>
	</td></tr></table> 
	<?php
}
?>