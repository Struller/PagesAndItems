<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/



defined('_JEXEC') or die('Restricted access');


?>

<div id="imageFormDiv" name="imageFormDiv" >
	<form action="index.php" id="xtdForm" name="xtdForm" method="post" enctype="multipart/form-data">
	<fieldset id="fieldset_top">
		<?php echo PagesAndItemsHelper::getHeaderImageTitle(PagesAndItemsHelper::getDirIcons().'icon-32-pi.png',JText::_( 'COM_PAGESANDITEMS').' :: <small><small> '.JText::_('COM_PAGESANDITEMS_SELECT_MENU_ITEM_TYPE').'</small></small>'); ?>
		<?php
		/*
		<div >
			<h1 class="pi_h1" >
			<img src="<?php echo PagesAndItemsHelper::getDirIcons(); ?>icon-32-pi.png" alt="..." class="pi_icon" />
			<?php echo
				JText::_( 'COM_PAGESANDITEMS').' :: <small><small> '.JText::_('COM_PAGESANDITEMS_SELECT_MENU_ITEM_TYPE').'</small></small>';
			?>
			</h1>

		</div>
		*/
		?>
	</fieldset>
	<fieldset id="fieldset_content">
		<div id="contentcontainer" name="contentcontainer" class="contentcontainer" >

			<?php
			/*
			<div id="tree_container" class="tree_container" style="float: left;height: 100%;overflow: auto;width: 100%;">
			</div>
			*/
			//echo '<div>';
				echo $this->menutypes;
			//echo '</div>';

				//echo $this->getMenuItemTypes();
			?>

		</div>
	</fieldset>

	<fieldset class="bottom" id="fieldset_bottom" style="float:none" >

			<div id="li_tag" class="div_left_bottom_path" >

			</div>

			<div class="clr_right">
			</div>
			<?php
			$button = PagesAndItemsHelper::getButtonMaker('close');
			$button->onclick = 'window.parent.document.getElementById(\'sbox-window\').close();';
			$button->style = 'float:right;';
			$htmlButton = $button->makeButton();
			//echo $htmlButton;


			$button = PagesAndItemsHelper::getButtonMaker('cancel');
			$button->onclick = 'window.parent.document.getElementById(\'sbox-window\').close();';
			$button->style = 'float:right;';
			$htmlButton = $button->makeButton();
			echo $htmlButton;
			?>
			<?php
			/*
			<div class="div_button" style="float:right">
					<button class="button_action" name="close-button" id="button_close" type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('Cancel') ?>
			</button>

								<button class="button_action" name="close-button" id="button_close" type="button" onclick="
					<?php if(PagesAndItemsHelper::getIsJoomlaVersion('>=','1.6')){ echo 'window.parent.SqueezeBox.close();';}else{echo'window.parent.document.getElementById(\'sbox-window\').close();';} ?>"><?php echo JText::_('Cancel') ?>

			<button name="ok-button" class="button_action" id="button_ok" type="button" onclick="onok();//alert('test');//XtdManager.onok();"><?php echo JText::_('Ok') ?>
			*/
			?>
			</button>
		</div>
	</fieldset>
<?php
/*
	<input type="hidden" name="option" value="com_ginkgo" />
	<input type="hidden" name="view" value="miftree" />
	<input type="hidden" name="task" value="indicators.display" />
	<input type="hidden" name="controller" value="indicators" />
	<input type="hidden" name="tmpl" value="component" />
*/
	/*
	$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../../../../../../../')));
	//add css over JHTML::stylesheet ?
	//JHTML::stylesheet('pagesanditems2.css',$path.'/css/');
	//here we add the stylesheet in the document not in the head
	//
	echo "<link href=\"".JURI::root(true).'/'.$path."/css/pagesanditems2.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
	*/
	$path = PagesAndItemsHelper::getDirCSS(); //here no juri JHTML make the juri

	JHtml::stylesheet($path.'/pagesanditems2.css');
?>
	</form>

</div>

