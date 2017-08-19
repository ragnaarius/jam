<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );
jimport('joomla.application.component.helper');

if (!function_exists('class_alias')) {
    function class_alias($original, $alias) {
        eval('class ' . $alias . ' extends ' . $original . ' {}');
    }
}

if (!class_exists('JViewLegacy')) {
  class_alias('JView', 'JViewLegacy');
} 

class UAMViewUAM extends JViewLegacy
{
	public function display($tpl = null){
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
