<?php
/**
* @version		2.1.0
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

defined('JPATH_BASE') or die;
/**
*/
require_once(dirname(__FILE__).DS.'extension.php');


abstract class PagesAndItemsExtensionManager extends PagesAndItemsExtension
{
	public $version = '';//

	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		jimport('joomla.filesystem.folder');
		$folder = realpath(dirname(__FILE__).'..'.DS.'..'.DS.'..'.DS);
		$files = JFolder::files($folder,'.xml',false,true);
		if(count($files))
		{
			foreach($files as $file)
			{
				$xml = simplexml_load_file($file);
				if ($xml)
				{
					if ( is_object($xml) && is_object($xml->install))
					{
						//ok we have the install file
						//we will get the version
						$element = (string)$xml->version;
						$this->version = $element ? $element : '';
					}
				}
			}
		}
	}


}
