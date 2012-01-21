<?php
/**
* @version		1.5.9 mvc
* @package		PagesAndItems
* @copyright	Copyright (C) 2006-2010 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport('joomla.plugin.plugin');

/**
 * @package		PagesAndItems
 * @subpackage	content-plugin for Pages-and-Items (com_pi_pages_and_items)
 */
class plgContentPagesanditems extends JPlugin
{
	
	function plgContenPages_and_items( &$subject, $config )
	{
		parent::__construct( $subject, $config );		
	}


	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart)
	{
		$view = JRequest::getVar('view', '');
		$option = JRequest::getVar('option', '');
		if($option=='com_content' && ($view == 'category' || $view=='featured'))
		{			
			//here is no article->text only $article->introtext			
			$article->text = $article->introtext;			
			$this->onPrepareContent( $article, $params, $limitstart );
			//we need to have the changed $article->introtext
			$article->introtext = $article->text;			
		}
	}
	
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{		
		return $this->onPrepareContent( $article, $params, $limitstart );
	}

	//the Joomla 1.5 code
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		
		$database = JFactory::getDBO();	
		
		if(isset($article->id))
		{			
			
			$id = $article->id;				
			$database->setQuery("SELECT show_title, itemtype FROM #__pi_item_index WHERE item_id='$id' LIMIT 1");
			$rows = $database->loadObjectList();
			$item_type = false;
			if($rows)
			{
				$itemrow = $rows[0];
				$item_type = $itemrow->itemtype;
			}
			//hide title, but only if item has been made with PI because if it is not, it won't be in the item index
			if($item_type !='')
			{
				$show_title = $itemrow->show_title;
				if($show_title==0)
				{
					//do the replacement
					$article->title = '';
				}
			}
			else
			{
				return true;
			}
			//insert anchor link
			echo '<a name="item'.$id.'"></a>';
			
			
			//if customitemtype
			if(strpos($item_type, 'ustom_'))
			{
				//get option and view
				$view = JRequest::getVar('view', '');
				$option = JRequest::getVar('option', '');

				if($option=='com_content' || $option=='com_frontpage')
				{
					if($view=='article')
					{
						//full item view, so take out any content which is has bot-code to take out in full view
						$regex = "/{hide_in_full_view}(.*?){\/hide_in_full_view}/is";
						$article->text = preg_replace($regex, '', $article->text);
						$regex = "/{hide_in_intro_view}/is";
						$article->text = preg_replace($regex, '', $article->text);
						$regex = "/{\/hide_in_intro_view}/is";
						$article->text = preg_replace($regex, '', $article->text);
					}
					else
					{
						//intro item view, so take out any bot-code
						$regex = "/{hide_in_full_view}/is";
						$article->text = preg_replace($regex, '', $article->text);
						$regex = "/{\/hide_in_full_view}/is";
						$article->text = preg_replace($regex, '', $article->text);
						$regex = "/{hide_in_intro_view}(.*?){\/hide_in_intro_view}/is";
						$article->text = preg_replace($regex, '', $article->text);
						$regex = "/{hide_in_intro_view}/is";
						$article->text = preg_replace($regex, '', $article->text);
						$regex = "/{\/hide_in_intro_view}/is";
						$article->text = preg_replace($regex, '', $article->text);
					}
				}
			}
			elseif($item_type!='' && $item_type!='content' && $item_type!='text' && $item_type!='html' && $item_type!='other_item')
			{
				//if item is not text or html or other_item or any customitemtype then get itemtype-plugin-specific output
				//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pi_pages_and_items'.DS.'includes'.DS.'extensions'.DS.'helper.php');
				//$itemtype = ExtensionHelper::importExtension('itemtype',null, $item_type,true,null,true);
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
				$itemtype = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
				$dispatcher = &JDispatcher::getInstance();
				$itemtypeHtml = & new JObject();
				$itemtypeHtml->text = '';
				$results = $dispatcher->trigger('onItemtypeDisplay_item_frontend', array(&$itemtypeHtml,$item_type,$id,$article)); //,$text,$itemIntroText,$itemFullText));
				$article->text = $itemtypeHtml->text;
				/*
				*/
				
				
				// && file_exists(dirname(__FILE__).'/../pages_and_items/itemtypes/'.$item_type.'/item_frontend.php')
				//include(dirname(__FILE__).'/../pages_and_items/itemtypes/'.$item_type.'/item_frontend.php');
			}
		
			//process dynamic fields (like the custom-itemtype-field item_hits, which need to be generated on the fly)
			//this is the new make shure that the function onDisplay_dynamic_field in the fieldtype not in extra file
			$regex = "/{pi_dynamic_field (.*?) (.*?)}/is";
			preg_match_all($regex, $article->text, $matches); 
			if(count($matches[1]))
			{
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
				$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, null,true,null,true);
				for($n = 0; $n < count($matches[1]); $n++)
				{
					$dynamic_field_params = $matches[2][$n];
					$output = '';
					$dispatcher->trigger('onDisplay_dynamic_field', array(&$output, $article, $matches[1][$n], $params, $dynamic_field_params));
					$code_to_replace = $matches[0][$n];
					$article->text = str_replace($code_to_replace, $output, $article->text);
				}
			}
			/*
			and this is the old
			for($n = 0; $n < count($matches[1]); $n++)
			{
				$class_name = 'class_fieldtype_'.$matches[1][$n].'_dynamic_output';
				if(!class_exists($class_name)){
					if($matches[1][$n]=='php'){
						require_once(dirname(__FILE__).'/../../administrator/components/com_pi_pages_and_items/dynamic_output_php_fieldtype.php');
					}else{
						require_once(dirname(__FILE__).'/../pages_and_items/fieldtypes/'.$matches[1][$n].'/dynamic_output.php');
					}
				}
				$class_plugin = new $class_name();
				$dynamic_field_params = $matches[2][$n];
				//dump($matches);
				$output = $class_plugin->display_dynamic_field($row, $params, $dynamic_field_params);		
				$code_to_replace = $matches[0][$n];		
				$row->text = str_replace($code_to_replace, $output, $row->text);
			}
			*/
			//$article->text = $article->id;
			
		}					
		
	}
	
	
}
?>