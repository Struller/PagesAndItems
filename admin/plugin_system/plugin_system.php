<?php


//-- No direct access
defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

/**
 * @package		PagesAndItems
 * @subpackage	system-plugin for Pages-and-Items (com_pi_pages_and_items)
 */
class plgSystemPagesanditems extends JPlugin
{
	/**
	 * Constructor
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 */
	
	function plgSystemPages_and_items( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		// Do some extra initialisation in this constructor if required
	}

	/**
	 * Do something onAfterRender
	 */
	function onAfterRender()
	{
		$option = JRequest::getVar('option', '', 'get');
		$view = JRequest::getVar('view', '', 'get');
		
		$application = JFactory::getApplication();
		if (!$application->isAdmin()) 
		{
			
			/*
			we are in frontend
			*/
			
			$task = JRequest::getVar('task', '', 'get');
			$layout = JRequest::getVar('layout', '', 'get');			
			$Itemid = JRequest::getVar('Itemid', '', 'get');
			/*
			in Joomla 1.6
			item edit
			com_content
			&view=form
			&layout=edit
			&a_id=22 is the article id
			&Itemid=437 is the menu id
			is an article edit
			
			if we have no a_id in the request is article submitt
			
			
			*/
			
			if($option=='com_content' && ($task=='edit' || $layout =='form' || $view == 'form' || $layout == 'edit')) 
			{
				if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php'))
				{
					require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php');
				}
				else
				{
					//dump('NOT');
					return;
				}
				$config = PagesAndItemsHelper::getConfig();
				
				//get config to check if we need to do a redirect	
				$use_pi_frontend_editting = 0;
				
				if($config['use_pi_frontend_editting']){
					$use_pi_frontend_editting = 1;
				}
				
				//we can use also:
				//$configTest = PagesAndItemsHelper::getConfigAsRegistry();
				//dump($configTest->get('use_pi_frontend_editting',0));
				//only redirect if in pi-config use_pi_frontend_editting is activated
				if($use_pi_frontend_editting)
				{
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
					$menu_id = JRequest::getVar('Itemid', '', 'get');	
					
					//redirect item new
					if($option=='com_content' && (($view=='article' && $layout=='form') || ($view=='form' && $layout=='edit' && !JRequest::getVar('a_id', null, 'get')) ) ) 
					{
						//echo 'new item';
						$sub_task = 'new';
					}
		
					//redirect item edit
					if($option=='com_content' && (($view=='article' && $task=='edit') || ($view=='form' && $layout=='edit' && JRequest::getVar('a_id', null, 'get')) ))
					{
						//TODO add the parameter to the pi config
						//pi_frontend = 2 is only edit pi items
						/*
						if($this->params->get('pi_frontend', 2) == 2)
						{
							$database->setQuery("SELECT * FROM #__pi_item_index WHERE item_id='$item_id'");
							$item_row = $database->loadObject();
							if(!$item_row)
							{
								return;
							}
						}
						*/
						
						//echo 'item edit';
						$sub_task = 'edit';
						
												
					}
					
					$referer = '';						
					if($config['item_save_redirect']=='current'){							
						//$referer = '&ret='.JRequest::getString('return',  base64_encode(JURI::base()), 'get');		
						$referer = '&return='.JRequest::getString('return',  '', 'get');
					}
					$layout_url = '';
					if($layout=='form'){
						$layout_url = '&layout=form';
					}
					
					$url = 'index.php?option=com_pagesanditems&view=item'.$layout_url.'&sub_task='.$sub_task.'&item_id='.$item_id.'&itemId='.$item_id.'&Itemid='.$Itemid.'&pageId='.$menu_id.$referer;
					$application->redirect($url);
					
				}
			}
			
			return;
		}else{
			//backend
			
			
			$option = JRequest::getVar('option', '', 'get');
			$sub_task = JRequest::getCmd('sub_task', '');
			$pageType = JRequest::getCmd('pageType', '');
			
			//replace disfunctional language key
			if($option=='com_pagesanditems' && $view=='page'){	
				$this->display_status_label();		
			}
			
			//add option to create a new category when a new cat-blog page is created
			if($option=='com_pagesanditems' && $view=='page' && $sub_task=='new' && $pageType=='content_category_blog'){				
				$this->display_new_category_option();					
			}
			
			
			//end backend
		}
		
		//dump($this->params);						
		
		//if(!$this->params->get('hidde_button', 1) && !$this->params->get('disable_content', 1) && !$this->params->get('add_button', 1))
		//{
			return;
		//}
		
		//$option = JRequest::getCmd('option', null);
		$task = JRequest::getCmd('task');
		
		//$view = JRequest::getCmd('view');
		$layout = JRequest::getCmd('layout');
		//view=article
		//layout=edit
		
		//&id=73
		
		if( $option == 'com_content' && ($task == 'edit' || ($view == 'article' && $layout == 'edit') ) )
		{
			$id = null;
			$ids = JRequest::getVar('cid',null);
			if(isset($ids) && $task == 'edit')
			{
				$id = $ids[0];
			}
			else
			{
				$id = JRequest::getVar('id',null);
			}
			//dump($id);
			$item_type = false;
			
			if($id)
			{
				$database = JFactory::getDBO();	
				$database->setQuery("SELECT itemtype FROM #__pi_item_index WHERE item_id='$id' LIMIT 1");
				$rows = $database->loadObjectList();
			
				if($rows)
				{
					$itemrow = $rows[0];
					$item_type = $itemrow->itemtype;
				}
			}
			//dump($item_type);
			//only if item has been made with PI
			if($item_type!='')
			{
				//$this->loadLanguage('', JPATH_ADMINISTRATOR);
				$lang = &JFactory::getLanguage();
				$lang->load('com_pagesanditems', JPATH_ADMINISTRATOR, null, false, false);
				
				$body = JResponse::getBody();
				$nl = "\n";
				$script_add = '';
				$style = '';
				$version = new JVersion();
				$joomlaVersion = $version->getShortVersion();

				if($this->params->get('add_button', 1))
				{
					$style .= '<style type="text/css">'.$nl;
					$style .= '.icon-32-PI';
					$style .= '{';
					//TODO ADD path and JURI
					$dirIcons = JURI::root(true).'/'.str_replace(DS,'/',str_replace(JPATH_ROOT.DS,'',realpath(dirname(__FILE__).DS.'..'.DS.'media').DS.'images'.DS.'icons'));
					$style .= 'background-image:url("'.$dirIcons.'/icon-32-pi.png");';
					$style .= '}'.$nl;
					$style .= '</style>'.$nl;
					$database = JFactory::getDBO();	
					
					$database->setQuery("SELECT * FROM #__content WHERE id='$id' LIMIT 1");
					$contentrow = $database->loadObject();
					if($contentrow)
					{
						//at this moment in PI we have one menutype/page = index.php?option=com_content&view=category&layout=blog
						/*
						view = section layout=blog
						view = archive
						and
						view = article
						are not init
						
						*/
						/*
						
						we need pageId or pageType if not found pageType we set pageType to content_article?
						pageId='.$contentrow->catid ???
						IDEA
						set in #__pi_item_index an field pageType for multiple values eg. an single value pageId=444;pageType=content_article
						an multiple value pageId=444;pageType=content_article\npageId=5124;pageType=content_category_blog
						or as json string (object/array)
						and have we an multiple let the user select
						
						
						*/
						/*
						can we make it easier?
						like
						
						JToolBarHelper::custom('manage.enable', 'publish.png', 'publish_f2.png', 'COM_PAGESANDITEMS_ENABLE', true);
						
						
						public function prependButton()
	{
		// Insert button into the front of the toolbar array.
		$btn = func_get_args();
		array_unshift($this->_bar, $btn);
		return true;
	}
						
						public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
						{

						}
						
						*/


						//$link = 'index.php?option=com_pi_pages_and_items&view=item&sub_task=edit&pageId='.$contentrow->catid.'&itemId='.$id.'&pageType=content_article';
						$link = 'index.php?option=com_pagesanditems&view=item&sub_task=edit&pageId='.$contentrow->catid.'&itemId='.$id.'&pageType=content_article';
						$bar = JToolBar::getInstance('toolbar');
						$icon = 'icon-32-PI';
						// Strip extension.
						$icon = preg_replace('#\.[^.]*$#', '', $icon);
						$alt = JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_OPEN_IN_PI');
						// Add a standard button.
						
						$script_add = '';
						//$script_add .= 
						$bar->appendButton('Standard', $icon, $alt, $link, false);
						//dump($bar);
						
						foreach($bar->getItems() as $item)
						{
							if(in_array('icon-32-PI',$item))
							{
								//dump($item);
								//dump($bar->renderButton($item));
								/*
								$script_add .= 'var elementToolbar = $(\'toolbar\');' .$nl;
								$script_add .= 'var elementToolbarChild = $(\'toolbar\').getElement(\'ul\');' .$nl;
								$script_add .= 'var childInnerHtml = elementToolbarChild.innerHTML;' .$nl;
								//$script_add .= 'elementToolbarChild.innerHTML = \''.addslashes($bar->renderButton($item)).'\' + childInnerHtml;';
								$script_add .= 'elementToolbarChild.innerHTML = \'<li class="button">';
								$script_add .= '<a href="#" ';
								
								$script_add .= 'onclick="';
								$script_add .= 'javascript:';
								$script_add .= 'Joomla';
								$script_add .= '.submitbutton';
								$script_add .= '(\'';
								$script_add .= 'index.php';
								//?option=com_pi_pages_and_items&view=item&sub_task=edit&pageId=0&itemId=73&pageType=content_article\')" 
								$script_add .= '\'';
								$script_add .= ');';
								$script_add .= '" ';
								
								
								$script_add .= 'class="toolbar" ';
								$script_add .= '>';
								
								//<span class="icon-32-icon-32-PI">
								//</span>
								//	open the Article in Pages and Items
								
								$script_add .= '</a>';
								
								$script_add .= '</li>\';
								// + childInnerHtml;' .$nl;
								*/
								//
								//$script_add .= $bar->renderButton($item);
							}
						
						}

						//renderButton(&$node)
						
						if($joomlaVersion < '1.6')
						{
						$script_add .= 'var elementTD = new Element(\'td\');' .$nl;
						$script_add .= 'elementTD.setProperty(\'class\',\'button\');' .$nl;
						$script_add .= 'elementTD.setProperty(\'id\',\'PI_'.$contentrow->catid.'\');' .$nl;
						$script_add .= 'var elementA = new Element(\'a\');' .$nl;
						$script_add .= 'elementA.setProperty(\'class\',\'toolbar\');' .$nl;
									
						//$script_add .= 'elementA.setText(\''.JText::_('edit').'\');' .$nl;
						$script_add .= 'elementA.innerHTML = \''.JText::_('edit').'\';' .$nl;
						
						
						$script_add .= 'elementA.setProperty(\'href\',\''.$link.'\');' .$nl;
						$script_add .= 'elementA.injectInside(elementTD);' .$nl;
									
						$script_add .= 'var elementSpan = new Element(\'span\');' .$nl;
						$script_add .= 'elementSpan.setProperty(\'class\',\'icon-32-PI\');' .$nl;
						
						$script_add .= 'elementSpan.setProperty(\'Title\',\''.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_OPEN_IN_PI').'\');' .$nl;
						
						$script_add .= 'elementSpan.injectTop(elementA);' .$nl;
						
						$script_add .= 'var elementApply = $(\'toolbar-apply\');' .$nl;
						$script_add .= 'elementTD.injectAfter(elementApply);' .$nl;
						}
						else
						{
						$script_add .= 'var elementTD = new Element(\'li\');' .$nl;
						$script_add .= 'elementTD.setProperty(\'class\',\'button\');' .$nl;
						$script_add .= 'elementTD.setProperty(\'id\',\'PI_'.$contentrow->catid.'\');' .$nl;
						$script_add .= 'var elementA = new Element(\'a\');' .$nl;
						$script_add .= 'elementA.setProperty(\'class\',\'toolbar\');' .$nl;
									
						//$script_add .= 'elementA.setText(\''.JText::_('edit').'\');' .$nl;
						$script_add .= 'elementA.innerHTML = \''.JText::_('edit').'\';' .$nl;
						
						
						$script_add .= 'elementA.setProperty(\'href\',\''.$link.'\');' .$nl;
						$script_add .= 'elementA.injectInside(elementTD);' .$nl;
									
						$script_add .= 'var elementSpan = new Element(\'span\');' .$nl;
						$script_add .= 'elementSpan.setProperty(\'class\',\'icon-32-PI\');' .$nl;
						
						$script_add .= 'elementSpan.setProperty(\'Title\',\''.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_OPEN_IN_PI').'\');' .$nl;
						
						$script_add .= 'elementSpan.injectTop(elementA);' .$nl;
						
						$script_add .= 'var elementCancel = $(\'toolbar-cancel\');' .$nl;
						$script_add .= 'elementTD.injectBefore(elementCancel);' .$nl;
						}
					}
				}

				
				$script_remove = '';
				//delete save and apply Button ?
				if($this->params->get('hidde_button', 1))
				{
					//$script_remove .= '$(\'toolbar-save\').style="display:none;";';//remove();';
					//$script_remove .= '$(\'toolbar-apply\').style="display:none;";';//remove();';

					if($joomlaVersion < '1.6')
					{
						$script_remove .= '$(\'toolbar-save\').remove();';
						$script_remove .= '$(\'toolbar-apply\').remove();';
					}
					else
					{
						$script_remove .= '$(\'toolbar-save\').destroy();';
						$script_remove .= '$(\'toolbar-apply\').destroy();';
						$script_remove .= '$(\'toolbar-save-new\').destroy();';
						$script_remove .= '$(\'toolbar-save-copy\').destroy();';
					}
				}
				
				// create the javascript
				// We can't use $document, because it's already rendered

				$script_onload = '';
				$script_disable = '';
				if($this->params->get('disable_content', 1))
				{
					if($joomlaVersion < '1.6')
					{
						$script_disable .= '$(\'catid\').disabled="disabled";' .$nl;
						$script_disable .= '$(\'sectionid\').disabled="disabled";' .$nl;
						$script_disable .= 'var elementText = $(\'text\').clone();' .$nl;
						$script_disable .= 'var elementParent = $(\'text\').getParent();' .$nl;
						$script_disable .= 'var elements = elementParent.getChildren();' .$nl;
						$script_disable .= 'elements.each(function(element,index) ' .$nl;
						$script_disable .= '{' .$nl;
						$script_disable .= 'element.remove()'.$nl;
						$script_disable .= '});' .$nl;
						$script_disable .= 'elementText.removeProperty(\'class\');' .$nl;
						$script_disable .= 'elementText.setStyle(\'display\',\'none\');' .$nl;
						$script_disable .= 'elementText.setProperty(\'readOnly\',\'true\');' .$nl;
						$script_disable .= 'elementText.injectInside(elementParent);' .$nl;
						$script_disable .= 'var elementDivT = new Element(\'div\');' .$nl;
					
						$script_disable .= 'elementDivT.setHTML(\'<strong>'.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_ONLY_DISPLAY').'</strong>\');' .$nl;
					
						$script_disable .= 'elementDivT.setStyles({margin:\'8px 0 15px\'});' .$nl;
				
						$script_disable .= 'elementDivT.injectInside(elementParent);' .$nl;
				
						$script_disable .= 'var elementDiv = new Element(\'div\');' .$nl;
						$script_disable .= 'elementDiv.setStyle(\'width\',elementText.getStyle(\'width\'));' .$nl;
						$script_disable .= 'elementDiv.setStyle(\'height\',elementText.getStyle(\'height\'));' .$nl;
						$script_disable .= 'elementDiv.setStyles({border:\'1px solid #D5D5D5\',margin:\'8px 0 15px\'});' .$nl;
						$script_disable .= 'elementDiv.setHTML(elementText.getText());' .$nl;
						$script_disable .= 'elementDiv.injectInside(elementParent);' .$nl;
					}
					else
					{
						$script_disable .= 'var elementItemForm = $(\'item-form\');' .$nl;
						$script_disable .= 'var elementItemFormLegend = elementItemForm.getElement(\'legend\');' .$nl;
						$script_disable .= 'elementItemFormLegend.set(\'html\',\''.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_ONLY_DISPLAY').'\');' .$nl;
						$script_disable .= 'var elementItemFormFieldset = elementItemForm.getElement(\'fieldset\');' .$nl;
						//$script_disable .= 'elementItemForm.setStyle(\'display\',\'none\');';
						
						//$script_disable .= 'var text= $(\'jform_articletext\').clone();' .$nl;
						//$script_disable .= 'alert($(\'jform_articletext\'));' .$nl;
						
						$script_onload .= 'var elementItemForm = $(\'item-form\');' .$nl;
						$script_onload .= 'var articletext_parent = $(\'jform_articletext_parent\');' .$nl;
						$script_onload .= 'var elementText = $(\'jform_articletext\');' .$nl;
						$script_onload .= 'var elementDiv = new Element(\'div\');' .$nl;
						$script_onload .= 'elementDiv.setStyle(\'width\',elementText.getStyle(\'width\'));' .$nl;
						$script_onload .= 'elementDiv.setStyle(\'height\',elementText.getStyle(\'height\'));' .$nl;
						$script_onload .= 'elementDiv.setStyles({border:\'1px solid #D5D5D5\',margin:\'8px 0 15px\'});' .$nl;
						$script_onload .= 'elementDiv.set(\'html\',elementText.get(\'html\'));' .$nl;
						$script_onload .= 'elementDiv.injectBefore(articletext_parent);' .$nl;
						$script_onload .= 'articletext_parent.setStyle(\'display\',\'none\');' .$nl;
						$script_onload .= 'var editor_xtd_buttons = $(\'editor-xtd-buttons\');' .$nl;
						$script_onload .= 'if(editor_xtd_buttons)' .$nl;
						$script_onload .= '{' .$nl;
						$script_onload .= '	editor_xtd_buttons.setStyle(\'display\',\'none\');' .$nl;
						$script_onload .= '}' .$nl;
						$script_onload .= 'var toogleButton = elementItemForm.getElement(\'div[class=toggle-editor]\');' .$nl;
						$script_onload .= 'if(toogleButton)' .$nl;
						$script_onload .= '{' .$nl;
						$script_onload .= '	toogleButton.setStyle(\'display\',\'none\');' .$nl;
						$script_onload .= '}' .$nl;
						
						//toggle-editor
						
						$script_disable .= 'var elements = elementItemForm.getElements(\'*\');' .$nl;
						//getChildren();' .$nl;
						
						$script_disable .= 'elements.each(function(element,index) ' .$nl;
						$script_disable .= '{' .$nl;
							//$script_disable .= 'alert(index);' .$nl;
							$script_disable .= 'element.setProperty(\'readonly\',\'readonly\');' .$nl;
							$script_disable .= 'element.setProperty(\'disabled\',\'disabled\');' .$nl;
							$script_disable .= 'element.set(\'disabled\',\'disabled\');' .$nl;
							$script_disable .= 'element.set(\'disabled\',\'true\');' .$nl;
						$script_disable .= '});' .$nl;
						
						/*
						//$script_disable .= 'alert($(\'jform_articletext_parent\'));' .$nl;
						//item-form
						//item-form
						//$script_disable .= '$(\'jform_catid\').disabled="disabled";' .$nl;
						
						//$script_disable .= 'var elementText = $(\'jform_articletext\').clone();' .$nl;
						//$script_disable .= 'var elementParent = $(\'jform_articletext_parent\');' .$nl;
						//$script_disable .= 'var elements = elementParent.getChildren();' .$nl;
						/*
						$script_disable .= 'elements.each(function(element,index) ' .$nl;
						$script_disable .= '{' .$nl;
						$script_disable .= 'element.remove()'.$nl;
						$script_disable .= '});' .$nl;
						$script_disable .= 'elementText.removeProperty(\'class\');' .$nl;
						$script_disable .= 'elementText.setStyle(\'display\',\'none\');' .$nl;
						$script_disable .= 'elementText.setProperty(\'readOnly\',\'true\');' .$nl;
						$script_disable .= 'elementText.injectInside(elementParent);' .$nl;
						$script_disable .= 'var elementDivT = new Element(\'div\');' .$nl;
					
						$script_disable .= 'elementDivT.setHTML(\'<strong>'.JText::_('COM_PAGESANDITEMS_PLG_SYSTEM_PAGES_AND_ITEMS_ONLY_DISPLAY').'</strong>\');' .$nl;
					
						$script_disable .= 'elementDivT.setStyles({margin:\'8px 0 15px\'});' .$nl;
				
						$script_disable .= 'elementDivT.injectInside(elementParent);' .$nl;
				
						$script_disable .= 'var elementDiv = new Element(\'div\');' .$nl;
						$script_disable .= 'elementDiv.setStyle(\'width\',elementText.getStyle(\'width\'));' .$nl;
						$script_disable .= 'elementDiv.setStyle(\'height\',elementText.getStyle(\'height\'));' .$nl;
						$script_disable .= 'elementDiv.setStyles({border:\'1px solid #D5D5D5\',margin:\'8px 0 15px\'});' .$nl;
						$script_disable .= 'elementDiv.setHTML(elementText.getText());' .$nl;
						$script_disable .= 'elementDiv.injectInside(elementParent);' .$nl;
						*/

					}
					/*
					jform_catid
					*/

				}
				$script = $nl. '<!-- system plugin Pages and items -->' .$nl.$style.
				'<script type="text/javascript">' .$nl.
				'// <!--' .$nl.
				'window.addEvent(\'domready\', function()' .$nl.
				'{' .$nl.
					$script_add.
					$script_remove.
					$script_disable.
				'});' .$nl.
				
				'window.addEvent(\'load\', function()' .$nl.
				'{' .$nl.
					$script_onload.
				'});' .$nl.
				'// -->' .$nl.
				'</script>' .$nl.
				'<!-- / system plugin Pages and items -->';
				// add the code to the header (thanks jenscski)
				$body = str_replace('</head>', $script.'</head>', $body);
				JResponse::setBody($body);
			}
		}
		return;
	}
	
	function display_new_category_option(){
		/*
		ms: i have move this to models/page.php
		line 3219.. and line 3263..
		
		
		$buffer = JResponse::getBody();	
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'simple_html_dom.php');
		$html_pi = new simple_html_dom();
		//$html_pi->load($buffer);

		$temp = $html_pi->find('[id=jform_request_id-lbl]', 0);
		$category_label = $temp->outertext;
		//take required out and make it label for the radio button
		$category_label2 = str_replace('<span class="star">&#160;*</span>', '', $category_label);
		$category_label2 = str_replace('jform_request_id', 'create_new_category_0', $category_label2);
		
		$temp = $html_pi->find('[id=jform_request_id]', 0);
		$category_select = $temp->outertext;
		
		$html = '<table>';
			$html .= '<tr>';
				$html .= '<td>';
				$html .= '<input type="radio" name="create_new_category" value="0" id="create_new_category_0" />';
				$html .= '</td>';
				$html .= '<td>';
				$html .= $category_label2;
				$html .= '</td>';
				$html .= '<td>';
				$html .= $category_select;
				$html .= '</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td>';
				//the checked part will be configurable in the pagetype config
				$html .= '<input type="radio" name="create_new_category" value="1" id="create_new_category_1" checked="checked" />';
				$html .= '</td>';
				$html .= '<td colspan="2">';
				$html .= '<label class="hasTip" for="create_new_category_1" title="';
				$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
				$html .= '::';
				$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY_TIP');
				$html .= '">';
				$html .= JText::_('COM_PAGESANDITEMS_CREATE_NEW_CATEGORY');
				$html .= '</label>';
				$html .= '</td>';
			$html .= '</tr>';
		$html .= '</table>';
		
		//take label out
		$buffer = str_replace($category_label, '', $buffer);
		$buffer = str_replace($category_select, $html, $buffer);	
		
		JResponse::setBody($buffer);
		*/
	}
	
	function display_status_label(){
		/*
		ms: i have move this to models/page.php
		line 3172 but see the comment in models/page.php before line 3172
		*/
		/*
		$buffer = JResponse::getBody();
		$buffer = str_replace('JGLOBAL_STATE' , JText::_('JSTATUS'), $buffer); //???
		//$buffer = str_replace('JSTATUS' , JText::_('JGLOBAL_STATE'), $buffer);
		JResponse::setBody($buffer);
		*/
	}
	
	/*
	function onContentAfterSave($context, &$article, $isNew){
		
		$article_id = $article->id;
		
		$database = JFactory::getDBO();
		$database->setQuery( "UPDATE #__pi_config SET config='$article_id' WHERE id='test' ");
		$database->query();
		
		return true;
	}
	*/
	
}
