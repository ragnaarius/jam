<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');

if (!function_exists('class_alias')) {
    function class_alias($original, $alias) {
        eval('class ' . $alias . ' extends ' . $original . ' {}');
    }
}

if (!class_exists('JControllerLegacy')) {
  class_alias('JController', 'JControllerLegacy');
} 

class UAMController extends JControllerLegacy {

	/**
	 * Method to display the view
	 * @access public
	 */
	function display($cachable = false, $urlparams = array()) {
		parent::display($cachable, $urlparams);
	}
	
	function edit() {
		$uri = JFactory::getURI();
		$uri_query = $uri->getQuery();
		parse_str($uri_query, $uri_params);
		$uri_params['option'] = 'com_content';
		$new_query = $uri->buildQuery($uri_params);
		
		$this->setRedirect('index.php?' . $new_query);
	}
	
	function unPublish() {
		$cid = JRequest::getInt('cid');
		$itemid = JRequest::getInt('Itemid');
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams('com_uam');

		$uam_model = $this->getModel();
		$uam_table = $uam_model->getTable();
		$uam_table->load($cid);

		$asset	= 'com_content.article.'.$cid;
		// Check general edit permission first.
		$can_publish = $user->authorise('core.edit.state', $asset);
		// Check general edit permission first.
		$can_edit = $user->authorise('core.edit', $asset);
		// Now check if edit.own is available.
		$can_editOwn = $user->authorise('core.edit.own', $asset) && ($user->id == $uam_table->created_by);

		$override = false;
		if(($can_edit || $can_editOwn) && $params->get('user_can_publish'))
		{
			$override = true;
		}

		if($can_publish || $override) {

			$publica = false;
			if(is_object($uam_table) && $override && $uam_table->created_by == $user->id && !$can_publish) {
				$publica = true;
			}
			elseif(is_object($uam_table) && $can_publish) {
				$publica = true;
			}

			if($publica) {
				//change state to published or unpublished
				$uam_table->state = ($uam_table->state == 0) ? 1 : 0;
				$uam_table->save(array());
			}
		}
		
		$this->setRedirect("index.php?option=com_uam&view=uam&Itemid=$itemid");
	}

	function unFeature() {
		$cid = JRequest::getInt('cid');
		$itemid = JRequest::getInt('Itemid');
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams('com_uam');

		$uam_model = $this->getModel();
		$uam_table = $uam_model->getTable();
		$uam_table->load($cid);

		$asset	= 'com_content.article.'.$cid;
		// Check general edit permission first.
		$can_publish = $user->authorise('core.edit.state', $asset);
		// Check general edit permission first.
		$can_edit = $user->authorise('core.edit', $asset);
		// Now check if edit.own is available.
		$can_editOwn = $user->authorise('core.edit.own', $asset) && ($user->id == $uam_table->created_by);

		$override = false;
		if(($can_edit || $can_editOwn) && $params->get('user_can_feature'))
		{
			$override = true;
		}

		if($can_publish || $override) {

			$feature = false;
			if(is_object($uam_table) && $override && $uam_table->created_by == $user->id && !$can_publish) {
				$feature = true;
			}
			elseif(is_object($uam_table) && $can_publish) {
				$feature = true;
			}

			if($feature) {
				$uam_model->featured($cid, ($uam_table->featured == 0) ? 1 : 0);
			}
		}
		
		$this->setRedirect("index.php?option=com_uam&view=uam&Itemid=$itemid");
	}

	function trash() {
		$cid = JRequest::getInt('cid');
		$itemid = JRequest::getInt('Itemid');
		$user = JFactory::getUser();

		$uam_model = $this->getModel();
		$uam_table = $uam_model->getTable();
		$uam_table->load($cid);
		
		$asset	= 'com_content.article.'.$cid;
		// Check general edit permission first.
		$can_publish = $user->authorise('core.edit.state', $asset);
		// Check general edit permission first.
		$can_edit = $user->authorise('core.edit', $asset);
		// Now check if edit.own is available.
		$can_edit_own = $user->authorise('core.edit.own', $asset) && ($user->id == $uam_table->created_by);

		if(is_object($uam_table) && ($can_edit || ($can_edit_own))) {
			//change state
			$uam_table->state = ($uam_table->state >= 0) ? -2 : 0;
			$uam_table->save(array());
		}
		
		$this->setRedirect("index.php?option=com_uam&view=uam&Itemid=$itemid");
	}

	function saveAlias() {
		$user = JFactory::getUser();
		$cid = JRequest::getInt('id_article');

		$uam_model = $this->getModel();
		$uam_table = $uam_model->getTable();
		$uam_table->load($cid);

		$asset	= 'com_content.article.'.$cid;
		// Check general edit permission first.
		$can_publish = $user->authorise('core.edit.state', $asset);
		// Check general edit permission first.
		$can_edit = $user->authorise('core.edit', $asset);
		// Now check if edit.own is available.
		$can_edit_own = $user->authorise('core.edit.own', $asset) && ($user->id == $uam_table->created_by);

		if(is_object($uam_table) && ($can_edit || ($can_edit_own))) {
			$uam_table->alias = JRequest::getString('alias');
			$uam_table->save(array());
			
			echo 'ok';
			jexit();
		}

		echo 'error';
		jexit();
	}
	
	function copy() {
		$cid = JRequest::getInt('cid');
		$itemid = JRequest::getInt('Itemid');
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams('com_uam');
		
		$uam_model = $this->getModel();
		$uam_table = $uam_model->getTable();
		$uam_table->load($cid);

		$asset	= 'com_content.article.'.$cid;
		// Check general edit permission first.
		$can_publish = $user->authorise('core.edit.state', $asset);
		// Check general edit permission first.
		$can_edit = $user->authorise('core.edit', $asset);
		// Now check if edit.own is available.
		$can_edit_own = $user->authorise('core.edit.own', $asset) && ($user->id == $uam_table->created_by);
		
		if(is_object($uam_table) && ($can_edit || $can_edit_own)) {
			$uam_table->id = 0;
			$uam_table->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
			if ($params->get('copy_uses_todays_date')) {
				$uam_table->created = JFactory::getDate()->format('Y-m-d-H-i-s');
			}
			if ($params->get('copy_uses_current_user')) {
				$uam_table->created_by = $user->id;
				$uam_table->created_by_alias = '';
			}
			$uam_table->save(array());
		}
		$this->setRedirect("index.php?option=com_uam&view=uam&Itemid=$itemid");
	}
}
?>
