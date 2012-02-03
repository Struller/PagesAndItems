<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.modeladmin' );
//require_once(dirname(__FILE__).DS.'base.php');
/**

 */


class PagesAndItemsModelCustomItemtype extends JModelAdmin //JModel //PagesAndItemsModelBase
{
	/*
	BEGIN adopt from J1.6
	*/
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// The folder and element vars are passed when saving the form.
		if (empty($data))
		{
			$item	= $this->getItem();
			//$folder	= $item->folder;
			//$element= $item->element;
			//$type	= $item->type;
		}
		else
		{
			//$folder	= JArrayHelper::getValue($data, 'folder', '', 'cmd');
			//$element	= JArrayHelper::getValue($data, 'element', '', 'cmd');
			//$type		= JArrayHelper::getValue($data, 'type', '', 'cmd');
		}

		// These variables are used to add data from the plugin XML files.
		//$this->setState('item.folder',	$folder);
		//$this->setState('item.element',	$element);
		//$this->setState('item.type',	$type);

		// Get the form.
		//$form = JForm::getInstance($name, $source, $options, false, $xpath);
		$form = $this->loadForm('com_pagesanditems.customitemtype', 'customitemtype', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		/*
		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('enabled', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('enabled', 'filter', 'unset');
		}
		*/
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_pagesanditems.edit.customitemtype.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('customitemtype.id');
		//echo '$pk: '.$pk.' :$pk,';
		//echo '$pk state : '.$this->getState('extension.extension_id').' :$pk state,';
		//echo 'X';
		//$this->_cache[$pk]
		if (!isset($this->_cache[$pk]))
		{
			//echo 'XX';
			$false	= false;

			// Get a row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return $false;
			}

			// Convert to the JObject before adding other data.
			$properties = $table->getProperties(1);
			$this->_cache[$pk] = JArrayHelper::toObject($properties, 'JObject');

			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadJSON($table->params);
			$this->_cache[$pk]->params = $registry->toArray();

			// Get the plugin XML.
			$path = realpath(dirname(__FILE__).'/../');
			$path = str_replace('/',DS,$path.DS.'extensions'.DS.'itemtypes'.DS.'custom');
			
			$path = JPath::clean($path.DS.'custom.xml');
			if (file_exists($path)) {
				$this->_cache[$pk]->xml = JFactory::getXML($path);
			} else {
				$this->_cache[$pk]->xml = null;
			}
			//$this->setState('extension.extension_id',$pk);
		}
		//$this->_cache[$pk]->test = 'test';
		return $this->_cache[$pk];
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 //'extension','PagesAndItemsTable'
	*/
	public function getTable($type = 'customitemtypes', $prefix = 'PagesAndItemsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Execute the parent method.
		parent::populateState();

		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$pk = (int) JRequest::getInt('type_id');
		$this->setState('customitemtype.id', $pk);
	}

	/**
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @return	mixed	True if successful.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = '')
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Initialise variables.
		// Get the plugin XML.
		$path = realpath(dirname(__FILE__).'/../');
		$path = str_replace('/',DS,$path.DS.'extensions'.DS.'itemtypes'.DS.'custom');

		$path = JPath::clean($path.DS.'custom.xml');

		$formFile = JPath::clean($path);

		if (!file_exists($formFile))
		{
			if (!file_exists($formFile))
			{
				throw new Exception(JText::sprintf('COM_PLUGINS_ERROR_FILE_NOT_FOUND', $element.'.xml'));
				return false;
			}
		}

		if (file_exists($formFile))
		{
			// Get the plugin form.
			if (!$form->loadFile($formFile, false, '//config'))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($formFile))
		{
			if ($type != 'language')
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}
		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);

	}
	/*
	END adopt from J1.6
	*/
	
	
	
	
	

	/*
	getFields only for Test?
	*/
	function getFields($type_id,$item_id,$new_or_edit)
	{
		$db = JFactory::getDBO();
		//".$type_id." as item_id
		if($new_or_edit == 'new')
		{
			$db->setQuery( "SELECT f.*, f.id AS field_id "
			. "\n FROM #__pi_custom_fields AS f "
			. "\n WHERE f.type_id='$type_id' "
			. "\n ORDER BY f.ordering ASC "
			);
			$fields = $db->loadObjectList();
		}
		else
		{
			/*
			$db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id "
			. "\n FROM #__pi_custom_fields AS f "
			. "\n LEFT JOIN #__pi_custom_fields_values AS v "
			. "\n ON v.field_id = f.id "
			. "\n WHERE f.type_id='$type_id' "
			. "\n AND v.item_id='$item_id' "
			. "\n AND f.id=v.field_id "
			. "\n ORDER BY f.ordering ASC "
			);
			*/
			$db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id "
			. "\n FROM #__pi_custom_fields_values AS v "
			. "\n LEFT JOIN #__pi_custom_fields AS f "
			. "\n ON f.id=v.field_id "
			. "\n WHERE f.type_id='$type_id' "
			. "\n AND v.item_id='$item_id' "
			. "\n AND f.id=v.field_id "
			. "\n ORDER BY f.ordering ASC "
			);
			$fields = $db->loadObjectList();
			if(empty($fields))
			{
				$db->setQuery( "SELECT f.*, f.id AS field_id "
				. "\n FROM #__pi_custom_fields AS f "
				. "\n WHERE f.type_id='$type_id' "
				. "\n ORDER BY f.ordering ASC "
				);
				$fields = $db->loadObjectList();
			}
		}
		return($fields);
	}


	function clean_cache_content()
	{
		$app = JFactory::getApplication();
		if ($app->getCfg('caching')) {

			//clean content cache
			$cache = & JFactory::getCache('com_content');
			$cache->clean();
		}
	}

	//copied to helper as is used in at least 3 different ways
	//to do find any calls to this function and make go to helper
	function keep_item_index_clean()
	{
		$db = JFactory::getDBO();
		//get content id's
		$db->setQuery( "SELECT id, state "
		. "FROM #__content "
		);
		$items = $db->loadObjectList();

		//make nice arrays
		$content_ids = array();
		$content_ids_tashed = array();
		foreach($items as $item){
			$content_ids[] = $item->id;
			if($item->state==-2){
				$content_ids_tashed[] = $item->id;
			}
		}

		//get item index data
		$db->setQuery( "SELECT id, item_id, itemtype "
		. "FROM #__pi_item_index "
		);
		$index_items = $db->loadObjectList();

		$from_cit_to_text = array();

		//loop through item index data.
		//delete rows which item in #__content has been deleted and
		foreach($index_items as $index_item)
		{
			$index_id = $index_item->id;
			$index_item_id = $index_item->item_id;

			$delete_index_row = 0;

			//customitemtypes which have been trashed, so delete it from index (makes it a normal item)
			$itemtype = $index_item->itemtype;

			if(strpos($itemtype, 'ustom_')){
				//custom itemtype
				if(in_array($index_item_id, $content_ids_tashed)){
					//trashed
					$delete_index_row = 1;
					//to make it a normal item, take out the custom-itemtype-codes
					$from_cit_to_text[] = $index_item_id;
				}
			}

			//if item is no longer in content table, take it out of index.
			if(!in_array($index_item_id, $content_ids)){
				$delete_index_row = 1;

			}

			//delete the index row if needed
			if($delete_index_row){
				$db->setQuery("DELETE FROM #__pi_item_index WHERE id='$index_id'");
				$db->query();
			}
		}

		/*
		//clean items which where customitemtypes, but have now become normal text-types, from custom itemtype codes
		foreach($from_cit_to_text as $itemid){
			//get item texts
			$this->db->setQuery( "SELECT introtext, fulltext "
			."FROM #__content "
			."WHERE id='$itemid' "
			);
			$items = $this->db->loadObjectList();

			//take the codes out
			foreach($items as $item){
				echo $item->introtext;
				exit;
				//$introtext = $this->take_cit_codes_out($item->introtext);
				//$fulltext = $this->take_cit_codes_out($item->fulltext);
			}

			//update item
			//$this->db->setQuery( "UPDATE #__content SET introtext='$introtext', fulltext='$fulltext' WHERE id='$itemid'");
			//$this->db->query();
		}
		*/
	}

	function get_menu_id_from_category_blog($cat_id){
		$db = JFactory::getDBO();
		//get page data
		$db->setQuery("SELECT * FROM #__menu ");
		$all_menuitems = $db->loadObjectList();

		//make a new array from all categories which are used as category-blog-pages in menu
		foreach($all_menuitems as $menuitem){
			if(((strstr($menuitem->link, 'com_content&view=category&layout=blog') && $menuitem->type=='url') || !strstr($menuitem->link, 'com_content&view=category&layout=blog')) && $menuitem->type!='content_blog_category'){
				//something else
			}else{
				//category-blog-page
				$cat_id_row = str_replace('index.php?option=com_content&view=category&layout=blog&id=','',$menuitem->link);
			}
		}
		return $menu_id;
	}

	function get_url_from_menuitem($menu_id){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT link FROM #__menu WHERE id='$menu_id' LIMIT 1");
		$menu_items = $db->loadResultArray();
		$menu_url = $menu_items[0].'&Itemid='.$menu_id;
		return $menu_url;
	}


	function reorderItemsCategory($catId)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id, ordering, catid FROM #__content WHERE catid='$catId' AND (state='0' OR state='1') ORDER BY ordering ASC" );
		$rows = $db-> loadObjectList();
		$counter = 1;
		foreach($rows as $row){
			//reorder to make sure all is well
			$rowId = $row->id;
			$db->setQuery( "UPDATE #__content SET ordering='$counter' WHERE id='$rowId'");
			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>";
				exit();
			}
			$counter = $counter + 1;
		}
		return $counter;
	}

	function make_value_into_parameter($parameter, $value)
	{
		return $parameter.'-=-'.$value.'[;-)# ]';
	}


	function delete_fields_values($field_id)
	{
		//delete field values
		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__pi_custom_fields_values WHERE field_id='$field_id'");
		$db->query();
	}



	function get_installed_fieldtypes(){
		$dir_itemtypes = array();
		$temp_plugins = array();
		//make an array from installed fieldtypes
		jimport( 'joomla.filesystem.folder');
		if(file_exists($this->pathPluginsFieldtypes))
		{
			$dir_itemtypes = JFolder::folders($this->pathPluginsFieldtypes.'/');
		}
		/*
		if(file_exists(dirname(__FILE__).'/../../../plugins/pages_and_items/fieldtypes'))
		{
			$dir_itemtypes = JFolder::folders(dirname(__FILE__).'/../../../plugins/pages_and_items/fieldtypes/');
		}
		*/

		$installed_fieldtypes = $this->fieldtypes_integrated;
		//TODO search in #__plugins_table or over PluginHelper
		//jimport( 'joomla.plugin.helper' );
		//get_installed_fieldtypes

		$plugins = JPluginHelper::getPlugin('pagesanditems');

		foreach($plugins as $plugin)
		{
				//if($plugin->name)
				//array_push($temp_plugins, $plugin->name);
		}

		foreach($dir_itemtypes as $itemtype)
		{
			//if(in_array($itemtype, $temp_plugins))
			//{
				array_push($installed_fieldtypes, $itemtype);
			//}
		}
		//sort fieltypes alfabetical
		$column = '';//reset column if you used this elsewhere
		foreach($installed_fieldtypes as $fieldtype)
		{
			$column[] = $fieldtype[0];
		}
		$sort_order = SORT_ASC;//define as a var or else ioncube goes mad
		array_multisort($column, $sort_order, $installed_fieldtypes);

		return $installed_fieldtypes;
	}


	function take_itemtype_out_of_configuration($itemtype)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT config "
		."FROM #__pi_config "
		."WHERE id='pi' "
		."LIMIT 1"
		);
		$temp = $db->loadObjectList();
		$temp = $temp[0];
		$config_string = $temp->config;

		$start_itemtypes = strpos($config_string, 'itemtypes=');
		$end_itemtypes = strpos($config_string, 'START_PAGE_NEW_ATTRIBUTES');
		$config_before = substr($config_string, 0, $start_itemtypes);
		$config_after = substr($config_string, $end_itemtypes, 999999);

		//make new config string
		$config = $config_before.'itemtypes=';
		$first = true;
		for($n = 0; $n < count($this->itemtypes); $n++)
		{
			if($this->itemtypes[$n] != $itemtype){
				if($first==false){
					$config .= ',';
				}
				$config .= $this->itemtypes[$n];
				$first = false;
			}
		}
		$config .= '
		'.$config_after;

		//update config
		$db->setQuery( "UPDATE #__pi_config SET config='$config' WHERE id='pi' ");
		$db->query();
	}


	/*
	function update_custom_itemtypes_by_type($type_id)
	who are used? ore is not used?
		this is in models/customitemtype.php in function custom_itemtype_fields_delete()
		and not longer used
		//update items output
		//$model->update_custom_itemtypes_by_type($type_id);

		//update items with ajax render script and return to cit-config-page
		$url = 'index.php?option=com_pagesanditems&view=render_items_by_custom_itemtype&type_id='.$type_id;
		$url .= '&futuretask=config_custom_itemtype';
		$model->redirect_to_url( $url, '');


	TODDO remove
	*/
	function Xupdate_custom_itemtypes_by_type($type_id)
	{
		//update html of all the items of this itemtype
		//here we need the model base for future configcustomitemtype
		$itemtype_name = 'custom_'.$type_id;
		$this->db->setQuery("SELECT item_id "
		."FROM #__pi_item_index "
		."WHERE itemtype='$itemtype_name' "
		);
		$items_array = $this->db->loadResultArray();

		foreach($items_array as $item_id)
		{
			$this->update_content_table_from_custom_itemtype($item_id, $itemtype_name);
			//TODO for pi_fish trigger extended.....
			//check if the saved item has other_items linked to it, if so, update those
			//update_other_items_if_needed is in models/customitemtype.php but i think it must move to extensions/itemtypes/other_item.php
			$this->update_other_items_if_needed($item_id);
		}
	}

	//update_other_items_if_needed is in models/customitemtype.php but i think it must move to extensions/itemtypes/other_item.php
	//TODO remove
	function Xupdate_other_items_if_needed($item_id)
	{
		$this->db->setQuery("SELECT item_id FROM #__pi_item_other_index WHERE other_item_id='$item_id' ");
		$other_items = $this->db->loadObjectList();
		foreach($other_items as $other_item)
		{
			//update_duplicate_item is in models/page.php but i think it must move to extensions/itemtypes/other_item.php
			$this->update_duplicate_item($other_item->item_id, $item_id);
		}
	}

	//TODO move to itemtype
	function Xupdate_content_table_from_custom_itemtype($item_id, $item_type,$new_item=false,$row = false, $fields = false, $into=false)
	{

		//get type_id
		$pos = strpos($item_type, 'ustom_');
		$type_id = substr($item_type, $pos+6, strlen($item_type));

		//get template
		$this->db->setQuery( "SELECT template_intro, template_full, read_more, editor_id, html_after, html_before "
		. "\nFROM #__pi_customitemtypes "
		. "\nWHERE id='$type_id' "
		. "\nLIMIT 1 "
		);
		$templates = $this->db->loadObjectList();
		$template = $templates[0];
		$template_intro = $template->template_intro;
		$template_full = $template->template_full;
		$read_more = $template->read_more;
		$editor_id = $template->editor_id;
		$html_after = $template->html_after;
		$html_before = $template->html_before;

		//CHANGE MS NOVEMBER 2010
		if(!$row)
		{
			//get item
			$this->db->setQuery( "SELECT * "
			. "\nFROM #__content "
			. "\nWHERE id='$item_id' "
			. "\nLIMIT 1 "
			);
			$items = $this->db->loadObjectList();
			$row = $items[0];
		}
		if(!$fields)
		{
			//get fields and values
			$this->db->setQuery( "SELECT f.*, v.*, v.id AS value_id, f.id AS field_id "
			. "\n FROM #__pi_custom_fields_values AS v "
			. "\n LEFT JOIN #__pi_custom_fields AS f "
			. "\n ON f.id=v.field_id "
			. "\n WHERE f.type_id='$type_id' "
			. "\n AND v.item_id='$item_id' "
			. "\n ORDER BY f.ordering ASC "
			);
			$fields = $this->db->loadObjectList();
		}
		//END CHANGE MS NOVEMBER 2010
		//function render_html_from_custom_itemtype_template($template, $fields, $row, $intro_or_full, $readmore_type=0, $editor_id=0, $html_after='', $html_before='', $language = null )
		$introtext = $this->render_html_from_custom_itemtype_template($template_intro, $fields, $row, 'intro', $read_more, $editor_id, $html_after, $html_before);
		//exit;
		if($read_more=='1'){
			//no readmore
			$fulltext = '';
		}elseif($read_more=='2'){
			//only read more if not empty
			$fulltext_temp = $this->render_html_from_custom_itemtype_template($template_full, $fields, $row, 'full');
			$fulltext_temp2 = str_replace(' ','',$fulltext_temp);
			$length_with_content = strlen($fulltext_temp2);

			$full_template_temp = $template_full;
			$template_array = explode('{field_',$full_template_temp);
			foreach($template_array as $template_chunk){
				if(strpos($template_chunk,'}')){
					$end_tag = strpos($template_chunk,'}');
					$temp = substr($template_chunk, 0, $end_tag);
					$underscore = strpos($temp,'_');
					$field_id = substr($temp, $underscore+1, strlen($temp));
					$the_rest = substr($template_chunk, $end_tag+1, strlen($template_chunk));
					$full_template_temp2 .= $the_rest;
				}else{
					$full_template_temp2 .= $template_chunk;
				}
			}

			$full_template_temp2 = str_replace(' ','',$full_template_temp2);
			$length_without_content = strlen($full_template_temp2);

			//if($length_with_content==$length_without_content){
			if($length_with_content==$length_without_content || $length_without_content==0){
				//there is no content in fulltext
				$fulltext = '';
			}else{
				//there is content in fulltext
				$fulltext = $this->render_html_from_custom_itemtype_template($template_full, $fields, $row, 'full', $read_more);
			}
		}elseif($read_more=='3'){
			//readmore using read-more button in one editor
			$fulltext = $this->render_html_from_custom_itemtype_template($template_full, $fields, $row, 'full', $read_more, $editor_id, $html_after, $html_before );
		}else{
			//normal read more
			$fulltext = $this->render_html_from_custom_itemtype_template($template_full, $fields, $row, 'full', $read_more);
		}

		//CHANGE MS NOVEMBER 2010
		if(!$into)
		{
			//update item for read_more link
			$this->db->setQuery( "UPDATE #__content SET introtext='$introtext', `fulltext`='$fulltext' WHERE id='$item_id' ");
			if (!$this->db->query()) {
				echo $this->db->getErrorMsg();
				exit();
			}
		}
		else
		{
			return array("introtext"=>$introtext,"fulltext"=>$fulltext);
		}
		//END CHANGE MS NOVEMBER 2010


		//ADD MS 18.09.2009
//		$fields = $this->db->loadObjectList();
		if(!$new_item && !$into){
			$this->db->setQuery( "SELECT * "
			."\nFROM #__pi_custom_fields "
			."\nWHERE type_id='$type_id' "
			);
			$fields = $this->db->loadObjectList();
			/*//get data-fields
			$this->db->setQuery( "SELECT id, field_id "
			. "\nFROM #__pi_custom_fields_values "
			. "\nWHERE item_id='$item_id' "
			);
			$data_fields = $this->db->loadObjectList();
			//make array of fields which have a data field and make fields-datafields array
			$new_cit = true;
			$fields_datafields_array = array();
			foreach($fields as $field){
				foreach($data_fields as $field_data){
					if($field_data->field_id==$field->id){
						$fields_datafields_array[$field_data->field_id] = $field_data->id;
						$new_cit = false;
						break;
					}
				}
			}*/
			/*
			todo must rewrite
			*/

			//get fields plugin
			$this->db->setQuery( "SELECT DISTINCT plugin "
			. "\nFROM #__pi_custom_fields "
			. "\nWHERE type_id='$type_id' "
			. "\nORDER BY ordering ASC"
			);
			$fieldPlugins = $this->db->loadResultArray();
			/*
			$version = new JVersion();
			$joomlaVersion = $version->getShortVersion();
			if($joomlaVersion < '1.6')
			{
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems';
			}
			else
			{
				$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems';
			}
			require_once($path.DS.'includes'.DS.'extensions'.DS.'helper.php');
			$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $fieldPlugins,true,null,true);
			*/
			$path = realpath(dirname(__FILE__).DS.'..');
			require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
			$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, $fieldPlugins,true,null,true);
			$dispatcher = &JDispatcher::getInstance();

			foreach($fields as $field)
			{
				//start update
				$dispatcher->trigger('onExtended_save_custom_itemtype_field_save', array ($field, 'update',$item_id,$type_id,$fields));
			}
		}
		//end field loop MS
		//ADD MS 18.09.2009 END

	}


	function render_html_from_custom_itemtype_template($template, $fields, $row, $intro_or_full, $readmore_type=0, $editor_id=0, $html_after='', $html_before='', $language = null )
	{
		$db = JFactory::getDBO();
		$read_more_link_defined = $this->check_for_read_more_link($fields, $editor_id);

		//no template specified, so render default template
		if(!$template)
		{
			//ADD MS
			/*
			$template_special = false;
			foreach($fields as $field)
			{
				//$this->get_field_class_object($field->plugin,'template_special_output');
				if($class_object = $this->get_field_class_object($field->plugin,'template_special_output'))
				{
					$template .= $class_object->template_special_output($fields);
				}
			}

			//if(!$template_spezial)
			if(!$template)
			{
			//END ADD MS
			*/
				foreach($fields as $field)
				{
					$template .= '<div>';
					//$template .= '{field_'.$field->plugin.'_'.$field->field_id.'}';
					$template .= '{field_'.$field->name.'_'.$field->field_id.'}';
					$template .= '</div>';
				}
			//ADD MS
			//}
		}

		$html = addslashes($template);
		//Deal with readmore type 3
		if($readmore_type=='3')
		{
			if($read_more_link_defined)
			{
				$editor_tag="";
				//Find editor_tag - brute force becase we do not know field name for editor!!
				foreach($fields as $field)
				{
					if ($field->field_id == $editor_id)
					{
						// CHANGE MS 09.09.2009
						//$editor_tag='{field_'.$field->name.'_'.$field->field_id.'}';
						$editor_tag='\{field_'.$field->name.'_'.$field->field_id.'\}';
						break;
					}
				}
				//ADD MS 09.09.2009
				/*
				foreach($fields as $field)
				{
					//$this->get_field_class_object($field->plugin,'extended_editor_tag');
					if($class_object = $this->get_field_class_object($field->plugin,'extended_editor_tag'))
					{
						$ex_editor_tag = $class_object->extended_editor_tag($fields,$editor_id,$intro_or_full);
						if (isset($ex_editor_tag))
						{
							$editor_tag = $ex_editor_tag;
							//ADDED MS 09.09.2009 16:30
							$html=stripslashes($html);
							break;
						}
					}

				}
				*/
				//ADD MS END 09.09.2009
				if (! empty($editor_tag))
				{
					switch($intro_or_full){
					case 'intro':
						// in intro view, delete everything after the editor (leave editor)
						//CHANGE MS 09.09.2009 16:30
						//$html=preg_replace('/'.$editor_tag.'(.*?)/is',$editor_tag,$html).$html_after;
						//CHANGE MS 10.09.2009 11:00 add div is </div> after $editor_tag
						//$html=preg_replace('/'.$editor_tag.'.*/is',stripslashes($editor_tag'),$html).$html_after;
						//$html=preg_replace('/'.$editor_tag.'(<\/div>{0,1}).*/is',stripslashes($editor_tag.'${1}'),$html).$html_after;
						preg_match('/'.$editor_tag.'(<\/div>?).*/is', $html, $matches);
						if($matches)
						{
							$html=preg_replace('/'.$editor_tag.'(<\/div>{0,1}).*/is',stripslashes($editor_tag.'${1}'),$html).$html_after;
						}
						else
						{
							$html=preg_replace('/'.$editor_tag.'.*/is',stripslashes($editor_tag),$html).$html_after;
						}

						break;
					case 'full':
						// in full view, delete everything before the editor (leave editor)
						//CHANGE MS 09.09.2009
						//$html=$html_before.preg_replace('/(.*?)'.$editor_tag.'/is',$editor_tag,$html);
						//CHANGE MS 10.09.2009 11:00 add </div> is <div> before $editor_tag
						//$html=$html_before.preg_replace('/(.*?)'.$editor_tag.'/is',stripslashes($editor_tag),$html);
						preg_match('/(<\/div>?)'.$editor_tag.'.*/is', $html, $matches);
						if($matches)
						{
							$html=$html_before.preg_replace('/(.*?)(<div>?)'.$editor_tag.'/is',stripslashes('${2}'.$editor_tag),$html);
						}
						else
						{
							$html=$html_before.preg_replace('/(.*?)'.$editor_tag.'/is',stripslashes($editor_tag),$html);
						}
						//check <div>
						break;
					default;
						break;
					}
				}
			}else{
				if ($intro_or_full=='full'){
					$html='';
				}
			}
		}

		$replaceRegex=array();
		$replaceValue=array();
		if (! empty($row)){
			$replaceRegex[]='/{article_id}/is';
			$replaceValue[]=$row->id;
			$replaceRegex[]='/{article_title}/is';
			$replaceValue[]=addslashes($row->title);
			//$replaceRegex[]='/{article_link}/is';
			//$replaceValue[]=JRoute::_('index.php?option=com_content&view=article&id='.$row->id);
			$replaceRegex[]='/{article_created}/is';
			$replaceValue[]=$row->created;
			$replaceRegex[]='/{article_modified}/is';
			$replaceValue[]=$row->modified;
			$replaceRegex[]='/{article_publish_up}/is';
			$replaceValue[]=$row->publish_up;
			$replaceRegex[]='/{article_rating}/is';
			//get average rating
			$db->setQuery( "SELECT rating_sum, rating_count "
			. "FROM #__content_rating "
			. "WHERE content_id='$row->id' "
			. "LIMIT 1 "
			);
			$ratings = $db->loadObjectList();
			$rating_ave = '';
			foreach($ratings as $rating){
				$rating_sum = $rating->rating_sum;
				$rating_count = $rating->rating_count;
				$rating_ave = floor($rating_sum/$rating_count);
			}
			$replaceValue[] = $rating_ave;
		}

		foreach($fields as $field){
			$field_tag="field_".$field->name."_".$field->field_id;
			if($field->plugin=='image_multisize'){
				for ($n = 1; $n <= 5; $n++){
					$field->size = $n;
					$value=$this->get_custom_itemtype_field_content($field, $intro_or_full, $readmore_type, $editor_id);
					$replaceRegex[]="/{".$field_tag." size=".$n."}/is";
					$replaceValue[] = $value;
					$field->output = 'alt';
					$value=$this->get_custom_itemtype_field_content($field, $intro_or_full, $readmore_type, $editor_id);
					$replaceRegex[]="/{".$field_tag." size=".$n." output=alt}/is";
					$replaceValue[] = $value;
				}
			}else{
				$value=$this->get_custom_itemtype_field_content($field, $intro_or_full, $readmore_type, $editor_id);
				if (empty($field->value)){
					$replaceRegex[]="/{if-not-empty_".$field_tag."}(.*?){\/if-not-empty_".$field_tag."}/is";
					$replaceValue[] = '';
					$replaceRegex[]="/{\/?if-empty_".$field_tag."}/is";
					$replaceValue[] = '';
				}else{
					$replaceRegex[]="/{if-empty_".$field_tag."}(.*?){\/if-empty_".$field_tag."}/is";
					$replaceValue[] = '';
					$replaceRegex[]="/{\/?if-not-empty_".$field_tag."}/is";
					$replaceValue[] = '';
				}
				$replaceRegex[]="/{".$field_tag."}/is";
				$replaceValue[] = $value;

			}
		}

		$html=preg_replace($replaceRegex, $replaceValue, $html);

		return $html;
	}

	function check_for_read_more_link($fields, $editor_id)
	{
		$is_defined = 0;
		foreach($fields as $field){
			if($field->field_id==$editor_id){
				$pos1 = strpos($field->value, '<hr id="system-readmore" />');
				$pos2 = strpos($field->value, '<hr id=\"system-readmore\" />');
				if($pos1 || $pos2){
					$is_defined = 1;
				}
				break;
			}
		}
		return $is_defined;
	}

	function get_custom_itemtype_field_content($field, $intro_or_full, $readmore_type=0, $editor_id=0){
		//explode params
		$html = '';
		$field_params = '';
		$temp = $field->params;
		$temp = explode( '[;-)# ]', $temp);
		for($n = 0; $n < count($temp)-1; $n++){
			list($var,$value) = split('-=-',$temp[$n]);
			$field_params[$var] = trim($value);
		}

		//explode values
		$temp = $field->value;
		$temp = explode('[;-)# ]', $temp);
		for($n = 0; $n < count($temp)-1; $n++){
			list($var,$value) = split('-=-',$temp[$n]);
			$values[$var] = trim($value);
		}

		//get output
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extensions'.DS.'helper.php');
		//$fieldtypes = ExtensionHelper::importExtension('fieldtype',null, $field->plugin,true,null,true);
		$path = realpath(dirname(__FILE__).DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'fieldtypehelper.php');
		$fieldtypes = ExtensionFieldtypeHelper::importExtension(null, $field->plugin,true,null,true);

		$dispatcher = &JDispatcher::getInstance();
		$html = '';
		$results = $dispatcher->trigger('onRender_field_output', array (&$html,$field, $intro_or_full, $readmore_type, $editor_id));
		//fix old editors html
		$html = str_replace('<br>','<br />', $html);


		return $html;
	}

}

