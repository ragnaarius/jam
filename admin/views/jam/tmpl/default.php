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
?>
<div class="container jam_info">
  	<h3>
		<?php echo JText::_('COM_JAM_WELCOME') . " " . $this->params->get('version'); ?>
	</h3>
	<p><?php echo JText::_('COM_JAM_MESSAGE'); ?></p>
  	<h4><?php echo JText::_('COM_JAM_TRANSLATIONS_BY'); ?></h4>
	<ul>
   		<li>Русский язык - Vladimir, JanRUmoN Team, Alexey Gubanov</li>
	</ul>
	<h4><?php echo JText::_('COM_JAM_DONATIONS'); ?></h4>
	<p><?php echo JText::_('COM_JAM_DONATE'); ?></p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="6KUBTAUA7N3GG">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/ru_RU/i/scr/pixel.gif" width="1" height="1">
	</form>
</div>