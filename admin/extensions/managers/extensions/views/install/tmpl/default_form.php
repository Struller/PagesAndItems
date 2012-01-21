<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

 // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//echo '123';
//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_installer'.DS.'views'.DS.'install'.DS.'tmpl'.DS.'default_form.php');

$option = JRequest::getVar('option');
$version = new JVersion();
$joomlaVersion = $version->getShortVersion();
if($joomlaVersion < '1.6')
{
	//joomla 1.5.x
	//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_installer'.DS.'views'.DS.'install'.DS.'view.php');
?>
<script language="javascript" type="text/javascript">
<!--
	function submitbutton3(pressbutton) {
		var form = document.adminForm;

		// do field validation
		if (form.install_directory.value == ""){
			alert( "<?php echo JText::_( 'Please select a directory', true ); ?>" );
		} else {
			form.installtype.value = 'folder';
			form.submit();
		}
	}

	function submitbutton4(pressbutton) {
		var form = document.adminForm;

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://"){
			alert( "<?php echo JText::_( 'Please enter a URL', true ); ?>" );
		} else {
			form.installtype.value = 'url';
			form.submit();
		}
	}
//-->
</script>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">

	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>

	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'Upload Package File' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_package"><?php echo JText::_( 'Package File' ); ?>:</label>
		</td>
		<td>
			<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
			<?php
			//$path = str_replace(DS,'/',str_replace(JPATH_SITE,'',JPATH_COMPONENT_ADMINISTRATOR));

			/*
			**********************
			* Button upload-file *
			**********************
			*/
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->buttonType = 'input';
			$button->text = JText::_( 'Upload File' ).'&amp;'.JText::_( 'Install' );
			//$button->imageName = $path.'/media/images/icons/extensions/icon-16-plugin_add.png';
			$button->imageName = PagesAndItemsHelper::getDirIcons().'extensions/icon-16-plugin_add.png';
			$button->onclick = 'submitbutton();';
			echo $button->makeButton();
			/*
			<input class="button" type="button" value="<?php echo JText::_( 'Upload File' ); ?> &amp; <?php echo JText::_( 'Install' ); ?>" onclick="submitbutton()" />
			*/
			?>
		</td>
	</tr>
	</table>

	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'Install from directory' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_directory"><?php echo JText::_( 'Install directory' ); ?>:</label>
		</td>
		<td>
			<input type="text" id="install_directory" name="install_directory" class="input_box" size="70" value="<?php echo $this->state->get('install.directory'); ?>" />
			<?php
			/*
			**********************
			* Button from-folder *
			**********************
			*/
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->buttonType = 'input';
			$button->text = JText::_( 'Install' );
			//$button->imageName = $path.'/media/images/icons/extensions/icon-16-plugin_add.png';
			$button->imageName = PagesAndItemsHelper::getDirIcons().'extensions/icon-16-plugin_add.png';
			$button->onclick = 'submitbutton3();';
			echo $button->makeButton();
			/*
			<input type="button" class="button" value="<?php echo JText::_( 'Install' ); ?>" onclick="submitbutton3()" />

			*/
			?>



		</td>
	</tr>
	</table>

	<table class="adminform">
	<tr>
		<th colspan="2"><?php echo JText::_( 'Install from URL' ); ?></th>
	</tr>
	<tr>
		<td width="120">
			<label for="install_url"><?php echo JText::_( 'Install URL' ); ?>:</label>
		</td>
		<td>
			<input type="text" id="install_url" name="install_url" class="input_box" size="70" value="http://" />
			<?php
			/*
			******************
			* Button via-url *
			******************
			*/
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->buttonType = 'input';
			$button->text = JText::_( 'Install' );
			//$button->imageName = $path.'/media/images/icons/extensions/icon-16-plugin_add.png';
			$button->imageName = PagesAndItemsHelper::getDirIcons().'extensions/icon-16-plugin_add.png';
			$button->onclick = 'submitbutton4();';
			echo $button->makeButton();
			/*
			<input type="button" class="button" value="<?php echo JText::_( 'Install' ); ?>" onclick="submitbutton4()" />

			*/
			?>
		</td>
	</tr>
	</table>

	<input type="hidden" name="type" value="" />
	<input type="hidden" name="installtype" value="upload" />
	<input type="hidden" name="task" value="install.install" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php
}
else
{
//joomla 1.6.x
	//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_installer'.DS.'views'.DS.'install'.DS.'view.html.php');
?>


	<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton)
	{
		Joomla.submitform( pressbutton, document.getElementById('adminForm' ));
	}

	Joomla.submitbutton3 = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.install_directory.value == ""){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_DIRECTORY', true); ?>");
		} else {
			form.installtype.value = 'folder';
			form.submit();
		}
	}

	Joomla.submitbutton4 = function(pressbutton) {
		var form = document.getElementById('adminForm');

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://"){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL', true); ?>");
		} else {
			form.installtype.value = 'url';
			form.submit();
		}
	}
</script>

<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_pagesanditems&view=manageinstall');?>" method="post" name="adminForm" id="adminForm">

	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>
	<div class="width-100 fltlft">
		<fieldset class="uploadform">
			<legend><?php echo JText::_('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></legend>
			<label for="install_package"><?php echo JText::_('COM_INSTALLER_PACKAGE_FILE'); ?></label>
			<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
			<?php
			//$path = str_replace(DS,'/',str_replace(JPATH_SITE,'',JPATH_COMPONENT_ADMINISTRATOR));

			/*
			**********************
			* Button upload-file *
			**********************
			*/
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->buttonType = 'input';
			$button->text = JText::_('COM_INSTALLER_UPLOAD_AND_INSTALL');
			//$button->imageName = $path.'/media/images/icons/extensions/icon-16-plugin_add.png';
			$button->imageName = PagesAndItemsHelper::getDirIcons().'extensions/icon-16-plugin_add.png';
			$button->onclick = 'Joomla.submitbutton();';
			echo $button->makeButton();
			/*
			<input class="button" type="button" value="<?php echo JText::_('COM_INSTALLER_UPLOAD_AND_INSTALL'); ?>" onclick="Joomla.submitbutton()" />
			*/
			?>

		</fieldset>
		<div class="clr"></div>
		<fieldset class="uploadform">
			<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_DIRECTORY'); ?></legend>
			<label for="install_directory"><?php echo JText::_('COM_INSTALLER_INSTALL_DIRECTORY'); ?></label>
			<input type="text" id="install_directory" name="install_directory" class="input_box" size="70" value="<?php echo $this->state->get('install.directory'); ?>" />

			<?php
			/*
			**********************
			* Button from-folder *
			**********************
			*/
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->buttonType = 'input';
			$button->text = JText::_( 'COM_INSTALLER_INSTALL_BUTTON' );
			//$button->imageName = $path.'/media/images/icons/extensions/icon-16-plugin_add.png';
			$button->imageName = PagesAndItemsHelper::getDirIcons().'extensions/icon-16-plugin_add.png';
			$button->onclick = 'Joomla.submitbutton3();';
			echo $button->makeButton();
			/*
			<input type="button" class="button" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton3()" />

			*/
			?>

		</fieldset>
		<div class="clr"></div>
		<fieldset class="uploadform">
			<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_URL'); ?></legend>
			<label for="install_url"><?php echo JText::_('COM_INSTALLER_INSTALL_URL'); ?></label>
			<input type="text" id="install_url" name="install_url" class="input_box" size="70" value="http://" />
			<?php
			/*
			******************
			* Button via-url *
			******************
			*/
			$button = PagesAndItemsHelper::getButtonMaker();
			$button->buttonType = 'input';
			$button->text = JText::_( 'COM_INSTALLER_INSTALL_BUTTON' );
			//$button->imageName = $path.'/media/images/icons/extensions/icon-16-plugin_add.png';
			$button->imageName = PagesAndItemsHelper::getDirIcons().'extensions/icon-16-plugin_add.png';
			$button->onclick = 'Joomla.submitbutton4();';
			echo $button->makeButton();
			/*
			<input type="button" class="button" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton4()" />
			*/
			?>

		</fieldset>
		<input type="hidden" name="type" value="" />
		<input type="hidden" name="installtype" value="upload" />
		<input type="hidden" id="task" name="task" value="extension.doExecute" />
		<input type="hidden" id="extensionName" name="extensionName" value="extensions" />
		<input type="hidden" id="extensionTask" name="extensionTask" value="install.install" />
		<input type="hidden" id="extensionType" name="extensionType" value="manager" />
		<input type="hidden" id="extensionFolder" name="extensionFolder" value="" />
		<input type="hidden" id="view" name="view" value="install" />


		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php
}

?>
<?php
//echo $this->loadTemplate('footer');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'tmpl'.DS.'default_footer.php');
?>