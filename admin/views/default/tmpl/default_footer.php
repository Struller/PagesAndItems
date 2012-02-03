<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
if(PagesAndItemsHelper::getIsAdmin()){ //$this->helper->app->isAdmin()){
	//only show footer in backend
	/*
	$document =& JFactory::getDocument();
	$path = PagesAndItemsHelper::getDirCSS(true);
	//$document->addStylesheet('components/com_pagesanditems/css/pagesanditems2.css');
	$document->addStylesheet($path.'/pagesanditems2.css');
	*/
	$path = PagesAndItemsHelper::getDirCSS();
	JHtml::stylesheet($path.'/pagesanditems2.css');
	
	//$document->addStylesheet('components/com_pagesanditems/css/dtree.css');

	echo '<div class="smallgrey" id="pi_footer">';
	echo '<table>';
	echo '<tr>';
	echo '<td class="text_right">';
	echo '<a href="http://www.pages-and-items.com" target="_blank">Pages-and-Items</a>';
	echo '</td>';
	echo '<td class="five_pix">';
	echo '&copy;';
	echo '</td>';
	echo '<td>';
	echo '2008 - 2011 Carsten Engel';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td class="text_right">';
	echo PagesAndItemsHelper::pi_strtolower(JText::_('JVERSION'));//$this->helper->pi_strtolower(JText::_('JVERSION'));
	echo '</td>';
	echo '<td class="five_pix">';
	echo '=';
	echo '</td>';
	echo '<td>';
	//echo $version.' ('.$this->fua_version_type.' '.$this->fua_strtolower(JText::_('JVERSION')).')';
	echo PagesAndItemsHelper::getPagesAndItemsVersion(); //$this->helper->version;
	//if($this->fua_version_type!='trial'){
		//echo ' <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="blank">GNU/GPL License</a>';
	//}
	echo '</td>';
	echo '</tr>';
	//version checker
	//if($this->pi_config['version_checker']){
	if(PagesAndItemsHelper::getConfigAsRegistry()->get('version_checker',0)){
		echo '<tr>';
		echo '<td class="text_right">';
		echo JText::_('COM_PAGESANDITEMS_LATEST_VERSION');
		echo '</td>';
		echo '<td class="five_pix">';
		echo '=';
		echo '</td>';
		echo '<td>';
		$latest_version_message = PagesAndItemsHelper::getApp()->getUserState( "com_pagesanditems.latest_version_message", ''); //$this->helper->app->getUserState( "com_pagesanditems.latest_version_message", '');
		if($latest_version_message==''){
			$latest_version_message = JText::_('COM_PAGESANDITEMS_VERSION_CHECKER_NOT_AVAILABLE');
			$url = 'http://www.pages-and-items.com/latest_version_pi_j1.6.txt';
			$file_object = @fopen($url, "r");
			if($file_object == TRUE){
				$version = fread($file_object, 1000);
				$latest_version_message = $version;
				//if($this->helper->version!=$version){
				if(PagesAndItemsHelper::getPagesAndItemsVersion()!=$version){
					$latest_version_message .= ' <span class="warning">'.JText::_('COM_PAGESANDITEMS_NEWER_VERSION').'</span>';
					//if($this->fua_version_type=='pro'){
						//$download_url = 'http://www.pages-and-items.com/my-extensions';
					//}else{
						$download_url = 'http://www.pages-and-items.com/extensions/pages-and-items';
					//}
					$latest_version_message .= ' <a href="'.$download_url.'" target="_blank">'.JText::_('COM_PAGESANDITEMS_DOWNLOAD').'</a>';
				}else{
					$latest_version_message .= ' <span style="color: #5F9E30;">'.JText::_('COM_PAGESANDITEMS_IS_LATEST_VERSION').'</span>';
				}
				fclose($file_object);
			}
			//$this->helper->app->setUserState( "com_pagesanditems.latest_version_message", $latest_version_message );
			PagesAndItemsHelper::getApp()->setUserState( "com_pagesanditems.latest_version_message", $latest_version_message );
		}
		echo $latest_version_message;
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
}
?>
