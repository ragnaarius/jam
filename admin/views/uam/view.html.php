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

class UAMViewUAM extends JViewLegacy
{
    public function display($tpl = null)
    {
        $params = JComponentHelper::getParams('com_uam');

        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::base().'/components/com_uam/assets/css/style.css');

        JToolBarHelper::title(JText::_('COM_UAM'), 'uam');
        JToolBarHelper::preferences('com_uam', '500', '500');

        $this->assignRef('params', $params);

        parent::display($tpl);
    }
}
?>
