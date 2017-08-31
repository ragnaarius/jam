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

// Require the base controller
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

$controller = JControllerLegacy::getInstance('UAM');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();

?>