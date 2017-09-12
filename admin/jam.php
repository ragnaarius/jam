<?php
/**
 * @version     1.0
 * @package     com_jam
 * @copyright   Copyright (C) 2017. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Felipe Quinto Busanello (FUAL), Rob Sykes (UAM), Alexey Gubanov
 * @link        https://github.com/ragnaarius/jam
 */
// No direct access
defined('_JEXEC') or die();

// Create the controller
$controller = JControllerLegacy::getInstance( 'jam' );

// Perform the Request task
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );

// Redirect if set by the controller
$controller->redirect();

?>
