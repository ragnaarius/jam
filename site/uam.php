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

// Get an instance of the controller
$controller = JControllerLegacy::getInstance('UAM');

// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
?>