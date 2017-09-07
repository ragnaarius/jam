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
 
/**
 * Script file of HelloWorld component
 */
class com_uamInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		// $parent is the class calling this method
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		// $parent is the class calling this method
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		// $parent is the class calling this method
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)

        $this->release = $parent->get( "manifest" )->version;

		// These are the option defaults in JSON form
		$defaults = '{';
		$defaults .= '"version":"' . $this->release . '",';
		$defaults .= '"new_article_button":"1",';
		$defaults .= '"new_article_button_custom":"0",';
		$defaults .= '"new_article_button_text":"New article",';
		$defaults .= '"link_new_article_default":"1",';
		$defaults .= '"link_new_article":"",';
		$defaults .= '"copy_uses_todays_date":"0",';
		$defaults .= '"copy_uses_current_user":"0",';
		$defaults .= '"showsearchfilter":"0",';
		$defaults .= '"showcategoryfilter":"1",';
		$defaults .= '"showauthorfilter":"1",';
		$defaults .= '"showpublishedstatefilter":"1",';
		$defaults .= '"showlanguagefilter":"0",';
		$defaults .= '"id_column":"1",';
		$defaults .= '"title_column":"1",';
		$defaults .= '"title_link":"1",';
		$defaults .= '"show_content":"0",';
		$defaults .= '"published_column":"1",';
		$defaults .= '"allow_subcategories":"0",';
		$defaults .= '"restrict_to_category":"0",';
		$defaults .= '"featured_column":"0",';
		$defaults .= '"category_column":"1",';
		$defaults .= '"category_in_title":"0",';
		$defaults .= '"author_column":"1",';
		$defaults .= '"show_alias":"1",';
		$defaults .= '"language_column":"0",';
		$defaults .= '"created_date_column":"1",';
		$defaults .= '"start_publishing_column":"0",';
		$defaults .= '"finish_publishing_column":"0",';
		$defaults .= '"hits_column":"1",';
		$defaults .= '"edit_alias_column":"0",';
		$defaults .= '"copy_column":"0",';
		$defaults .= '"edit_column":"1",';
		$defaults .= '"trash_column":"1",';
		$defaults .= '"user_can_publish":"0",';
		$defaults .= '"user_can_feature":"0",';
		$defaults .= '"user_can_trash":"0",';
		$defaults .= '"user_can_view":"0",';
        $defaults .= '"user_can_editpublished":"0"';
		$defaults .= '}';

		if ($type == 'install') 
		{
			$db = JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->update('#__extensions');
			$query->set("params = " . "'".$defaults."'");
			$query->where("name = 'com_uam'");
			$db->setQuery($query);
			$db->query();
		}
		else if ($type == 'update') 
		{
			$db = JFactory::getDBO();
			$query	= $db->getQuery(true);

			$query->select('params');
			$query->from('#__extensions');
			$query->where("name = 'com_uam'");
			$db->setQuery($query);
			$saved = $db->loadAssoc();
			$old = json_decode($saved['params'], true);
			$new = json_decode($defaults, true);

			// If options already exist, keep the old ones (except for version number)
			if ($old) 
			{
				$old = array_merge($old, array("version" => "$this->release"));
				$new = array_merge($new, $old);
				$options = json_encode($new);
			}
			else 
			{
				$options = $defaults;
			}

			$query->update('#__extensions');
			$query->set("params = " . "'".$options."'");
			$query->where("name = 'com_uam'");
			$db->setQuery($query);
			$db->query();
		}
	}
}