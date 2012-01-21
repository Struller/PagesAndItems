<?php
/**
* @package PI!Fish
 translating custom itemtype field from Pages and Items using Joom!Fish
* @version 1.6.2.2
* @copyright Copyright (C) 2009-2010 Michael Struller. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author http://gecko.struller.de
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class PagesAndItemsViewTranslate_itemtype extends JView
{
	function display( $tpl = null )
	{
		$db	=&	JFactory::getDBO();
		$catid = JRequest::getVar('catid', '');
		$item_id = JRequest::getVar('item_id', '');
		$joomfish_id = JRequest::getVar('joomfish_id', '');
		$language_id = JRequest::getVar('language_id', null);
		
		$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../..')));
		$this->assignRef('path',$path);
		JHTML::stylesheet('translate.css', $path.'/css/');
		JHTML::script('translatemanager_itemtype.js', $path.'/javascript/',false);

		$select_language_id = JRequest::getVar('select_language_id', null);
		
		$no_language_select = JRequest::getVar('no_language_select', false);
		$no_language_select_id = JRequest::getVar('no_language_select_id', null);
		if($no_language_select && $no_language_select_id)
		{
			$select_language_id = $no_language_select_id;
		}
		/*
			Joomfish save select_language_id in UserState so have the user an other instance of Joomfish witanother language select this language was selected here too
			and in the form the other language will display
			set language_id to the request?
		*/
		$app = JFactory::getApplication();
		$app->setUserState('selected_lang', '-1');
		/*
		$this->_select_language_id = $mainframe->getUserStateFromRequest('selected_lang','select_language_id', '-1');
		$this->_language_id =  JRequest::getVar( 'language_id', $this->_select_language_id );
		$this->_select_language_id = ($this->_select_language_id == -1 && $this->_language_id != -1) ? $this->_language_id : $this->_select_language_id;
		*/

		$query = 'SELECT template'
		. ' FROM #__templates_menu'
		. ' WHERE client_id = 1'
		. ' AND menuid = 0'
		;
		$db->setQuery($query);
		$template = $db->loadResult();
		$iconCss = JURI::root().'/administrator/templates/'.$template.'/css/icon.css';

		$extension = 'com_joomfish';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);

		//ok we need the fieldtype/pi_fish so we will trigger
		//$extension = 'plg_pages_and_items_fieldtype_pi_fish';
		//$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);

		$extension = 'com_content';
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);

		$langParams	=	JComponentHelper::getParams( 'com_languages' );
		$langSite	=	$langParams->get( "site", 'en-GB' );
		$langDefault=	substr( $langSite, 0, 2 );

		$query	= 'SELECT s.id'
		. ' FROM #__languages AS s'
		. ' WHERE s.shortcode="'.$langDefault.'"';
		;
		$db->setQuery( $query );
		$langDefaultId	=	$db->loadResult();

		/*
		must rewrite
		
		$componentPath = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'..');
		require_once($componentPath.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
		ExtensionFieldtypeHelper::importExtension(null, 'pi_fish',true,null,true);
		$dispatcher = &JDispatcher::getInstance();
		$params = null;
		$dispatcher->trigger('onGetParams',array(&$params, 'pi_fish'));
		$pluginParams = $params;
		$display_inaktive = $pluginParams->get('display_inaktive',0);

		*/
		$display_inaktive = 0;
		
		//oder über JoomFish
		$sql = 'SELECT * FROM #__languages';
		if(!$display_inaktive)
		{
			$sql .= " WHERE active='1'";
		}
		$sql .= ' ORDER BY ordering';
		$db->setQuery( $sql );
		$languages = $db->loadObjectList();
		if( $languages ) 
		{
			$langOptions[] = JHTML::_('select.option',  '-1', JText::_('COM_PAGESANDITEMS_EXTENSIONS_SELECT_LANGUAGE') );
			foreach ($languages as $language) 
			{
				if($language->id != $langDefaultId)
				{
					if(!$select_language_id)
					{
						$select_language_id = $language->id;
					}
					$langOptions[] = JHTML::_('select.option',  $language->id, $language->name );
				}
			}
		}
		
		
		//change
		$query = "SELECT * FROM #__jf_content WHERE reference_table = '$catid' AND language_id = '$select_language_id' AND reference_id = '$joomfish_id' AND reference_field='title' LIMIT 1";
		$db->setQuery($query);
		$pijf_row = $db->loadObject();
		$translation_id = '';
		if($pijf_row)
		{
			$translation_id = $pijf_row->id;
		}
		
		if($no_language_select && $no_language_select_id)
		{
			$langlist = '<input type="hidden" name="select_language_id" id="select_language_id" value="'.$select_language_id.'" />';
		}
		else
		{
			$langlist = JText::_('COM_PAGESANDITEMS_EXTENSIONS_SELECT_LANGUAGE').': '.JHTML::_('select.genericlist', $langOptions, 'select_language_id', 'class="select_cgp" size="1" onchange="if(document.getElementById(\'catid\').value.length>0) document.adminForm.submit();"', 'value', 'text', $select_language_id );
		}
		
		/*
		$query = "SELECT * FROM #__jf_content WHERE reference_table = 'content' AND language_id = '$select_language_id' AND reference_id = '$item_id' AND reference_field='title' LIMIT 1";
		$db->setQuery($query);
		$content_row = $db->loadObject();
		$translation_id = '';
		if($content_row)
		{
			$content_translation_id = $content_row->id;
		}
		$this->assignRef('content_translation_id',$content_translation_id);
		*/
		$this->assignRef('item_id',$item_id);
		//$content_catid = 'conten_pi_fish_table_'.$type_id; // CHANGE  //$content_catid = 'conten_pi_fish_table_'.$pf_id; //.'.xml'; //with $pf_id
		//$this->assignRef('content_catid',$content_catid);
		
		
		$this->assignRef('translation_id',$translation_id);
		$this->assignRef('joomfish_id',$joomfish_id);
		$this->assignRef('select_language_id',$select_language_id);
		$this->assignRef('langlist',$langlist);
		$this->assignRef('catid',$catid);
		$this->assignRef('iconCss',$iconCss);
		$this->assignRef('no_language_select_id',$no_language_select_id);
		$this->assignRef('no_language_select',$no_language_select);
		$task = JRequest::getVar('task', '');
		$this->assignRef('task',$task);
		//$this->assignRef('typeName',$typeName);
		//$this->assignRef('fieldName',$fieldName);
		JHTML::_('behavior.tooltip');
		parent::display($tpl);
	}
}