<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class PagesAndItemsVersion{
	var $_version	= '2.1.0';
	/*
	var $_versionid	= 'Twinkle';
	var $_date		= '2009-11-15';
	var $_status	= 'Stable release';
	var $_revision	= '$Rev: 1531 $';
	var $_copyyears = '2003-2010';
	*/

	function getVersionNr() {
		return $this->_version;
		jimport('joomla.filesystem.folder');
		$folder = realpath(dirname(__FILE__).'..'.DS.'..'.DS);
		$files = JFolder::files($folder,'.xml',false,true);
		if(count($files))
		{
			foreach($files as $file)
			{
				$xml = simplexml_load_file($file);
				if ($xml)
				{
					if ( is_object($xml) && (is_object($xml->install) || is_object($xml->extension)))
					{
						//ok we have the install file
						//we will get the version
						$element = (string)$xml->version;
						return $element ? $element : '';
						//$this->version = $element ? $element : '';
						
					}
				}
			}
		}
	}


	/**
	 * This method delivers the full version information in one line
	 *
	 * @return string
	 */
	/*
	function getVersion() {
		
		return 'V' .$this->_version. ' ('.$this->_versionid.')';
	}
	*/



	/**
	 * This method delivers a special version String for the footer of the application
	 *
	 * @return string
	 */
	/*
	function getCopyright() {
		return '&copy; ' .$this->_copyyears;
	}
	*/

	/**
	 * Returns the complete revision string for detailed packaging information
	 *
	 * @return unknown
	 */
	/*
	function getRevision() {
		return '' .$this->_revision. ' (' .$this->_date. ')';
	}
	*/
}
