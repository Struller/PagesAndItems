<?php
/**
* @package PI!Fish
 translating custom itemtype field from Pages and Items using Joom!Fish
* @version 1.6.2.2
* @copyright Copyright (C) 2009-2010 Michael Struller. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author http://gecko.struller.de
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="translateFormDiv" name="translateFormDiv" >
	<form action="index.php" id="adminForm" name="adminForm" method="post" enctype="multipart/form-data">
	<fieldset id="fieldset_top">
		<div >
			<?php 
				$title = '';
				$title .= JText::_('COM_PAGESANDITEMS_TRANSLATION').'::';
				//$title .= JText::_('PI_EXTENSION_FIELDTYPE_PI_FISH_CUSTOM_ITEMTYPE').': ';
				//$title .= $this->typeName.' ';
				//$title .= JText::_('PI_EXTENSION_FIELDTYPE_PI_FISH_CUSTOM_ITEMTYPE_FIELD_SHORT').': ';
				//$title .= $this->fieldName.', ';
				$title .= JText::_('COM_PAGESANDITEMS_ARTICLE').' Id: '.$this->item_id;
			?>
			<h1 class="ginkgo_h1 hasTip" title="<?php echo $title; ?>">
			<img src="<?php echo $this->path ?>/media/images/icons/icon-24-pi.png" alt="..." class="ginkgo_icon" />
			
			<?php 
				echo JText::_('LANGUAGE TITLE');
				echo '&nbsp;';
				echo JText::_('COM_PAGESANDITEMS_TRANSLATION').': <small><small>[' .JText::_('Edit'). '] ';
				//echo '<small>';
					
					//echo '</small>';
				//echo '</small>';
				//echo JText::_('PI_EXTENSION_FIELDTYPE_PI_FISH_CUSTOM_ITEMTYPE_FIELD').':';
				//echo $this->fieldName;
				echo '</small></small> ';
				//.JText::_('LANGUAGE TITLE');
			?>
			</h1>
		</div>
	</fieldset>
	
	<fieldset id="fieldset_content">
		<div id="languageframe" name="languageframe">
			<div id="parent_content" >
			<iframe id="languageiframe" name="languageiframe" class="disabled" src="index.php?option=com_joomfish&amp;task=translate.edit&amp;select_language_id =<?php echo $this->select_language_id; ?>&amp;boxchecked=<?php echo '0'; ?>&amp;catid=<?php echo $this->catid; ?> &amp;cid[]=<?php echo $this->translation_id; ?>|<?php echo $this->joomfish_id; ?>|<?php echo $this->select_language_id; ?>&amp;tmpl=component'" >
			</iframe>
			</div>
		</div>
	</fieldset>
	<fieldset class="bottom" id="fieldset_bottom" >
	
			<?php
				echo '<div id="select_holder" class="div_left_bottom" >';
					$title = '';
					$title .= '<h5 style="margin: 0;">';
					$title .= JText::_('COM_PAGESANDITEMS_ARTICLE').' Id: ';
					$title .= '<small>';
					$title .=$this->item_id;
					$title .= '</small>';
					$title .= '</h5>';
					echo $title;
					//echo $this->langlist;
				echo '</div>';
			?>
			<div class="clr_right">
			</div>
			
			<div class="div_button" >
					<button class="button_action" name="close-button" id="button_close" type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('Cancel') ?>
			</button>
			<button name="button_translate_apply" class="button_action_disabled" disabled="disabled" id="button_translate_apply" type="button" onclick="TranslateManager_itemtype.translateApply();"><?php echo JText::_('APPLY') ?>
			</button>
		</div>
	</fieldset>
	<input type="hidden" name="option" value="com_pagesanditems" />
	
	<input type="hidden" name="task" value="" />

	<input type="hidden" name="view" value="translate_itemtype" />
	<input type="hidden" name="tmpl" value="component" />
	
	<input type="hidden" name="joomfish_id" id="pf_id" value="<?php echo $this->joomfish_id; ?>" />
	<input type="hidden" name="catid" id="catid" value="<?php echo $this->catid; ?>" />
	<input type="hidden" name="iconCss" id="iconCss" value="<?php echo $this->iconCss; ?>" />
	
	<input type="hidden" name="no_language_select_id" id="no_language_select_id" value="<?php echo $this->no_language_select_id; ?>" />
	<input type="hidden" name="no_language_select" id="no_language_select" value="<?php echo $this->no_language_select; ?>" />
	</form>
</div>