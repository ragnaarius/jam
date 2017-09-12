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

class JAMModelJAM extends JModelAdmin 
{
	var $_data;
	var $_total = null;
	var $_pagination = null;

    function __construct() 
    {
        parent::__construct();

        $app = JFactory::getApplication();
        $option = $app->input->getCmd('option', '');

		// Get the pagination request variables
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = $app->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

        // In case limit has been changed, adjust limitstart accordingly
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     *
     * @return	mixed	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        return parent::getForm($data, $loadData);
    }

    /**
     * Method to get the total number of items
     *
     * @access public
     * @return integer
     */
    function getTotal() 
    {
        // Lets load the content if it doesn't already exist
        if (empty($this->_total)) 
        {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    /**
     * Method to get a pagination object
     *
     * @access public
     * @return  integer
     */
    public function getPagination()
    {
        // Lets load the content if it doesn't already exist
        if (empty($this->_pagination))
        {
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        }
        
        return $this->_pagination;
    }

    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */
    function _buildQuery() 
    {
        // Get the WHERE and ORDER BY clauses for the query
        $where = $this->_buildContentWhere();
        $orderby = $this->_buildContentOrderBy();

        $query = "SELECT c.*, u.name AS author, cc.title AS category,
                    CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(':', c.id, c.alias) ELSE c.id END as slug, 
                    CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(':', cc.id, cc.alias) ELSE cc.id END as catslug, 
                    g.title AS groups, cc.published AS cat_pub, cc.access AS cat_access, l.title AS language_title 
                    FROM #__content AS c 
                    LEFT JOIN #__categories AS cc ON cc.id = c.catid 
                    LEFT JOIN #__users AS u ON u.id = c.created_by 
                    LEFT JOIN #__usergroups AS g ON c.access = g.id
                    LEFT JOIN #__languages AS l ON l.lang_code = c.language $where $orderby";
        return $query;
    }

    function _buildContentOrderBy() 
    {
        $app = JFactory::getApplication();
        $option = $app->input->getCmd('option', '');

        $filter_order = $app->getUserStateFromRequest($option.'filter_order', 'filter_order', 'c.created', 'cmd');
        ///TODO: resolver essa gambi (um dia ou nunca iauhaiuhaiu)
        $filter_order = ($filter_order == "c.ordering") ? "c.created" : $filter_order; //afffff
        $filter_order_Dir = $app->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

        $orderby = "ORDER BY $filter_order $filter_order_Dir";

        return $orderby;
    }

    function _buildContentWhere() 
    {
        $app = JFactory::getApplication();
        $option = $app->input->getCmd('option', '');
        $apparams = $app->getParams('com_jam');

        $menuparams = new JRegistry;
        
        if ($menu = $app->getMenu()->getActive())
        {
            $menuparams->loadString($menu->params);
        }
        
        $params = clone $apparams;
        $params->merge($menuparams);
		
        $db = JFactory::getDBO();
        $user = JFactory::getUser();

        $canEditOwnOnly = true;		// Assume that the current user has only edit own mode for all visible categories - will update later

        $where = array();

        $filter_search = $app->getUserStateFromRequest($option.'filter_search', 'filter_search', strtolower($app->input->getString('filter_search', '')), 'string');
        $filter_search = $db->Quote( '%'.$db->getEscaped($filter_search, true ).'%', false );

        $filter_state = $app->getUserStateFromRequest($option.'filter_state', 'filter_state', '', 'word');
        $filter_catid = $app->getUserStateFromRequest($option.'filter_catid', 'filter_catid', -1, 'int');
        $filter_langid = $app->getUserStateFromRequest($option.'filter_langid', 'filter_langid', '', 'string');
        $filter_authorid = $app->getUserStateFromRequest($option.'filter_authorid', 'filter_authorid', 0, 'int');

        if (strlen($filter_search) > 0) 
        {
            $where2 = array();
            $where2[] = "c.title like $filter_search";
            $where2[] = "c.introtext like $filter_search";
            $where2[] = "c.fulltext like $filter_search";
            $where2[] = "c.metakey like $filter_search";
            $where2[] = "c.metadesc like $filter_search";
            $where[] = '((' . implode( ') OR (', $where2 ) . '))';
        }
        
        if ($filter_state) 
        {
            if ($filter_state == 'P') 
            {
                $where[] = 'c.state = 1';
            }
            elseif ($filter_state == 'U') 
            {
                $where[] = 'c.state = 0';
            }
        }
        
        if ($params->get('useallcategories') == 1) 
        {
            // Get list of categories
            $c = JHtml::_('category.options', 'com_content');

            // Remove those categories the user can't see
            foreach ($c as $i => $option)
            {
                // To take save or create in a category you need to have create rights for that category
                // unless the item is already in that category.
                // Unset the option if the user isn't authorised for it. In this field assets are always categories.
                if ($user->authorise('core.create', 'com_content.category.'.$option->value) != true )
                {
                    unset ($c[$i]);
                }
                
                if ($user->authorise('core.edit', 'com_content.category.'.$option->value) == true )
                {
                    $canEditOwnOnly = false;
                }
            }
            
            if ($params->get('user_can_view'))
            {
                if (isset($l))
                {
                    unset ($l);
				}
				
                if (count($c) > 0) 
                {
                    $l = '';
                    // Convert into "(id1, id2...)" for the query
                    foreach (array_values($c) as $k)
                    {
                        // $k is a JObject with ->value = category id
                        $l .= $k->value .', ';
                    }
                    $l = '(' . strrev(substr(strrev($l), 2)) . ')';
                }

                if ($filter_catid > 0)
                {
                    $where[] = 'c.catid = '.$db->Quote($filter_catid);
                }
                else if (isset($l))
                {
                    $where[] = 'c.catid in ' . $l;
                }
                else $where[] = '0';	// Can't see any categories so can't see any articles
            }
            else
            {
                if (count($c) > 0)
                {
                    // Convert into "(id1, id2...)" for the query
                    $where2 = array();
                    foreach (array_values($c) as $k)
                    {
                        // $k is a JObject with ->value = category id
                        if ($user->authorise('core.edit', 'com_content.category.'.$k->value) == true )
                        {
                            $where2[] = "(c.catid = '$k->value')";
                        }
                        else if ($user->authorise('core.edit.own', 'com_content.category.'.$k->value) == true )
                        {
                            $where2[] = "(c.catid = '$k->value' AND c.created_by = '$user->id')";
                        }
                    }
                    if (count($where2) > 0)
                    {
                        $l = '((' . implode( ') OR (', $where2 ) . '))';
                    }		
                }

                if ($filter_catid > 0)
                {
                    if ($user->authorise('core.edit', 'com_content.category.'.$filter_catid) == true )
                    {
                        $where[] = "(c.catid = '$filter_catid')";
                    }
                    else if ($user->authorise('core.edit.own', 'com_content.category.'.$filter_catid) == true )
                    {
                        $where[] = "(c.catid = '$filter_catid' AND c.created_by = '$user->id')";
                    }
                }
                else if (isset($l)) 
                {
                    $where[] = $l;
                }
                else
                {
                    $where[] = '0';	// Can't see any categories so can't see any articles
                }
            }
        } 
        else 
        {
            // Just use the single category defined by the drop-down
            if ($user->authorise('core.edit', 'com_content.category.'.$params->get('mycategory')) == true ) 
            {
                $canEditOwnOnly = false;
            }
			
            $userquery = "c.created_by = '$user->id'";

            if ($filter_catid > 0)
            {
                $cats = 'c.catid = '.$filter_catid;
            } 
            else 
            {
                if ($params->get('allow_subcategories') == 1) 
                {
                    $cats = "catid IN (SELECT id FROM
                            (SELECT a.id FROM #__categories AS a WHERE a.parent_id > 0 AND
                            extension = 'com_content' AND
                            a.published = 1 AND
                            a.lft >= (SELECT b.lft FROM #__categories b WHERE b.id = ".$params->get('mycategory'). ") AND
                            a.rgt <= (SELECT c.rgt FROM #__categories c WHERE c.id = ".$params->get('mycategory'). ")
                            ) tmp)";
                }
                else 
                {
                    $cats = 'c.catid = '.$params->get('mycategory');
                }
            }
			
            if ($params->get('user_can_view'))
            {
                $where[] = $cats;
            }
            else
            {
                $where[] = $cats . ' AND ' . $userquery;
            }
        }

        if ($filter_authorid)
        {
            $where[] = "c.created_by = '$filter_authorid'";
        }
		
        if ($filter_langid)
        {
            $where[] = "c.language = '$filter_langid'";
        }
		
        $where = (count($where) ? ' WHERE '. implode(' AND ', $where) : '');

        return $where;
    }

    /**
     * Retrieves the data
     * @return array Array of objects containing the data from the database
     */
    function getData()
    {
        if (empty($this->_data)) 
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }
	
    function getItem($pk = NULL)
    {
        if ($item = parent::getItem($pk))
        {
            // Convert the params field to an array.
            $registry = new JRegistry;
            $registry->loadJSON($item->attribs);
            $item->attribs = $registry->toArray();

            // Convert the params field to an array.
            $registry = new JRegistry;
            $registry->loadJSON($item->metadata);
            $item->metadata = $registry->toArray();

            $item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
        }

        return $item;
    }
    /**
     * Method to toggle the featured setting of an article.
     *
     * @param	int		The id of the item to toggle.
     * @param	int		The value to toggle to.
     *
     * @return	boolean	True on success.
     */
    public function featured($id, $value = 0)
    {
        try 
        {
            $db = $this->getDbo();

            $db->setQuery(
                'UPDATE #__content AS a' .
                ' SET a.featured = '.(int) $value.
                ' WHERE a.id = ' . $id
            );
            
            if (!$db->query()) 
            {
                throw new Exception($db->getErrorMsg());
            }

            if ((int)$value == 0)
            {
                // Adjust the mapping table.
                // Clear the existing features settings.
                $db->setQuery(
                    'DELETE FROM #__content_frontpage' .
                    ' WHERE content_id = ' . $id
                );
                
                if (!$db->query()) 
                {
                    throw new Exception($db->getErrorMsg());
                }
            }
            else
            {
                $db->setQuery(
                    'UPDATE #__content_frontpage SET ordering = ordering + 1'
                );
                
                if (!$db->query()) 
                {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
                $db->setQuery(
                    'INSERT INTO #__content_frontpage (`content_id`, `ordering`)' .
                    ' VALUES ('. $id . ', 1)'
                );
                
                if (!$db->query())
                {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
            }
        } 
        catch (Exception $e) 
        {
            $this->setError($e->getMessage());
            return false;
        }
        return true;
    }
}
