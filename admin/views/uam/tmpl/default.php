<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="container">
<h3>
	<?php echo JText::_('COM_UAM_WELCOME')." ".$this->params->get('version'); ?>
</h3>
<img class="uam_logo" src="/administrator/components/com_uam/assets/images/logouam.png" alt="<?php echo JText::_('COM_UAM'); ?>" />
<p><?php echo JText::_('COM_UAM_MESSAGE'); ?></p>
  <h4><?php echo JText::_('COM_UAM_TRANSLATIONS_BY'); ?></h4>
<ul>
   <li>Русский язык - Vladimir, JanRUmoN Team, Alexey Gubanov</li>
</ul>

<p><?php echo JText::_('COM_UAM_DONATE'); ?></p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="6KUBTAUA7N3GG">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/ru_RU/i/scr/pixel.gif" width="1" height="1">
</form>
</div>