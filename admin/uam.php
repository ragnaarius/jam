<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('DIRECTORY_SEPARATOR'))
	define('DIRECTORY_SEPARATOR', DS);

// Require the base controller
require_once (JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php');

// Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
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
