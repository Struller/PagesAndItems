<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;



class PagesAndItemsHelperTranslateFish
{
	//TODO preview button for the content in the language?


	// TODO in config we must add an tab for params??
	function languageDisplayItemtypeItemEdit($item_type,$item_id,$contentelement,$params,$joomfish_id = null)
	{
		$app = JFactory::getApplication();
		if (!$app->isAdmin()) 
		{
			//display an message no translation in frontend, joomfish in frontend only avaible by joomfishPlus but i can not check this
			$html = '';
		}
		else
		{
			if($contentelement == 'content' || !$joomfish_id)
			{
				$joomfish_id = $item_id;
			}
		
			$row->joomfish_id = $joomfish_id;
			$url = JURI::root(true).'/administrator/index.php?option=com_pagesanditems';
		
			//must rewrite to integrated view
			//$url .= '&task=extension.doExecute'; // the task we will execute
			//$url .= '&extension=pi_fish'; //the name from the extension
			//$url .= '&extensionType=fieldtype'; //the type  from the extension

			$url .= '&amp;view=translate_itemtype';
			$url .= '&amp;tmpl=component';
	
			$url .= '&amp;catid='.$contentelement.''; //is the contentelement
			//need we here ? $url .= '&amp;content_catid=conten_pi_fish_table_'.$field->type_id.'';
					
			//need we here ? $url .= '&amp;type_id='.$field->type_id; //ADD
			//need we here ? $url .= '&amp;typeName='.$typeName;
			//need we here ? $url .= '&amp;fieldName='.$fieldName;
			$url .= '&amp;joomfish_id='.$joomfish_id.'';
			$url .= '&amp;item_id='.$item_id;
		
		
			$descripton = 'PifishTranslate';
			$descripton .= '<br />';
			$descripton .= 'Table for pi_fish is: '.$contentelement;
		
			$return = PagesAndItemsHelperTranslateFish::item_edit_check_joomfish();
			if($return  != 'loaded')
			{
				$html = $return;
				//The next 4 lines must remove only for demostrating where the content show
				$html = '<fieldset class="adminform">';
					$html .= '<legend>'.JText::_('COM_PAGESANDITEMS_LANGUAGE_TRANSLATE').'</legend>';
					$html .= 'here come something for translate. <br />'.$return;
				$html .= '</fieldset>';
			}
			else
			{
				$html = '';
				$html = '<fieldset class="adminform">';
					$html .= '<legend>'.JText::_('COM_PAGESANDITEMS_LANGUAGE_TRANSLATE').'</legend>';
					$html .='<div>'; // class="pi_wrapper language">';
						$html .='<div class="pi_width20">';
							//$html .= JText::_('COM_PAGESANDITEMS_LANGUAGE_TRANSLATE');
							//$html .= '<br />';
							$html .=JText::_('COM_PAGESANDITEMS_LANGUAGE_TABLE').': '.$contentelement;
						$html .='</div>';
						$html .='<div class="pi_width80">';
							//$html .= PagesAndItemsHelperTranslateFish::renderButtons($row,$contentelement,$url,$descripton,$params);
							$html .= PagesAndItemsHelperTranslateFish::renderButtons($row,$contentelement,$url,$params,$item_id);
						$html .='</div>';
					$html .='</div>';
				$html .= '</fieldset>';
				$html .='<br />';
			}
		}
		return $html;
	}

	// TODO integrate it in PI but where?
	function languageItemtypeParamsSave($params,$item_type)
	{
		//ok will run so we must check for param
		if($params->get('translatable',0))
		{
			$content_table = $params->get('content_table',0);
			if($content_table)
			{
				jimport('joomla.filesystem.file');
				if(JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions/itemtypes/'.$item_type.'/contentelements/'.$content_table.'.xml'))
				{
					JFile::copy(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions/itemtypes/'.$item_type.'/contentelements/'.$content_table.'.xml', JPATH_ADMINISTRATOR.DS.'components/com_joomfish/contentelements/'.$content_table.'.xml');
				}
			}
			
			$tableNames = $params->get('table_names',0);
			if($tableNames)
			{
				$tableNames = explode(';',$tableNames);
				foreach($tableNames as $tableName)
				{
					if($tableName != 'content')
					{
						jimport('joomla.filesystem.file');
						if(JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions/itemtypes/'.$item_type.'/contentelements/'.$tableName.'.xml'))
						{
							JFile::copy(JPATH_COMPONENT_ADMINISTRATOR.DS.'extensions/itemtypes/'.$item_type.'/contentelements/'.$tableName.'.xml', JPATH_ADMINISTRATOR.DS.'components/com_joomfish/contentelements/'.$tableName.'.xml');
						}
					}
				}
			}
		}
		/*
		TODO remove the contentelement if not
		*/
		
		
		
		
	}


	function item_edit_check_joomfish()
	{
		if(JFile::exists(JPATH_SITE .DS. 'components' .DS. 'com_joomfish' .DS. 'helpers' .DS. 'defines.php')) 
		{
			static $loaded;
			if($loaded)
			{
				return 'loaded';
			}
			//$loaded = true;
			require_once( JPATH_SITE .DS. 'components' .DS. 'com_joomfish' .DS. 'helpers' .DS. 'defines.php' );
			JLoader::register('JoomfishManager', JOOMFISH_ADMINPATH .DS. 'classes' .DS. 'JoomfishManager.class.php' );
			JLoader::register('JoomFishVersion', JOOMFISH_ADMINPATH .DS. 'version.php' );
			JLoader::register('JoomFish', JOOMFISH_PATH .DS. 'helpers' .DS. 'joomfish.class.php' );
			include_once(JOOMFISH_ADMINPATH .DS. "models".DS."ContentElement.php");
			JTable::addIncludePath( JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table' );
			//require( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'models'.DS.'JFContent.php' );
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'models'.DS.'JFContent.php' );
			return 'loaded';
		}
		else
		{
			return JText::_('COM_PAGESANDITEMS_LANGUAGE_JOOMFISH_NOT_INSTALLED');
		}
		
	}

	//function renderButtons($row,$tablename,$url,$descripton,$params)
	function renderButtons($row,$tablename,$url,$params,$item_id)
	{
		$css = '.button_action_language_div {border:1px solid silver;display:block;float:left;margin:3px;padding:3px;}';
		$doc =& JFactory::getDocument();
		$doc->addStyleDeclaration( $css );
		JHtml::_('behavior.modal', 'a.modal-button');
		$modal = 'class="modal-button button_action_language_div"';
		$size_x = '1024';
		$size_x = '1030';
		//$size_x = '900';
		$size_y = '600';
		$rel = 'rel="{handler: \'iframe\', size: {x: '.$size_x.', y: '.$size_y.'}}"';
		$extension = 'com_joomfish';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false);
		
		$db = & JFactory::getDBO();		
		$html = '';
		$html = '<div class="pi_wrapper">';

			//$field_name = $descripton;
			$field_content = '';
			
			$display_inaktive = $params->get('display_inactive',0);
			$display_preview = $params->get('display_preview',0);
			$langParams	=	JComponentHelper::getParams( 'com_languages' );
			$langSite	=	$langParams->get( "site", 'en-GB' );
			$langDefault=	substr( $langSite, 0, 2 );
	
			$shortCode	=	substr( $langSite, 0, 2 );
	
			$query	= 'SELECT s.id'
			. ' FROM #__languages AS s'
			. ' WHERE s.shortcode="'.$shortCode.'"';
			$db->setQuery( $query );
			$langDefaultID	=	$db->loadResult();
			
			//oder über JoomFish
			$sql = 'SELECT * FROM #__languages';
			if(!$display_inaktive)
			{
				$sql .= " WHERE active='1'";
			}
			$sql .= ' ORDER BY ordering';
			$db->setQuery( $sql );
			$langrows = $db->loadObjectList();
			if( $langrows ) 
			{
				foreach ($langrows as $langrow) 
				{
					if($langrow->shortcode != $langDefault)
					{
						$language_ids[] = $langrow->id;
						$language_names[$langrow->id] = $langrow->name;
						$language_shortcodes[$langrow->id] = $langrow->shortcode;
						$language_codes[$langrow->id] = $langrow->code;
					}
				}
			}
				
			if(isset($language_ids) && count($language_ids))
			{
				$display_name = false;
				$display_image = false;
				switch($params->get('display_type','namesplusimages'))
				{
					case 'names':
						$display_name = true;
					break;
					case 'namesplusimages':
						$display_name = true;
						$display_image = true;
					break;
					case 'images':
						$display_image = true;
					break;
				}
		
				if($params->get('display_status',1))
				{
					$display_status = true;
				}
				else
				{
					$display_status = false;
				}
				if($params->get('display_status_published',1))
				{
					$display_status_published = true;
				}
				else
				{
					$display_status_published = false;
				}
				$joomfishManager =& JoomFishManager::getInstance();
				
				foreach($language_ids as $language_id)
				{
					
					$contentElement = $joomfishManager->getContentElement( $tablename );
					//todo check contentelement
					JLoader::import( 'models.TranslationFilter',JOOMFISH_ADMINPATH);
					$tranFilters = getTranslationFilters($tablename,$contentElement);
					//$db->setQuery( $contentElement->createContentSQL( $language_id, $row->pf_id, null, null,$tranFilters ) );
					$db->setQuery( $contentElement->createContentSQL( $language_id, $row->joomfish_id, null, null,$tranFilters ) );
					$jf_row = $db->loadObject();
					if ($db->getErrorNum()) 
					{
						JError::raiseWarning( 200,JTEXT::_('No valid database connection: ') .$db->stderr());
						// should not stop the page here otherwise there is no way for the user to recover
						$jf_row = null;
					}
					if(!$jf_row)
					{
						//$jf_row->id = null;
						$jf_row =&	JTable::getInstance( 'jfContent', '' );
						$jf_row->jfc_id = null;
						$jf_row->titleTranslation = null;
						$jf_row->lastchanged = null;
						$jf_row->published = null;
						$jf_row->language_id = null;
						$jf_row->language = null;
						$jf_row->jfc_refid = null;
						$jf_row->title = null;
					}
					// Manipulation of result based on further information
					JLoader::import( 'models.ContentObject',JOOMFISH_ADMINPATH);
					$contentObject = new ContentObject( $language_id, $contentElement );
					$contentObject->readFromRow( $jf_row );
					$jf_row = $contentObject;
					switch( $jf_row->state ) 
					{
						case 1:
							$img = 'status_g.png';
						break;
						case 0:
							$img = 'status_y.png';
						break;
						case -1:
						default:
							$img = 'status_r.png';
						break;
					}
					if (isset($jf_row->published) && $jf_row->published) 
					{
						$imgPublished = 'publish_g.png';
					}
					else
					{
						$imgPublished = 'publish_x.png';
					}

					$url .= '&amp;no_language_select=true';
					$url .= '&amp;no_language_select_id='.$language_id;
					$href = 'href="'.$url.'"';
					$class = 'class="div_button_ginkgo_select_indicator-xtd-button"';
						$title = 'translate';
						$field_content .= '<div id="button_translate_'.$language_id.'" '.$class.' >';
							$field_content .= '<a id="a_button_translate_'.$language_id.'" '.$modal.' '.$title.' '.$href.' '.$rel.' >';
							if($display_image)
							{
								$field_content .= '<img style="vertical-align:middle;" alt="" src="'.JURI::root(true).'/components/com_joomfish/images/flags/'.$language_shortcodes[$language_id].'.gif">';
							}
							if($display_name)
							{
								$field_content .= ' '.$language_names[$language_id];
							}
							if($display_status)
							{
								$field_content .= ' <img style="vertical-align:middle;" src="'.JURI::root(true).'/administrator/components/com_joomfish/assets/images/'.$img.'" width="12" height="12" border="0" alt="" />';
							}
							if($display_status_published)
							{
								$field_content .= ' <img style="vertical-align:middle;" src="'.JURI::root(true).'/administrator/images/'.$imgPublished.'" width="16" height="16" border="0" alt="" />';
							}
							$field_content .= '</a>';
						$field_content .= '</div>';
						
						
						
						
						//$field_content .= '<br />';
					//$field_content .= '</div>';
				}
			}
			else
			{
				//no languages
			}
			$field_content .= '</div>';
			$field_content .= '<br />';
			//$html .= $this->display_field($field_name, $field_content);
			$html .= $field_content;
			
			if($display_status_published || $display_status)
			{
				$field_name = '';
				$field_content = '';
				if($display_status)
				{
					$field_content .= '<div style="float:left;">';
						$field_content .= '<div style="width:20px; float:left;">';
							$field_content .= '<img src="'.JURI::root(true).'/administrator/components/com_joomfish/assets/images/status_g.png" width="12" height="12" border=0 alt="'.JText::_('COM_PAGESANDITEMS_STATE_OK').'" />';
						$field_content .= '</div>';
						$field_content .= '<div style="float:left;margin-right:5px;">';
							$field_content .= JText::_('COM_PAGESANDITEMS_TRANSLATION_UPTODATE');
						$field_content .= '</div>';
					$field_content .= '</div>';
					
					$field_content .= '<div style="float:left;">';
						$field_content .= '<div style="width:20px; float:left;">';
							$field_content .= '<img src="'.JURI::root(true).'/administrator/components/com_joomfish/assets/images/status_y.png" width="12" height="12" border=0 alt="'.JText::_('STATE_CHANGED').'" />';
						$field_content .= '</div>';
						$field_content .= '<div style="float:left;margin-right:5px;">';
							$field_content .= JText::_('COM_PAGESANDITEMS_TRANSLATION_INCOMPLETE');
						$field_content .= '</div>';
					$field_content .= '</div>';
					
					$field_content .= '<div style="float:left;">';
						$field_content .= '<div style="width:20px; float:left;">';
							$field_content .='<img src="'.JURI::root(true).'/administrator/components/com_joomfish/assets/images/status_r.png" width="12" height="12" border=0 alt="'.JText::_('STATE_NOTEXISTING').'" />';
						$field_content .= '</div>';
						$field_content .= '<div style="float:left;margin-right:5px;">';
							$field_content .= JText::_('COM_PAGESANDITEMS_TRANSLATION_NOT_EXISTING');
						$field_content .= '</div>';
					$field_content .= '</div>';
				
					//$html .= $this->display_field($field_name, $field_content);
					$html .= $field_content;
					$field_content .= '<br />';
				}
				if($display_status_published)
				{
					$field_name = '';
					$field_content = '';
					$field_content .= '<div style="float:left;">';
						$field_content .= '<div style="width:20px; float:left;">';
							$field_content .= '<img src="'.JURI::root(true).'/administrator/images/publish_g.png" width="16" height="16" border=0 alt="'.JText::_('Translation visible').'" />';
						$field_content .= '</div>';
						$field_content .= '<div style="float:left;margin-right:5px;">';
							$field_content .= JText::_('COM_PAGESANDITEMS_TRANSLATION_PUBLISHED');
						$field_content .= '</div>';
					$field_content .= '</div>';
		
					$field_content .= '<div style="float:left;">';
						$field_content .= '<div style="width:20px; float:left;">';
							$field_content .= '<img src="'.JURI::root(true).'/administrator/images/publish_x.png" width="16" height="16" border=0 alt="'.JText::_('Finished').'" />';
						$field_content .= '</div>';
						$field_content .= '<div style="float:left;margin-right:5px;">';
							$field_content .= JText::_('COM_PAGESANDITEMS_TRANSLATION_NOT_PUBLISHED');
						$field_content .= '</div>';
					$field_content .= '</div>';
					$html .= $field_content;//$this->display_field($field_name, $field_content);
				}
			}
			/*
			TODO preview
			if($display_preview)
			{
				$field_content = '';
				$field_content .= '<div style="float:left;">';
				$modal = 'class="modal-button button_action_language_div"';
				$size_x = '1024';
				$size_y = '600';
				$rel = 'rel="{handler: \'iframe\', size: {x: '.$size_x.', y: '.$size_y.'}}"';
				
				foreach($language_ids as $language_id)
				{
					$language_code
					$url = ????
					$url = 'index.php?option=com_pagesanditems&view=preview&layout=language&tmpl=component&language='.$language_shortcodes[$language_id].'&item_id='.$item_id;
					$href = 'href="'.$url.'"';
					$title = 'preview '.$language_names[$language_id];
					$class = 'class="div_button_ginkgo_select_indicator-xtd-button"';
					$field_content .= '<div id="button_translate_preview_'.$language_id.'" '.$class.' >';
						$field_content .= '<a id="a_button_translate_preview_'.$language_id.'" '.$modal.' '.$title.' '.$href.' '.$rel.' >';
						if($display_image)
						{
							$field_content .= '<img style="vertical-align:middle;" alt="" src="'.JURI::root().'components/com_joomfish/images/flags/'.$language_shortcodes[$language_id].'.gif">';
						}
						if($display_name)
						{
							$field_content .= ' '.$language_names[$language_id];
						}
						$field_content .= '</a>';
					$field_content .= '</div>';
				}
				$field_content .= '</div>';
				$html .= $field_content;
				*/
			
		//$html .= '</div>';
		return $html;
	}
}
?>