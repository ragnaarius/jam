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

$user = JFactory::getUser();
$jam_jversion = new JVersion();
$app = JFactory::getApplication();

?>
<div class="jam_page<?php echo $this->params->get('pageclass_sfx'); ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
	</div>
	<?php endif; ?>
	<form class="form-inline" name="adminForm" id="adminForm" method="post" action="<?php echo $this->action; ?>">
		<div class="jam_toolbar">
			<div class="jam_new_article">
    		<?php
            if ($this->params->get('new_article_button')) 
            {
                $button = $this->getNewArticleButton($this->params);
            ?>
        		<button class="btn" type="button" id="bt_new_article" onclick="location.href='<?php echo $button['link']; ?>';">
					<span class="icon-plus"> </span><?php echo $button['text']; ?>
        		</button>
			<?php
            }
            ?>       		
            	<button class="btn" type="button" id="bt_filters" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
					<span class="icon-filter"> </span><?php echo JText::_('COM_JAM_FILTER'); ?>
        		</button>
			</div>
   			<?php
            if ($this->params->get('showsearchfilter') == 1) 
            {
            ?>
			<div class="control-group">
				<div class="input-append">
					<div class="controls">
						<input class="input-large" id="filter_search" type="text" name="filter_search" placeholder="<?php echo JText::_('COM_JAM_SEARCH_PLACEHOLDER'); ?>" value="<?php echo $this->escape($this->lists['filter_search']);?>" /> 
						<button class="btn" type="submit" onclick="this.form.submit();">
							<span class="icon-search"> </span><?php echo JText::_('COM_JAM_GO'); ?>
						</button>
						<button class="btn" onclick="document.getElementById('filter_search').value=''; document.getElementById('filter_state').value=''; document.getElementById('filter_catid').value='0'; document.getElementById('filter_authorid').value='0'; document.getElementById('filter_lang').value=''; this.form.submit();">
							<span class="icon-remove"> </span><?php echo JText::_('COM_JAM_RESET'); ?>
						</button>
					</div>
				</div>
			</div>    	
		</div>
			<?php
            }
            ?>
        <div class="clear"></div>
		<div class="accordion" id="accordion">
			<div id="collapseOne" class="accordion-body collapse">
				<div class="accordion-inner">
					<div class="lists">
					<?php
                    if ((($this->params->get('useallcategories') == 1) 
                        || ($this->params->get('allow_subcategories') == 1)) 
                        && ($this->params->get('showcategoryfilter') == 1)) 
                    {
                        echo $this->lists['catid'];
                    }
                    if (($this->canEditOwnOnly == false) 
                        && ($this->params->get('showauthorfilter') == 1)) 
                    {
                        echo $this->lists['authorid'];
                    }
                    if ($this->params->get('showpublishedstatefilter') == 1) 
                    {
                        echo $this->lists['state'];
                    }
                    if ($this->params->get('showlanguagefilter') == 1) 
                        {
                        echo $this->lists['langs'];
                    }
                    ?>
					</div>
				</div>
			</div>
        </div>
    	<?php
        $count_itens = count($this->itens);

        //without article
        if (!$count_itens) 
        { 
            $app->enqueueMessage(JText::_('COM_JAM_NO_ARTICLES_FOUND'), 'warning');
        }
        else 
        {
        ?>
    	<table class="table table-striped">
        	<thead>
            	<tr>
				<th class="nowrap">
                		<?php echo JHTML::_('grid.sort', 'COM_JAM_STATE', 'c.state', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
                	<th class="nowrap">
                		<?php echo JHTML::_('grid.sort', 'COM_JAM_TITLE', 'c.title', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
            	<?php
                if ($this->params->get('category_column')) :
                ?>
                	<th class="nowrap">
                	<?php echo JHTML::_('grid.sort', 'COM_JAM_CATEGORY', 'category', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
            	<?php
                endif;
                if ($this->params->get('author_column')) :
                ?>
                	<th class="nowrap">
                	<?php echo JHTML::_('grid.sort', 'COM_JAM_AUTHOR', 'author', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
            	<?php
                endif;
                if ($this->params->get('language_column')) :
                ?>
                	<th class="nowrap">
                	<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'c.language', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
				<?php
                endif;
                if ($this->params->get('created_date_column')) :
                ?>
                	<th class="nowrap">
                	<?php echo JHTML::_('grid.sort', 'COM_JAM_CREATED_DATE', 'c.created', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
            	<?php
                endif;
                if ($this->params->get('start_publishing_column')) :
                ?>
                	<th class="nowrap">
                	<?php echo JHTML::_('grid.sort', 'COM_JAM_START_PUBLISHING', 'c.publish_up', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
            	<?php
                endif;
                if ($this->params->get('finish_publishing_column')) :
                ?>
                	<th class="nowrap">
                	<?php echo JHTML::_('grid.sort', 'COM_JAM_FINISH_PUBLISHING', 'c.publish_down', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
            	<?php
                endif;
                if ($this->params->get('hits_column')) :
                ?>
                	<th class="nowrap">
                	<?php echo JHTML::_('grid.sort', 'COM_JAM_HITS', 'c.hits', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
            	<?php
                endif;
                if ($this->params->get('id_column')) :
                ?>
                	<th class="nowrap">
                	<?php echo JHTML::_('grid.sort', 'COM_JAM_ID', 'c.id', $this->lists['order_Dir'], $this->lists['order']); ?>
                	</th>
            	<?php
                endif;
                ?>
            	</tr>
        	</thead>
        	<tbody>
			<?php
            for ($i=0; $i < $count_itens; $i++) 
            {
                $row = $this->getItem($i, $this->params);
                $asset	= "com_content.article." . $row->id;
                $this->access->canCreate = $user->authorise('core.create', 'com_content.category.'.$row->catid);
                // Check general edit permission first.
                $this->access->canPublish = $user->authorise('core.edit.state', $asset);
                // Check general edit permission first.
                $this->access->canEdit = $user->authorise('core.edit', $asset);
                // Now check if edit.own is available.
                $this->access->canEditOwn = $user->authorise('core.edit.own', $asset) && ($this->user->id == $row->created_by);
            ?>
				<tr id="article<?php echo $row->id; ?>">
                	<td align="center">
					<div class="btn-group">
						<?php
                            $published = $this->getPublished($row, $row->params, $this->access, 'button');
                            echo   "<a class=\"btn btn-micro hasTooltip " . $published['class'] . "\" href=\"" . $published['link'] . "\" title=\"" . $published['title'] . "\">
                                        <span class=\"" . $published['icon'] . "\"></span>
                                    </a>";

                            $featured = $this->getFeatured($row, $row->params, $this->access, 'button');
                            echo   "<a class=\"btn btn-micro  hasTooltip " . $featured['class'] . "\" href=\"" . $featured['link'] . "\" title=\"" . $featured['title'] . "\">
                                        <span class=\"" . $featured['icon'] ."\"></span>
                                    </a>";
                        ?>                   
                        	<a class="btn btn-micro dropdown-toggle" data-toggle="dropdown" href="#">
                            	<span class="caret"></span>
                        	</a>
                        	<ul class="dropdown-menu">
                        	<?php
                            // Edit Item     
                            if ($this->params->get('edit_menuitem')) :
                                $edit = $this->getEdit($row, $row->params, $this->access);
                                echo   "<li class=\"menuitem " . $edit['class'] . "\">
                                            <a href=\"" . $edit['link'] . "\">
                                                <span class=\"" . $edit['icon'] . "\"></span>" . $edit['item_txt'] . "
                                            </a>
                                        </li>";
                            endif;
                            // Copy Item
                            if ($this->params->get('copy_menuitem')) :
                                $copy = $this->getCopy($row, $row->params, $this->access);
                                echo   "<li class=\"menuitem " . $copy['class'] . "\">
                                            <a  class=\"menuitem_lnk\" href=\"" . $copy['link'] . "\" data-confirm-message=\"" . $copy['msg_confirm'] . "\">
                                                <span class=\"icon-copy\"></span>" . $copy['item_txt'] . "
                                            </a>
                                        </li>";
                            endif;
                            // Edit alias Item
                            if ($this->params->get('edit_alias_menuitem')) :
                                $editalias = $this->getEditAlias($row, $row->params, $this->access);
                                echo   "<li class=\"menuitem " . $editalias['class'] . "\">
                                            <a class=\"menuitem_alias\" href=\"#edit_alias_form\" data-toggle=\"modal\" data-article-id=\"" . $editalias['article_id'] . "\" data-article-alias=\"" . $row->alias . "\" data-article-title=\"" . $row->title . "\">
                                                <span class=\"icon-share-alt\"></span>" . $editalias['item_txt'] . "
                                            </a>
                                        </li>";
                            endif;          
                            // Public Item
                            if ($this->params->get('published_menuitem')) :
                                $published = $this->getPublished($row, $row->params, $this->access, 'menuitem');
                                echo   "<li class=\"menuitem " . $published['class'] . "\">
                                            <a href=\"" . $published['link'] . "\">
                                                <span class=\"" . $published['icon'] . "\"></span>" . $published['item_txt'] . "
                                            </a>
                                        </li>";
                            endif;
                            // Featured Item
                            if ($this->params->get('featured_menuitem')) :
                                $featured = $this->getFeatured($row, $row->params, $this->access, 'menuitem');
                                echo   "<li class=\"menuitem " . $featured['class'] . "\">
                                            <a href=\"" . $featured['link'] . "\">
                                                <span class=\"" . $featured['icon'] . "\"></span>" . $featured['item_txt'] . "
                                            </a>
                                        </li>";
                            endif;          
                            // Trash / Restore Item
                            if ($this->params->get('trash_menuitem')) :
                                $trash = $this->getTrash($row, $row->params, $this->access);
                                echo   "<li class=\"divider\"></li>
										<li class=\"menuitem " . $trash['class'] . "\">
                                            <a class=\"menuitem_lnk\" href=\"" . $trash['link'] . "\" data-confirm-message=\"" . $trash['msg_confirm'] . "\">
                                                <span class=\"icon-trash\"></span>" . $trash['item_txt'] . "
                                            </a>
                                        </li>";
                            endif;
                            ?>
                        	</ul>
                    	</div>        
                	</td>
                	<td>
                	<?php
                	// Title column
                    $title = $this->getTitle($row, $row->params, $this->access);

                    if ($title['linked'])
                    {
                        echo   "<a href=\"" . $title['link'] . "\">
                                    <span class=\"title " . $title['class'] . "\" title=\"" . $title['tooltip'] . "\" >" . $title['title'] . "</span>
                                </a>";
                    }
                    else
                    {
                        if ($title['checkout'])
                        {
                            echo "<span class=\"btn btn-micro icon-lock " . $title['class'] . "\" title=\"" . $title['tooltip'] . "\" ></span><span class=\"title\">" . $title['title'] . "</span>";
                        }
                        else 
                        {
                            echo "<span class=\"title " . $title['class'] . "\" title=\"" . $title['tooltip'] . "\" >" . $title['title'] . "</span>";
                        }
                    }
                    // Category after title
                    if ($this->params->get('category_in_title')) :
                ?>
					<div class="small">
						<?php echo JText::_('COM_JAM_CATEGORY') . ":"; ?>
						<a href="<?php echo ContentHelperRoute::getCategoryRoute($row->catid); ?>">
							<?php echo $row->category; ?>
						</a>
					</div>
                	</td>
                	<?php
                    endif;
                    // Category column
                    if ($this->params->get('category_column')) :
                    ?>
                	<td class="small">
                    	<a href="<?php echo ContentHelperRoute::getCategoryRoute($row->catid); ?>">
                        	<?php echo $row->category; ?>
                    	</a>
                	</td>
                	<?php
                    endif;
                    // Author column
				    if ($this->params->get('author_column')) :
                    ?>
                	<td class="small">
                	<?php
                        if ((strlen(trim($row->created_by_alias))) && ($this->params->get('show_alias'))) 
                        {
                            echo $row->created_by_alias;
                            echo "<br />(" . $row->author . ")";
                        }
                        else 
                        {
                            echo $row->author;
                        }
                    ?>
					</td>
                	<?php
                    endif;
                    // Language column
                    if ($this->params->get('language_column')) :
                    ?>
                	<td class="small">
                	<?php 
                        if ($row->language == '*') 
                        {
                            echo JText::alt('JALL','language');
                        } 
                        else 
                        {
                            echo $row->language_title ? $row->language_title : JText::_('JUNDEFINED');
                        }
                    ?>
                	</td>
                	<?php	
				    endif;
				    // Created date column
                    if ($this->params->get('created_date_column')) :
                    ?>
                	<td class="small">
                    	<?php echo JHTML::_('date', $row->created, JText::_('DATE_FORMAT_LC4')); ?>
                	</td>
                	<?php
                    endif;
                    // Start publishing column
				    if ($this->params->get('start_publishing_column')) :
                    ?>
                	<td class="small">
                	<?php
                        if ($row->publish_up == '0000-00-00 00:00:00') 
                        {
                            echo JText::_('COM_JAM_NEVER_PUBLISHED');
                        }
                        else 
                        {
                            echo JHTML::_('date', $row->publish_up, JText::_('DATE_FORMAT_LC4'));
                        }
                    ?>
                	</td>
                	<?php
                    endif;
                    // Finish publishing column
				    if ($this->params->get('finish_publishing_column')) :
                    ?>
                	<td class="small">
                	<?php
                        if ($row->publish_down == '0000-00-00 00:00:00') 
                        {
                            echo JText::_('COM_JAM_NEVER');
                        }
                        else 
                        {
                            echo JHTML::_('date', $row->publish_down, JText::_('DATE_FORMAT_LC4'));
                        }
                    ?>
                	</td>
                	<?php
                    endif;
                    // Hits column
				    if ($this->params->get('hits_column')) :
                    ?>
                	<td>
                    	<span class="badge <?php if ($row->hits > 0){echo "badge-info";} ?>"><?php echo $row->hits; ?></span>
                	</td>
                	<?php
                    endif;
                    // ID column
				    if ($this->params->get('id_column')) :
                    ?>
                	<td>
                    	<span class="badge badge-success"><?php echo $row->id; ?></span>
                	</td>
                	<?php
                    endif;
                    ?>
            	</tr>
            	<?php
            }
        }
                ?>

        	</tbody>
    	</table>
  
    	<?php echo $this->pagination->getListFooter(); ?>

    	<input type="hidden" name="option" value="com_jam" />
    	<input type="hidden" name="task" value="" />
    	<input type="hidden" name="view" value="jam" />
    	<input type="hidden" name="controller" value="" />
    	<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt('Itemid', ''); ?>" />
    	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
    	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

    	<?php echo JHTML::_('form.token'); ?>
    
	</form>
</div>
<!-- Edit alias form code -->

<div id="edit_alias_form" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><?php echo JText::_('COM_JAM_EDIT_ALIAS'); ?></h3>
    </div>
    <div class="modal-body">
        <dl class="dl-horizontal">
            <dt>ID:</dt>
                <dd id="eaf_id_article"></dd>
            <dt><?php echo JText::_('COM_JAM_TITLE'); ?>:</dt>
                <dd id="eaf_title"></dd>
            <dt><?php echo JText::_('COM_JAM_ALIAS'); ?>:</dt>
                <dd><input type="text" id="eaf_alias" class="input-large" maxlength="255" /></dd>
        </dl>
        <div id="alert-block" class="alert" style="display: none"></div>
    </div>
    <div class="modal-footer">
        <button class="btn" type="button" id="eaf_btn_cancel" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_JAM_CANCEL'); ?></button>
        <button class="btn btn-primary" type="button" id="eaf_btn_save"><?php echo JText::_('COM_JAM_SAVE'); ?></button>
    </div>
</div>
