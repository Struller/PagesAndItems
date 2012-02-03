<?php
/**
* @version		2.1.5
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
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
	var $joomlaToolTip = 1;
	var $disabled = 0;
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

		$buttonClass = array();
		$html = '';
		if($this->modal && $this->buttonType == 'input')
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
			$buttonClass[] = 'modal-button';
			$buttonClass[] = $this->class;
		}
		elseif($this->modal && ($this->buttonType == 'div' || $this->buttonType == 'joomla'))
		{
			
			//JHTML::_('behavior.modal'); //, 'a.modal-button');
			//the link will not work with '&amp;' for modal
			if($this->buttonType == 'div')
			{ 
				JHtml::_('behavior.modal', 'a.modal-button');
				$this->href = preg_replace('/&amp;/', '&', $this->href);
			}
			else
			{
				//$this->href = preg_replace('/&/', '&amp;', $this->href);
			}
			if(!$this->id || $this->id == '')
			{
				/*
				make unique id with $buttonType
				*/
				$this->id = $this->buttonType.uniqid();
				$buttonClass[] = 'modal-button';
				$buttonClass[] = $this->class;
			}
			$html .= '<script language="JavaScript"  type="text/javascript">';
			$html .= 	"<!-- \n";
			$html .= 	"window.addEvent('domready', function() \n";
			$html .= 	"{ \n";
			$html .= 		"window.document.getElementById('sbox-window').close = function(){SqueezeBox.close();}";
			$html .= 	"}); \n";
			$html .= 	"-->\n";
			$html .= "</script>\n";
		}
		else
		{
			$buttonClass[] = $this->class;
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
			break;
			

			case 'joomla':
				//$html = 'TODO buttonType: '.$this->buttonType;
				//$buttonClass[] = 'div_button';
				if($this->imageName )
				{
					$imgageHaveClass = false;
					$imgClass = explode("class:",$this->imageName);
					if(count($imgClass) && count($imgClass) == 2)
					{
						//we have an class
						$buttonClass[] = $imgClass[1];
						$imgageHaveClass = true;
					}
				}
				
				$style = 'style="';
				$style .= $this->style;
				if($this->imageName && !$imgageHaveClass)
				{
					$style .= 'background-image: url('.$this->imagePath.$this->imageName.'); ';
				}
				if(!$this->paddingLeft && $this->imageName)
				{
					$this->paddingLeft = '16';
				}
				elseif(!$this->imageName)
				{
					$this->paddingLeft = 0;
				}
				if($this->text == '' || $this->text == '&nbsp;')
				{
					$style .= 'padding-right: 0;';
					$style .= $this->paddingLeft ? 'padding-left: '.(int)$this->paddingLeft.'px;' : '';
				}
				else
				{
					$paddingLeft = ((int)$this->paddingLeft)+4;
					$style .= 'padding-left: '.$paddingLeft.'px;';
				}
				$style .= ' " ';
				$class = $this->class ? 'class="'.$this->class.'" ' : '';
				$href = 'class="'.$this->class.'"';
				$html .= '<div class="button2-left">';
				//<div class="readmore">
				$html .= '<div '.($this->id ? 'id="'.$this->id.'"' : '').' '.$class.' >';
					$modal = 'class="modal-button"';
					$href = 'href="'.$this->href.'"';
					$rel = 'rel="'.$this->rel.'"';
					$title = 'title="'.$this->title.'"';
					$html .= '<a '.$modal.' '.$title.' '.$href.' '.$rel.' '.$style.' >';
						$html .= $this->text;
					$html .= '</a>';
				$html .= '</div>';
				$html .= '</div>';
				
			break;
			
			/*
			<div class="button2-left"><div class="readmore"><a id="field_values_21_button_readmore" rel="{handler: 'iframe', size: {x: 0, y: 0}}" href="http://127.0.0.1:4001/administrator/index.php?option=com_pagesanditems&amp;view=indicator&amp;layout=indicator&amp;button=readmore&amp;buttonType=editors-xtd&amp;tmpl=component&amp;field_id=field_values_21&amp;indicator=codemirror&amp;popup=1&amp;size_x=0&amp;size_y=0&amp;onclick=aW5zZXJ0UmVhZG1vcmUoJ2ZpZWxkX3ZhbHVlc18yMScpO3JldHVybiBmYWxzZTs=" title="" class="modal-button" floatingtitle="Weiterlesen">Weiterlesen</a></div></div>
			
			
			*/
			
			case 'div':
				//$html = 'TODO buttonType: '.$this->buttonType;
				$buttonClass[] = 'div_button';
				$class = count($buttonClass) ? 'class="'.implode(' ',$buttonClass).'" ' : '';
				
				
				$href = 'class="'.$this->class.'"';
				$html .= '<div '.($this->id ? 'id="'.$this->id.'"' : '').' '.$class.' >';
					$modal = 'class="modal-button"';
					$href = 'href="'.$this->href.'"';
					$rel = 'rel="'.$this->rel.'"';
					$title = 'title="'.$this->title.'"';
					$html .= '<a '.$modal.' '.$title.' '.$href.' '.$rel.' >';
						$html .= $this->text;
					$html .= '</a>';
				$html .= '</div>';
				
				
			break;	

			default:
				//$html = 'TODO buttonType: '.$this->buttonType;
			break;

			
			//
			/*
			 TODO 
			 for more browser compatible
			 if $this->text = '' || $this->text = '&nbsp;'
			
			 input type="image
			 or <button></button>
			 
			  <button name="Klickmich" type="button"
      value="Überraschung" onclick="alert('Überraschung!');">
      <p>
        <img src="selfhtml.gif" width="106" height="109" alt="SELFHTML Logo"><br>
        <b>Was passiert wohl?</b>
      </p>
    </button>
			 
			*/
			case 'input':
				
				$html .= '<input type="button" ';
				if($this->text == '' || $this->text == '&nbsp;')
				{
					//$html .= '<input type="image" ';
					//$this->text = '&nbsp;';
				}
				else
				{
					
				}
				$html .= 'value="'.$this->text.'" ';


				if($this->disabled)
				{
					$html .= 'disabled="disabled" ';

				}
				
				if($this->id)
				{
					$html .= 'id="'.$this->id.'" ';
				}

				if($this->alt)
				{
					if($this->joomlaToolTip == 1)
					{
						JHTML::_('behavior.tooltip');
						$buttonClass[] = 'hasTip';
						if($this->altTitle)
						{
							$html .= 'title="'.$this->altTitle.'::'.$this->alt.'" ';
						}
						else
						{
							$html .= 'title="'.$this->alt.'" ';
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
						$buttonClass[] = $imgClass[1];
						$imgageHaveClass = true;
					}
				}
				
				/*
				if($buttonClass)
				{
					$html .= 'class="input_button '.$buttonClass.'" ';
				}
				else
				{
					$html .= 'class="input_button" ';
				}
				*/
				$buttonClass[] = 'input_button';
				
				$html .= count($buttonClass) ? 'class="'.implode(' ',$buttonClass).'" ' : '';
				

				if($this->onclick)
				{
					$html .= 'onclick="'.$this->onclick.'" ';
				}
				$html .= 'style="';
				$html .= $this->style;
				if($this->imageName && !$imgageHaveClass)
				{
					//in futur we can handle this with class?
					//$html .= 'background-position:center center;';
					//$html .= 'background-repeat: no-repeat; ';
					//$html .= 'background-position:3px center;';
					/*
					if($this->text == '' || $this->text == '&nbsp;')
					{
						$html .= 'src="'.$this->imagePath.$this->imageName.'" ';
					}
					else
					{
					*/
						$html .= 'background-image: url('.$this->imagePath.$this->imageName.'); ';
					//}
					/*
					$html .= 'background-repeat: no-repeat;';
					*/
					//$html .= 'min-height:16px;';
				}
					//$html .= 'background-color: #D4D0C8;';
				/*
				$html .= '-moz-border-radius-bottomleft: 0px;';
				$html .= '-moz-border-radius-bottomright: 0px;';
				$html .= '-moz-border-radius-topleft: 0px;';
				$html .= '-moz-border-radius-topright: 0px;';
				*/
					if(!$this->paddingLeft && $this->imageName)
					{
						$this->paddingLeft = '16';
					}
					elseif(!$this->imageName)
					{
						$this->paddingLeft = 0;
					}

					if($this->text == '' || $this->text == '&nbsp;')
					{
						$html .= 'padding-right: 0;';
						$html .= $this->paddingLeft ? 'padding-left: '.(int)$this->paddingLeft.'px;' : '';

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