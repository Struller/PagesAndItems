<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('_JEXEC') or die('Restricted access'); ?>

<?php

JHTML::_('behavior.tooltip');


	// clean item data
	JFilterOutput::objectHTMLSafe( $this->item, ENT_QUOTES, '' );
?>
<?php
	/*
	$this->item->nameA = '';
	if ( $this->item->extension_id )
	{
		$row->nameA = '<small><small>[ '. $this->plugin->name .' ]</small></small>';
	}
	*/
	$tmpl = JRequest::getVar('tmpl', 0 );
?>
<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function(pressbutton)
	{
		if (pressbutton == "piextension.cancel")
		{
			document.getElementById('extensionTask').value = pressbutton;
			document.adminForm.submit();
			//Joomla.submitform( ); //pressbutton, document.getElementById('adminForm' ));
			//submitform(pressbutton);
			return;
		}
		// validation
		<?php
		/*
		var form = document.adminForm;
		if (form.name.value == "")
		{
			alert( "<?php echo JText::_( 'Plugin must have a name', true ); ?>" );
		}
		else if (form.element.value == "")
		{
			alert( "<?php echo JText::_( 'Plugin must have a filename', true ); ?>" );
		}
		else
		*/
		?>
		if (pressbutton == "piextension.save")
		{

			document.getElementById('extensionTask').value = pressbutton;
			document.adminForm.submit();
			//submitform(pressbutton);
		}
		else if (pressbutton == "piextension.apply")
		{
			document.getElementById('extensionTask').value = pressbutton;
			document.adminForm.submit();
			//submitform(pressbutton);
		}

	}
</script>
<div id="page_content">
<!-- begin id="form_content" need for css-->
<div id="form_content">
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php





/*
<fieldset class="adminform">

<h1>
	<?php echo JText::_('COM_PAGESANDITEMS_EXTENSIONS');?>:
	<small>
		[<?php echo JText::_('edit');?>]
	</small>
</h1>

</fieldset>


COM_PAGESANDITEMS_EXTENSION="Erweiterung"
COM_PAGESANDITEMS_EXTENSIONS="Erweiterungen"
COM_PAGESANDITEMS_EDIT="Bearbeiten"
COM_PAGESANDITEMS_MANAGEEXTENSIONS="Erweiterungen verwalten"


*/

$hasTipType = JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_'.strtoupper ($this->form->getValue('type')).'_1').'::'.JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_'.strtoupper ($this->form->getValue('type')).'_2');
?>
<div class="col width-60" style=" float: left;">
	<fieldset class="adminform">
	<legend class="hasTip" title="<?php echo $hasTipType;?>"><?php echo JText::_('JDETAILS') ?>
	</legend>

	 <?php /*echo JText::_('COM_PAGESANDITEMS_EXTENSION');?>: <?php echo JText::_('COM_PAGESANDITEMS_MANAGE_EXTENSIONS_TIP_'.$this->form->getValue('type').'1');?></legend> */?>

			<ul class="adminformlist">
			<li>
			<?php
				echo $this->form->getLabel('name');
			//$this->form->setFieldAttribute('name', 'hidden','true');
				//echo '<span style="display:none;">';
				$this->form->setFieldAttribute('name', 'type', 'hidden');
				echo $this->form->getInput('name');
				//echo '</span>';
				echo '<span class="readonly plg-name">'.JText::_($this->item->name).'</span>';
			?>
			</li>

			<?php
			if( ($this->item->type == 'itemtype' && ($this->item->element == 'content' || $this->item->element == 'text')) || ($this->item->type == 'pagetype' && $this->item->version == 'integrated')  || ($this->item->type == 'manager' && $this->item->element == 'extensions' && $this->item->version == 'integrated') || $this->item->type == 'language' )
			{
				$this->form->setFieldAttribute('enabled', 'readonly','true');
				//$this->form->setFieldAttribute('enabled', 'disabled', 'disabled');
			}
			//$this->form->setFieldAttribute('enabled', 'disabled', true);

			?>

			<li><?php echo $this->form->getLabel('enabled'); ?>
			<?php echo $this->form->getInput('enabled'); ?></li>

			<li><?php echo $this->form->getLabel('version'); ?>
			<?php echo $this->form->getInput('version'); ?></li>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>

			<li>
				<?php echo $this->form->getLabel('folder'); ?>
				<?php
					if($this->form->getValue('folder') != '')
					{
						$view = '';
						if(strpos($this->form->getValue('folder'), '_') !== false)
						{
							list($view,$type) = explode('_',$this->form->getValue('folder'));
						}
						$image = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'images'.DS.'view_'.$view.'.png');
						//if(file_exists($image))
						if($image)
						{
							JHTML::_( 'behavior.modal' );
							$image = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',$image));
							$value = '<span class="editlinktip readonly plg-name"><a class="modal hasTip" title="'.JText::_('COM_PAGESANDITEMS_EXTENSION_FOLDER_TIP').'" href="'.$image.'" >';
								$value .= $this->form->getValue('folder');
							$value .= '</a></span>';
							echo $value;
							$this->form->setFieldAttribute('folder', 'type', 'hidden');
						}
					}
					else
					{
						echo '<input type="text" readonly="readonly" size="20" class="readonly" value="'.JText::_('JOPTION_UNASSIGNED').'" >';
						//
					}
					echo $this->form->getInput('folder');
					//echo JText::_('JOPTION_UNASSIGNED');
				?>
			</li>

			<li>
				<?php
					echo $this->form->getLabel('type');
				?>
				<?php
					//class="hasTip" title="echo $hasTipType;"
					$value = '<span class="hasTip editlinktip readonly plg-name" title="'.$hasTipType.'" href="#">';
					$value .= $this->form->getValue('type');
							//$value .= '<img src="/images/stories/thumb_picture.jpg" border="0" alt="Ein Bild"> ';
					$value .= '</span>';
					echo $value;
					//$this->form->setFieldAttribute('type', 'value', $this->form->getValue('type'));
					//$this->form->setValue('type', null, $value);
					// set the fields path
					//JForm::addFieldPath(realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'models'.DS.'fields'));
					//$this->form->setFieldAttribute('type', 'type', 'aimage');
					//$this->form->setFieldAttribute('type', 'type', 'html');
					$this->form->setFieldAttribute('type', 'type', 'hidden');
					echo $this->form->getInput('type');
				?>
			</li>

			<li><?php echo $this->form->getLabel('element'); ?>
			<?php echo $this->form->getInput('element'); ?></li>

			<li><?php echo $this->form->getLabel('access'); ?>
			<?php echo $this->form->getInput('access'); ?></li>

			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>

			<li><?php echo $this->form->getLabel('state'); ?>
			<?php echo $this->form->getInput('state'); ?></li>


			<?php if ($this->item->extension_id) : ?>
				<li><?php echo $this->form->getLabel('extension_id'); ?>
				<?php echo $this->form->getInput('extension_id'); ?></li>
			<?php endif; ?>
			</ul>
			<!-- Plugin metadata -->
			<?php if ($this->item->xml) : ?>
				<?php if ($text = trim($this->item->xml->description)) : ?>
					<label id="jform_extdescription-lbl">
						<?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>
						</label>
						<div class="clr"></div>
						<span class="readonly plg-desc"><?php echo JText::_($text); ?></span>

				<?php endif; ?>
			<?php else : ?>
				<?php if ($this->item->type != 'language') : ?>
					<?php echo JText::_('COM_PLUGINS_XML_ERR'); ?>
				<?php endif; ?>
			<?php endif; ?>
	</fieldset>
</div>

<?php

	JHTML::_('behavior.tooltip');
	JHtml::_('behavior.formvalidation');
?>

<div class="col width-40" style=" float: right;">
	<?php
	/*
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'JFIELD_PARAMS_LABEL');//Parameters' ); ?></legend>
	*/
	?>
	<?php echo JHtml::_('sliders.start','plugin-sliders-'.$this->item->extension_id); ?>
	<?php echo $this->loadTemplate('options'); ?>
	<?php echo JHtml::_('sliders.end'); ?>
	<?php
	/*
	</fieldset>
	*/
	?>
</div>
<div class="clr"></div>

<?php
/*
<?php if ($this->item->type == 'language') : ?>
	<?php echo $this->loadTemplate('languages'); ?>
<?php endif; ?>
*/
?>
<?php
//

//loadTemplate
?>

	<input type="hidden" name="option" value="com_pagesanditems" />
	<input type="hidden" id="task" name="task" value="extension.doExecute" />
	<input type="hidden" id="extensionName" name="extensionName" value="extensions" />
	<input type="hidden" id="extensionTask" name="extensionTask" value="display" />
	<input type="hidden" id="extensionType" name="extensionType" value="manager" />
	<input type="hidden" id="extensionFolder" name="extensionFolder" value="" />
	<input type="hidden" id="view" name="view" value="piextension" />



	<input type="hidden" name="id" value="<?php echo $this->item->extension_id; ?>" />
	<input type="hidden" name="extension_id" value="<?php echo $this->item->extension_id; ?>" />
	<input type="hidden" name="type" id="type" value="<?php echo $this->item->type; ?>" />
	<input type="hidden" name="folder" id="folder" value="<?php echo $this->item->folder; ?>" />

	<input type="hidden" name="cid[]" value="<?php echo $this->item->extension_id; ?>" />
	<input type="hidden" name="client" value="<?php echo $this->item->client_id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<!-- end id="form_content" need for css-->
</div>
<!-- end id="page_content"-->
</div>
<?php
//echo $this->loadTemplate('footer');
if(!$tmpl)
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>
