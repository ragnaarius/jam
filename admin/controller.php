<?php
/**
 * @version     0.20
 * @package     com_juam
 * @copyright   Copyright (C) 2017. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Felipe Quinto Busanello, Rob Sykes, Alexey Gubanov
 * @link        https://github.com/ragnaarius/juam
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

if (!function_exists('class_alias'))
{
    function class_alias($original, $alias)
	{
        eval('class ' . $alias . ' extends ' . $original . ' {}');
    }
}

if (!class_exists('JControllerLegacy'))
{
  class_alias('JController', 'JControllerLegacy');
} 

class UAMController extends JControllerLegacy
{
	/**
	 * Custom Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to display the view
	 * @access public
	 */
	public function display($cachable = false, $urlparams = array())
	{
		JRequest::setVar( 'view', 'uam');
		parent::display($cachable, $urlparams);
	}
}
?>
