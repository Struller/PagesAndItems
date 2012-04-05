<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// No direct access
defined('JPATH_BASE') or die;

class PagesAndItemsTree
{
	var $pageMenuItem = null;
	var $currentMenuitems = null;
	var $_tree = null;
	
	function getTreeClass()
	{
		/*
		static $tree;
		if (isset($tree))
		{
			return $tree;
		}
		$tree = realpath(dirname(__FILE__).DS.'..'.DS.'extensions');
		*/
		if(!$this->_tree)
		{
			$view = JRequest::getVar('view', '');
			$page_id = intval(JRequest::getVar('pageId', null));
			$menutype = JRequest::getVar('menutype', '');
			$categoryId = intval(JRequest::getVar('categoryId', null));
			switch($view)
			{
				case 'category':
					require_once('tree_category.php');
					$this->_tree = new PagesAndItemsTreeCategory();//$categoryId);
				break;
			
				case 'page':
					require_once('tree_page.php');
					$this->_tree = new PagesAndItemsTreePage();//true,$menutype,$page_id);
				break;
			
				default:
					if(!$page_id && !$menutype && $categoryId)
					{
						require_once('tree_category.php');
						$this->_tree = new PagesAndItemsTreeCategory();//$categoryId);
					}
					else
					{
						require_once('tree_page.php');
						$this->_tree = new PagesAndItemsTreePage();// false,$menutype,$page_id);
					}
				break;
			}	
		}
		
		return $this->_tree;
	}
	
	function getTree()
	{
		$view = JRequest::getVar('view', '');
		$page_id = intval(JRequest::getVar('pageId', null));
		$menutype = JRequest::getVar('menutype', '');
		$categoryId = intval(JRequest::getVar('categoryId', null));
		switch($view)
		{
			case 'category':
				//require_once('tree_category.php');
				$tree = $this->getTreeClass(); //new PagesAndItemsTreeCategory($categoryId);
				$html = $tree->getTree($categoryId);
			break;
		
			case 'page':
				//require_once('tree_page.php');
				$tree = $this->getTreeClass(); //new PagesAndItemsTreePage(true,$menutype,$page_id);
				$html = $tree->getTree(true);
			break;
			
			default:
				if(!$page_id && !$menutype && $categoryId)
				{
					//require_once('tree_category.php');
					$tree = $this->getTreeClass(); //new PagesAndItemsTreeCategory($categoryId);
					$html = $tree->getTree($categoryId);
				}
				else
				{
					//require_once('tree_page.php');
					$tree = $this->getTreeClass(); //new PagesAndItemsTreePage(false,$menutype,$page_id);
					$html = $tree->getTree(false);
				}
			break;
		}
		
		
		
		
		$this->pageMenuItem = isset($tree->pageMenuItem) ? $tree->pageMenuItem : null;
		$this->currentMenuitems = isset($tree->currentMenuitems) ? $tree->currentMenuitems : array();
		/*
		$select .= JHTML::_('select.genericlist', $options, 'categoryExtension', 'class="inputbox" size="1" onchange="Javascript:change_extension();"', 'value', 'text', $categoryExtension );
		
		*/
		$sub_task = JRequest::getVar('sub_task', '');
		$this->useCheckedOut = PagesAndItemsHelper::getUseCheckedOut();
		
		if($this->useCheckedOut && $sub_task != '')
		{
			$languageSelect = '';
		}
		else
		{
			$languageSelect = PagesAndItemsHelper::makeLanguageSelect();
		}
		
		
		return $languageSelect.$html;
	}
}
