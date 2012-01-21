<?php
/**
* @version		2.1.1
* @package		PagesAndItems com_pagesanditems
* @copyright	Copyright (C) 2006-2012 Carsten Engel. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author		www.pages-and-items.com
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Extension table
 */
class PagesAndItemsTablePiExtension extends JTable
{
	var $extension_id = null;
	var $name = null;	//
	var $type = null;
	var $element = null; //???
	var $folder = null;
	var $version = null;	//$version = 'integrated': the extension can not uninstall,
	var $required_version = null;
	var $description = null;
	var $client_id = null;
	var $enabled = 1;
	//var $published = 1;
	var $access = 0;
	var $protected = null; //$protected = 1: the extension can not uninstall and not enabled=0
	var $manifest_cache = null;
	var $params = null;
	var $custom_data = null; //??
	var $system_data = null; //??
	var $checked_out = null;
	var $checked_out_time = null;
	var $ordering = null;
	var $state = null;


	/**
	 * Contructor
	 *
	 * @access var
	 * @param database A database connector object
	 */

	function __construct(&$db)
	{
		parent::__construct('#__pi_extensions', 'extension_id', $db);


	}

	function getNextTypeId( $where=null , $type =null)
	{
		if ($where === null)
		{
			return false;
		}
		if ($type === null)
		{
			return false;
		}
		// Get the largest  value for a given where clause.
		$query = $this->_db->getQuery(true);
		$query->select('MAX(extension_id)');
		$query->from($this->_tbl);
		$query->where($where);
		$this->_db->setQuery($query);
		$max = (int) $this->_db->loadResult();
		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$e = new JException(
				JText::sprintf('JLIB_DATABASE_ERROR_GET_NEXT_ORDER_FAILED', get_class($this), $this->_db->getErrorMsg())
			);
			$this->setError($e);

			return false;
		}

		if($max == 0 || $max === null)
		{
			/*
			based of the type we need an bas id

			*/
			switch($type)
			{
				case 'fieldtype':
				case 'fieldtypes':
				return '1';
				break;

				case 'itemtype':
				case 'itemtypes':
				return '1000';
				break;

				case 'pagetype':
				case 'pagetypes':
				return '2000';
				break;


				case 'html':
				case 'htmls':
				return '3000';
				break;

				case 'manager':
				case 'managers':
				return '4000';
				break;

				case 'language':
				case 'languagess':
				return '5000';
				break;
			}

		}
		// Return the largest extension_id value + 1.
		return ($max + 1);
	}

	function loadType( $type=null )
	{
		if ($type === null)
		{
			return false;
		}
		$this->reset();

		$db =& $this->getDBO();


		$query = 'SELECT *'
		. ' FROM '.$this->_tbl
		. ' WHERE '.$this->type.' = '.$db->Quote($type);
		$db->setQuery( $query );

		if ($result = $db->loadAssoc( ))
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}


	/**
	* Overloaded check function
	*
	* @access public
	* @return boolean True if the object is ok
	*/
	function check()
	{
		// check for valid name
		if (trim($this->name) == '' || trim($this->element) == '')
		{
			$this->setError(JText::sprintf('MUST_CONTAIN_A_TITLE', JText::_('Extension')));
			return false;
		}
		return true;
	}

	function bind($array, $ignore = '')
	{
		//parent::bind($array, $ignore);

		if (is_array($array))
		{

			if (isset( $array['params']) )
			{
				if(is_array($array['params']) || is_object($array['params']))
				{
					$array['params'] = json_encode($array['params']);
				}
				else
				{
				
				}
			}
		}
		/*
		elseif(is_array($array))
		{
			
		}
		*/
		
		return parent::bind($array, $ignore);
	}
}
