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

class JAMViewJAM extends JViewLegacy
{
    public function display($tpl = null)
    {
        $params = JComponentHelper::getParams('com_jam');

        $document = JFactory::getDocument();
        JHTML::stylesheet( 'media/com_jam/css/admin.style.css' ); 

        JToolBarHelper::title(JText::_('COM_JAM'), 'jam');
        JToolBarHelper::preferences('com_jam', '500', '500');

        $this->assignRef('params', $params);

        parent::display($tpl);
    }
}
?>
