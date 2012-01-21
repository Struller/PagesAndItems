<?php
/**
* @version		2.0.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2011 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

class ButtonMaker 
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
	var $style = '';
	var $class = 'button';
	var $joomlaToolTip = 0;
	/**
	 * constructor
	 *

	 */

	function __construct()
	{
		
	}
	
	function makeButton()
	{
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		
		
		$html = '';
		if($this->modal)
		{
			//COMMENT WOW SquezzeBox work with input type=button to so we can make same style for buttons
			// we make own script for the SquezzeBox
			JHTML::_('behavior.modal'); //, 'a.modal-button');
			//the link will not work with '&amp;' for modal
			$this->href = preg_replace('/&amp;/', '&', $this->href);
			
			if(!$this->id || $this->id == '')
			{
				/*
				make unique id with $buttonType
				*/
				$this->id = $this->buttonType.uniqid();
			}
			
			/*
			window.document.getElementById('sbox-window').close = function() 
			{
				SqueezeBox.close();
			}
			
			*/
			
			$html .= '<script language="JavaScript"  type="text/javascript">';
			$html .= 	"<!-- \n";
			$html .= 	"window.addEvent('domready', function() \n";
			$html .= 	"{ \n";
			$html .= 		"SqueezeBox.initialize({  }); \n";
			//document.id(
			$html .= 			"el = $('".$this->id."'); \n";
			$html .= 			"el.addEvent('click', function(e) \n";
			$html .= 			"{ \n";
			$html .= 				"new Event(e).stop(); \n";
			$html .= 				"SqueezeBox.fromElement(el,{url: '".$this->href."', ".$this->rel."}); \n"; // here is the way to tell the SquezzeBox what we will
			$html .= 			"}); \n";
			if($joomlaVersion >= '1.6')
			{
				$html .= 		"window.document.getElementById('sbox-window').close = function(){SqueezeBox.close();}";
			}
			/*
			$html .= 		"$$('".$buttonType.".modal-button').each(function(el) \n";
			$html .= 		"{ \n";
			$html .= 			"el.addEvent('click', function(e) \n";
			$html .= 			"{ \n";
			$html .= 				"new Event(e).stop(); \n";
			$html .= 				"SqueezeBox.fromElement(el,{url: '".$href."', ".$rel."}); \n";
			$html .= 			"}); \n";
			$html .= 		"}); \n";
			*/
			$html .= 	"}); \n";
			$html .= 	"-->\n";
			$html .= "</script>\n";
			$buttonClass = 'modal-button '.$this->class.' ';
		}
		else
		{
			$buttonClass = $this->class.' ';
		}
		
		switch($this->buttonType)
		{
			case 'aspan':
				$html = '';
				//$title = 'title="'.$this->title.'"';
				$title = '';
				$onclick = 'onclick="'.$this->onclick.'"';
				$text = '<span class="state '.$this->imageName.'"></span>';
				$html .= '<span class="button jgrid"> <a '.$title.' '.$onclick.'> '.$text.'</a></span>';
				
				
			case 'button':
				//$html = 'TODO buttonType: '.$this->buttonType;
			case 'div':
				//$html = 'TODO buttonType: '.$this->buttonType;
			
			default:
				//$html = 'TODO buttonType: '.$this->buttonType;
			break;
			
			case 'input':
				$html .= '<input type="button" ';
				
				$html .= 'value="'.$this->text.'" ';
				
				if($this->id)
				{
					$html .= 'id="'.$this->id.'" ';
				}
				
				if($this->alt)
				{
					if($this->joomlaToolTip == 1)
					{
						JHTML::_('behavior.tooltip');
						$buttonClass .=' hasTip ';
						if($this->altTitle)
						{
							$html .= 'title="'.$this->altTitle.'::'.$this->alt.'" ';
						}
						else
						{
							$html .= 'title="'.$this->alt.'::" ';
						}
						
					}
					else
					{
						//$html .= 'alt="'.$this->alt.'" ';
						/*
						$html .= '<span class="editlinktip" onMouseOver="return overlib(\''.$no_access.'\', CAPTION, \'\', BELOW, RIGHT);" onMouseOut="return nd();" >';
							$image = $row->dtree_imageNoAccess;
							$html .= '<img src="'.$imagePath.'no_access.gif" alt="'.$no_access.'" />';
						$html .= '</span>&nbsp;';
						
						*/
						
						$html .= 'title="'.$this->alt.'" ';
					}
				}
				
				if($this->imageName )
				{
					$imgageHaveClass = false;
					$imgClass = explode("class:",$this->imageName);
					if(count($imgClass) && count($imgClass) == 2)
					{
						//we have an class
						$buttonClass .= ' '.$imgClass[1];
						$imgageHaveClass = true;
					}
				}
				
				if($buttonClass)
				{
					$html .= 'class="input_button '.$buttonClass.'" ';
				}
				else
				{
					$html .= 'class="input_button" ';
				}
				
				if($this->onclick)
				{
					$html .= 'onclick="'.$this->onclick.'" ';
				}
				$html .= 'style="';
				$html .= $this->style;
				$html .= 'background-repeat: no-repeat;';
				
				if($this->imageName && !$imgageHaveClass)
				{
					//in futur we can handle this with class?
					//$html .= 'background-position:center center;';
					//$html .= 'background-repeat: no-repeat; ';
					//$html .= 'background-position:3px center;';
					$html .= 'background-image: url('.$this->imagePath.$this->imageName.'); ';
					//$html .= 'min-height:16px;';
				}	
					//BEGIN will we do this to config?
					//check border-radius  for other browser
/*
W3C Specification
border-radius
border-top-left-radius
border-top-right-radius
border-bottom-right-radius
border-bottom-left-radius

Mozilla Implementation
-moz-border-radius
-moz-border-radius-topleft
-moz-border-radius-topright
-moz-border-radius-bottomright
-moz-border-radius-bottomleft


Webkit (Safari x.x), KHTML (Konqueror x.x)
-khtml-border-radius-topleft
-khtml-border-radius-topright
-khtml-border-radius-bottomleft
-khtml-border-radius-bottomright

Webkit (Safari, Google Chrome, Chromium)
-webkit-border-radius
-webkit-border-top-left-radius
-webkit-border-top-right-radius
-webkit-border-bottom-right-radius
-webkit-border-bottom-left-radius


*/
					
					//$html .= 'background-color: #D4D0C8;';
					$html .= '-moz-border-radius-bottomleft: 0px;';
					$html .= '-moz-border-radius-bottomright: 0px;';
					$html .= '-moz-border-radius-topleft: 0px;';
					$html .= '-moz-border-radius-topright: 0px;';
					//END will we do this to config?
					
					if(!$this->paddingLeft && $this->imageName)
					{
						$this->paddingLeft = '16';
					}
					elseif(!$this->imageName)
					{
						$this->paddingLeft = '0';
					}
					
					if($this->text == '' || $this->text == '&nbsp;')
					{
						$html .= 'padding-right: 0;';
						$html .= 'padding-left: '.(int)$this->paddingLeft.'px;';
						
					}
					else
					{
						$paddingLeft = ((int)$this->paddingLeft)+4;
						$html .= 'padding-left: '.$paddingLeft.'px;';
					}
					//$html .= 'margin-left: 0;';
					//$html .= 'margin-right: 6px;';
					$html .= ' " ';
				//}
				$html .= '/>';
			break;
		}
		return $html;
	}
	
}