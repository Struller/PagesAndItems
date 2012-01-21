<?php 
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// no direct access
	defined( '_JEXEC' ) or die( 'Restricted access' ); 
	//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_installer'.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_message.php');

	$state			= &$this->get('State');
/*
	$msg = $state->get('msg');
	$type = $state->get('type');
	$subtype = $state->get('subtype');
	$name = $state->get('name');
	$result = $state->get('result');
	
	if($msg)
	{
		$app = &JFactory::getApplication();
		if($subtype)
		{
			
			$type = JText::_($subtype);
		}
		else
		{
			$type = JText::_($type);
		}
		if($result)
		{
			
			$msg = JText::sprintf('COM_PAGESANDITEMS_INSTALLEXT', $type, JText::_('COM_PAGESANDITEMS_INSTALLEXT_SUCCESS'));
			//	$msg = JText::_('Error');
				//JText::_($package['type']), JText::_('Error'));
			
			$app->enqueueMessage($msg, 'message' );
		}
		else
		{
			$msg = JText::sprintf('COM_PAGESANDITEMS_INSTALLEXT', $type, JText::_('COM_PAGESANDITEMS_INSTALLEXT_FAILED'));
			$app->enqueueMessage($msg,'Error');
		}
	}
*/
	$message1		= $state->get('message');
	$message2		= $state->get('extension.message');
?>
		<?php if($message1) : ?>
		<fieldset class="uploadform">
			<?php echo JText::_($message1) ?>
		</fieldset>
		<?php endif; ?>
		<?php if($message2) : ?>
		<fieldset class="uploadform">
			<?php echo $message2; ?>
		</fieldset>
		<?php endif; ?>


<?php
/*
<table class="adminform">
	<tbody>
		<?php if($message1) : ?>
		<tr>
			<th><?php echo JText::_($message1) ?></th>
		</tr>
		<?php endif; ?>
		<?php if($message2) : ?>
		<tr>
			<td><?php echo $message2; ?></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
*/
?>