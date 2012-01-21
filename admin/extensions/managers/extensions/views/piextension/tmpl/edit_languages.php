<?php
/**
 * @version		1.6.2.2$Id: edit_options.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
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
//dump($this->item);
?>
	</fieldset>
</div>

<?php

?>
