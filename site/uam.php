<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/* +RNJS+ */
/*
// if user hasn't permission, redirect to index.php
$user = JFactory::getUser();
if(!$user->get('id') || $user->usertype == 'Registered') {
	header('location: index.php');
}
*/
/* -RNJS- */

// Require the base controller
jimport('joomla.application.component.controller');

if (!function_exists('class_alias')) {
    function class_alias($original, $alias) {
        eval('class ' . $alias . ' extends ' . $original . ' {}');
    }
}

if (!class_exists('JControllerLegacy')) {
  class_alias('JController', 'JControllerLegacy');
} 

$controller = JControllerLegacy::getInstance('UAM');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();

?>