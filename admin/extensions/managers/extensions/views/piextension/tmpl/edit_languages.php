<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;

//$hasTipType = 'languages';
?>

<div class="col width-100" style=" float: left;">
	<fieldset class="adminform">
	<legend><?php echo JText::_('COM_PAGESANDITEMS_LANGUAGE_FILES') ?>
	</legend>

<?php
echo 'languages';
/*
here we add an table with all language files
include the component language-files must search in the xml

columns are: name, delete(an button), version (from xml or from ini),



$this->languageItems

version and other informations:
*/
/*

rows:
 en-GB
 component language files
 extensions language files

$tag = $item->element;

*/
?>
	</fieldset>
</div>

<?php

?>
