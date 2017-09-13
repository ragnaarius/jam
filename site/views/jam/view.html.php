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
    function display($tpl = null) {
        $app = JFactory::getApplication();
        $apparams = $app->getParams('com_jam');
        $user = JFactory::getUser();
        $uri = JFactory::getURI();
            
        // get menu parameters and merge it with component params 
        $menuparams = new JRegistry;
        if ($menu = $app->getMenu()->getActive())
        {
            $menuparams->loadString($menu->params);
        }
            
        $params = clone $apparams;
        $params->merge($menuparams);
            
        // Require the com_content helper library
        require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');

        //load frameworks for right sequence in the page header
        JHtml::_('jquery.framework', true, true);
        JHtml::_('bootstrap.framework', true, true);
			
        //load stylesheet and javascript
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::base(true).'/media/com_jam/css/style.css');
        $document->addScript(JURI::base(true).'/media/com_jam/js/script.js');
        $document->addScript(JURI::base(true).'/media/com_jam/js/confirm-bootstrap.js');
 
        // Get data from the model
        $itens = $this->get('Data');
        $total = $this->get('Total');
        $pagination = $this->get('Pagination');
        $access = new stdClass();
        $canEditOwnOnly = $this->_canEditOwnOnly();
            
        $lists = $this->_getLists();
            
        $this->assign('action', str_replace('&', '&amp;', $uri->toString()));
        $this->assignRef('params', $params);
        $this->assignRef('itens', $itens);
        $this->assignRef('lists', $lists);
        $this->assignRef('access', $access);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('user', $user);
        $this->assignRef('canEditOwnOnly', $canEditOwnOnly);
            
        $this->_prepareDocument();
            
        parent::display($tpl);
    }
        
        /**
         * Prepares the document
         *
         * @return  void
         */
        protected function _prepareDocument()
        {
            $app   = JFactory::getApplication();
            $menus = $app->getMenu();
            $title = null;
            
            // Because the application sets a default page title,
            // we need to get it from the menu item itself
            $menu = $menus->getActive();
            
            if ($menu)
            {
                $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
            }
            else
            {
                $this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
            }
            
            $title = $this->params->get('page_title', '');
            
            if (empty($title))
            {
                $title = $app->get('sitename');
            }
            elseif ($app->get('sitename_pagetitles', 0) == 1)
            {
                $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
            }
            elseif ($app->get('sitename_pagetitles', 0) == 2)
            {
                $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
            }
            
            $this->document->setTitle($title);
            
            if ($this->params->get('menu-meta_description'))
            {
                $this->document->setDescription($this->params->get('menu-meta_description'));
            }
            
            if ($this->params->get('menu-meta_keywords'))
            {
                $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
            }
            
            if ($this->params->get('robots'))
            {
                $this->document->setMetadata('robots', $this->params->get('robots'));
            }
        }
        
        function &getItem($index = 0, &$params) {
            $item =& $this->itens[$index];
            $item->text = $item->introtext;
            
            // Get the page/component configuration and article parameters
            $item->params = clone($params);
            if (class_exists('JRegistry')) {
                $aparams = new JRegistry();
                $aparams->loadString($item->attribs); // this should be json
                
            } else {
                $aparams = new JParameter($item->attribs);
            }
            
            // Merge article parameters into the page configuration
            $item->params->merge($aparams);
            
            return $item;
        }
        
        function _canEditOwnOnly() 
		{
            // get list of categories and check edit capability;
            
            $c = JHtml::_('category.options', 'com_content');
            
            // Remove those categories the user can't see
            $user = JFactory::getUser();
			
            foreach($c as $i => $option)
            {
                if ($user->authorise('core.edit', 'com_content.category.'.$option->value) == true ) 
				{
                    return false;
                    break;
                }
            }
            return true;
        }
        
        function _getLists() 
		{
            $app =  JFactory::getApplication();
            $option = $app->input->get('option');
            $params = $app->getParams($option);
            
            // Initialize variables
            $db = JFactory::getDBO();
            
            // Get some variables from the request
            $filter_order = $app->getUserStateFromRequest($option.'filter_order', 'filter_order', 'c.id', 'cmd');
            $filter_order_Dir = $app->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', '', 'word');
            $filter_state = $app->getUserStateFromRequest($option.'filter_state', 'filter_state', '', 'word');
            $filter_catid = $app->getUserStateFromRequest($option.'filter_catid', 'filter_catid', -1, 'int');
            $filter_langid = $app->getUserStateFromRequest($option.'filter_langid', 'filter_langid', '', 'string');
            $filter_authorid = $app->getUserStateFromRequest($option.'filter_authorid', 'filter_authorid', 0, 'int');
            $search = $app->getUserStateFromRequest($option.'filter_search', 'filter_search', '', 'string');
            $search = JString::strtolower($search);
            
            if ($params->get('useallcategories') == 1) 
			{
                // get list of categories for dropdown filter
                $c = JHtml::_('category.options', 'com_content');
            }
            else 
			{
                $query = "SELECT a.id as value, a.title as text FROM #__categories AS a WHERE a.parent_id > 0 AND
						extension = 'com_content' AND
						a.published = 1 AND
						a.lft >= (SELECT b.lft FROM #__categories b WHERE b.id = ".$params->get('mycategory'). ") AND
						a.rgt <= (SELECT c.rgt FROM #__categories c WHERE c.id = ".$params->get('mycategory'). ")";
                
                $db =  JFactory::getDBO();
                $db->setQuery($query);
                $c = $db->loadObjectList();
            }
            
            // Remove those categories the user can't see
            $user = JFactory::getUser();
			
            foreach($c as $i => $option)
            {
                // To take save or create in a category you need to have create rights for that category
                // unless the item is already in that category.
                // Unset the option if the user isn't authorised for it. In this field assets are always categories.
                if ($user->authorise('core.create', 'com_content.category.'.$option->value) != true ) 
				{
                    unset($c[$i]);
                }
            }
            
            $cats[] = JHtml::_('select.option', '0', '- '.JText::_('COM_JAM_SELECT_CATEGORY').' -', 'value', 'text');
            $cats = array_merge($cats, $c);
            $lists['catid'] = JHTML::_('select.genericlist',  $cats, 'filter_catid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_catid);
            $lists['filter_catid'] = $filter_catid;
            
            // get list of Authors for dropdown filter
            
            if (isset($l))
                unset($l);
                
                if (count($c) > 0) 
				{
                    $l = '';
                    // Convert into "(id1, id2...)" for the query
                    foreach (array_values($c) as $k)
                        // $k is a JObject with ->value = category id
                        $l .= $k->value .', ';
                        $l = '(' . strrev(substr(strrev($l), 2)) . ')';
                }
                
                $query = 'SELECT c.created_by, u.name' .
                    ' FROM #__content AS c' .
                    ' LEFT JOIN #__users AS u ON u.id = c.created_by' .
                    ' WHERE (c.state <> -1' .
                    ' AND c.state <> -2)';
                
                if ($filter_catid > 0) 
				{
                    $query .= ' AND (c.catid = '.$db->Quote($filter_catid) . ')';
                }
                else if (isset($l)) 
				{
                    $query .= ' AND (c.catid in ' . $l . ')';
                }
                else $query .= ' AND 0';	// Can't see any categories so can't see any authors
                
                $query .= ' GROUP BY u.name ORDER BY u.name';
                
                $authors[] = JHTML::_('select.option', '0', '- '.JText::_('COM_JAM_SELECT_AUTHOR').' -', 'created_by', 'name');
                $db->setQuery($query);
                $authors = array_merge($authors, $db->loadObjectList());
                $lists['authorid'] = JHTML::_('select.genericlist',  $authors, 'filter_authorid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'created_by', 'name', $filter_authorid);
                
                // table ordering
                $lists['order_Dir'] = $filter_order_Dir;
                $lists['order'] = $filter_order;
                
                // search filter
                $lists['filter_search'] = $search;
                
                // state filter
                $states = array();
                $states[] = JHTML::_('select.option', '', JText::_('JOPTION_SELECT_PUBLISHED'), 'value', 'text');
                $states[] = JHTML::_('select.option', 'P', JText::_('JPUBLISHED'), 'value', 'text');
                $states[] = JHTML::_('select.option', 'U', JText::_('JUNPUBLISHED'), 'value', 'text');
                $lists['state'] = JHTML::_('select.genericlist',  $states, 'filter_state', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_state);
                
                $l = JHtml::_('contentlanguage.existing', true, true);
                $langs[] = JHtml::_('select.option', '', JText::_('JOPTION_SELECT_LANGUAGE'), 'value', 'text');
                $langs = array_merge($langs, $l);
                $lists['langs'] = JHTML::_('select.genericlist',  $langs, 'filter_langid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $filter_langid);
                
                return $lists;
        }
        
        function filterCategory($query, $active = NULL) 
        {
            // Initialize variables
            $db =  JFactory::getDBO();
            
            $categories[] = JHTML::_('select.option', '0', '- '.JText::_('COM_JAM_SELECT_CATEGORY').' -');
            $db->setQuery($query);
            
            $categories = array_merge($categories, $db->loadObjectList());
            
            $category = JHTML::_('select.genericlist',  $categories, 'filter_catid', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', $active);
            
            return $category;
        }
        
        /**
         * Method to get Title.
         *
         * @return  array
         *
         */
        function getTitle ($article, $params, $access, $attribs = array()) 
        {
            $txt = htmlentities($article->introtext . $article->fulltext, ENT_COMPAT, "UTF-8");
            $link = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catslug));
            $linked = false;
			$checkout = false;
			$class = "";
			$tooltip = "";
            
            $user = JFactory::getUser();
            
            // Special state for dates
            if ($article->publish_up || $article->publish_down)
            {
                $nullDate = JFActory::getDBO()->getNullDate();
                $nowDate = JFactory::getDate()->toUnix();
                
                $tz = new DateTimeZone(JFactory::getUser()->getParam('timezone', JFactory::getConfig()->get('offset')));
                
                $publish_up = ($article->publish_up != $nullDate) ? JFactory::getDate($article->publish_up, 'UTC')->setTimeZone($tz) : false;
                $publish_down = ($article->publish_down != $nullDate) ? JFactory::getDate($article->publish_down, 'UTC')->setTimeZone($tz) : false;
                
                if ($article->state == 1) 
				{
                    if ($publish_up 
						&& $nowDate < $publish_up->toUnix()) 
					{
                        $linked = false;
                    }
                    else if ($publish_down 
						&& $nowDate > $publish_down->toUnix()) 
					{
                        $linked = false;
                    }
                    else 
					{
                        $linked = true;
                    }
                }
                else 
				{
                    $linked = false;
                }
            }
            else 
			{
                $linked = ($article->state > 0) ? true : false;
            }
            
            /* Link setting is overridden by backend or menu options */
            $linked = $params->get('title_link');
            
            if ($params->get('show_content')) 
            {
				$tooltip = JText::_( $article->title ) . "<br />" . $txt;
				$class = "hasTooltip";
            }
			
			if ($article->checked_out > 0 
				&& $article->checked_out != $user->get('id')) 
			{
                $checkoutUser = JFactory::getUser($article->checked_out);
                $date = JHTML::_('date',$article->checked_out_time);
                $tooltip = JText::_('COM_JAM_CHECKED_OUT') . "<br />" . $checkoutUser->name . "<br />" . $date;
                $class = "hasTooltip";
                
				$checkout = true;
				$linked = false;
			}
			
			$output = array(
				'link' => $link,
				'title' => $article->title,
				'class' => $class,
				'tooltip' => $tooltip,
				'linked' => $linked,
				'checkout' => $checkout
			);
			
			return $output;
        }
        
        /**
         * Method to get New Article button.
         *
         * @return  array
         *
         */
        function getNewArticleButton ($params)
        {
            //default link
            if ($params->get('link_new_article_default') 
                || (!$params->get('link_new_article_default') 
                && strlen($custom_link) == 0)) 
            {
                $app = JFactory::getApplication();
                $itemid =  $app->getMenu()->getActive()->id;
                $uri = JFactory::getURI();
                
                if (($params->get('useallcategories') == 0) 
                    && ($params->get('restrict_to_category') == 1)) 
                {
                    $catid = "&catid=" . $params->get('mycategory');
                }
                else 
                {
                    $catid = "";
                }
                if ($params->get('utf8_url_fix') ) 
                {
                    $ret = base64_encode(urlencode($uri->toString()));
                }
                else 
                {
                    $ret = base64_encode($uri->toString());
                }
                $url = "index.php?option=com_content&task=article.add&Itemid=" . $itemid . $catid . "&return=" . $ret;
                $link = JRoute::_($url);
            }
            
            //custom link
            else 
            {
                $link = trim($params->get('link_new_article'));
            }
            if ($params->get('new_article_button_custom')) 
            {
                $button_text = $params->get('new_article_button_text');
            }
            else 
            {
                $button_text = JText::_('COM_JAM_NEW_ARTICLE');
            }
            
            $output = array(
                'link' => $link,
                'text' => $button_text
            );
            
            return $output;
        }
        
        /**
         * Method to get Edit article menuitem.
         *
         * @return  array
         *
         */
        function getEdit($article, $params, $access, $attribs = array())
        {
            $user = JFactory::getUser();
            $uri = JFactory::getURI();
            $ret = $uri->toString();
			
			$icon = "icon-edit";
			$item_txt = JText::_( 'COM_JAM_EDIT' );
			$class = "";
            
			if ($article->state != -2) 
			{
				if (($access->canEdit) 
					|| ($params->get('user_can_editpublished') && ($access->canEdit || ($access->canEditOwn && ($article->created_by == $user->get('id'))))
					|| (!$params->get('user_can_editpublished') && $article->state != 1 && $access->canEditOwn && ($article->created_by == $user->get('id'))))) 
				{
					$app = JFactory::getApplication();
					$itemid =  $app->getMenu()->getActive()->id;
                        
					if ($params->get('utf8_url_fix') ) 
					{
						$url = "index.php?option=com_content&task=article.edit&a_id=" . $article->id. "&Itemid=" . $itemid. "&return=" . base64_encode(urlencode($ret));
					} 
					else 
					{
						$url = "index.php?option=com_content&task=article.edit&a_id=" . $article->id . "&Itemid=" . $itemid. "&return=" .base64_encode($ret);
					}
                    
					$link = JRoute::_($url);
				}
			}
			else 
			{
				$link = "#";
				$class = "disabled";
			}
			
			if ($article->checked_out > 0 
                && $article->checked_out != $user->get('id')) 
            {
				$class = "disabled";
				$icon = "icon-lock";
				$link = "#";
				$item_txt = JText::_('COM_JAM_CHECKED_OUT');
			}
			
			$output = array(
                'link' => $link,
                'icon' => $icon,
                'item_txt' => $item_txt,
                'class' => $class			
			);
			
            return $output;
        }
        
        /**
         * Method to get Copy article menuitem.
         *
         * @return  array
         *
         */
        function getCopy($article, $params, $access)
        {
            $app = JFactory::getApplication();
            $itemid = $app->getMenu()->getActive()->id;
            
            $user = JFactory::getUser();
            $class = "";
            $item_txt = JText::_('COM_JAM_CREATE_A_COPY', true);
            
            if ($article->state != -2) 
            {
                $url = "index.php?option=com_jam&controller=&task=copy&cid=" . $article->id . "&Itemid=" . $itemid;
                $link = JRoute::_($url);
                
                $msg_confirm = JText::_('COM_JAM_WOULD_YOU_LIKE_TO_CREATE_AN_ARTICLE_COPY', true);
            }
            else
            {
                $link = "#";
                $class = "disabled";
                $msg_confirm = "";
                
            }
            
            $output = array(
                'item_txt' => $item_txt,
                'class' => $class,
                'msg_confirm' => $msg_confirm,
                'link' => $link,

            );
            
            return $output;
        }

        /**
         * Method to get Edit alias menuitem.
         *
         * @return  array
         *
         */
        function getEditAlias($article, $params, $access)
        {
            $user = JFactory::getUser();
            $class = "";
            $article_id = "";
            $item_txt = JText::_('COM_JAM_EDIT_ALIAS');
            
            if ($article->state != -2 
                && ($access->canEdit || $access->canEditOwn)) 
            {
                $article_id = $article->id;
            }
            else 
            {
                $class = "disabled";
            }
            
            $output = array(
                'item_txt' => $item_txt,
                'article_id' => $article_id,
                'class' => $class,
            );
            
            return $output;
        }
        
        /**
         * Method to get Feature menuitem and button.
         *
         * @return  array
         *
         */
        function getFeatured($article, $params, $access, $attribs = array())
        {
            $app = JFactory::getApplication();
            $itemid = $app->getMenu()->getActive()->id;
            
            $user = JFactory::getUser();
            $override = false;
            $class = "";
            
            if ($article->featured == 1)
            {
                if ($attribs == "menuitem")
                {
                    $icon = "icon-unfeatured";
                }
                else
                {
                    $class = "active";
                    $icon = "icon-featured";
                }
                $title = JText::_('COM_JAM_TOOLTIP_FEATURED');
                $item_txt = JText::_('COM_JAM_UNFEATURE');
            }
            else
            {
                if ($attribs == "menuitem")
                {
                    $icon = "icon-featured";
                }
                else 
                {
                    $icon = "icon-unfeatured";
                }
                $title = JText::_('COM_JAM_TOOLTIP_NOT_FEATURED');
                $item_txt = JText::_('COM_JAM_FEATURE');
            }
            
            
            if (($access->canEdit || $access->canEditOwn)
                && $params->get('user_can_feature'))
            {
                $override = true;
            }
            if (($access->canPublish && $article->state != -2)
                || ($user->id == $article->created_by && $override))
            {
                $url = "index.php?option=com_jam&view=jam&task=unFeature&cid=" . $article->id . "&Itemid=" . $itemid;
                $link = JRoute::_($url);
            }
            else
            {
                $link = "#";
                $class = "disabled";
            }
            $output = array(
                'link' => $link,
                'icon' => $icon,
                'item_txt' => $item_txt,
                'title' => $title,
                'class' => $class
            );
            
            return $output;
        }
        
        /**
         * Method to get Publish menuitem and button.
         *
         * @return  array
         *
         */
        function getPublished($article, $params, $access, $attribs = array())
        {
            $app = JFactory::getApplication();
            $itemid = $app->getMenu()->getActive()->id;
            
            $user = JFactory::getUser();
            $override = false;
            $class = "";
            
            // Special state for dates
            $nullDate = JFActory::getDBO()->getNullDate();
            $nowDate = JFactory::getDate()->toUnix();
                
            $tz = new DateTimeZone(JFactory::getUser()->getParam('timezone', JFactory::getConfig()->get('offset')));
                
            $publish_up = ($article->publish_up != $nullDate) ? JFactory::getDate($article->publish_up, 'UTC')->setTimeZone($tz) : false;
            $publish_down = ($article->publish_down != $nullDate) ? JFactory::getDate($article->publish_down, 'UTC')->setTimeZone($tz) : false;
                
            if ($article->state == 1)
            {
                if ($attribs == "button")
                {
                    if ($publish_up && $nowDate < $publish_up->toUnix())
                    {
                        $icon = "icon-pending";
                        $title = JText::_('JLIB_HTML_PUBLISHED_PENDING_ITEM');
                    }
                    else if ($publish_down && $nowDate > $publish_down->toUnix())
                    {
                        $icon = "icon-expired";
                        $title = JText::_('JLIB_HTML_PUBLISHED_EXPIRED_ITEM');
                    }
                    else
                    {
                        $icon = "icon-publish";
                        $title = JText::_('COM_JAM_TOOLTIP_PUBLISHED');
                    }
                    $class = "active";
                    $item_txt = "";
                }
                else
                {
                    $icon = "icon-unpublish";
                    $title = "";
                    $item_txt = JText::_('COM_JAM_UNPUBLISH');
                }
            }
            else
            {
                if ($attribs == "menuitem")
                {
                    $icon = "icon-publish";
                }
                else{
                    $icon = "icon-unpublish";
                }
                $title = JText::_('COM_JAM_TOOLTIP_UNPUBLISHED');
                $item_txt = JText::_('COM_JAM_PUBLISH');
            }

            if (($access->canEdit || $access->canEditOwn) 
                && $params->get('user_can_publish'))
            {
                $override = true;
            }
            if (($access->canPublish && $article->state != -2)
                || ($user->id == $article->created_by && $override))
            {
                $url = "index.php?option=com_jam&view=jam&task=unPublish&cid=" . $article->id . "&Itemid=" . $itemid;
                $link = JRoute::_($url);
            }
            else 
            {
                $link = "#";
                $class = "disabled";
            }
           
            $output = array(
                'link' => $link,
                'icon' => $icon,
                'item_txt' => $item_txt,
                'title' => $title,
                'class' => $class
            );
            
            return $output;
            
        }

        /**
         * Method to get Trash / Restore menuitem.
         *
         * @return  array
         *
         */
        function getTrash ($article, $params, $access)
        {
            $app = JFactory::getApplication();
            $itemid = $app->getMenu()->getActive()->id;
            
            $user = JFactory::getUser();
            $override = false;
            $class = "";

            if ($article->state == -2) 
            {
                $msg_confirm = JText::_('COM_JAM_RESTORE_CONFIRM', true);
                $item_txt = JText::_('COM_JAM_RESTORE_FROM_TRASH');
            }
            else 
            {
                $msg_confirm = JText::_('COM_JAM_TRASH_CONFIRM', true);
                $item_txt = JText::_('COM_JAM_MOVE_TO_TRASH');
            }
            
            if (($access->canEdit || $access->canEditOwn) 
                && $params->get('user_can_trash'))
            {
                $override = true;
            }
            
            if ($access->canPublish 
                || ($user->id == $article->created_by && $override))
            {
                $url = "index.php?option=com_jam&controller=&task=trash&cid=" . $article->id . "&Itemid=" . $itemid;
                $link = JRoute::_($url);
            }
            
            else 
            {
                $link = "#";
                $class = "disabled";
            }
            
            $output = array(
                'item_txt' => $item_txt,
                'msg_confirm' => $msg_confirm,
                'link' => $link,
                'class' => $class
            );
            
            return $output;
        }
    }
    ?>
