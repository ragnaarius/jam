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

jimport( 'joomla.application.component.view' );
jimport('joomla.application.component.helper');

if (!function_exists('class_alias'))
{
    function class_alias($original, $alias)
	{
        eval('class ' . $alias . ' extends ' . $original . ' {}');
    }
}

if (!class_exists('JViewLegacy'))
{
  class_alias('JView', 'JViewLegacy');
} 

class UAMViewUAM extends JViewLegacy
{
	public function display($tpl = null)
	{
		$params = JComponentHelper::getParams('com_uam');

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'/components/com_uam/assets/css/style.css');

		JToolBarHelper::title(JText::_('User Article Manager'), 'uam');
		JToolBarHelper::preferences('com_uam', '500', '500');

		$this->assignRef('params', $params);

		parent::display($tpl);

	}
}
?>
