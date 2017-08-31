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

if (!defined('DIRECTORY_SEPARATOR'))
{
	define('DIRECTORY_SEPARATOR', DS);
}

// Require the base controller
require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php');

// Require specific controller if requested
if($controller = JRequest::getVar('controller')) 
{
	require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controller.'.php');
}

// Create the controller
$classname = 'UAMController' . $controller;
$controller = new $classname();

// Perform the Request task
$controller->execute(JRequest::getVar('task', null, 'default', 'cmd'));

// Redirect if set by the controller
$controller->redirect();

?>
