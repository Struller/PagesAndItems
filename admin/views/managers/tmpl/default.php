<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}
//$path = str_replace(DS,'/',str_replace(JPATH_SITE,'',JPATH_COMPONENT_ADMINISTRATOR));
JHTML::_('behavior.tooltip');
$managers =& $this->managers;
?>
<!-- begin id="form_content" need for css-->
<div id="form_content">
<form action="index.php" method="post" name="adminForm">
	<div id="cpanel">
	<?php
	/*
		here we set the panels
	*/
	foreach($managers as $manager)
	{
		echo '<div style="float: left;">';
			echo '<div class="icon">';
				echo '<a href="'.$manager->link.'">';
					echo '<img alt="'.$manager->alt.'" src="'.$manager->image.'">';
					echo '<span>';
						echo $manager->text;
					echo '</span>';
				echo '</a>';
			echo '</div>';
		echo '</div>';
	}
	?>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="option" value="com_pagesanditems" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="managers" />
</form>
<!-- end id="form_content" need for css-->
</div>
<?php
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>