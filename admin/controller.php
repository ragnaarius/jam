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

class JAMController extends JControllerLegacy
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
        $jinput = JFactory::getApplication()->input;
	    $jinput->set( 'view', 'jam' );
	    
		parent::display( $cachable, $urlparams );
	}
}
?>
