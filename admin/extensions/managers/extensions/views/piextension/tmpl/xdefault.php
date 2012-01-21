<?php 
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die('Restricted access'); ?>

<?php 

JHTML::_('behavior.tooltip');


	// clean item data
	JFilterOutput::objectHTMLSafe( $this->plugin, ENT_QUOTES, '' );
?>
<?php
	$this->plugin->nameA = '';
	if ( $this->plugin->extension_id ) 
	{
		$row->nameA = '<small><small>[ '. $this->plugin->name .' ]</small></small>';
	}
?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		if (pressbutton == "manageextension.cancel") {
			submitform(pressbutton);
			return;
		}
		// validation
		var form = document.adminForm;
		if (form.name.value == "") {
			alert( "<?php echo JText::_( 'Plugin must have a name', true ); ?>" );
		} else if (form.element.value == "") {
			alert( "<?php echo JText::_( 'Plugin must have a filename', true ); ?>" );
		} else {
			submitform(pressbutton);
		}
	}
</script>

<form action="index.php" method="post" name="adminForm">
<fieldset class="adminform">
<h1>
	<?php echo JText::_('COM_PAGESANDITEMS_EXTENSIONS');?>: 
	<small>
		[<?php echo JText::_('edit');?>]
	</small>
</h1>
</fieldset>
<div class="col width-60" style=" float: left;">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Details' ); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" class="key">
				<label for="name">
					<?php echo JText::_( 'Name' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->plugin->element; ?>
				<input class="text_area" type="hidden" name="element" id="element" size="35" value="<?php echo $this->plugin->element; ?>" />
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label for="displayName">
					<?php echo JText::_( 'displayName' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->plugin->name; ?>
				<input class="text_area" type="hidden" name="name" id="name" size="35" value="<?php echo $this->plugin->name; ?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" class="key">
				<?php echo JText::_( 'Published' ); ?>:
			</td>
			<td>
				<?php 
					if( ($this->plugin->type == 'itemtype' && $this->plugin->element == 'content') || ($this->plugin->type == 'pagetype' && $this->plugin->version == 'integrated'))
					{
						if($this->plugin->enabled)
						{
							echo JText::_('YES');
						}
						else
						{
							echo JText::_('NO');
						}
						//echo $this->plugin->enabled;
					}
					else
					{
						echo $this->lists['published']; 
					}
				?>
			</td>
		</tr>

		<tr>
			<td valign="top" class="key">
				<?php echo JText::_( 'Version' ); ?>:
			</td>
			<td>
				<?php echo $this->plugin->version; ?>
			</td>
		</tr>
		
		<tr>
			<td valign="top" class="key">
				<label for="folder">
					<?php echo JText::_( 'Type' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->plugin->type; ?>
			</td>
		</tr>
		<?php
		/*
		<tr>
			<td valign="top" class="key">
				<label for="access">
					<?php echo JText::_( 'Access Level' ); ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['access']; ?>
			</td>
		</tr>

		<tr>
			<td valign="top" class="key">
				<?php echo JText::_( 'Order' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['ordering']; ?>
			</td>
		</tr>
		*/
		?>
		<tr>
			<td valign="top" class="key">
				<?php echo JText::_( 'Description' ); ?>:
			</td>
			<td>
				<?php echo html_entity_decode(JText::_( $this->plugin->description )); //htmlspecialchas ?>
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div class="col width-40" style=" float: right;">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Parameters' ); ?></legend>
	<?php

		if($this->params)
		{
			jimport('joomla.html.pane');
			$pane = &JPane::getInstance('sliders', array('allowAllClose' => true));
			echo $pane->startPane('plugin-pane');
			echo $pane->startPanel(JText :: _('Plugin Parameters'), 'param-page');
			if($output = $this->params->render('params')) :
				echo $output;
			else :
				echo "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
			endif;
			echo $pane->endPanel();

			if ($this->params->getNumParams('advanced')) 
			{
				echo $pane->startPanel(JText :: _('Advanced Parameters'), "advanced-page");
				if($output = $this->params->render('params', 'advanced')) :
					echo $output;
				else :
					echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('There are no advanced parameters for this item')."</div>";
				endif;
				echo $pane->endPanel();
			}

			if ($this->params->getNumParams('legacy')) 
			{
				echo $pane->startPanel(JText :: _('Legacy Parameters'), "legacy-page");
				if($output = $this->params->render('params', 'legacy')) :
					echo $output;
				else :
					echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('There are no legacy parameters for this item')."</div>";
				endif;
				echo $pane->endPanel();
			}
			echo $pane->endPane();
		}
		else
		{
		echo "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
		}
	?>
	</fieldset>
</div>
<div class="clr"></div>

	<input type="hidden" name="option" value="com_pagesanditems" />
	<input type="hidden" name="id" value="<?php echo $this->plugin->extension_id; ?>" />
	<input type="hidden" name="extension_id" value="<?php echo $this->plugin->extension_id; ?>" />
	<input type="hidden" name="type" id="type" value="<?php echo $this->plugin->type; ?>" />
	<input type="hidden" name="folder" id="folder" value="<?php echo $this->plugin->folder; ?>" />

	<input type="hidden" name="cid[]" value="<?php echo $this->plugin->extension_id; ?>" />
	<input type="hidden" name="client" value="<?php echo $this->plugin->client_id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
//echo $this->loadTemplate('footer'); 
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>