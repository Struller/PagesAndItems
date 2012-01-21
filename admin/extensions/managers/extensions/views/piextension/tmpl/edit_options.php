<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access.
defined('_JEXEC') or die;
//echo 'test';
$fieldSets = $this->form->getFieldsets('params');
foreach ($fieldSets as $name => $fieldSet) :
	$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_PLUGINS_'.$name.'_FIELDSET_LABEL';
	//$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_PLUGINS_'.$name.'_FIELDSET_LABEL';

	$label_two = '';
	if (isset($fieldSet->label_two))
	{
		$label_two = !empty($fieldSet->label_two) ? $fieldSet->label_two : '';
	}
	$label_three = '';
	if (isset($fieldSet->label_three))
	{
		$label_three = !empty($fieldSet->label_three) ? $fieldSet->label_three : '';
	}
	if($name != 'hidden')
	{
		echo JHtml::_('sliders.panel',JText::_($label).' '.JText::_($label_two).' '.JText::_($label_three), $name.'-options');
		if (isset($fieldSet->description) && trim($fieldSet->description)) :
			echo '<p class="tip">'.$this->escape(JText::_($fieldSet->description)).'</p>';
		endif;
		$hidden = '';
	}
	else
	{
		$hidden = 'style="display:none;"';
	}
	?>
	<fieldset class="panelform" <?php echo $hidden; ?>>
		<?php
		$hidden_fields = '';
		$countHiddenFields = 0;
		$countFields = 0;
		?>
		<ul class="adminformlist">
			<?php foreach ($this->form->getFieldset($name) as $field) : ?>
			<?php if (!$field->hidden) : ?>
			<li>
				<?php echo $field->label; ?>
				<?php echo $field->input;
				$countFields++;
				?>
			</li>
			<?php else : $hidden_fields.= $field->input;
			$countHiddenFields++;
			?>
			<?php endif; ?>
			<?php endforeach; ?>
		</ul>
		<?php echo $hidden_fields;
		if($countHiddenFields == count($this->form->getFieldset($name)))
		{
			//echo JText::_('COM_PAGESANDITEMS_EXTENSION_ONLY_HIDDEN_PARAMS');
		}
		?>
	</fieldset>
<?php
endforeach;

?>
