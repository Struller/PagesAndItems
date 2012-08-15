<?php
/**
* @version		2.1.6
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');

/**
controller.php
*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');
/**
 * @package		PagesAndItems
*/

class PagesAndItemsControllerConfig extends PagesAndItemsController
{
	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct($default);
	}

	function config_itemtype()
	{
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$option = JRequest::getVar('option');
		$itemtype = JRequest::getVar( 'sub_task', '');
		
		$app->setUserState( $option.'.refer','&view=config');
		$query = 'SELECT *'
			. ' FROM #__pi_extensions '
			. " WHERE element ='".$itemtype."' "
			. " AND type = 'itemtype' ";
		$db->setQuery($query);
		$row = $db->loadObject();
		$id = 0;
		$msg = '';
		if($row)
		{
			$id = $row->extension_id;
			$url = 'index.php?option=com_pagesanditems&task=extension.doExecute&extensionName=extensions&extensionType=manager&layout=edit&extensionTask=display&view=piextension&client=both&sub_task=edit&cid[]='.$id.'&extension_id='.$id;
		}
		else
		{
			$msg = JText::_('COM_PAGESANDITEMS_ITEMTYPENOTINSTALLED');
			$url = 'index.php?option=com_pagesanditems&view=config';
		}
		$this->setRedirect(JRoute::_($url, false),$msg);
	}


	function config_save()
	{

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		//here we need the model base for future config?
		//$model = &$this->getModel('Base','PagesAndItemsModel');
		
		$config = PagesAndItemsHelper::getConfig();
		$db = JFactory::getDBO();
		//get plugin_syntax_cheatcheat
		$plugin_syntax_cheatcheat = JRequest::getVar('plugin_syntax_cheatcheat','','post','string', JREQUEST_ALLOWRAW);
		//clean lines
		
		
		$plugin_syntax_cheatcheat = str_replace("\n",'[newline]',$plugin_syntax_cheatcheat);
		$plugin_syntax_cheatcheat = str_replace('=','[equal]',$plugin_syntax_cheatcheat);
		$plugin_syntax_cheatcheat = addslashes($plugin_syntax_cheatcheat);

		//get custom redirect url
		$item_save_redirect_url = JRequest::getVar('item_save_redirect_url','','post','string', JREQUEST_ALLOWRAW);
		//clean lines
		$item_save_redirect_url = str_replace('=','[equal]',$item_save_redirect_url);

		//menus
		$menus = JRequest::getVar('menus',array(0));
		
		//if menutype is not selected, take it out of array
		//added the 'm' because of the problem with numerical indexes when unsetting in loop
		$loops = count($menus);
		for($n = 0; $n <= $loops; $n++)
		{
			if(!isset($menus['m'.$n]['menutype']))
			{
				unset ($menus['m'.$n]);
			}
		}
		//sort array by order
		foreach ($menus as $key => $row) {
			$order[$key] = (int)$row['order'];
		}
		if(count($menus)!=0)
		{
			array_multisort($order, SORT_ASC, $menus);
		}
		
		//write menus array to config string
		$menu_string = '';
		if (is_array($menus)){
			$counter = 0;
			foreach($menus as $menu){
				if($counter!=0)
				{
					$menu_string .= ',';
				}
				$menu_string .= $menu['menutype'].';'.$menu['title']; //.';'.$menu['id'];
				$counter = $counter+1;
			}
		}
		


		$config = 'use_pi_frontend_editting='.JRequest::getVar('use_pi_frontend_editting', 'false').'
useCheckedOut='.JRequest::getVar('useCheckedOut', '', 'post').'
enabled_view_category='.JRequest::getVar('enabled_view_category', '', 'post').'
showSlider='.JRequest::getVar('showSlider', -1, 'post').'
plugin_system_add_button='.JRequest::getVar('plugin_system_add_button', 'false').'
plugin_system_hidde_button='.JRequest::getVar('plugin_system_hidde_button', 'false').'
menus='.$menu_string.'
cit=1
item_show_frontpage_option='.JRequest::getVar('item_show_frontpage_option', 'false').'
plugin_syntax_cheatcheat='.$plugin_syntax_cheatcheat.'
make_article_alias_unique='.JRequest::getVar('make_article_alias_unique', 'false').'
item_save_redirect='.JRequest::getVar('item_save_redirect', 'category_blog').'
item_save_redirect_url='.$item_save_redirect_url.'
item_props_hideforsuperadmin='.JRequest::getVar('item_props_hideforsuperadmin', 'false').'
item_props_details='.JRequest::getVar('item_props_details', 'false').'
item_props_title='.JRequest::getVar('item_props_title', 'false').'
item_props_alias='.JRequest::getVar('item_props_alias', 'false').'
item_props_category='.JRequest::getVar('item_props_category', 'false').'
item_props_status='.JRequest::getVar('item_props_status', 'false').'
item_props_access='.JRequest::getVar('item_props_access', 'false').'
item_props_featured='.JRequest::getVar('item_props_featured', 'false').'
item_props_language='.JRequest::getVar('item_props_language', 'false').'
item_props_id='.JRequest::getVar('item_props_id', 'false').'
item_props_articletext='.JRequest::getVar('item_props_articletext', 'false').'
item_props_publishingoptions='.JRequest::getVar('item_props_publishingoptions', 'false').'
item_props_createdby='.JRequest::getVar('item_props_createdby', 'false').'
item_props_createdbyalias='.JRequest::getVar('item_props_createdbyalias', 'false').'
item_props_createddate='.JRequest::getVar('item_props_createddate', 'false').'
item_props_start='.JRequest::getVar('item_props_start', 'false').'
item_props_finish='.JRequest::getVar('item_props_finish', 'false').'
item_props_modified_by='.JRequest::getVar('item_props_modified_by', 'false').'
item_props_modified='.JRequest::getVar('item_props_modified', 'false').'
item_props_revision='.JRequest::getVar('item_props_revision', 'false').'
item_props_hits='.JRequest::getVar('item_props_hits', 'false').'
item_props_articleoptions='.JRequest::getVar('item_props_articleoptions', 'false').'
item_props_show_title='.JRequest::getVar('item_props_show_title', 'false').'
item_props_link_titles='.JRequest::getVar('item_props_link_titles', 'false').'
item_props_show_intro='.JRequest::getVar('item_props_show_intro', 'false').'
item_props_show_category='.JRequest::getVar('item_props_show_category', 'false').'
item_props_link_category='.JRequest::getVar('item_props_link_category', 'false').'
item_props_show_parent_category='.JRequest::getVar('item_props_show_parent_category', 'false').'
item_props_link_parent_category='.JRequest::getVar('item_props_link_parent_category', 'false').'
item_props_show_author='.JRequest::getVar('item_props_show_author', 'false').'
item_props_link_author='.JRequest::getVar('item_props_link_author', 'false').'
item_props_show_create_date='.JRequest::getVar('item_props_show_create_date', 'false').'
item_props_show_modify_date='.JRequest::getVar('item_props_show_modify_date', 'false').'
item_props_show_publish_date='.JRequest::getVar('item_props_show_publish_date', 'false').'
item_props_show_item_navigation='.JRequest::getVar('item_props_show_item_navigation', 'false').'
item_props_show_icons='.JRequest::getVar('item_props_show_icons', 'false').'
item_props_show_print_icon='.JRequest::getVar('item_props_show_print_icon', 'false').'
item_props_show_email_icon='.JRequest::getVar('item_props_show_email_icon', 'false').'
item_props_show_vote='.JRequest::getVar('item_props_show_vote', 'false').'
item_props_show_hits='.JRequest::getVar('item_props_show_hits', 'false').'
item_props_show_noauth='.JRequest::getVar('item_props_show_noauth', 'false').'
item_props_alternative_readmore='.JRequest::getVar('item_props_alternative_readmore', 'false').'
item_props_article_layout='.JRequest::getVar('item_props_article_layout', 'false').'
item_props_metadataoptions='.JRequest::getVar('item_props_metadataoptions', 'false').'
item_props_desc='.JRequest::getVar('item_props_desc', 'false').'
item_props_keywords='.JRequest::getVar('item_props_keywords', 'false').'
item_props_robots='.JRequest::getVar('item_props_robots', 'false').'
item_props_author='.JRequest::getVar('item_props_author', 'false').'
item_props_rights='.JRequest::getVar('item_props_rights', 'false').'
item_props_xreference='.JRequest::getVar('item_props_xreference', 'false').'
item_props_pioptions='.JRequest::getVar('item_props_pioptions', 'false').'
item_props_instance='.JRequest::getVar('item_props_instance', 'false').'
item_props_pishowtitle='.JRequest::getVar('item_props_pishowtitle', 'false').'
item_props_permissions='.JRequest::getVar('item_props_permissions', 'false').'
item_new_show_title='.JRequest::getVar('item_new_show_title', 'false').'
item_type_select_frontend='.JRequest::getVar('item_type_select_frontend', 'false').'
version_checker='.JRequest::getVar('version_checker', '', 'post').'
page_trash_cat='.JRequest::getVar('page_trash_cat', 'false').'
page_trash_items='.JRequest::getVar('page_trash_items', 'false').'
page_delete_cat='.JRequest::getVar('page_delete_cat', 'false').'
page_delete_items='.JRequest::getVar('page_delete_items', 'false').'
make_page_alias_unique='.JRequest::getVar('make_page_alias_unique', 'false').'
truncate_item_title='.intval(JRequest::getVar('truncate_item_title', 0)).'
page_props_hideforsuperadmin='.JRequest::getVar('page_props_hideforsuperadmin', 'false').'
page_props_id='.JRequest::getVar('page_props_id', 'false').'
page_props_type='.JRequest::getVar('page_props_type', 'false').'
page_props_title='.JRequest::getVar('page_props_title', 'false').'
page_props_alias='.JRequest::getVar('page_props_alias', 'false').'
page_props_note='.JRequest::getVar('page_props_note', 'false').'
page_props_link='.JRequest::getVar('page_props_link', 'false').'
page_props_published='.JRequest::getVar('page_props_published', 'false').'
page_props_access='.JRequest::getVar('page_props_access', 'false').'
page_props_menutype='.JRequest::getVar('page_props_menutype', 'false').'
page_props_parent_id='.JRequest::getVar('page_props_parent_id', 'false').'
page_props_browserNav='.JRequest::getVar('page_props_browserNav', 'false').'
page_props_home='.JRequest::getVar('page_props_home', 'false').'
page_props_language='.JRequest::getVar('page_props_language', 'false').'
page_props_template_style_id='.JRequest::getVar('page_props_template_style_id', 'false').'
page_props_linktype_options='.JRequest::getVar('page_props_linktype_options', 'false').'
page_props_link_title_attri='.JRequest::getVar('page_props_link_title_attri', 'false').'
page_props_link_css='.JRequest::getVar('page_props_link_css', 'false').'
page_props_link_image='.JRequest::getVar('page_props_link_image', 'false').'
page_props_add_title='.JRequest::getVar('page_props_add_title', 'false').'
page_props_metadata_options='.JRequest::getVar('page_props_metadata_options', 'false').'
page_props_meta_desc='.JRequest::getVar('page_props_meta_desc', 'false').'
page_props_meta_keys='.JRequest::getVar('page_props_meta_keys', 'false').'
page_props_robots='.JRequest::getVar('page_props_robots', 'false').'
page_props_secure='.JRequest::getVar('page_props_secure', 'false').'
page_props_page_display_options='.JRequest::getVar('page_props_page_display_options', 'false').'
page_props_browser_page='.JRequest::getVar('page_props_browser_page', 'false').'
page_props_show_page_heading='.JRequest::getVar('page_props_show_page_heading', 'false').'
page_props_page_heading='.JRequest::getVar('page_props_page_heading', 'false').'
page_props_page_class='.JRequest::getVar('page_props_page_class', 'false').'
page_props_modules='.JRequest::getVar('page_props_modules', 'false').'
page_props_required_settings='.JRequest::getVar('page_props_required_settings', 'false').'
page_props_category_options='.JRequest::getVar('page_props_category_options', 'false').'
page_props_cat_title='.JRequest::getVar('page_props_cat_title', 'false').'
page_props_cat_desc='.JRequest::getVar('page_props_cat_desc', 'false').'
page_props_cat_img='.JRequest::getVar('page_props_cat_img', 'false').'
page_props_cat_levels='.JRequest::getVar('page_props_cat_levels', 'false').'
page_props_cat_empty='.JRequest::getVar('page_props_cat_empty', 'false').'
page_props_cat_no_art_mess='.JRequest::getVar('page_props_cat_no_art_mess', 'false').'
page_props_cat_subcat_desc='.JRequest::getVar('page_props_cat_subcat_desc', 'false').'
page_props_cat_artincat='.JRequest::getVar('page_props_cat_artincat', 'false').'
page_props_cat_subheading='.JRequest::getVar('page_props_cat_subheading', 'false').'
page_props_blog_options='.JRequest::getVar('page_props_blog_options', 'false').'
page_props_blog_leading='.JRequest::getVar('page_props_blog_leading', 'false').'
page_props_blog_intro='.JRequest::getVar('page_props_blog_intro', 'false').'
page_props_blog_cols='.JRequest::getVar('page_props_blog_cols', 'false').'
page_props_blog_links='.JRequest::getVar('page_props_blog_links', 'false').'
page_props_blog_multicolorder='.JRequest::getVar('page_props_blog_multicolorder', 'false').'
page_props_blog_incsubcat='.JRequest::getVar('page_props_blog_incsubcat', 'false').'
page_props_blog_catorder='.JRequest::getVar('page_props_blog_catorderv', 'false').'
page_props_blog_artorder='.JRequest::getVar('page_props_blog_artorder', 'false').'
page_props_blog_dateorder='.JRequest::getVar('page_props_blog_dateorder', 'false').'
page_props_blog_pagination='.JRequest::getVar('page_props_blog_pagination', 'false').'
page_props_blog_results='.JRequest::getVar('page_props_blog_results', 'false').'
page_props_article_options='.JRequest::getVar('page_props_article_options', 'false').'
page_props_art_title='.JRequest::getVar('page_props_art_title', 'false').'
page_props_art_linkedtitles='.JRequest::getVar('page_props_art_linkedtitles', 'false').'
page_props_art_introtext='.JRequest::getVar('page_props_art_introtext', 'false').'
page_props_art_cat='.JRequest::getVar('page_props_art_cat', 'false').'
page_props_art_catlink='.JRequest::getVar('page_props_art_catlink', 'false').'
page_props_art_parent='.JRequest::getVar('page_props_art_parent', 'false').'
page_props_art_parentlink='.JRequest::getVar('page_props_art_parentlink', 'false').'
page_props_art_author='.JRequest::getVar('page_props_art_author', 'false').'
page_props_art_authorlink='.JRequest::getVar('page_props_art_authorlink', 'false').'
page_props_art_create='.JRequest::getVar('page_props_art_create', 'false').'
page_props_art_modify='.JRequest::getVar('page_props_art_modify', 'false').'
page_props_art_pub='.JRequest::getVar('page_props_art_pub', 'false').'
page_props_art_nav='.JRequest::getVar('page_props_art_nav', 'false').'
page_props_art_vote='.JRequest::getVar('page_props_art_vote', 'false').'
page_props_art_read='.JRequest::getVar('page_props_art_read', 'false').'
page_props_art_readtitle='.JRequest::getVar('page_props_art_readtitle', 'false').'
page_props_art_icons='.JRequest::getVar('page_props_art_icons', 'false').'
page_props_art_print='.JRequest::getVar('page_props_art_print', 'false').'
page_props_art_email='.JRequest::getVar('page_props_art_email', 'false').'
page_props_art_hits='.JRequest::getVar('page_props_art_hits', 'false').'
page_props_art_unauthorised='.JRequest::getVar('page_props_art_unauthorised', 'false').'
page_props_integration_options='.JRequest::getVar('page_props_integration_options', 'false').'
page_props_int_feed='.JRequest::getVar('page_props_int_feed', 'false').'
page_props_int_each='.JRequest::getVar('page_props_int_each', 'false').'
page_new_publish_category='.JRequest::getVar('page_new_publish_category', 'false').'
page_new_publish_menu='.JRequest::getVar('page_new_publish_menu', 'false').'
page_new_access_menu='.JRequest::getVar('page_new_access_menu', '0').'
page_new_access_category='.JRequest::getVar('page_new_access_category', '0').'
';
//multigroup_access_requirement='.JRequest::getVar('multigroup_access_requirement', 'one_group').'
		//get published itemtypes
		$itemtypes = JRequest::getVar('itemtypes');

		$enabledItemtypes = array();
		$customItemtypes = array();

		$config .= '
itemtypes=';
		for($n = 0; $n < count($itemtypes); $n++)
		{
			$row = each($itemtypes);
			//only custom will add to config
			if(strpos($row['key'], 'ustom_') !== false)
			{
				$customItemtypes[] = $row['key'];
			}

			if(strpos($row['key'], 'ustom_') === false)
			{
				$enabledItemtypes[] = $row['key'];
			}
		}
		if(count($customItemtypes))
		{
			//if itemtype custom not enabled
			$db->setQuery( "UPDATE #__pi_extensions SET enabled='1' WHERE type='itemtype' AND element='custom' ");
			$db->query();
			$config .= implode(',',$customItemtypes);
		}
		
		//make sure no empty lines
		$configParams = array();
		$configurationParams = explode( "\n", $config);
		for($n = 0; $n < count($configurationParams); $n++)
		{
			$var = '';
			$temp = explode('=',$configurationParams[$n]);
			$var = trim($temp[0]);
			$value = '';
			if(count($temp)==2){
				$value = trim($temp[1]);
				if($value=='false'){
					$value = false;
				}
				if($value=='true'){
					$value = true;
				}
			}
			if($var != '')
			{
				$configParams[] = $var.'='.$value;
			}
		}
		$config = implode( "\n", $configParams);
		//end make sure no empty lines
		
		//ms: we must check if an itemtype enabled ore not and set this also in #__pi_extensions
		$query = 'SELECT extension_id,element,enabled ';
		$query .='FROM #__pi_extensions ';
		$query .='WHERE type='.$db->Quote('itemtype').' ';
		$query .='AND element <>'.$db->Quote('custom');
		$db->setQuery( $query );
		$itemtypeRows = $db->loadObjectList();
		if($itemtypeRows)
		{
			foreach($itemtypeRows as $itemtype)
			{
				if(!in_array($itemtype->element,$enabledItemtypes) && $itemtype->enabled)
				{
					//we must set the extension field enabled to 0
					$db->setQuery( "UPDATE #__pi_extensions SET enabled='0' WHERE extension_id='$itemtype->extension_id' ");
					$db->query();
				}
				elseif(in_array($itemtype->element,$enabledItemtypes) && !$itemtype->enabled)
				{
					//we must set the extension field enabled to 1
					$db->setQuery( "UPDATE #__pi_extensions SET enabled='1' WHERE extension_id='$itemtype->extension_id' ");
					$db->query();
				}
			}
		}

		//update config
		$db->setQuery( "UPDATE #__pi_config SET config='$config' WHERE id='pi' ");
		$db->query();

		//redirect
		$sub_task = JRequest::getVar('sub_task', '');
		if($sub_task=='apply')
		{
			$url = 'index.php?option=com_pagesanditems&view=config';
		}
		else
		{
			$url = 'index.php?option=com_pagesanditems';
		}
		
		$this->setRedirect($url, JText::_('COM_PAGESANDITEMS_CONFIGSAVED'));
		//$model->redirect_to_url($url, JText::_('COM_PAGESANDITEMS_CONFIGSAVED'));
	}
}