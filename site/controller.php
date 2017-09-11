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

class JAMController extends JControllerLegacy {   
    /**
     * Method to display the view
     * @access public
     */
    function display($cachable = false, $urlparams = array()) 
    {
        parent::display($cachable, $urlparams);
    }
    
    function edit() 
    {
        $uri = JFactory::getURI();
        $uri_query = $uri->getQuery();
        parse_str($uri_query, $uri_params);
        $uri_params['option'] = 'com_content';
        $new_query = $uri->buildQuery($uri_params);
        
        $this->setRedirect('index.php?' . $new_query);
    }
    
    function unPublish() 
    {
        $cid = JRequest::getInt('cid');
        $itemid = JRequest::getInt('Itemid');
        $user = JFactory::getUser();
        $params = JComponentHelper::getParams('com_jam');
        
        $jam_model = $this->getModel();
        $jam_table = $jam_model->getTable();
        $jam_table->load($cid);

        $asset	= 'com_content.article.'.$cid;
        // Check general edit permission first.
        $can_publish = $user->authorise('core.edit.state', $asset);
        // Check general edit permission first.
        $can_edit = $user->authorise('core.edit', $asset);
        // Now check if edit.own is available.
        $can_editOwn = $user->authorise('core.edit.own', $asset) && ($user->id == $jam_table->created_by);
        
        $override = false;
        
        if (($can_edit || $can_editOwn) 
			&& $params->get('user_can_publish'))
        {
            $override = true;
        }
        
        if ($can_publish || $override) 
        {
            $publica = false;
			
            if (is_object($jam_table) && $override && $jam_table->created_by == $user->id && !$can_publish) 
			{
                $publica = true;
            }
            elseif (is_object($jam_table) && $can_publish) 
			{
                $publica = true;
            }
            
            if ($publica) 
			{
                //change state to published or unpublished
				if ($jam_table->state == 0)
				{
					$jam_table->state = 1;
                  
					if ($jam_table->publish_up == '0000-00-00 00:00:00')
					{
                    	$jam_table->publish_up = JFactory::getDate()->toSql();
					}
					$message = JText::_('COM_JAM_MSG_PUBLISH_SUCCESSFULLY');
				}
				else 
				{
					$jam_table->state = 0;
                	$message = JText::_('COM_JAM_MSG_UNPUBLISH_SUCCESSFULLY');
				}
	
                $jam_table->save(array());
            }
        }
        $this->setRedirect('index.php?option=com_jam&view=jam&Itemid=' . $itemid);
        JFactory::getApplication()->enqueueMessage($message, 'message');
    }
    
    function unFeature() 
    {
        $cid = JRequest::getInt('cid');
        $itemid = JRequest::getInt('Itemid');
        $user = JFactory::getUser();
        $params = JComponentHelper::getParams('com_jam');
        
        $jam_model = $this->getModel();
        $jam_table = $jam_model->getTable();
        $jam_table->load($cid);
        
        $asset	= 'com_content.article.'.$cid;
        // Check general edit permission first.
        $can_publish = $user->authorise('core.edit.state', $asset);
        // Check general edit permission first.
        $can_edit = $user->authorise('core.edit', $asset);
        // Now check if edit.own is available.
        $can_editOwn = $user->authorise('core.edit.own', $asset) && ($user->id == $jam_table->created_by);
        
        $override = false;
        
        if (($can_edit || $can_editOwn) && $params->get('user_can_feature'))
        {
            $override = true;
        }
        
        if ($can_publish || $override) 
        {
            $feature = false;
            
            if (is_object($jam_table) && $override && $jam_table->created_by == $user->id && !$can_publish)
            {
                $feature = true;
            }
            elseif (is_object($jam_table) && $can_publish) 
            {
                $feature = true;
            }
            
            if ($feature) 
            {
				if ($jam_table->featured == 0)
				{
					$jam_table->featured = 1;
					$message = JText::_('COM_JAM_MSG_FEATURE_SUCCESSFULLY');
				}
				else 
				{
					$jam_table->featured = 0;
					$message = JText::_('COM_JAM_MSG_UNFEATURE_SUCCESSFULLY');
				}
                $jam_table->save(array());
            }
        }
        
        $this->setRedirect('index.php?option=com_jam&view=jam&Itemid=' . $itemid);
		JFactory::getApplication()->enqueueMessage($message, 'message');
    }
    
    function trash() 
    {
        $cid = JRequest::getInt('cid');
        $itemid = JRequest::getInt('Itemid');
        $user = JFactory::getUser();
        
        $jam_model = $this->getModel();
        $jam_table = $jam_model->getTable();
        $jam_table->load($cid);
        
        $asset	= 'com_content.article.'.$cid;
        // Check general edit permission first.
        $can_publish = $user->authorise('core.edit.state', $asset);
        // Check general edit permission first.
        $can_edit = $user->authorise('core.edit', $asset);
        // Now check if edit.own is available.
        $can_edit_own = $user->authorise('core.edit.own', $asset) && ($user->id == $jam_table->created_by);
        
        if (is_object($jam_table) 
            && ($can_edit || ($can_edit_own))) 
        {
            //change state
            if ($jam_table->state >= 0)
            {
                $jam_table->state = -2;
                $message = JText::_('COM_JAM_MSG_TRASH_SUCCESSFULLY');
            }
            else
            {
                $jam_table->state = 0;
                $message = JText::_('COM_JAM_MSG_RESTORE_SUCCESSFULLY');
            }
            $jam_table->save(array());
        }
        
        $this->setRedirect('index.php?option=com_jam&view=jam&Itemid=' . $itemid);
        JFactory::getApplication()->enqueueMessage($message, 'message');
    }
    
    function saveAlias() 
    {
        $user = JFactory::getUser();
        $cid = JRequest::getInt('id_article');
        
        $jam_model = $this->getModel();
        $jam_table = $jam_model->getTable();
        $jam_table->load($cid);
        
        $asset	= 'com_content.article.'.$cid;
        // Check general edit permission first.
        $can_publish = $user->authorise('core.edit.state', $asset);
        // Check general edit permission first.
        $can_edit = $user->authorise('core.edit', $asset);
        // Now check if edit.own is available.
        $can_edit_own = $user->authorise('core.edit.own', $asset) && ($user->id == $jam_table->created_by);
        
        if (is_object($jam_table) && ($can_edit || ($can_edit_own))) 
        {
            $jam_table->alias = JRequest::getString('alias');
            $jam_table->save(array());
            
            echo json_encode(array(
              'success' => true, 
              'message' => JText::_('COM_JAM_ALIAS_SAVED'))
			);
            jexit();
        }
        
        echo json_encode(array('success' => false));
        jexit();
    }
    
    function copy() 
    {
        $cid = JRequest::getInt('cid');
        $itemid = JRequest::getInt('Itemid');
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $params = JComponentHelper::getParams('com_jam');
        
        $jam_model = $this->getModel();
        $jam_table = $jam_model->getTable();
        $jam_table->load($cid);
        
        $asset	= 'com_content.article.'.$cid;
        // Check general edit permission first.
        $can_publish = $user->authorise('core.edit.state', $asset);
        // Check general edit permission first.
        $can_edit = $user->authorise('core.edit', $asset);
        // Now check if edit.own is available.
        $can_edit_own = $user->authorise('core.edit.own', $asset) && ($user->id == $jam_table->created_by);
        
        if(is_object($jam_table) && ($can_edit || $can_edit_own)) 
		{
            $jam_table->id = 0;
            $jam_table->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
			
            if ($params->get('copy_uses_todays_date')) 
			{
                $jam_table->created = JFactory::getDate()->toSql();
            }
            if ($params->get('copy_uses_current_user')) 
			{
                $jam_table->created_by = $user->id;
                $jam_table->created_by_alias = '';
            }
			
			$message = JText::_('COM_JAM_MSG_COPIED_SUCCESSFULLY');
            $jam_table->save(array());
        }
        $this->setRedirect('index.php?option=com_jam&view=jam&Itemid=' . $itemid);
		JFactory::getApplication()->enqueueMessage($message, 'message');
    }
}
?>
