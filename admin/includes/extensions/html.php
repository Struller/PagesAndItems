<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;

require_once(dirname(__FILE__).DS.'extension.php');

abstract class PagesAndItemsExtensionHtml extends PagesAndItemsExtension
{
	var $imagePath = null;
	var $buttonType = 'input';
	var $name = null;
	var $id = null;
	var $text = null;	//button value
	var $alt = null;
	var $altTitle = null;
	var $title = null;
	var $titleText = null;
	var $onclick = null;
	var $imageName = null; 	//image
	//var $imageNameNoAccess = null; 	//image no access
	var $rel = null;
	var $href = null;
	var $modal = null;
	var $paddingLeft = '16'; //for input button and backgroundimage if image > 16px
	var $style = null;
	var $class = 'button';
	var $joomlaToolTip = 0;


	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

	}


	/*
	 * @param 	string 	$htmlelement
	 * @param 	object	$htmlelementVars	The htmlelement vars
	 * @param 	string	$name 		optional
	*/
	function onGetButton(&$htmlelement,$htmlelementVars,$name)
	{
		require_once(realpath(dirname(__FILE__).DS.'..'.DS.'html'.DS.'buttonmaker.php'));
		$newButton = new ButtonMaker();
		foreach($htmlelementVars as $key => $value)
		{
			$newButton->$key = $value;
		}


		$htmlelement->html = $newButton->makeButton();
		//$htmlelement = $newButton->makeButton();

		return $htmlelement->html;

		//return $button;
		//return $this->makeButton();
		//$button = $this->makeButton();

		//return true;

	}
}
