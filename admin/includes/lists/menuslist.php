<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;


class MenusList
{


	function renderItems()
	{
		$temp_menus = PagesAndItemsHelper::getConfigAsRegistry()->get('menus',array());
		//get all menutypes
		$menutypes_db = array();
		$db = JFactory::getDBO();
		$db->setQuery("SELECT title, menutype, id FROM #__menu_types ORDER BY title ASC"  );
		$rows = $db->loadObjectList();

		$menus_on_page = array();
		$menus = array();
		$counterM = 0;
		foreach($temp_menus as $key => $value)
		{
			if(isset($value[1]))
			{
				$counterM++;
				$obj = new stdClass;
				$obj->menutype = $value[0];
				$obj->title = $value[1];
				$obj->checked = 1;
				$obj->order = $counterM;
				$obj->id = isset($value[2]) ? $value[2] : 0;
				
				
				//we want get from orginal
				if(!$obj->id)
				{
					foreach($rows as $row)
					{
						if($row->menutype == $obj->menutype)
						{
							$obj->title = $row->title;
							$obj->id = $row->id;
						}
					}
				}
				else
				{
					foreach($rows as $row)
					{
						if($row->id == $obj->id)
						{
							$obj->title = $row->title;
							$obj->menutype = $row->menutype;
						}
					}
				}
				
				$menus[] = $obj;
				array_push($menus_on_page, $obj->menutype);

			}
		}
		
		//loop through menutypes from database
		foreach($rows as $row)
		{
			
			if(!in_array($row->menutype, $menus_on_page)){
				$counterM++;
				$obj = new stdClass;
				$obj->menutype = $row->menutype;
				$obj->title = $row->title;
				$obj->checked = 0;
				$obj->order = $counterM;
				$obj->id = $row->id;
				$menus[] = $obj;
			}
		}

		$outputRows = '';
		if(count($menus))
		{
			//headers
			require_once(realpath(dirname(__FILE__).DS.'..'.DS.'..').DS.'includes'.DS.'html'.DS.'tableitems.php');
			$countColumns = 2;
			//$config = array('countRows'=>count($menus),'countColumns'=>$countColumns,'itemName'=>'menus','output'=>true,'attributesTable'=>array('class'=>'menus piadminform xadminform','style'=>'border:none;'));
			$config = array('countRows'=>count($menus),'countColumns'=>$countColumns,'itemName'=>'menus','output'=>true,'attributesTable'=>array('class'=>'menus adminlist','style'=>'border:none;'));
			$table = new htmlTableItems($config);
			$table->table();
			$columns = array();
			//$columns[] = array('type'=>'th');
			$columns[] = array('type'=>'th','content'=>JText::_('COM_PAGESANDITEMS_NAME'),'config'=> array('attributes'=>array('colSpan'=>2,'style'=>'text-align:left')));
			
			//$columns[] = array('type' => 'orderingIcon','config'=> array('loadJs'=>0)); //only thre icon
			$columns[] = array('type' => 'ordering','config'=> array('attributes'=>array('colSpan'=>1)));

			$columnsRow1 = array();
			$columnsRow1[] = array('type'=>'th','content'=>JText::_('COM_PAGESANDITEMS_MENUS_TIP_B'),'config'=> array('attributes'=>array('colSpan'=>4,'style'=>'text-align:left;font-weight: normal;')));
			
			$table->header(array('rows'=>array($columnsRow1,$columns)));
			
			$table->tbody();
		
			$imageDir = PagesAndItemsHelper::getDirIcons().'ui/';
			
			$counter = 0;
			foreach($menus as $menu)
			{
				
				$areThereItems = true;
				$counter++;
				$title = PagesAndItemsHelper::truncate_string(stripslashes($menu->title), '50');
				$title = str_replace('"','&quot;',$title);
				$table->trColored();
				$outputRows .= '<input id="reorder_menus_id_'.$counter.'" type="hidden" name="menus[m'.($counter-1).'][order]" size="2" value="'.($menu->order).'" />';
				
				$outputRows .= '<input type="hidden" name="menus[m'.($counter-1).'][id]" size="2" value="'.($menu->id).'" />';
				
				
				//$table->td('&nbsp;');
				
				$column1 = '';
				$column1 .= '<input type="checkbox" class="checkbox" name="menus[m'.($counter-1).'][menutype]" value="'.$menu->menutype.'"';
				if($menu->checked)
				$column1 .= ' checked="checked"';
				$column1 .= ' />';
				$table->td($column1,array('attributes'=>array('class'=>'items_row_checkbox')));
				
				
				$column2 = '';
				$column2 .= '<span class="sidestep2">';
				$column2 .= '<label>';
				$column2 .= $menu->title;
				$column2 .= '</label>';
				$column2 .= '</span>';
				
				
				//column 3
				$column3 = '';
				$column3 .= '<input type="hidden" name="menus[m'.($counter-1).'][title]" value="'.$menu->title.'" />';
				$table->td($column2.$column3);

				//column 4
				$configTd4 = array('countRows'=>count($menus), 'currentRow'=>$counter,'addJs'=>'itemsReorderField');
				$table->tdOrdering('',$configTd4);

			}
			$outputRows .= $table->getOutput();
		}
		else
		{
			$outputRows .= JText::_('COM_PAGESANDITEMS_NO_MENUS');
		}
		//here we must replace the ids from the reorder_menus_id_...
		$html = '<script language="JavaScript"  type="text/javascript">';
		$html .= "<!--\n";
		$html .= "function itemsReorderField(oldPosition,newPosition,name,number_of_columns) {"."\n";
		$html .= "	var departure = document.id('reorder_'+name+'_id_'+oldPosition);"."\n";
		$html .= "	var destination = document.id('reorder_'+name+'_id_'+newPosition);"."\n";
		$html .= "	departure.set('id','reorder_'+name+'_id_'+newPosition);"."\n";
		$html .= "	destination.set('id','reorder_'+name+'_id_'+oldPosition);"."\n";
		$html .= "}\n";
		$html .= "-->\n";
		$html .= "</script>\n";
		return $outputRows.$html;
	}
}