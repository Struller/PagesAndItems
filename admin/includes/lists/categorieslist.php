<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;

//require_once(dirname(__FILE__).DS.'extension.php');

class CategoriesList
{
	private $_parent = null;
	private $_items = null;
	private $_item = null;
	private $_maxLevelcat = null;
	

	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
	
	
	}


	/*
	
	categories category and category_blog
	*/
	
	function getCategories($html,$model,$maxLevelcat = 'maxLevelcat',$open = true, $parent = false, $add = false,$categoryExtension='com_content')
	{
		$output = $this->prepareItemsOutput($model,$maxLevelcat,$open,$parent, $add,$categoryExtension);
		$html = '';
	
		if($output)
		{
			if(isset($model->menuItemsType->icons->default->imageUrl))
			{
				$image = $model->menuItemsType->icons->default->imageUrl;
			}
			/*
			if($image)
			{
				$image = '<img src="'.$image.'" alt="" style="vertical-align: middle;position: relative;" />&nbsp;';
			}
			else
			{
				$image = '<img src="'.PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png" alt="" style="vertical-align: middle;" />&nbsp;';
			}
			*/
			if(!$image)
			{
				$image = PagesAndItemsHelper::getDirIcons().'category/icon-16-category.png';
			}
			
			
			$html .= '<table class="piadminform xadminform" width="98%">';
				$html .= '<thead class="piheader">';
					$html .= '<tr>';
						$html .= '<th>'; // class="piheader">';//style="background: none repeat scroll 0 0 #F0F0F0;border-bottom: 1px solid #999999;">';
							//$html .= $image;
							//$html .= JText::_('JCATEGORY').JText::_('COM_PAGESANDITEMS_ON').JText::_('COM_PAGESANDITEMS_PAGE'); ??
							//$html .= JText::_('COM_PAGESANDITEMS_CATEGORIES_ON_PAGE');
							$html .= PagesAndItemsHelper::getThImageTitle($image,JText::_('COM_PAGESANDITEMS_CATEGORIES_ON_PAGE'));
						$html .= '</th>';
					$html .= '</tr>';
				$html .= '</thead>';
				$html .= '<tbody>';
					/*
					if($add )
					{
						$html .= '<tr>';
							$html .= '<td>';
							$html .= 'add';
							$html .= '</td>';
						$html .= '</tr>';
					}
					*/
					$html .= '<tr>';
						$html .= '<td>';
							$html .= $output; //$this->prepareItemsOutput($model,$maxLevelcat,'maxLevelcat');
						$html .= '</td>';
					$html .= '</tr>';
				$html .= '</tbody>';
			$html .= '</table>';
		}
		return $html;
		//return true;
	}
	
	
	function prepareItemsOutput($model,$maxLevelcatName,$open,$parent, $add,$categoryExtension)
	{
		$html = '';
			//if($model->menu_item)
			if($model->menuItem)
			{
				//we need the com_content params maxLevelCat
				jimport( 'joomla.application.component.helper' );
				
				$contentParams  = JComponentHelper::getParams($categoryExtension); //'com_content');
				//$this->_maxLevelcat = $contentParams->get('maxLevelcat','');
				$this->_maxLevelcat = $contentParams->get($maxLevelcatName,'');
				
				$item = $model->menuItem;//$model->menu_item->getItem();
				$params = new JRegistry();
				$request = new JRegistry();
				if ($item) 
				{
					$params->loadArray($item->params);
					$request->loadArray($item->request);
				}
				$this->_item = $item;

				$maxLevelcat = $params->get($maxLevelcatName,'');
				if($maxLevelcat == '')
				{
					$maxLevelcat = $this->_maxLevelcat;
				}

				//if($maxLevelcat == 0)
				if($maxLevelcat)
				{
					//unset($items);
					if($maxLevelcat == -1)
					{
						//is set to all
						$maxLevelcat = 0;
					}

					$items = $this->getItems($maxLevelcatName);
				
					if($items || $parent || $add)
					{
				
						$level = isset($this->_parent->level) ? $this->_parent->level : 1;
						if(count($items))
						foreach($items as $item => $value)
						{
							if( $maxLevelcat && ($value->level > ($level + $maxLevelcat) ) )
							{
								unset($items[$item]);
							}
						}
						
						//$this->_parent

						$output = $this->getOutputItems($items,$level,$open,$this->_parent, $add,$categoryExtension);
						if($output != '')
						{
					
							$html .= $output;
						}
						else
						{
							//no Categories in the page
						}
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				//the model will not load
			}

		return $html;
	}


	

	function getOutputItems($items,$level,$open,$parent, $add,$categoryExtension)
	{
		$html = '';
		//TODO an category new button?
		//TODO add script with function publish_unpublish_category(id,newState)?
		
		//load here from models/categorie function 
		//
		//rename to category ore move from Categorie to here
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'categorie.php');
		//$modelCategorie = new PagesAndItemsModelCategorie();
		//return $modelCategorie->getUnderlyingCategories($items,true,$level,true,$open);
		return $this->getUnderlyingCategories($items,true,$level,true,$open,$parent, $add,$categoryExtension);
		
		foreach($items as $item)
		{
			//TODO output like page-items ? with link to view=category?
			$html .= str_repeat('<span class="gi">|&mdash;</span>', $item->level-($level+1)).'<small>Category Title:</small> '.$item->title.', <small>Category Id:</small>'.$item->id;
			$html .= $this->getState($item);
			
			$html .= '<br />';
		}
		
		return $html;
	}

	public function getItems($maxLevelcatName)
	{
		if (!count($this->_items)) {
			
			$app = JFactory::getApplication();
			$params = new JRegistry();
			$request = new JRegistry();
			if ($this->_item) 
			{
				$params->loadArray($this->_item->params);
				$request->loadArray($this->_item->request);
			}

			$options = array();
			$options['countItems'] = $params->get('show_cat_num_articles_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$options['published'] = 0;
			$recursive = false;
			
			switch($params->get($maxLevelcatName,''))
			{
				case 1:
					$recursive = false;
				break;
				
				
				case '':
					if($this->_maxLevelcat == -1 || $this->_maxLevelcat > 1)
					$recursive = true;
				break;
				
				default:
					$recursive = true;
				break;
			}
			//$path = realpath(dirname(__FILE__).DS.'..');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'includes'.DS.'extendedjcategories.php');
			//$categories = extendedJCategories::getInstance($categoryExtension , $options); will not run correct
			//,$categoryExtension='com_content'
			
			
			/*
			$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
			$parts = explode('.', $extension);
			$component = 'com_'.strtolower($parts[0]);
			$section = count($parts) > 1 ? $parts[1] : '';
			$classname = ucfirst(substr($component, 4)).ucfirst($section).'Categories';

			if (!class_exists($classname)) {
				$path = JPATH_SITE . '/components/' . $component . '/helpers/category.php';
				if (is_file($path)) {
					require_once $path;
				}
				else {
					return false;
				}
			}
		
		class ContentCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__content';
		$options['extension'] = 'com_content';
		parent::__construct($options);
	}
}
			
			
			*/
			
			
			//$options['countItems'] = $params->get('show_cat_num_articles_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$options['countItems'] = 1;
			/*
			$options['table'] = '#__content';
			$options['extension'] = 'com_content';
			$categories = new extendedJCategories($options);
			*/
			$categoryExtension = JRequest::getVar('categoryExtension', 'com_content');
			//$categoryExtension = (strpos($categoryExtension,'com_') !== false) ? strtolower($categoryExtension) : 'com_'.strtolower($categoryExtension);
			$categoryExtension = (strpos($categoryExtension,'com_') !== false) ? strtolower(str_replace('com_','',$categoryExtension)) : strtolower($categoryExtension);
			$categories = extendedJCategories::getInstance($categoryExtension , $options);
			
			$this->_parent = $categories->get($request->get('id', 'root'),true);
			if (is_object($this->_parent)) {
				$this->_items = $this->_parent->getChildren($recursive);
			}
			else {
				$this->_items = false;
			}
		}

		return $this->_items;
	}
	
	function getUnderlyingCategories($rows,$hide_arrows = false,$level = false, $showType = false,$open = true,$parent = false, $add = false,$categoryExtension='com_content')
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		JHTML::script('submit_actions_categories.js', PagesAndItemsHelper::getDirJS().'/',false);
		$doc =& JFactory::getDocument();
		$html = '';
		$html .= '<div class="paddingList">';
		$imagePath = PagesAndItemsHelper::getDirIcons();
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
		$dispatcher = &JDispatcher::getInstance();
		$htmlelement->html = '';
		$option = JRequest::getCmd('option', '');
		$canDoCategory = PagesAndItemsHelper::canDoContent();
		
		$return = null;
		$pageId = JRequest::getVar('pageId',null);
		if($pageId) {
			$url = 'index.php?option=com_pagesanditems&view=page';
			$url .= '&pageId='.$pageId;
			$url .= JRequest::getVar('menutype',null) ? '&menutype='.JRequest::getVar('menutype','') : '';
			$url .= JRequest::getVar('sub_task',null) ? '&sub_task='.JRequest::getVar('sub_task','') : '';
			$url = base64_encode($url);
			$return = '&return='.$url;
		}

		//loop through items and echo data to hidden fields
		$counter = 0;
		$category_ids = array();
		$enabled_view_category = false;
		$config = PagesAndItemsHelper::getConfigAsRegistry();
		if($config->get('enabled_view_category'))
		{
			$enabled_view_category = true;
		}
		$itemsRows = array();
		$outputRows = '';
		$buttonNew = false;
		$useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		$sub_task = JRequest::getCmd('sub_task', '');
		if($parent)
		{
			require_once(realpath(dirname(__FILE__).DS.'..'.DS.'..').DS.'includes'.DS.'html'.DS.'tableitems.php');
			$countColumns = 6;
			$config = array('itemName'=>'category','itemTask'=>JRequest::getVar('view',''),'output'=>true);
			$table = new htmlTableItems($config);
			$table->table();
			$table->tbody();
			$table->tr();
			$row = $parent;
			
			$image = $imagePath.'category/icon-16-category.png';
			$category_title = htmlspecialchars($row->title);
			
			$no_access = '';
			$canDoCategory = PagesAndItemsHelper::canDoContent($row->id);
			if(!$canDoCategory->get('core.edit'))
			{
				$no_access = addslashes(JText::_('COM_PAGESANDITEMS_CATEGORY_NO_ACCESS'));
			}
				
			
			if($no_access)
			{
				//column 1
				$table->td('<img src="'.$image.'" alt="'.$no_access.'" />',array('attributes'=>array('class'=>'items_row_image'))); //&nbsp;';
				$column2 = '<span class="editlinktip hasTip" title="'.$no_access.'">';
				$column2 .= $category_title;
				$column2 .= '</span>';
				//column 2
				$table->td($column2);
			}
			else
			{
				//column 1
				$table->td('<img src="'.$image.'" alt="'.$no_access.'" />',array('attributes'=>array('class'=>'items_row_image')));

				
				$column2 = '';
				if($useCheckedOut && $sub_task == 'edit')
				{
					//,$categoryExtension='com_content'
					$column2 = '<a class="no_underline">';
					$column2 .= $category_title;
					$column2 .= '</a>';
					
				}
				else
				{
					if($enabled_view_category) {
					//,$categoryExtension='com_content'
					$column2 .= '<a href="index.php?option=com_pagesanditems&view=category&sub_task=edit&categoryId='.$row->id.'&categoryExtension='.$categoryExtension.$return.'" alt="'.$no_access.'">';
					}
					else
					{
					$column2 = '<a class="no_underline">';
					}
					$column2 .= $category_title;

				//if($enabled_view_category) {
					$column2 .= '</a>';
				//}
				}
				//column 2
				$table->td($column2);
			}
			
			$column3 = '';
			$column3 .= '<span class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'">';
				$column3 .= $row->numitems;
			$column3 .= '</span>';
			$table->td($column3,array('attributes'=>array('class'=>'count_items_right','style'=>'width:30px;')));
			
			//column 4
			
			$column4 = '<span class="display_none"><input type="checkbox" name="categoryCid[]" id="categoryCid_'.$row->id.'" value="'.$row->id.'" /></span>';//remove if we want we want not to unpublish the parent
			//$column4 = ''; //remove if we want we want to unpublish the parent
			$publish = empty($column4) ? 0 : 1;
			$configTd4 = array('rowPublished'=>$row->published, 'rowId'=>$row->id,'canDo'=>$canDoCategory->get('core.edit.state'),'attributes'=>array('style'=>'width:40px;min-width:20px;'),'unpublish'=>$publish); //,'unpublishTip'=>'is the parent can not unpublish');
			$table->tdState($column4,$configTd4);

			$table->td(JText::_('JCATEGORY').'-Parent'); //TODO own JText?
			
			
			if($add && $enabled_view_category && $parent && $canDoCategory->get('core.create')) // && (!$useCheckedOut || !$sub_task == 'edit')) //&& (!$useCheckedOut || !$sub_task == 'edit'))
			{
				$buttonNew = true;
				if(!$useCheckedOut || !$sub_task == 'edit')
				{
				$column5 = '';
				//$column5 = '<div class="width-100 fltrt">';
					$button = PagesAndItemsHelper::getButtonMaker();
					$button->class = 'fltrt button';
					$button->imagePath = PagesAndItemsHelper::getDirIcons();
					$button->buttonType = 'input';
					$button->text = JText::_('COM_PAGESANDITEMS_NEW_CATEGORY');
					$button->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_CATEGORY');
					$link = 'index.php?option=com_pagesanditems&view=category&hideTree=1&sub_task=new&categoryId='.$parent->id.'&categoryExtension='.$categoryExtension.$return;
					$button->onclick = 'document.location.href=\''.$link.'\';';
					$button->imageName = 'category/icon-16-category_add.png';
					$column5 .= $button->makeButton();
				//$column5 .= '</div>';
				$table->td($column5); //,array('attributes'=>array('class'=>'items_row_checkbox'));
				}
			}
			
			
			$out_html = '';
			$out_html = '<div class="width-100 fltlft items_target" id="target_categories_parent" >';
			$out_html .= $table->getOutput();
			$out_html .= '</div>';
			
			$outputRows .= $out_html;
		}
		$outputRows2 = '';
		if($rows && count($rows))
		{
			require_once(realpath(dirname(__FILE__).DS.'..'.DS.'..').DS.'includes'.DS.'html'.DS.'tableitems.php');
			$countColumns = 5;
			if($showType)
			{
				$countColumns = 6;
			}
			$config = array('countRows'=>count($rows),'countColumns'=>$countColumns,'itemName'=>'category','itemTask'=>JRequest::getVar('view',''),'output'=>true);
			$table = new htmlTableItems($config);
			$table->table();
			/*
			$table->thead();
			$table->tr();
			$configThTitle = array('attributes'=> array('colSpan'=>3));
			$table->th(JText::_('COM_PAGESANDITEMS_TITLE'),$configThTitle);
			$table->th('<span class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'"> # </span>');
			$table->th(JText::_('COM_PAGESANDITEMS_PUBLISHED'));
			*/
			$columns = array();
			$columns[] = array('type'=>'title','config'=> array('attributes'=>array('colSpan'=>3)));
			
			//$columns[] = array('type' => 'th','content' =>'<span class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'"> # </span>');
			//$columns[] = array('type' => 'countitems');
			$columns[] = array('type' => 'CountItemsIcon');
			//,'content' =>'<span class="hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'"> # </span>');
			
			$columns[] = array('type' => 'state');
			
			if(!$hide_arrows) {
				//$table->th(JText::_('COM_PAGESANDITEMS_ORDERING')); 
				if($useCheckedOut && $sub_task != 'edit')
				{
					$columns[] = array('type' => 'orderingIcon');
				}
				else
				{
					//$columns[] = array('type' => 'orderingIcon','config'=> array('loadJs'=>0)); //only thre icon
					$columns[] = array('type' => 'ordering');
				}
			}
			
			if($showType) { 
				//$table->th(JText::_('COM_PAGESANDITEMS_TYPE')); 
				$columns[] = array('type' => 'type');
			}
			$table->header($columns);
			
			
			$table->tbody();
			//$colored = 0;
			foreach($rows as $row)
			{
				//$class = $rowColored ? 'class="row'.$colored.'"' : '';
				//class="row0"
				//$configTr = array('attributes'=>array('class'=>'row'.$colored));
				//$table->tr($configTr);
				$table->trColored();
				$category_ids[] = $row->id;
				$areThereUnderlyingCategories = true;
				$counter = $counter + 1;

				$image = $imagePath.'category/icon-16-category.png';
				
				$category_title = htmlspecialchars($row->title);
				
				$no_access = '';

				$canDoCategory = PagesAndItemsHelper::canDoContent($row->id);
				
				if(!$canDoCategory->get('core.edit'))
				{
					$no_access = addslashes(JText::_('COM_PAGESANDITEMS_CATEGORY_NO_ACCESS'));
				}
				//column 1
				
				
				$outputRows2 .= '<input name="reorder_category_id_'.$counter.'" id="reorder_category_id_'.$counter.'" type="hidden" value="'.$row->id.'" />';
				$outputRows2 .= '<input name="reorder_category_lft_'.$counter.'" id="reorder_category_lft_'.$counter.'" type="hidden" value="'.$row->lft.'" />';
				$outputRows2 .= '<input name="reorder_category_rgt_'.$counter.'" id="reorder_category_rgt_'.$counter.'" type="hidden" value="'.$row->rgt.'" />';
				
				$column1 = '';
				$column1 .= '<input type="checkbox" name="categoryCid[]" onclick="isCheckedCategory(this.checked);" id="categoryCid_'.$row->id.'" value="'.$row->id.'" />';
				$table->td($column1,array('attributes'=>array('class'=>'items_row_checkbox')));

				//column 2 + 3
				if($level)
				{
					$category_title = str_repeat('<span class="gi">|&mdash;</span>', $row->level-($level+1)).$category_title;
				}
				if($no_access)
				{
					//column 2
					$table->td('<img src="'.$image.'" alt="'.$no_access.'" />',array('attributes'=>array('class'=>'items_row_image')));
					
					//column 3
					$column3 = '<span class="editlinktip hasTip" title="'.$no_access.'">';
					$column3 .= $category_title;
					$column3 .= '</span>';
					$table->td($column3);
				}
				else
				{
					//column 2 
					$table->td('<img src="'.$image.'" alt="'.$no_access.'" />',array('attributes'=>array('class'=>'items_row_image')));
					//column 3
					
					$column3 = '';
					
					if($useCheckedOut && $sub_task == 'edit')
					{
						//,$categoryExtension='com_content'
						$column3 = '<a class="no_underline">';
						$column3 .= $category_title;
						$column3 .= '</a>';
					
					}
					else
					{
						if($enabled_view_category) { 
							$column3 .= '<a href="index.php?option=com_pagesanditems&view=category'.($useCheckedOut ? '' : '&sub_task=edit').'&categoryId='.$row->id.'&categoryExtension='.$categoryExtension.$return.'" alt="'.$no_access.'">';
						}
						else
						{
							$column2 = '<a class="no_underline">';
						}
					
						$column3 .= $category_title;
						//if($enabled_view_category){
						$column3 .= '</a>';
					}
					//}
					$table->td($column3);
				}
				//column 4
				$column4 = '';
				
				$column4 .= '<div class="count_items_right">';
				$column4 .= '<span>'; // class="editlinktip hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'">';
					$column4 .= $row->numitems;
				$column4 .= '</span>';
				$column4 .= '</div>';
				
				$table->td($column4,array('attributes'=>array('class'=>'count_items_right','ch'=>'right')));
				//column 5
				
				
				$configTd3 = array('rowPublished'=>$row->published, 'rowId'=>$row->id,'canDo'=>$canDoCategory->get('core.edit.state'));
				$table->tdState('',$configTd3);
				//$currentRow = 1;
				
				if(!$hide_arrows) {
					$configTd4 = array('countRows'=>count($rows), 'currentRow'=>$counter);
					$table->tdOrdering('',$configTd4);
				}
				if($showType)
				{
					//column 3
					$table->td(JText::_('JCATEGORY')); //,array('attributes'=>array('ch'=>'right')));
				}
			}

			$outputRows2 .= $table->getOutput();
		}
		else
		{
			$outputRows2 .= JText::_('COM_PAGESANDITEMS_THIS_CATEGORY_NO_UNDERLYING_CATEGORIES');
		}
		
		$configShowSlider = PagesAndItemsHelper::getConfigAsRegistry()->get('showSlider','-1');
		$showSlider = (int)$configShowSlider ? ((int)$configShowSlider == -1 ? (($counter || ($counter && $add && $parent)) ? 1 : 0) : (($counter && $counter > (int)$configShowSlider) || $add && $parent ) ? 1 : 0 ) : 0;
		//$showSlider = (($counter && $counter > 1 && $add && $parent) || ($counter && !$add && !$parent)) ? 1 : 0; //TODO option in config showSlider -1 = allways, 0 = none , > 0 
		
		
		//ms: add slider
		if($showSlider)
		{
			$html .= JHtml::_('sliders.start','categories_sliders', ($open ? array('useCookie'=>1) : array('useCookie'=>0,'startOffset'=>-1)) );
				$html .=  JHtml::_('sliders.panel',JText::_('JCATEGORIES'), 'categories');
					$html .= '<fieldset class="panelform">';
		}
		/*
		$canDoCategory = PagesAndItemsHelper::canDoContent($row->id);
			if(!$canDoCategory->get('core.edit'))
			{
		
		*/
		//todo canDo create
		if($add && $enabled_view_category && $parent && $canDoCategory->get('core.create') && !$buttonNew)
		{
		
			//$html .= '<div class="paddingList" >'; //style=" overflow-x: hidden; overflow-y: hidden;">';
			$html .= '<div class="width-100 fltlft">';
				//$html .= '&nbsp;&nbsp;';
				$button = PagesAndItemsHelper::getButtonMaker();
				$button->imagePath = PagesAndItemsHelper::getDirIcons();
				$button->buttonType = 'input';
				$button->text = JText::_('COM_PAGESANDITEMS_NEW_CATEGORY');
				$button->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_CATEGORY');
				//$button->onclick = 'new_item('.$menu_id.');';
				//<a alt="" href="index.php?option=com_pagesanditems&amp;view=category&amp;sub_task=edit&amp;categoryId=20&amp;return=aW5kZXgucGhwP29wdGlvbj1jb21fcGFnZXNhbmRpdGVtcyZ2aWV3PXBhZ2UmcGFnZUlkPTI3NyZzdWJfdGFzaz1lZGl0">Extensions</a>
				
				$link = 'index.php?option=com_pagesanditems&view=category&hideTree=1&sub_task=new&categoryId='.$parent->id.'&categoryExtension='.$categoryExtension.$return;
				
				$button->onclick = 'document.location.href=\''.$link.'\';';
				$button->imageName = 'category/icon-16-category_add.png';
				$html .= $button->makeButton();
			$html .= '</div>';
		}
		
		if($parent)
		{

				$html .= $outputRows;
				//$html .= '&nbsp;&nbsp;';
				$html .= '<div class="line_top clr paddingList">';
				$html .= '</div>';
				//$html .= '<div class="pi_wrapper clr">';
				//$html .= '</div>';
			//$html .= '</div>';
		}


		//2 hidden fields which are usefull for updating the ordering when submitted
		//$html .= '<input name="items_category_are_reordered" id="items_category_are_reordered" type="hidden" value="false" />';
		//$html .= '<input name="items_category_total" id="items_category_total" type="hidden" value="'.$counter.'" />';


		if($counter) // || $parent)
		{
			$html .= '<div id="target_categories_actions" class="items_target_actions">';
				$html .= '<div class="items_target_actions_buttons">'; //style="float:right;">';
					ExtensionHtmlHelper::importExtension('category_actions',null,true,null,true);
					$htmlelement->html = '';
					$htmlOptions = null;
					$htmlOptions->canDo = $canDoCategory;
					$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'category_actions', $htmlOptions));
					$html .= $htmlelement->html;
				$html .= '</div>';
			$html .= '</div>';

			//$html .= '<div class="clr">';
			//$html .= '</div>';
		}
		
		$html .= '<div id="target_categories" class="items_target">';
			if(!$showSlider) //$counter == 1)
			{
				$html .= '<fieldset class="noborder">';
			}
			$html .= $outputRows2;
			if(!$showSlider) //$counter == 1)
			{
				$html .= '</fieldset>';
			}
		$html .= '</div>';
		$script = '';
		$html .= '<script language="JavaScript"  type="text/javascript">';
		$html .= "<!--\n";
		
		//$html .= "var items_category_total = ".$counter.";\n";
		//$html .= "var joomlaVersion = '".$joomlaVersion."';\n";
		//$html .= "var moveUp = '".JText::_('JLIB_HTML_MOVE_UP')."';\n";
		//$html .= "var moveDown = '".JText::_('JLIB_HTML_MOVE_DOWN')."';\n";
		/*
		if($showType)
		{
			$html .= "var number_of_columns_items_category = '3';\n";
		}
		else
		{
		//if we add an state column we must set to 2
		$html .= "var number_of_columns_items_category = '2';\n";
		}
		*/
		
		//$html .= "var ordering = '".JText::_('COM_PAGESANDITEMS_ORDERING')."';\n";
		//$html .= "var no_categories = '".JText::_('COM_PAGESANDITEMS_THIS_CATEGORY_NO_UNDERLYING_CATEGORIES')."';\n";
		/*
		$hide_arrows = $hide_arrows ? 'true' : 'false';
		$html .= "var category_hide_arrows = ".$hide_arrows.";\n";
		*/
		//$html .= "document.onload = print_categories();\n";

		$html .= "var category_ids = new Array(";
		$first = 1;
		foreach($category_ids as $category_ids_categorie){
			if(!$first){
				$html .= ",";
			}
			$html .= "'".$category_ids_categorie."'";
			$first = 0;
		}
		$html .= ");\n";

		$html .= "function isCheckedCategory(isitchecked) {"."\n";
		$html .= "	if (isitchecked == true) {"."\n";
		$html .= "		document.adminForm.boxcheckedCategory.value++;"."\n";
		$html .= "	} else {"."\n";
		$html .= "	document.adminForm.boxcheckedCategory.value--;"."\n";
		$html .= "	}"."\n";
		$html .= "}\n";
		$html .= "-->\n";
		$html .= "</script>\n";
		$html .= '<input type="hidden" name="boxcheckedCategory" id="boxcheckedCategory" value="0" />';

		if($showSlider)
		{
			$html .= '</fieldset>';
			$html .= JHtml::_('sliders.end');
			
		}
		$html .= '</div>';
		return $html;
	}
	//end getUnderlyingCategories
}
class xxx
{	
	function getUnderlyingCategories($rows,$hide_arrows = false,$level = false, $showType = false,$open = true,$parent = false, $add = false)
	{
		//
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		
		/*
		JText::_("COM_PAGESANDITEMS_CONFIRM_CATEGORIES_ARCHIVE", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_CATEGORIES_TRASH", array("script"=>true));
		JText::_("COM_PAGESANDITEMS_CONFIRM_CATEGORIES_DELETE", array("script"=>true));
		JText::_('JLIB_HTML_MOVE_UP', array("script"=>true));
		JText::_('JLIB_HTML_MOVE_DOWN', array("script"=>true));
		JText::_('COM_PAGESANDITEMS_ORDERING', array("script"=>true));
		JText::_('COM_PAGESANDITEMS_THIS_CATEGORY_NO_UNDERLYING_CATEGORIES', array("script"=>true));

		*/
				
		
		
		
		JHTML::script('submit_actions_categories.js', PagesAndItemsHelper::getDirJS().'/',false);
		//JHTML::script('reorder_categories.js', PagesAndItemsHelper::getDirJS().'/',false);
		
		$doc =& JFactory::getDocument();
		$html = '';
		
		//only Test
		

		
		$html .= $this->testTable3($rows,$hide_arrows,$level, $showType,$open,$parent, $add);
		
		//
		
		$html .= '<div class="paddingList">';
		$imagePath = PagesAndItemsHelper::getDirIcons();
		
		$path = realpath(dirname(__FILE__).DS.'..'.DS.'..');
		require_once($path.DS.'includes'.DS.'extensions'.DS.'htmlhelper.php');
		$dispatcher = &JDispatcher::getInstance();
		$htmlelement->html = '';
		$option = JRequest::getCmd('option', '');
		
		$canDoCategory = PagesAndItemsHelper::canDoContent();

	
		//loop through items and echo data to hidden fields
		$counter = 0;
		$category_ids = array();
		$enabled_view_category = false;
		$config = PagesAndItemsHelper::getConfigAsRegistry();
		if($config->get('enabled_view_category'))
		{
			$enabled_view_category = true;
		}
		$header = array();
		$header['column1'] = JText::_('COM_PAGESANDITEMS_TITLE');
		$header['column2'] = '<span class="editlinktip hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'"> # </span>';
		$header['class']['column2'] = 'count_items_right';
		$header['classHeader']['column2'] = 'state_items_center';
		
		$header['column3'] = JText::_('COM_PAGESANDITEMS_PUBLISHED');
		//$header['class']['column3'] = 'state_items_center';
		
		if($showType)
		{
			$header['column4'] = JText::_('COM_PAGESANDITEMS_TYPE');
		}
		$itemsRows = array();
		$outputRows = '';
		
		if($parent)
		{
			$row = $parent;
			$category_ids[] = $row->id;
			$image = $imagePath.'category/icon-16-category.png';

			$category_title = htmlspecialchars($row->title);
				
			$no_access = '';
			$canDoCategory = PagesAndItemsHelper::canDoContent($row->id);
			if(!$canDoCategory->get('core.edit'))
			{
				$no_access = addslashes(JText::_('COM_PAGESANDITEMS_CATEGORY_NO_ACCESS'));
			}
				
			$columnshtml_1 = '';

			//column 1
			
			$columnshtml_1 .= '<table style="border-spacing: 0px;">';
				$columnshtml_1 .= '<tbody>';
					$columnshtml_1 .= '<tr>';
						/*
						$columnshtml_1 .= '<td>';
							$columnshtml_1 .= '<input type="checkbox" name="categoryCid[]" onclick="isCheckedCategory(this.checked);" id="categoryCid_'.$row->id.'" value="'.$row->id.'" />';
						$columnshtml_1 .= '</td>';
						*/
						$columnshtml_1 .= '<td>';
						
			
				if($no_access)
				{
					//$columnshtml_1 .= '<label style="display: inline; clear: none;">';
						$columnshtml_1 .= '<img src="'.$image.'" alt="'.$no_access.'" />'; //&nbsp;';
						$columnshtml_1 .= '</td>';
						$columnshtml_1 .= '<td>';
						$columnshtml_1 .= '<span class="editlinktip hasTip" title="'.$no_access.'">';
						$columnshtml_1 .= $category_title;
						$columnshtml_1 .= '</span>';
					//$columnshtml_1 .= '</label>&nbsp;';
				}
				else
				{
					$columnshtml_1 .= '<img src="'.$image.'" alt="'.$no_access.'" />'; //&nbsp;';
						$columnshtml_1 .= '</td>';
						$columnshtml_1 .= '<td>';
					$return = null;
					$pageId = JRequest::getVar('pageId',null);
					if($pageId)
					{
						$url = 'index.php?option=com_pagesanditems&view=page';
						$url .= '&pageId='.$pageId;
						$url .= JRequest::getVar('sub_task',null) ? '&sub_task='.JRequest::getVar('sub_task','') : '';
						$url = base64_encode($url);
						$return = '&return='.$url;
					}
					if($enabled_view_category)
					$columnshtml_1 .= '<a href="index.php?option=com_pagesanditems&view=category&sub_task=edit&categoryId='.$row->id.'&categoryExtension='.$categoryExtension.$return.'" alt="'.$no_access.'">';
						$columnshtml_1 .= $category_title;
					if($enabled_view_category)
					$columnshtml_1 .= '</a>';
				}

				/*
				$columnshtml_1 .= '<span class="editlinktip hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'">';
					$columnshtml_1 .= ' '.$row->numitems;
				$columnshtml_1 .= '</span>';
				*/
			
						$columnshtml_1 .= '</td>';
					$columnshtml_1 .= '</tr>';
			$columnshtml_1 .= '</tbody>';
			$columnshtml_1 .= '</table>';
			
			//column 2
			switch($row->published)
			{
				case '1':
					//$state = 'published';
					$state = '<span class="state publish"></span>';
					$title = 'title="'.JText::_('COM_PAGESANDITEMS_PUBLISHED').'"';
					$alt = JText::_('COM_PAGESANDITEMS_PUBLISHED'); //JText::_('COM_PAGESANDITEMS_UNPUBLISH');
					$image = 'tick';
					$new_state = '0';
					$onclick = ''; //'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
				break;

				case '0':
					//$state = 'unpublished';
					$state = '<span class="state unpublish"></span>';
					$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
					$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
					$image = 'cross';
					$new_state = '1';
					$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
				break;

				case '2':
					//$state = 'archive';
					$state = '<span class="state archive"></span>';
					$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
					$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
					$image = 'archive';
					$new_state = '1';
					$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
				break;

				case '-1':
					//$state = 'archive';
					$state = '<span class="state archive"></span>';
					$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
					$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
					$image = 'archive';
					$new_state = '1';
					$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
				break;
				case '-2':
					//$state = 'trash';
					$state = '<span class="state trash"></span>';
					$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
					$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
					$image = 'trash';
					$new_state = '1';
					$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
				break;
			}
			$hand = 'pi_hand';
			if(!$canDoCategory->get('core.edit.state')){
				$onclick = '';
				$title = 'title="'.JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THE_STATE').'"';
				$hand = '';
			}

				$columnshtml_2 = '<span class="editlinktip hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'">';
					$columnshtml_2 .= ' '.$row->numitems;
				$columnshtml_2 .= '</span>';
				
			$columnshtml_3 = ' <a class="jgrid hasTip '.$hand.'" '.$title.' '.$onclick.' > '.$state.'</a>';
	
			$columnshtml_4 = JText::_('JCATEGORY').'-Parent';
			
			$out_html = '';
			//$out_html = '<div class="clr">&nbsp;</div>';
			$out_html = '<div class="width-100 fltlft items_target" id="target_categories_parent" >';
			$out_html .= '<table  width="100%" border="0" cellpadding="0" cellspacing="0">';
			/*
			if(count($header)>=1)
			{
				$out_html .= '<tr>';
				for ($k = 1; $k <= count($header); $k++)
				{
					$out_html .= '<td>';
						$out_html .= '<strong>';
							$out_html .= $header['column'.$k];
						$out_html .= '<strong>';
					$out_html .= '</td>';
				}
				$out_html .= '</tr>';
			}
			*/
			$out_html .= '<tr>';
				$out_html .= '<td>';
			
					$out_html .= $columnshtml_1;
				$out_html .= '</td>';
				$out_html .= '<td>';
					$out_html .= $columnshtml_2;
				$out_html .= '</td>';
				$out_html .= '<td>';
					$out_html .= $columnshtml_3;
				$out_html .= '</td>';
				$out_html .= '<td>';
						$out_html .= $columnshtml_4;
				$out_html .= '</td>';
			$out_html .= '</tr>';
			$out_html .= '</table>';
			$out_html .= '</div>';
			
			//$out_html .= '</table>';
			$outputRows .= $out_html;
		}
		$return = null;
		$pageId = JRequest::getVar('pageId',null);
		if($pageId)
		{
			$url = 'index.php?option=com_pagesanditems&view=page';
			$url .= '&pageId='.$pageId;
			$url .= JRequest::getVar('sub_task',null) ? '&sub_task='.JRequest::getVar('sub_task','') : '';
			$url = base64_encode($url);
			$return = '&return='.$url;
		}
		$outputRows2 = '';
		if($rows && count($rows))
		{
			foreach($rows as $row)
			{
				$category_ids[] = $row->id;
				$areThereUnderlyingCategories = true;
				$counter = $counter + 1;

				$image = $imagePath.'category/icon-16-category.png';
				
				$category_title = htmlspecialchars($row->title);
				
				$no_access = '';

				$canDoCategory = PagesAndItemsHelper::canDoContent($row->id);
				
				if(!$canDoCategory->get('core.edit'))
				{
					$no_access = addslashes(JText::_('COM_PAGESANDITEMS_CATEGORY_NO_ACCESS'));
				}
				
								
				
				//column 1
				
				$columnshtml1 = '';
				
				
				$columnshtml1 .= '<input name="reorder_category_id_'.$counter.'" id="reorder_category_id_'.$counter.'" type="hidden" value="'.$row->id.'" />';
				$columnshtml1 .= '<input name="reorder_category_lft_'.$counter.'" id="reorder_category_lft_'.$counter.'" type="hidden" value="'.$row->lft.'" />';
				$columnshtml1 .= '<input name="reorder_category_rgt_'.$counter.'" id="reorder_category_rgt_'.$counter.'" type="hidden" value="'.$row->rgt.'" />';
				$columnshtml1 .= '<table style="border-spacing: 0px;">';
					$columnshtml1 .= '<tbody>';
						$columnshtml1 .= '<tr>';
				
							if($level)
							{
								$category_title = str_repeat('<span class="gi">|&mdash;</span>', $row->level-($level+1)).$category_title;
							}
							$columnshtml1 .= '<td>';
								$columnshtml1 .= '<input type="checkbox" name="categoryCid[]" onclick="isCheckedCategory(this.checked);" id="categoryCid_'.$row->id.'" value="'.$row->id.'" />';
							$columnshtml1 .= '</td>';
							$columnshtml1 .= '<td>';
					if($no_access)
					{
							//$image = $row->dtree_imageNoAccess;
							$columnshtml1 .= '<img src="'.$image.'" alt="'.$no_access.'" />'; //&nbsp;';
						$columnshtml1 .= '</td>';
							$columnshtml1 .= '<td>';
						//$columnshtml1 .= '<label style="display: inline; clear: none;">';
							$columnshtml1 .= '<span class="editlinktip hasTip" title="'.$no_access.'">';
							$columnshtml1 .= $category_title;
							$columnshtml1 .= '</span>';
						//$columnshtml1 .= '</label>'; //&nbsp;';
					}
					else
					{
						$columnshtml1 .= '<img src="'.$image.'" alt="'.$no_access.'" />'; //&nbsp;';
							$columnshtml1 .= '</td>';
							$columnshtml1 .= '<td>';
						
						
						
						if($enabled_view_category)
						$columnshtml1 .= '<a href="index.php?option=com_pagesanditems&view=category&sub_task=edit&categoryId='.$row->id.'&categoryExtension='.$categoryExtension.$return.'" alt="'.$no_access.'">';
							$columnshtml1 .= $category_title;
						if($enabled_view_category)
						$columnshtml1 .= '</a>';
					}
					//$columnshtml1 .= '</td>';
					//		$columnshtml1 .= '<td>';
					/*
					$columnshtml1 .= '<span class="editlinktip hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'">';
					$columnshtml1 .= ' '.$row->numitems;
				$columnshtml1 .= '</span>';
				*/
					
							$columnshtml1 .= '</td>';
						$columnshtml1 .= '</tr>';
					$columnshtml1 .= '</tbody>';
				$columnshtml1 .= '</table>';
				$itemsRows[$counter]['column1'] = $columnshtml1;

				$columnshtml2 = '';
					$columnshtml2 .= '<span>'; // class="editlinktip hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'">';
					$columnshtml2 .= $row->numitems;
				$columnshtml2 .= '</span>';

				//$columnshtml1 .= '</div>';

				$itemsRows[$counter]['column2'] = $columnshtml2;
				//column 2/3
				//$html .= '<div id="category_column_2_'.$counter.'">';
					switch($row->published)
					{
						case '1':
							//$state = 'published';
							$state = '<span class="state publish"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_UNPUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_UNPUBLISH');
							$image = 'tick';
							$new_state = '0';
							$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
						break;

						case '0':
							//$state = 'unpublished';
							$state = '<span class="state unpublish"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'cross';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
						break;

						case '2':
							//$state = 'archive';
							$state = '<span class="state archive"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'archive';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
						break;

						case '-1':
							//$state = 'archive';
							$state = '<span class="state archive"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'archive';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
						break;
						case '-2':
							//$state = 'trash';
							$state = '<span class="state trash"></span>';
							$title = 'title="'.JText::_('JLIB_HTML_PUBLISH_ITEM').'"';
							$alt = JText::_('COM_PAGESANDITEMS_PUBLISH');
							$image = 'trash';
							$new_state = '1';
							$onclick = 'onclick="publish_unpublish_category('.$row->id.','.$new_state.');"';
						break;
					}
					$hand = 'pi_hand';
					if(!$canDoCategory->get('core.edit.state')){
						$onclick = '';
						$title = 'title="'.JText::_('COM_PAGESANDITEMS_NO_PERMISSION_TO_EDIT_THE_STATE').'"';
						$hand = '';
					}
					
					$columnshtml3 = '';
					$columnshtml3 .= '<div class="div_reorder_rows_arrows">';
					/*
					$columnshtml2 .= '<span class="editlinktip hasTip" title="'.JText::_('COM_PAGESANDITEMS_COUNT_ITEMS_IN_CATEGORY').'">';
					$columnshtml2 .= ' '.$row->numitems;
					$columnshtml2 .= '</span>';
					*/
					$columnshtml3 .= '<a class="jgrid hasTip '.$hand.'" '.$title.' '.$onclick.' > '.$state.'</a>';
					$columnshtml3 .= '</div>'; //'<div class="div_reorder_rows_arrows">';
				//$html .= '</div>';
				$itemsRows[$counter]['column3'] = $columnshtml3;
				if($showType)
				{
					//column 3
					$itemsRows[$counter]['column4'] = JText::_('JCATEGORY');;
				}
			}
			$numberOfColumns = 3;
			if($showType)
			{
				$numberOfColumns = 4;
			}
			$outputRows2 .= PagesAndItemsHelper::htmlReorderRows('category',$itemsRows,$header,$numberOfColumns,$hide_arrows);
		}
		else
		{
			$outputRows2 .= JText::_('COM_PAGESANDITEMS_THIS_CATEGORY_NO_UNDERLYING_CATEGORIES');
		}
		
		//ms: add slider
		$showSlider = true;
		//$showSlider = false;
		if( !$counter && !$add && !$parent)
		{
			$showSlider = false;
		}
		
		//ms: add slider
		if($showSlider)
		{
			$html .= JHtml::_('sliders.start','categories_sliders', ($open ? array('useCookie'=>1) : array('useCookie'=>0,'startOffset'=>-1)) );
				$html .=  JHtml::_('sliders.panel',JText::_('JCATEGORIES'), 'categories');
					$html .= '<fieldset class="panelform">';
		}
		/*
		$canDoCategory = PagesAndItemsHelper::canDoContent($row->id);
			if(!$canDoCategory->get('core.edit'))
			{
		
		*/
		//todo canDo create
		if($add && $enabled_view_category && $parent && $canDoCategory->get('core.create'))
		{
		
			//$html .= '<div class="paddingList" >'; //style=" overflow-x: hidden; overflow-y: hidden;">';
			$html .= '<div class="width-100 fltlft">';
				//$html .= '&nbsp;&nbsp;';
				$button = PagesAndItemsHelper::getButtonMaker();
				$button->imagePath = PagesAndItemsHelper::getDirIcons();
				$button->buttonType = 'input';
				$button->text = JText::_('COM_PAGESANDITEMS_NEW_CATEGORY');
				$button->alt = JText::_('COM_PAGESANDITEMS_MAKE_NEW_CATEGORY');
				//$button->onclick = 'new_item('.$menu_id.');';
				//<a alt="" href="index.php?option=com_pagesanditems&amp;view=category&amp;sub_task=edit&amp;categoryId=20&amp;return=aW5kZXgucGhwP29wdGlvbj1jb21fcGFnZXNhbmRpdGVtcyZ2aWV3PXBhZ2UmcGFnZUlkPTI3NyZzdWJfdGFzaz1lZGl0">Extensions</a>
				
				$link = 'index.php?option=com_pagesanditems&view=category&hideTree=1&sub_task=new&categoryId='.$parent->id.'&categoryExtension='.$categoryExtension.$return;
				
				$button->onclick = 'document.location.href=\''.$link.'\';';
				$button->imageName = 'category/icon-16-category_add.png';
				$html .= $button->makeButton();
			$html .= '</div>';
		}
		
		if($parent)
		{

				$html .= $outputRows;
				//$html .= '&nbsp;&nbsp;';
				$html .= '<div class="line_top clr paddingList">';
				$html .= '</div>';
				//$html .= '<div class="pi_wrapper clr">';
				//$html .= '</div>';
			//$html .= '</div>';
		}


		//2 hidden fields which are usefull for updating the ordering when submitted
		//$html .= '<input name="items_category_are_reordered" id="items_category_are_reordered" type="hidden" value="false" />';
		//$html .= '<input name="items_category_total" id="items_category_total" type="hidden" value="'.$counter.'" />';


		if($counter) // || $parent)
		{
			$html .= '<div id="target_categories_actions" class="items_target_actions">';
				$html .= '<div class="items_target_actions_buttons">'; //style="float:right;">';
					ExtensionHtmlHelper::importExtension('category_actions',null,true,null,true);
					$htmlelement->html = '';
					$htmlOptions = null;
					$htmlOptions->canDo = $canDoCategory;
					$dispatcher->trigger('onGetHtmlelement',array(&$htmlelement,'category_actions', $htmlOptions));
					$html .= $htmlelement->html;
				$html .= '</div>';
			$html .= '</div>';

			//$html .= '<div class="clr">';
			//$html .= '</div>';
		}
		
		$html .= '<div id="target_categories" class="items_target">';
		$html .= $outputRows2;
		$html .= '</div>';
		$script = '';
		$html .= '<script language="JavaScript"  type="text/javascript">';
		$html .= "<!--\n";
		
		//$html .= "var items_category_total = ".$counter.";\n";
		//$html .= "var joomlaVersion = '".$joomlaVersion."';\n";
		//$html .= "var moveUp = '".JText::_('JLIB_HTML_MOVE_UP')."';\n";
		//$html .= "var moveDown = '".JText::_('JLIB_HTML_MOVE_DOWN')."';\n";
		/*
		if($showType)
		{
			$html .= "var number_of_columns_items_category = '3';\n";
		}
		else
		{
		//if we add an state column we must set to 2
		$html .= "var number_of_columns_items_category = '2';\n";
		}
		*/
		
		//$html .= "var ordering = '".JText::_('COM_PAGESANDITEMS_ORDERING')."';\n";
		//$html .= "var no_categories = '".JText::_('COM_PAGESANDITEMS_THIS_CATEGORY_NO_UNDERLYING_CATEGORIES')."';\n";
		/*
		$hide_arrows = $hide_arrows ? 'true' : 'false';
		$html .= "var category_hide_arrows = ".$hide_arrows.";\n";
		*/
		//$html .= "document.onload = print_categories();\n";

		$html .= "var category_ids = new Array(";
		$first = 1;
		foreach($category_ids as $category_ids_categorie){
			if(!$first){
				$html .= ",";
			}
			$html .= "'".$category_ids_categorie."'";
			$first = 0;
		}
		$html .= ");\n";

		$html .= "function isCheckedCategory(isitchecked) {"."\n";
		$html .= "	if (isitchecked == true) {"."\n";
		$html .= "		document.adminForm.boxcheckedCategory.value++;"."\n";
		$html .= "	} else {"."\n";
		$html .= "	document.adminForm.boxcheckedCategory.value--;"."\n";
		$html .= "	}"."\n";
		$html .= "}\n";
		$html .= "-->\n";
		$html .= "</script>\n";
		$html .= '<input type="hidden" name="boxcheckedCategory" id="boxcheckedCategory" value="0" />';

		if($showSlider)
		{
			$html .= '</fieldset>';
			$html .= JHtml::_('sliders.end');
			
		}
		$html .= '</div>';
		return $html;
	}
	//end getUnderlyingCategories
	
	
}
