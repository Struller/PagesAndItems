<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die('Restricted access');?>


<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tbody>
		<tr>
			<td  valign="top" width="25%">
<?php
	echo $this->pageTree;
?>
			</td>
			<td valign="top">
				<table class="adminform">
					<tbody>
						<tr>
							<th>
								here we set the Archive content archiveType = <?php echo $this->archiveType; ?>
							</th>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php
/*
<!--the stylesheets must load into document not header so no other stylesheets can  override it \\-->
<link href="components/com_pagesanditems/css/pagesanditems2.css" rel="stylesheet" type="text/css" />
<link href="components/com_pagesanditems/css/dtree.css" rel="stylesheet" type="text/css" />
<?php
$this->controller->display_footer();
*/
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>