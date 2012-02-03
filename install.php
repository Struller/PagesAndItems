<?php
/**
* @version		2.1.5
* @package		PagesAndItems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
 * This is the special installer addon created by Andrew Eddie and the team of jXtended.
 * We thank for this cool idea of extending the installation process easily
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

//give it time
@set_time_limit(240);

//give it memory
$max_memory = trim(@ini_get('memory_limit'));
if($max_memory){
	$end =strtolower($max_memory{strlen($max_memory) - 1});
	switch($end) {
		case 'g':
			$max_memory	*=	1024;
		case 'm':
			$max_memory	*=	1024;
		case 'k':
			$max_memory	*=	1024;
	}
	if ( $max_memory < 16000000 ) {
		@ini_set( 'memory_limit', '16M' );
	}
	if ( $max_memory < 32000000 ) {
		@ini_set( 'memory_limit', '32M' );
	}
	if ( $max_memory < 48000000 ) {
		@ini_set( 'memory_limit', '48M' );
	}
}
ignore_user_abort(true);


$version = new JVersion();
$joomlaVersion = $version->getShortVersion();
$JVersion = '1.5';
if($joomlaVersion >= '1.6')
{
	$JVersion = '1.6';
	if(!isset($parent))
	{
		$parent = $this->parent;
	}
}
else
{
if(!isset($parent))
	{
		$parent = $this->parent;
	}
}


/*
// Joomla! 1.6
// see install.script.php
if( $joomlaVersion >= '1.6') && !defined('COM_PAGESANDITEMS_INSTALL') )
{
	return;
}
else
{
	global $pi_installation_has_run;
	if($pi_installation_has_run) return;
}
*/

$status = new JObject();

$database = JFactory::getDBO();

/*
*********
* BEGIN *
*********
* table for custom itemtypes
*/

	$database->setQuery("CREATE TABLE IF NOT EXISTS #__pi_customitemtypes (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `read_more` varchar(1) NOT NULL,
  `template_intro` text NOT NULL,
  `template_full` text NOT NULL,
  `editor_id` INT NOT NULL,
  `html_after` text NOT NULL,
  `html_before` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` TINYINT( 3 ) NOT NULL DEFAULT '1',
  `params` TEXT NOT NULL ,
  PRIMARY KEY  (`id`)
)");

	$database->query();

	$database->setQuery("SHOW COLUMNS FROM #__pi_customitemtypes");
	$columns = $database->loadResultArray();
	if(!in_array('editor_id', $columns)){
		$database->setQuery("ALTER TABLE #__pi_customitemtypes ADD `editor_id` INT NOT NULL AFTER `template_full`");
		$database->query();
	}
	if(!in_array('html_after', $columns)){
		$database->setQuery("ALTER TABLE #__pi_customitemtypes ADD `html_after` text NOT NULL AFTER `editor_id`");
		$database->query();
	}
	if(!in_array('html_before', $columns)){
		$database->setQuery("ALTER TABLE #__pi_customitemtypes ADD `html_before` text NOT NULL AFTER `html_after`");
		$database->query();
	}
	if(!in_array('state', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_customitemtypes ADD `state` TINYINT( 3 ) NOT NULL DEFAULT '1'");
		$database->query();
	}
	if(!in_array('checked_out', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_customitemtypes ADD `checked_out` int(10) unsigned NOT NULL default '0'");
		$database->query();
	}

	if(!in_array('checked_out_time', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_customitemtypes ADD `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'");
		$database->query();
	}
	if(!in_array('params', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_customitemtypes ADD `params` text NOT NULL");
		$database->query();
	}
/*
* table for custom itemtypes
*******
* END *
*******
*/

/*
*********
* BEGIN *
*********
* table for custom itemtype fields
*/
	$database->setQuery("CREATE TABLE IF NOT EXISTS #__pi_custom_fields (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `type_id` int(11) NOT NULL,
  `plugin` tinytext NOT NULL,
  `ordering` int(11) NOT NULL,
  `params` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` TINYINT( 3 ) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
)");
	$database->query();

	$database->setQuery("SHOW COLUMNS FROM #__pi_custom_fields ");
	$columns = $database->loadResultArray();
	if(!in_array('state', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_custom_fields ADD `state` TINYINT( 3 ) NOT NULL DEFAULT '1'");
		$database->query();
	}
	if(!in_array('checked_out', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_custom_fields ADD `checked_out` int(10) unsigned NOT NULL default '0'");
		$database->query();
	}

	if(!in_array('checked_out_time', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_custom_fields ADD `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'");
		$database->query();
	}
/*
* table for custom itemtype fields
*******
* END *
*******
*/

/*
*********
* BEGIN *
*********
* table for custom itemtype fields-values
*/

	$database->setQuery("CREATE TABLE IF NOT EXISTS #__pi_custom_fields_values (
  `id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` TINYINT( 3 ) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
)");
	$database->query();

	$database->setQuery("SHOW COLUMNS FROM #__pi_custom_fields_values ");
	$columns = $database->loadResultArray();
	if(!in_array('state', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_custom_fields_values ADD `state` TINYINT( 3 ) NOT NULL DEFAULT '1'");
		$database->query();
	}
		if(!in_array('checked_out', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_custom_fields_values ADD `checked_out` int(10) unsigned NOT NULL default '0'");
		$database->query();
	}

	if(!in_array('checked_out_time', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_custom_fields_values ADD `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'");
		$database->query();
	}
/*
* table for custom itemtype fields-values
*******
* END *
*******
*/

/*
*********
* BEGIN *
*********
* table for item index
*/

	$database->setQuery("CREATE TABLE IF NOT EXISTS #__pi_item_index (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `itemtype` tinytext NOT NULL,
  `show_title` tinyint(4) NOT NULL,
  `checked_out` int(10) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` TINYINT( 3 ) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ");
$database->query();

	$database->setQuery("SHOW COLUMNS FROM #__pi_item_index ");
	$columns = $database->loadResultArray();
	if(!in_array('state', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_item_index ADD `state` TINYINT( 3 ) NOT NULL DEFAULT '1'");
		$database->query();
	}
		if(!in_array('checked_out', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_item_index ADD `checked_out` int(10) unsigned NOT NULL default '0'");
		$database->query();
	}

	if(!in_array('checked_out_time', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_item_index ADD `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'");
		$database->query();
	}
/*
* table for item index
*******
* END *
*******
*/

/*
*********
* BEGIN *
*********
* table for itemtype other_item
*/
	$database->setQuery("CREATE TABLE IF NOT EXISTS #__pi_item_other_index (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL,
  `other_item_id` int(11) NOT NULL,
  `checked_out` int(10) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` TINYINT( 3 ) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
)");
	$database->query();

	$database->setQuery("SHOW COLUMNS FROM #__pi_item_other_index ");
	$columns = $database->loadResultArray();
	if(!in_array('state', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_item_other_index  ADD `state` TINYINT( 3 ) NOT NULL DEFAULT '1'");
		$database->query();
	}
		if(!in_array('checked_out', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_item_other_index ADD `checked_out` int(10) unsigned NOT NULL default '0'");
		$database->query();
	}

	if(!in_array('checked_out_time', $columns))
	{
		$database->setQuery("ALTER TABLE #__pi_item_other_index ADD `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'");
		$database->query();
	}
/*
* table for itemtype other_item
*******
* END *
*******
*/

/*
*********
* BEGIN *
*********

*****************
* CONFIGURATION *
*****************

* table for configuration
*/

	$database->setQuery("CREATE TABLE IF NOT EXISTS #__pi_config (
  `id` varchar(255) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY  (`id`)
)");
	$database->query();

/*
* table for configuration
*/

	//check if config is empty, if so insert default config
	$database->setQuery("SELECT * FROM #__pi_config WHERE id='pi' ");
	$rows = $database -> loadObjectList();
	$pi_config = '';
	if(count($rows) > 0)
	{
		$pirow = $rows[0];
		$pi_config = $pirow->id;
		//$pi_config = $pirow->config; ??
	}

	if($pi_config=='')
	{
		/*
		change for menu_types id
		and set all avaible menutypes in pi config
		*/
			$database->setQuery("SELECT title, menutype,id FROM #__menu_types ORDER BY title ASC"  );
			//$menutypesDb = $db->loadObjectList();
			$menutypes = array();
			foreach($database->loadObjectList() as $menutype)
			{
				$menutypes[] = $menutype->menutype.';'.$menutype->title; //.';'.$menutype->id;
			
			}
			$menus = implode(',',$menutypes);
		
/*
				$configuration = 'showSlider=-1
enabled_view_category=false
use_pi_frontend_editting=true
menus=mainmenu;Main Menu
*/		
		
		//language=en-GB
		$configuration = 'useCheckedOut=false
plugin_system_add_button=false
plugin_system_hidde_button=false
showSlider=-1
enabled_view_category=false
use_pi_frontend_editting=true
menus='.$menus.'
cit=1
item_props_publish=true
item_show_frontpage_option=true
item_props_alias=true
plugin_syntax_cheatcheat=
item_save_redirect=item
make_article_alias_unique=true
create_sef_urls=
sef_url_cat=true
sef_url_id=true
sef_url_ext=
item_save_redirect_url=
item_props_hideforsuperadmin=true
item_props_details=true
item_props_title=true
item_props_alias=true
item_props_category=true
item_props_status=true
item_props_access=true
item_props_featured=true
item_props_language=true
item_props_id=true
item_props_articletext=true
item_props_publishingoptions=true
item_props_createdby=true
item_props_createdbyalias=true
item_props_createddate=true
item_props_start=true
item_props_finish=true
item_props_modified_by=true
item_props_modified=true
item_props_revision=true
item_props_hits=true
item_props_articleoptions=true
item_props_show_title=true
item_props_link_titles=true
item_props_show_intro=true
item_props_show_category=true
item_props_link_category=true
item_props_show_parent_category=true
item_props_link_parent_category=true
item_props_show_author=true
item_props_link_author=true
item_props_show_create_date=true
item_props_show_modify_date=true
item_props_show_publish_date=true
item_props_show_item_navigation=true
item_props_show_icons=true
item_props_show_print_icon=true
item_props_show_email_icon=true
item_props_show_vote=true
item_props_show_hits=true
item_props_show_noauth=true
item_props_alternative_readmore=true
item_props_article_layout=true
item_props_metadataoptions=true
item_props_desc=true
item_props_keywords=true
item_props_robots=true
item_props_author=true
item_props_rights=true
item_props_xreference=true
item_props_pioptions=true
item_props_instance=true
item_props_pishowtitle=true
item_props_permissions=true
item_new_show_title=true
item_type_select_frontend=false
inherit_from_parent=true
sections_from_db=true
inherit_from_parent_move=true
child_inherit_from_parent_move=true
child_inherit_from_parent_change=true
make_page_alias_unique=
truncate_item_title=0
page_props_hideforsuperadmin=true
page_props_id=true
page_props_type=true
page_props_title=true
page_props_alias=true
page_props_note=true
page_props_link=true
page_props_published=true
page_props_access=true
page_props_menutype=true
page_props_parent_id=true
page_props_browserNav=true
page_props_home=true
page_props_language=true
page_props_template_style_id=true
page_props_linktype_options=true
page_props_link_title_attri=true
page_props_link_css=true
page_props_link_image=true
page_props_add_title=true
page_props_metadata_options=true
page_props_meta_desc=true
page_props_meta_keys=true
page_props_robots=true
page_props_secure=true
page_props_page_display_options=true
page_props_browser_page=true
page_props_show_page_heading=true
page_props_page_heading=true
page_props_page_class=true
page_props_modules=true
page_props_required_settings=true
page_props_category_options=true
page_props_cat_title=true
page_props_cat_desc=true
page_props_cat_img=true
page_props_cat_levels=true
page_props_cat_empty=true
page_props_cat_no_art_mess=true
page_props_cat_subcat_desc=true
page_props_cat_artincat=true
page_props_cat_subheading=true
page_props_blog_options=true
page_props_blog_leading=true
page_props_blog_intro=true
page_props_blog_cols=true
page_props_blog_links=true
page_props_blog_multicolorder=true
page_props_blog_incsubcat=true
page_props_blog_catorder=false
page_props_blog_artorder=true
page_props_blog_dateorder=true
page_props_blog_pagination=true
page_props_blog_results=true
page_props_article_options=true
page_props_art_title=true
page_props_art_linkedtitles=true
page_props_art_introtext=true
page_props_art_cat=true
page_props_art_catlink=true
page_props_art_parent=true
page_props_art_parentlink=true
page_props_art_author=true
page_props_art_authorlink=true
page_props_art_create=true
page_props_art_modify=true
page_props_art_pub=true
page_props_art_nav=true
page_props_art_vote=true
page_props_art_read=true
page_props_art_readtitle=true
page_props_art_icons=true
page_props_art_print=true
page_props_art_email=true
page_props_art_hits=true
page_props_art_unauthorised=true
page_props_integration_options=true
page_props_int_feed=true
page_props_int_each=true
page_new_publish_category=1
page_new_publish_menu=true
page_new_access_menu=0
page_new_access_category=0
itemtypes=
version_checker=true
page_trash_cat=
page_trash_items=
page_delete_cat=
page_delete_items=
';
//itemtypes=html,other_item,text is replace with itemtypes= only custem_* will add later

		//make sure no empty lines
		$configParams = array();
		$configurationParams = explode( "\n", $configuration);
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
		
		
		$configuration = implode( "\n", $configParams);
//multigroup_access_requirement=one_group
//permissions=6_1,6_2,6_3,6_4,7_1,7_2,7_3,7_4,3_3,4_3,4_4,5_3,5_4,8_1,8_2,8_3,8_4
		//insert fresh config
		//QUESTION want use JSON?
		//ce: maybe later, lets focus on getting everything working first.
		$database->setQuery( "INSERT INTO #__pi_config SET id='pi', config='$configuration'");
		$database->query();
	}
	else
	{

		$config_needs_updating = 0;
		$updated_config = $pirow->config;


		//added in version 2.1.0 | 2.0.2
		/*
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_pagesanditems'.DS.'helpers'.DS.'pagesanditems.php')
		$config = PagesAndItemsHelper::getConfig();
		if(isset($config['menus']) && $config['menus'] != '')
		{
			$code_to_replace = $config['menus'];
			$menus_needs_updating = 0;
			$menutypes = array();
			$temp_menus = explode(',',$config['menus']);
			for($n = 0; $n < count($temp_menus); $n++)
			{
				$temp_menutype = explode(';',$temp_menus[$n]);
				if(count($temp_menutype) == 2)
				{
					$menus_needs_updating = 1;
					$config_needs_updating = 1;
					$database->setQuery("SELECT title, menutype,id FROM #__menu_types WHERE menutype = '$temp_menutype[0]' ORDER BY title ASC"  );
					$menutype = $database->loadObject();
					$menutypes[] = $menutype->menutype.';'.$menutype->title; //.';'.$menutype->id;
				}
			}
			if($menus_needs_updating)
			{
				$menus = implode(',',$menutypes);
				$updated_config = str_replace($code_to_replace, $menus, $updated_config);
			}
		}
		*/
		if(strpos($pirow->config, 'plugin_system_add_button=') === false){
			$config_needs_updating = 1;
			$updated_config .= '
plugin_system_add_button=false
';
		}
		if(strpos($pirow->config, 'plugin_system_hidde_button=') === false){
			$config_needs_updating = 1;
			$updated_config .= '
plugin_system_hidde_button=false
';
		}
		
		
		if(strpos($pirow->config, 'useCheckedOut=') === false){
			//added in version 2.1.0
			$config_needs_updating = 1;
			$updated_config .= '
useCheckedOut=false
';
		}
		
		
		if(strpos($pirow->config, 'showSlider=') === false){
			//added in version 2.1.0
			$config_needs_updating = 1;
			$updated_config .= '
showSlider=-1
';
		}
		
		if(strpos($pirow->config, 'enabled_view_category=') === false){
			//added in version 2.1.0
			$config_needs_updating = 1;
			$updated_config .= '
enabled_view_category=false
';
		}
		
		if(strpos($pirow->config, 'version_checker=') === false){
			//added in version 2.0.0
			$config_needs_updating = 1;
			$updated_config .= '
version_checker=true
item_props_details=true
item_props_title=true
item_props_alias=true
item_props_category=true
item_props_status=true
item_props_access=true
item_props_featured=true
item_props_language=true
item_props_id=true
item_props_articletext=true
item_props_publishingoptions=true
item_props_createdby=true
item_props_createdbyalias=true
item_props_createddate=true
item_props_start=true
item_props_finish=true
item_props_modified_by=true
item_props_modified=true
item_props_revision=true
item_props_hits=true
item_props_articleoptions=true
item_props_show_title=true
item_props_link_titles=true
item_props_show_intro=true
item_props_show_category=true
item_props_link_category=true
item_props_show_parent_category=true
item_props_link_parent_category=true
item_props_show_author=true
item_props_link_author=true
item_props_show_create_date=true
item_props_show_modify_date=true
item_props_show_publish_date=true
item_props_show_item_navigation=true
item_props_show_icons=true
item_props_show_print_icon=true
item_props_show_email_icon=true
item_props_show_vote=true
item_props_show_hits=true
item_props_show_noauth=true
item_props_alternative_readmore=true
item_props_article_layout=true
item_props_metadataoptions=true
item_props_desc=true
item_props_keywords=true
item_props_robots=true
item_props_author=true
item_props_rights=true
item_props_xreference=true
item_props_pioptions=true
item_props_instance=true
item_props_pishowtitle=true
item_props_permissions=true
page_props_id=true
page_props_type=true
page_props_title=true
page_props_alias=true
page_props_note=true
page_props_link=true
page_props_published=true
page_props_access=true
page_props_menutype=true
page_props_parent_id=true
page_props_browserNav=true
page_props_home=true
page_props_language=true
page_props_template_style_id=true
page_props_linktype_options=true
page_props_link_title_attri=true
page_props_link_css=true
page_props_link_image=true
page_props_add_title=true
page_props_metadata_options=true
page_props_meta_desc=true
page_props_meta_keys=true
page_props_robots=true
page_props_secure=true
page_props_page_display_options=true
page_props_browser_page=true
page_props_show_page_heading=true
page_props_page_heading=true
page_props_page_class=true
page_props_modules=true
page_props_required_settings=true
page_props_category_options=true
page_props_cat_title=true
page_props_cat_desc=true
page_props_cat_img=true
page_props_cat_levels=true
page_props_cat_empty=true
page_props_cat_no_art_mess=true
page_props_cat_subcat_desc=true
page_props_cat_artincat=true
page_props_cat_subheading=true
page_props_blog_options=true
page_props_blog_leading=true
page_props_blog_intro=true
page_props_blog_cols=true
page_props_blog_links=true
page_props_blog_multicolorder=true
page_props_blog_incsubcat=true
page_props_blog_catorder=false
page_props_blog_artorder=true
page_props_blog_dateorder=true
page_props_blog_pagination=true
page_props_blog_results=true
page_props_article_options=true
page_props_art_title=true
page_props_art_linkedtitles=true
page_props_art_introtext=true
page_props_art_cat=true
page_props_art_catlink=true
page_props_art_parent=true
page_props_art_parentlink=true
page_props_art_author=true
page_props_art_authorlink=true
page_props_art_create=true
page_props_art_modify=true
page_props_art_pub=true
page_props_art_nav=true
page_props_art_vote=true
page_props_art_read=true
page_props_art_readtitle=true
page_props_art_icons=true
page_props_art_print=true
page_props_art_email=true
page_props_art_hits=true
page_props_art_unauthorised=true
page_props_integration_options=true
page_props_int_feed=true
page_props_int_each=true
page_trash_cat=
page_trash_items=
page_delete_cat=
page_delete_items=
';
//permissions=6_1,6_2,6_3,6_4,7_1,7_2,7_3,7_4,3_3,4_3,4_4,5_3,5_4,8_1,8_2,8_3,8_4
//multigroup_access_requirement=one_group
	}

		if($config_needs_updating){
			//make sure no empty lines
			$configParams = array();
			$configurationParams = explode( "\n", $updated_config);
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
			$updated_config = implode( "\n", $configParams);
			$database->setQuery( "UPDATE #__pi_config SET config='$updated_config' WHERE id='pi' ");
			$database->query();
		}


	}

/*
*****************
* CONFIGURATION *
*****************
*******
* END *
*******
*/

	if($joomlaVersion < '1.6')
	{
		//do icon
		$icon_path = 'components';
		$database->setQuery("UPDATE #__components SET admin_menu_img='$icon_path/com_pagesanditems/images/icon.gif' WHERE link='option=com_pagesanditems'");
		$database->query();
	}

/*
*****************
* PI EXTENSIONS *
*****************
*/
$componentPath = $parent->getPath('extension_administrator');
require_once( $componentPath.DS.'install'.DS.'install.piextensions.php' );
$piExtensions = new piExtensions();

// table extensions
$piExtensions->createTable();
$piExtensions->installLanguage($parent);

// install extensions
$status->extensions = $piExtensions->installExtensions($parent);

/*
*********************
* END PI EXTENSIONS *
*********************
*/

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* PLUGIN INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
require_once( $componentPath.DS.'install'.DS.'install.plugins.php' );
$install_plugins = new installPlugins();

$status->plugins = array();
$status->plugins = $install_plugins->installUseXML($parent);

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* PLUGIN INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/


/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* OUTPUT TO SCREEN
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$rows = 0;
?>

<div style="width: 500px; text-align: left;">
	<h2>Pages and Items</h2>
	<p>
		Thank you for using the Pages-and-Items framework for Joomla.
	</p>
	<p>
		Check <a href="http://www.pages-and-items.com" target="_blank">www.pages-and-items.com</a> for:
		<ul>
			<li><a href="http://www.pages-and-items.com/extensions/pages-and-items" target="_blank">updates</a></li>
			<li><a href="http://www.pages-and-items.com/extensions/pages-and-items/faqs" target="_blank">FAQs</a></li>
			<li><a href="http://www.pages-and-items.com/forum/8-pages-and-items" target="_blank">forum</a></li>			
			<li><a href="http://www.pages-and-items.com/component/comprofiler/registers" target="_blank">email notification service for updates and new extensions</a></li>
			<li><a href="http://www.pages-and-items.com" target="_blank">RSS feed notification of updates</a></li>
		</ul>
	</p>
	<p>
		Component Pages-and-Items is only fully functional with the content and system plugin installed and enabled.
	</p>
	<p>
		Follow us on <a href="http://www.twitter.com/PagesAndItems" target="_blank">Twitter</a> (update notifications).
	</p>
</div>
<?php

if(is_array($status->plugins))
{
?>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="3"><?php echo JText::_('Extension'); ?></th>
			<th width="30%" colspan="1"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="4"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="3"><strong><?php echo 'Pages and Items '?></strong></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
<?php if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th><?php echo JText::_('Name'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['element']); ?></td>
			<td><strong><?php echo $plugin['installed'] ? JText::_('Installed') : JText::_('Not').' '.JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>
<?php
}


if(is_array($status->extensions))
{
$rows = 0;
?>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="3"><?php echo JText::_('PiExtension'); ?></th>
			<th width="30%" colspan="1"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="4"></td>
		</tr>
	</tfoot>
	<tbody>
<?php if (count($status->extensions)) : ?>
		<tr>
			<th><?php echo JText::_('Type'); ?></th>
			<th><?php echo JText::_('Folder'); ?></th>
			<th><?php echo JText::_('Name'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->extensions as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['type']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['folder']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td><strong><?php echo $plugin['installed'] ? JText::_('Installed') : JText::_('Not').' '.JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>
<?php
}

?>