<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport('joomla.plugin.plugin');

/**
 * @package		PagesAndItems
 * @subpackage	content-plugin for Pages-and-Items (com_pagesanditems)
 */
class plgContentPagesanditems extends JPlugin
{

	function plgContenPages_and_items( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}


	/*
	Description

	Called before a JForm is rendered. It can be used to modify the JForm object in memory before rendering. For example, use JForm->loadFile() to add fields or JForm->removeField() to remove fields. Or use JForm->setFieldAttribute() or other JForm methods to modify fields for the form.
	
	TODO $config['plugin_system_disable_content']
	TODO find an 
	*/
	public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		
		$option = JRequest::getVar('option');
		$task = JRequest::getCmd('task');
		$view = JRequest::getCmd('view');
		$layout = JRequest::getCmd('layout');
		
		if($option=='com_content' && ($task=='edit' || $layout =='form' || $view == 'form' || $layout == 'edit'))
		{
		}
		
		if( $option == 'com_content' && ($task == 'edit' || ($view == 'article' && $layout == 'edit') ) )
		{
			if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php'))
			{
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
			}
			else
			{
				return;
			}
			$config = PagesAndItemsHelper::getConfig();
			//&& !$config['plugin_system_disable_content']
			
			if(!$config['plugin_system_hidde_button'] )
			{
				return;
			}
			/*
			if(!$disable_content = $config['plugin_system_disable_content'] ? 1 : 0)
			{
				return;
			}
			*/
			
			$item_id = null;
			$ids = JRequest::getVar('cid',null);
			if(isset($ids) && $task == 'edit')
			{
				$item_id = $ids[0];
			}
			else
			{
				$id = JRequest::getVar('id',null);
				$id = JRequest::getVar('id', JRequest::getVar('a_id', '', 'get'), 'get');
				if(strpos($id, ':'))
				{
					$pos = strpos($id, ':');
					$item_id = intval(substr($id, 0, $pos));
				}
				else
				{
					$item_id = intval($id);
				}
			}

			$item_type = false;

			if($item_id)
			{
				$database = JFactory::getDBO();
				$database->setQuery("SELECT itemtype FROM #__pi_item_index WHERE item_id='$item_id' LIMIT 1");
				$rows = $database->loadObjectList();

				if($rows)
				{
					$itemrow = $rows[0];
					$item_type = $itemrow->itemtype;
				}
			}
			//$item_type = '';
			//only if item has been made with PI
			if($item_type != '' && $item_type != 'text')
			{
				JForm::addFieldPath(dirname(__FILE__).'/../models/fields');
				//$form->setFieldAttribute('articletext', 'type', 'textarea');
				$form->setFieldAttribute('articletext', 'type', 'editornone');
				/*
				$form->setFieldAttribute('articletext','disabled','true');
				$form->setFieldAttribute('articletext','class','articletext_disabled');
				*/
				$form->setFieldAttribute('articletext','rows','20');
				
				$form->setFieldAttribute('title','class','readonly');
				
				$form->setFieldAttribute('title','readonly','true');

				$form->setFieldAttribute('alias','class','readonly');
				$form->setFieldAttribute('alias','readonly','true');

				$form->setFieldAttribute('catid','disabled','true');

				$form->setFieldAttribute('state','disabled','true');
			
				$form->setFieldAttribute('access','disabled','true');

				$form->setFieldAttribute('featured','disabled','true');
		
				$form->setFieldAttribute('language','readonly','true');
				/*
				TODO
				
				<?php $fieldSets = $this->form->getFieldsets('attribs'); ?>
			<?php foreach ($fieldSets as $name => $fieldSet) : ?>
				<?php echo JHtml::_('sliders.panel',JText::_($fieldSet->label), $name.'-options'); ?>
				<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
					<p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
				<?php endif; ?>
				<fieldset class="panelform">
					<ul class="adminformlist">
					<?php foreach ($this->form->getFieldset($name) as $field) : ?>
						<li><?php echo $field->label; ?>
						<?php echo $field->input; ?></li>
					<?php endforeach; ?>
					</ul>
				</fieldset>
			<?php endforeach; ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
						<li><?php echo $this->form->getLabel('xreference'); ?>
						<?php echo $this->form->getInput('xreference'); ?></li>
				</ul>
			</fieldset>
			
				<li><?php echo $this->form->getLabel('metadesc'); ?>
	<?php echo $this->form->getInput('metadesc'); ?></li>

	<li><?php echo $this->form->getLabel('metakey'); ?>
	<?php echo $this->form->getInput('metakey'); ?></li>


<?php foreach($this->form->getGroup('metadata') as $field): ?>
	<li>
		<?php if (!$field->hidden): ?>
			<?php echo $field->label; ?>
		<?php endif; ?>
		<?php echo $field->input; ?>
	</li>
<?php endforeach; ?>


				
				
				<?php echo $form->getLabel('rules'); ?>
				<?php echo $form->etInput('rules'); ?>
				$data
				*/
				
			}
		}
		//return true;
	}
	
	public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0)
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

	public function onContentPrepare($context, &$article, &$params, $limitstart = 0)
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

			//if customitemtype
			if(strpos($item_type, 'ustom_'))
			{
				//load modal stuff when a fieldtype image gallery is on the page
				/*
				if(strpos($article->text, 'pi_image_gallery_fieldtype')){
					JHTML::_('behavior.modal');
					$document =& JFactory::getDocument();	
					$document->addStyleSheet('administrator/components/com_pagesanditems/extensions/fieldtypes/image_gallery/image_gallery.css');
				}
				*/
				/*
				first get the fieldtypes from the itemtype
				*/
				$pos = strpos($item_type, 'ustom_');
				$type_id = substr($item_type, $pos+6, strlen($item_type));
				$item_id = $id;
				//$database;
				//get fields and values
				$database->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id "
				. "\n FROM #__pi_custom_fields_values AS v "
				. "\n LEFT JOIN #__pi_custom_fields AS f "
				. "\n ON f.id=v.field_id "
				. "\n WHERE f.type_id='$type_id' "
				. "\n AND v.item_id='$item_id' "
				. "\n ORDER BY f.ordering ASC "
				);
				$fields = $database->loadObjectList();
				//we do not check for the templates

				//get fields plugin
				$database->setQuery( "SELECT DISTINCT plugin "
				. "\nFROM #__pi_custom_fields "
				. "\nWHERE type_id='$type_id' "
				. "\nORDER BY ordering ASC"
				);
				$fieldPlugins = $database->loadResultArray();
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
				$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
				$dispatcher = &JDispatcher::getInstance();
				
				foreach($fields as $field)
				{
					$dispatcher->trigger('onFieldtypeFrontend', array (&$article,$field, $item_id,$type_id));
				}
				/*
				*/
				
				
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
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
				
				//$itemtype = ExtensionItemtypeHelper::importExtension(null, $item_type,true,null,true);
				$itemtype = ExtensionItemtypeHelper::importExtension(null, null,true,null,true);
				$dispatcher = &JDispatcher::getInstance();
				//$itemtypeHtml = & new JObject();
				$itemtypeHtml = new JObject();
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
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
				require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
				$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, null,true,null,true);
				for($n = 0; $n < count($matches[1]); $n++)
				{
					$dynamic_field_params = $matches[2][$n];
					$output = '';
					$dispatcher = &JDispatcher::getInstance();
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
				$output = $class_plugin->display_dynamic_field($row, $params, $dynamic_field_params);
				$code_to_replace = $matches[0][$n];
				$row->text = str_replace($code_to_replace, $output, $row->text);
			}
			*/
			//$article->text = $article->id;
			
			

		}

	}
	
	function onContentAfterSave($context, &$article, $isNew )
	{
		if ( $isNew ) {
			// If the article is new, this step is not needed
			return true;
		}
		
		$app = JFactory::getApplication();
		if($app->input->get('option') != 'com_content')
		{
			return true;
		}
		//only if option com_content
		$path = realpath(dirname(__FILE__).DS.'..');
		//$option = JRequest::getVar('option');
		require_once($path.DS.'helpers'.DS.'pagesanditems.php');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'itemtypehelper.php');
		$dispatcher = &JDispatcher::getInstance();
		
		
		//check for dependant items of type 'other item' and update those if needed
		ExtensionItemtypeHelper::importExtension(null, 'other_item',true,null,true);
		$dispatcher->trigger('update_other_items_if_needed', array($article->id));
	}


}
?>