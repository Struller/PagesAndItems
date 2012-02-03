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

jimport( 'joomla.application.component.view');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'default'.DS.'view.html.php');

/**
 * HTML View class for the  component

 */
class PagesAndItemsViewMenuItemTypeSelect extends PagesAndItemsViewDefault
{
	function display($tpl = null)
	{
		$doc =& JFactory::getDocument();
		
		/*
		if ($model = &$this->getModel('Page')) //,'PagesAndItemsModel'))
		{
			//echo 'model';
			$this->assignRef( 'model',$model);
			//$joomlaVersion = $model->getJoomlaVersion();
			//$this->assignRef( 'joomlaVersion',$joomlaVersion);
		}
		*/

		$path = PagesAndItemsHelper::getDirCSS();
		JHTML::stylesheet('popup.css', $path.'/');
		//JHTML::stylesheet('pagesanditems2.css', $path.'/');
		
		$path = str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).'/../../media/css/').DS));
		JHTML::stylesheet($path.'/menuitemtypeselect.css');
/*
		}
		else
		{
			JHTML::stylesheet($path.'menuitemtypeselect.css');
		}
*/


		JHTML::_('behavior.tooltip');


		$pageId = JRequest::getVar( '$pageId', 0);
		$menutype = JRequest::getVar( 'menutype', null);
		$menutypes = null;
		$modelMenu = new PagesAndItemsModelMenutypes();
		$menutypes = $modelMenu->getTypeListItems(null,$pageId,$menutype);
		$this->assignRef( 'menutypes',$menutypes);
		$lang = JFactory::getLanguage();

		parent::display($tpl);

	}



	function getMenuItemTypes16()
	{
		/*
		//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'com_menus'.DS.'models'.DS.'fields'.DS.'menutype.php');
		//TODO make new model componentmenutype and menutype extends JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'com_menus'.DS.'models'.DS.'fields'.DS.'menutype.php

		from this model we can use here we can use
		_getTypeOptions()
		here we can get all we need

		// Initialise variables.
		$html		= array();
		$types		= $this->_getTypeOptions();
		$recordId	= (int) $this->form->getValue('id');

		$html[] = '<h2 class="modal-title">'.JText::_('COM_MENUS_TYPE_CHOOSE').'</h2>';
		$html[] = '<ul class="menu_types">';

		foreach ($types as $name => $list)
		{
			$html[] = '<li>';
			$html[] = '<dl class="menu_type">';
			$html[] = '	<dt>'.JText::_($name).'</dt>';
			$html[] = '	<dd>';
			$html[] = '		<ul>';
			foreach ($list as $item)
			{
				$html[] = '			<li>';
				$html[] = '				<a class="choose_type" href="#" onclick="javascript:Joomla.submitbutton(\'item.setType\', \''.
											base64_encode(json_encode(array('id' => $recordId, 'title' => $item->title, 'request' => $item->request))).'\')"' .
											' title="'.JText::_($item->description).'">'.
											JText::_($item->title).'</a>';
				$html[] = '			</li>';
			}

			$html[] = '		</ul>';
			$html[] = '	</dd>';
			$html[] = '</dl>';
			$html[] = '</li>';
		}

		$html[] = '<li>';
		$html[] = '<dl class="menu_type">';
		$html[] = '	<dt>'.JText::_('COM_MENUS_TYPE_SYSTEM').'</dt>';
		$html[] = '	<dd>';
		$html[] = '		<ul>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" onclick="javascript:Joomla.submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('id' => $recordId, 'title'=>'url'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_EXTERNAL_URL_DESC').'">'.
									JText::_('COM_MENUS_TYPE_EXTERNAL_URL').'</a>';
		$html[] = '			</li>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" onclick="javascript:Joomla.submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('id' => $recordId, 'title'=>'alias'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_ALIAS_DESC').'">'.
									JText::_('COM_MENUS_TYPE_ALIAS').'</a>';
		$html[] = '			</li>';
		$html[] = '			<li>';
		$html[] = '				<a class="choose_type" href="#" onclick="javascript:Joomla.submitbutton(\'item.setType\', \''.
									base64_encode(json_encode(array('id' => $recordId, 'title'=>'separator'))).'\')"' .
									' title="'.JText::_('COM_MENUS_TYPE_SEPARATOR_DESC').'">'.
									JText::_('COM_MENUS_TYPE_SEPARATOR').'</a>';
		$html[] = '			</li>';
		$html[] = '		</ul>';
		$html[] = '	</dd>';
		$html[] = '</dl>';
		$html[] = '</li>';
		$html[] = '</ul>';

		return implode("\n", $html);


		*/

	}


	function getMenuItemTypes()
	{
		if(PagesAndItemsHelper::getIsJoomlaVersion('>=','1.6'))
		{
			return $this->getMenuItemTypes16();
		}


		$sub_task = JRequest::getVar( 'sub_task', 'edit');
		$extension = 'com_menus';
		$lang = &JFactory::getLanguage();
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, false) || $lang->load(strtolower($extension), JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);
		$pageType = JRequest::getVar( 'pageType', '' );
		$pageId = JRequest::getVar( 'pageId', 0 );
		$type = JRequest::getVar( 'type', 'component' );
		$pageUrl = JRequest::getVar( 'pageUrl', array());
		$pageUrlOption = JRequest::getVar( 'pageUrlOption', array());
		$sub_task = JRequest::getVar('sub_task');
		require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'helper.php' );
		//require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php' );

		$requestUrl = JRequest::getVar('url', array(0),'', 'array');
		$requestType = JRequest::getVar('type', null);
		$requestEdit = JRequest::getVar( 'edit', false );
		$requestCid = JRequest::getVar('cid', array(0),'', 'array');
		$requestExpand = JRequest::getVar('expand', '');

		/*
		$dirIcons = $this->controller->dirIcons;
		JRequest::setVar( 'dirIcons', $dirIcons );
		*/
		if($sub_task=='new')
		{
			JRequest::setVar( 'edit', false );
			//JRequest::setVar( 'type',  'component');
		}
		else
		{
			JRequest::setVar( 'edit', true );
			JRequest::setVar( 'cid',  array($pageId));
		}
		$html = '';
		$components = MenusHelper::getComponentList();
		//$html .= '<ul class="adminformlist" style="float: left;">'; //style="float: left;height: 254px;overflow: auto;width: auto;">';
		//require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'helpers'.DS.'helper.php' );
		//require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_menus'.DS.'models'.DS.'item.php' );
		/*
		$image = '<img src="'.$dirIcons.'processing.gif" >';
		$text = '';
		//$text = '<div style="padding:50px;">';
		$text .= JText::_('LOAD');
		$text .= $image;
		//$text .= '</div>';
		//$text = htmlspecialchars($text);
		$text = '';
		*/
		$html .= '<ul class="uLmenulist" id="uLmenulist" style="">';
		for ($i=0,$n=count($components);$i<$n;$i++)
		{
			//if($components[$i]->option != 'com_content')
			//{
				//TODO replace href with onclick
				//in models/page_item.php too
				$path = JPATH_SITE.DS.'components'.DS.$components[$i]->option.DS.'views';
				$components[$i]->legacy = !is_dir($path);
				$lang->load($components[$i]->option, JPATH_ADMINISTRATOR, null, false, false) || $lang->load($components[$i]->option, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false);

				if($components[$i]->legacy)
				{
					//JRequest::setVar( 'url', $pageUrl);
					//JRequest::setVar( 'type', $type);
					//$menu_item = new PagesAndItemsModelPage_item();
					$menu_item = new PagesAndItemsModelPage_items();
					$item = $menu_item->getItem();


					$html .='<li>';
						//$html .='<div class="node-open">';
							//$html .='<span></span>';

							$html .='<a ';
								$html .= 'class="child" ';
								$html .= 'onclick="';
									$html .='window.parent.document.getElementById(\'urloption\').value = \''.$components[$i]->option.'\'; ';
									$html .='window.parent.document.getElementById(\'pageType\').value = \''.str_replace('com_', '', $components[$i]->option).'\'; ';
									$html .='window.parent.document.getElementById(\'type\').value = \'component\'; ';
									$html .='window.parent.document.getElementById(\'pageId\').value = \''.$pageId.'\'; ';
									$html .='window.parent.document.getElementById(\'menutype\').value = \''.htmlspecialchars($item->menutype).'\'; ';
									$html .='window.parent.document.getElementById(\'sub_task\').value = \'new\'; ';
									$html .='window.parent.document.getElementById(\'view\').value = \'page\'; ';
									$version = new JVersion();
									$joomlaVersion = $version->getShortVersion();
									if($joomlaVersion >= '1.6')
									{
										//$onclick .='Joomla.';
										$html .='window.parent.Joomla.submitbutton(\'newMenuItem\'); ';
									}
									else
									{
										$html .='window.parent.submitbutton(\'newMenuItem\'); ';
									}
									$html .='window.parent.document.getElementById(\'sbox-window\').close();" ';
								$html .= ' id="'.str_replace('com_', '', $components[$i]->option).'" ';
								$html .= ' >';
								$html .= JText::_($components[$i]->name);
							$html .='</a>';

				}
				elseif(isset($pageUrlOption) && $components[$i]->option == $pageUrlOption)
				{
					$html .= '<li class="expand" style="">';
					JRequest::setVar( 'url', $pageUrl);
					JRequest::setVar( 'type', $type);
					//$menu_item = new PagesAndItemsModelPage_item($pageId,true);
					$menu_item = new PagesAndItemsModelPage_items($pageId,true);
					$item = $menu_item->getItem();
					$typeCid = '&amp;menutype=' . htmlspecialchars($item->menutype).'&amp;cid[]=' . $item->id;

					$html .= '<a ';
						$html .= 'class="parent" ';
						$html .= '>';
						$html .= JText::_($components[$i]->name);
					$html .= '</a>';
				}
				else
				{
					JRequest::setVar( 'type',  'component');
					JRequest::setVar( 'url', array('option'=>$components[$i]->option));
					//$menu_item = new PagesAndItemsModelPage_item();
					$menu_item = new PagesAndItemsModelPage_items();
					$item = $menu_item->getItem();
					JRequest::setVar('expand',str_replace('com_', '', $components[$i]->option));
					$expansion = &$menu_item->getExpansion();
					$class = ($i == $n-1)? 'class="last"' : '';
					$html .= '<li '.$class.'>';
					//	$html .= '<div class="node-open">';
					//		$html .= '<span></span>';
							if($expansion['html'] != '')
							{
								$html .= '<a id="'.str_replace('com_', '', $components[$i]->option).'" ';
								$html .= 'class="parent" ';
								$html .= '>';
									$html .= JText::_($components[$i]->name);
									//$html .= $components[$i]->name;
								$html .= '</a>';
							}
							else
							{
								$html .='<a ';

									/*
									$url = array();
									*/
									$html .= 'class="child" ';
									//$this->_output .= 'title="' . JText::_($this->_current->title);
									//$this->_output .= '::' . JText::_($this->_current->msg) . '" ';

									$html .= 'onclick="';
									$html .='window.parent.document.getElementById(\'urloption\').value = \''.$components[$i]->option.'\'; ';

									$html .='window.parent.document.getElementById(\'pageType\').value = \''.str_replace('com_', '', $components[$i]->option).'\'; ';
									$html .='window.parent.document.getElementById(\'type\').value = \'component\'; ';
									//if($pageId)
									//{
										$html .='window.parent.document.getElementById(\'pageId\').value = \''.$pageId.'\'; ';
									//}
									$html .='window.parent.document.getElementById(\'menutype\').value = \''.htmlspecialchars($item->menutype).'\'; ';
									//$html .='window.parent.document.getElementById(\'sub_task\').value = \''.$sub_task.'\'; ';
									$html .='window.parent.document.getElementById(\'sub_task\').value = \'new\'; ';
									$html .='window.parent.document.getElementById(\'view\').value = \'page\'; ';
									//TODO place here an image
									//$html .='window.parent.document.getElementById(\'underlayingPages\').innerHTML = \''.$text.'\'; ';
									$version = new JVersion();
									$joomlaVersion = $version->getShortVersion();
									if($joomlaVersion >= '1.6')
									{
										//$onclick .='Joomla.';
										$html .='window.parent.Joomla.submitbutton(\'newMenuItem\'); ';
									}
									else
									{
										$html .='window.parent.submitbutton(\'newMenuItem\'); ';
									}
									$html .='window.parent.document.getElementById(\'sbox-window\').close();" ';




									/*
									$html .= 'onclick="';
									$html .= 'window.parent.document.location.href=\'';
									$html .= 'index.php?option=com_pagesanditems';
									$html .= '&amp;view=page';
									if(!$pageId)
									{
										$html .= '&amp;layout=root';
									}
									else
									{
										$html .= '&amp;pageId='.$pageId;
									}
									$html .= '&amp;sub_task='.$sub_task;
									$html .= '';
									$html .= '&amp;type=component';
									$html .= '&amp;url[option]=';
									$html .= $components[$i]->option;
									$html .= '&amp;menutype='.htmlspecialchars($item->menutype);
									$html .= '&amp;pageType='.str_replace('com_', '', $components[$i]->option);
									$html .= '&amp;cid[]='.$item->id;
									$html .= '\'';
									$html .= ';window.parent.document.getElementById(\'sbox-window\').close();"';
									*/

									$html .='id="';
									$html .= str_replace('com_', '', $components[$i]->option);
									$html .= '" ';
									$html .= ' >';
									$html .= JText::_($components[$i]->name);
									/*
									$this->_output .= 'title="' . JText::_($this->_current->title);
									$this->_output .= '::' . JText::_($this->_current->msg) . '">';
									$this->_output .= JText::_($this->_current->title);
									*/
								$html .= '</a>';
							}
					//	$html .= '</div>';

					$html .= $expansion['html'];

						/*
						expansion['html'] =
						"index.php?
						option=com_menus
						&amp;task=edit
						&amp;type=component
						&amp;url[option]=com_content
						&amp;url[view]=archive
						&amp;menutype=customcontent"

						 title="Archived Article List Layout::Das Standardlayout für archivierte Beiträge zeigt Beiträge die archiviert wurden. Sie sind nach dem Datum durchsuchbar.">Archived Article List Layout</a></div></li>

						*/

				//}
				$html .= '</li>';
			//$html .= '</ul>';
			//$html .= '</div>';
			//$html .= '<div class="clr_left"></div>';
			}
		}
		$html .= '</ul>';
		/*

		JRequest::setVar( 'type',  'component');
		JRequest::setVar( 'url', array('option'=>$components[$i]->option));
		JRequest::setVar('expand',str_replace('com_', '', $components[$i]->option));
		*/
		JRequest::setVar( 'type', $requestType);
		JRequest::setVar( 'url', $requestUrl);
		JRequest::setVar( 'edit', $requestEdit );
		JRequest::setVar( 'cid', $requestCid );
		JRequest::setVar( 'expand', $requestExpand );



		return $html;
		/*
		onclick="window.parent.document.getElementById('sbox-window').close();">

		$expansion		= &$this->get('Expansion');

		//$this->menu_item
		//$expansion = $this->menu_item->getExpansion();


		for ($i=0,$n=count($components);$i<$n;$i++)
		{
			//$menu_item = &new MenusModelItem();
			if(components[$i]->option == $pageUrl['option'])
			{

				JRequest::setVar( 'url',  array("option"=>$pageUrl['option'],"view"=>$pageUrl['view'],"layout"=>$pageUrl['layout']));
			}
			else
			{
				JRequest::setVar( 'url',  array());
			}
			$menu_item = new PagesAndItemsModelPage_item(null,true);
			$menu_item_urlparams = $menu_item->getUrlParams();

					$menu_item_urlparams_option = $menu_item_urlparams->get('option',null);
					$menu_item_urlparams_view = $menu_item_urlparams->get('view',null);
					$menu_item_urlparams_layout = $menu_item_urlparams->get('layout',null);
					$menu_item_urlparams_id = $menu_item_urlparams->get('id',null);

			//test for expansion
			//if(!$components[$i]->option == 'com_content'
			//{
				if($components[$i]->legacy)
				{
					<li>
						<div class="node-open">
							<span></span>
							<a href="index.php?option=com_menus&amp;task=edit&amp;type=component&amp;url[option]=
								<?php echo $this->components[$i]->option . $typeCid; ?>"
								id="<?php echo str_replace('com_', '', $this->components[$i]->option); ?>">
								<?php echo $this->components[$i]->name; ?>
							</a>
						</div>
				}
				elseif ($this->expansion['option'] == str_replace('com_', '', $this->components[$i]->option))
				{
					<li <?php echo ($i == $n-1)? 'class="last"' : '' ?>>
						<div class="node-open">
							<span></span>
								<a id="<?php echo str_replace('com_', '', $this->components[$i]->option); ?>"><?php echo JText::_($this->components[$i]->name); ?>
								</a>
							</div>
						<?php echo $this->expansion['html']; ?>
				}
				else
				{
					<li <?php echo ($i == $n-1)? 'class="last"' : '' ?>>
						<div class="node">
							<span></span>
							<a href="index.php?option=com_menus&amp;task=type<?php echo $typeCid; ?>&amp;expand=<?php echo str_replace('com_', '', $this->components[$i]->option); ?>" id="<?php echo str_replace('com_', '', $this->components[$i]->option); ?>"><?php echo JText::_($this->components[$i]->name); ?>
							</a>
						</div>
				}
				</li>
			//}
		}

		/*
		$menu_item = &new MenusModelItem();
		$menu_item->pageType = $pageType;
		$this->menu_item = $menu_item; //->getItem();
		if($sub_task=='new')
		{
			$this->pageMenuItem = $menu_item->getItem();
			//$this->pageMenuItem->pageType = $pageType;
		}
		*/
		/*
		if($sub_task=='new')
		{
			$this->menu_item = $menu_item->getItem();
		}
		*/
	}

}
