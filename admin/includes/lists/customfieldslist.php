<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;


class CustomFieldsList
{
	function renderItems($fields,$dispatcher)
	{
		//loop through items and echo data to hidden fields
		$counter = 0;
		$outputRows = '';
		if(count($fields))
		{
			//headers
			require_once(realpath(dirname(__FILE__).DS.'..'.DS.'..').DS.'includes'.DS.'html'.DS.'tableitems.php');
			$countColumns = 4;
			//$config['attributesTable']['class']
			$config = array('countRows'=>count($fields),'countColumns'=>$countColumns,'itemName'=>'customfield','output'=>true,'attributesTable'=>array('class'=>'customitemtype piadminform xadminform')); //,'cellSpacing'=>'0')); //,'cellPadding'=>'2'
			$table = new htmlTableItems($config);
			$table->table();
			$columns = array();
			$columns[] = array('type'=>'title','config'=> array('attributes'=>array('colSpan'=>3)));
			$columns[] = array('type' => 'th','content' =>JText::_('COM_PAGESANDITEMS_TEMPLATE_CODE'));
			$columns[] = array('type' => 'type');
			//$columns[] = array('type' => 'orderingIcon','config'=> array('loadJs'=>0)); //only thre icon
			$columns[] = array('type' => 'ordering');
			$table->header($columns);
			$table->tbody();
		
			$imageDir = PagesAndItemsHelper::getDirIcons().'ui/';
			
			//for subdomains we must have
			$jpathRoot = str_ireplace(JURI::root(true),'',str_ireplace(DS,'/',JPATH_ROOT));
			
			
			//loop through items and echo data to hidden fields
			$counter = 0;
			foreach($fields as $field)
			{
				$areThereItems = true;
				$counter = $counter + 1;
				$title = PagesAndItemsHelper::truncate_string(stripslashes($field->name), '50');
				$title = str_replace('"','&quot;',$title);
				$table->trColored();
				$outputRows .= '<input name="reorder_customfield_id_'.$counter.'" id="reorder_customfield_id_'.$counter.'" type="hidden" value="'.$field->id.'" />';
				
				//column 1
				$column1 = '';
				$column1 .= '<input type="checkbox" class="checkbox" id="items_to_delete_'.$counter.'" name="items_to_delete[]" value="'.$field->id.'"  onclick="isChecked(this.checked);" />';
				$table->td($column1,array('attributes'=>array('class'=>'items_row_checkbox')));
				$params = null;
				$results = $dispatcher->trigger('onGetParams',array(&$params,$field->plugin));
				
				$image = $imageDir.'ui-'.$field->plugin.'.png';
				//if(file_exists(JPATH_ROOT.$image))
				if(file_exists($jpathRoot.$image))
				{
					/*
					html
					item_author
					item_creation_date
					item_modified_date
					item_publish_date
					item_read_more_link
					item_read_more_url
					item_title
					item_version
					link
					*/
					$table->td('<img src="'.$image.'" />',array('attributes'=>array('class'=>'items_row_image')));
				}
				else
				{
					//look in field params
					$image = $imageDir.'ui-blank.png';
					
					if($uiImage = $params->get('uiImage'))
					{
						$folder = '';
						$dispatcher->trigger('onGetFolder',array(&$folder,$field->plugin));
						if(file_exists($folder.DS.$uiImage))
						{
							$folder = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath($folder)));
							$image = str_replace(DS,'/',$folder.DS.$uiImage);
						}
					}
					$table->td('<img src="'.$image.'" />',array('attributes'=>array('class'=>'items_row_image')));
				}

				$column2 = '';
				if(! in_array(true,$results))
				{
					$field->installed = false;
					$column2 .= '<a title="not Installed" >'; //href="index.php?option=com_pagesanditems&view=config_custom_itemtype_field&field_id='.$field->id.'">';
				}
				else
				{
					$field->installed = true;
					$column2 .= '<a href="index.php?option=com_pagesanditems&view=config_custom_itemtype_field&field_id='.$field->id.'">';
				}
				$column2 .= $title.'</a>';
				$table->td($column2);
				
				//column 3
				$table->td('{field_'.$field->name.'_'.$field->id.'}');

				//column 4
				$table->td($field->plugin);
				
				//column 5
				$configTd5 = array('countRows'=>count($fields), 'currentRow'=>$counter);
				$table->tdOrdering('',$configTd5);

			}
			$outputRows .= $table->getOutput();
		}
		else
		{
			$outputRows .= JText::_('COM_PAGESANDITEMS_CUSTOMITEMTYPE_HAS_NO_FIELDS');
		}
		$outputRows .= '<br />';
		$outputRows .= '<div class="line_top">';
		$outputRows .= '<br />';

		return $outputRows;
	}

}