<?php
/**
* @version		2.1.3
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//no direct access
if(!defined('_JEXEC'))
{
	die('Restricted access');
}

function com_uninstall(){

	$database = JFactory::getDBO();

	//delete content plugin
	$plugin_php = JPATH_PLUGINS.DS.'content'.DS.'pagesanditems'.DS.'pagesanditems.php';
	$plugin_xml = JPATH_PLUGINS.DS.'content'.DS.'pagesanditems'.DS.'pagesanditems.xml';
	$content_plugin_success = 0;
	if(file_exists($plugin_php) && file_exists($plugin_xml)){
		$content_plugin_success = JFile::delete($plugin_php);
		JFile::delete($plugin_xml);
	}
	if($content_plugin_success){
		echo '<p style="color: #5F9E30;">content plugin succesfully uninstalled</p>';
	}else{
		echo '<p style="color: red;">could not uninstall content plugin</p>';
	}
	$database->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder='content' AND element='pagesanditems' LIMIT 1");
	$database->query();

	//delete system plugin
	$plugin_php = JPATH_PLUGINS.DS.'system'.DS.'pagesanditems'.DS.'pagesanditems.php';
	$plugin_xml = JPATH_PLUGINS.DS.'system'.DS.'pagesanditems'.DS.'pagesanditems.xml';
	$content_plugin_success = 0;
	if(file_exists($plugin_php) && file_exists($plugin_xml)){
		$content_plugin_success = JFile::delete($plugin_php);
		JFile::delete($plugin_xml);
	}
	if($content_plugin_success){
		echo '<p style="color: #5F9E30;">system plugin succesfully uninstalled</p>';
	}else{
		echo '<p style="color: red;">could not uninstall system plugin</p>';
	}
	$database->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder='system' AND element='pagesanditems' LIMIT 1");
	$database->query();

	//at uninstall empty the pi extensions table
	$database->setQuery("TRUNCATE TABLE #__pi_extensions ");
	$database->query();

	//uninstall all pi core language files
	$lang_array = array(array('en','en-GB','English'),array('ar','ar-EG','Arabic'),array('bg','bg-BG','Bulgarian'),array('zh-CN','zh-CN','Chinese (Simplified)'),array('zh-TW','zh-TW','Chinese (Traditional)'),array('hr','hr-HR','Croatian'),array('cs','cs-CZ','Czech'),array('da','da-DK','Danish'),array('nl','nl-NL','Dutch'),array('fi','fi-FI','Finnish'),array('fr','fr-FR','French'),array('de','de-DE','German'),array('el','el-GR','Greek'),array('hi','hi-IN','Hindi'),array('it','it-IT','Italian'),array('ja','ja-JP','Japanese'),array('ko','ko-KR','Korean'),array('no','no-NO','Norwegian'),array('pl','pl-PL','Polish'),array('pt','pt-PT','Portuguese'),array('ro','ro-RO','Romanian'),array('ru','ru-RU','Russian'),array('es','es-ES','Spanish'),array('sv','sv-SE','Swedish'),array('ca','ca-ES','Catalan'),array('tl','tl-PH','Filipino'),array('iw','he-IL','Hebrew'),array('id','id-ID','Indonesian'),array('lv','lv-LV','Latvian'),array('lt','lt-LT','Lithuanian'),array('sr','sr-RS','Serbian'),array('sk','sk-SK','Slovak'),array('sl','sl-SI','Slovenian'),array('uk','uk-UA','Ukrainian'),array('vi','vi-VN','Vietnamese'),array('sq','sq-AL','Albanian'),array('et','et-EE','Estonian'),array('gl','gl-ES','Galician'),array('hu','hu-HU','Hungarian'),array('mt','mt-MT','Maltese'),array('th','th-TH','Thai'),array('tr','tr-TR','Turkish'),array('fa','fa-IR','Persian'),array('af','af-ZA','Afrikaans'),array('ms','ms-MY','Malay'),array('sw','sw-TZ','Swahili'),array('ga','ga-IE','Irish'),array('cy','cy-GB','Welsh'),array('be','be-BE','Belarusian'),array('is','is-IS','Icelandic'),array('mk','mk-MK','Macedonian'),array('yi','yi-IL','Yiddish'),array('ht','ht-NG','Haitian Creole'),array('pt','pt-BR','Portuguese Brasil'));
	foreach($lang_array as $language){
		$lang = $language[1];
		if(file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'language'.DS.$lang.DS.$lang.'.com_pagesanditems.ini')){
			JFile::delete(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'language'.DS.$lang.DS.$lang.'.com_pagesanditems.ini');
		}
		if(file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'language'.DS.$lang.DS.$lang.'.com_pagesanditems.sys.ini')){
			JFile::delete(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'language'.DS.$lang.DS.$lang.'.com_pagesanditems.sys.ini');
		}

	}

}

?>
