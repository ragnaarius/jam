<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

if (!function_exists('class_alias')) {
    function class_alias($original, $alias) {
        eval('class ' . $alias . ' extends ' . $original . ' {}');
    }
}

if (!class_exists('JControllerLegacy')) {
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
	public function display($cachable = false, $urlparams = array()) {
		JRequest::setVar( 'view', 'uam');
		parent::display($cachable, $urlparams);
	}
}
?>
