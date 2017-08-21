<?php
/**
 * @version     0.19
 * @package     com_juam
 * @copyright   Copyright (C) 2017. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Felipe Quinto Busanello, Rob Sykes, Alexey Gubanov
 * @link        https://github.com/ragnaarius/juam
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();
$uam_jversion = new JVersion();
?>
<div class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
    <?php $this->escape($this->params->get('page_title')); ?>
</div>
<form class="form-inline" name="adminForm" id="adminForm" method="post" action="<?php echo $this->action; ?>">
    <div class="accordion" id="accordion2">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne"><?php echo JText::_('COM_UAM_FILTER'); ?></a>
            </div>
            <div id="collapseOne" class="accordion-body collapse">
                <div class="accordion-inner">
                    <?php
                    if ($this->params->get('showsearchfilter') == 1) {
                    ?>
                    <div class="control-group">
                        <div class="input-append">
                            <div class="controls">
                                <label class="control-label" for="filter_search"><?php echo JText::_('COM_UAM_FILTER'); ?>: </label>
                                <input class="input-large" id="filter_search" type="text" name="filter_search" value="<?php echo $this->escape($this->lists['filter_search']);?>" /> 
                                <button class="btn" type="submit" onclick="this.form.submit();"><?php echo JText::_('COM_UAM_GO'); ?></button>
                                <button class="btn" onclick="document.getElementById('filter_search').value=''; document.getElementById('filter_state').value=''; document.getElementById('filter_catid').value='0'; document.getElementById('filter_authorid').value='0'; document.getElementById('filter_lang').value=''; this.form.submit();"><?php echo JText::_('COM_UAM_RESET'); ?></button>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    <div class="control-group">
                    <?php
                    if ((($this->params->get('useallcategories') == 1) || ($this->params->get('allow_subcategories') == 1)) && ($this->params->get('showcategoryfilter') == 1)) {
                        echo $this->lists['catid'];
                    }
                    if (($this->canEditOwnOnly == false) && ($this->params->get('showauthorfilter') == 1)) {
                        echo $this->lists['authorid'];
                    }
                    if ($this->params->get('showpublishedstatefilter') == 1) {
                        echo $this->lists['state'];
                    }
                    if ($this->params->get('showlanguagefilter') == 1) {
                        echo $this->lists['langs'];
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ($this->params->get('new_article_button')) {
		$button = $this->getNewArticleButton($this->params);
    ?>
    <div class="uam_new_article">
        <button class="btn" type="button" id="bt_new_article" onclick="location.href='<?php echo $button['link']; ?>';">
            <span class="icon-plus"> </span><?php echo $button['text']; ?>
        </button>
    </div>
	<?php
    }
	$count_itens = count($this->itens);

	//without article
	if (!$count_itens) { 
	?>
    <div class="alert">
        <?php echo JText::_('COM_UAM_NO_ARTICLES_FOUND'); ?>
    </div>
	<?php
	}
	else {
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <?php
            if ($this->params->get('published_column')) :
			?>
                <th class="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_UAM_STATE', 'c.state', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            <?php
            endif;
            if ($this->params->get('title_column')) :
            ?>
                <th class="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_UAM_TITLE', 'c.title', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            <?php
            endif;
            if ($this->params->get('category_column')) :
            ?>
                <th class="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_UAM_CATEGORY', 'category', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            <?php
            endif;
            if ($this->params->get('author_column')) :
            ?>
                <th class="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_UAM_AUTHOR', 'author', $this->lists['order_Dir'], $this->lists['order']); ?>
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
                <?php echo JHTML::_('grid.sort', 'COM_UAM_CREATED_DATE', 'c.created', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            <?php
            endif;
            if ($this->params->get('start_publishing_column')) :
            ?>
                <th class="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_UAM_START_PUBLISHING', 'c.publish_up', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            <?php
            endif;
            if ($this->params->get('finish_publishing_column')) :
            ?>
                <th class="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_UAM_FINISH_PUBLISHING', 'c.publish_down', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            <?php
            endif;
            if ($this->params->get('hits_column')) :
            ?>
                <th class="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_UAM_HITS', 'c.hits', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            <?php
            endif;
            if ($this->params->get('id_column')) :
            ?>
                <th class="nowrap">
                <?php echo JHTML::_('grid.sort', 'COM_UAM_ID', 'c.id', $this->lists['order_Dir'], $this->lists['order']); ?>
                </th>
            <?php
            endif;
            ?>
            </tr>
        </thead>
        <tbody>
       <?php
    	for ($i=0; $i < $count_itens; $i++) {
            $row = $this->getItem($i, $this->params);
            $asset	= 'com_content.article.'.$row->id;
            $this->access->canCreate = $user->authorise('core.create', 'com_content.category.'.$row->catid);
            // Check general edit permission first.
            $this->access->canPublish = $user->authorise('core.edit.state', $asset);
            // Check general edit permission first.
            $this->access->canEdit = $user->authorise('core.edit', $asset);
            // Now check if edit.own is available.
            $this->access->canEditOwn = $user->authorise('core.edit.own', $asset) && ($this->user->id == $row->created_by);
        ?>
            <tr>
                <td align="center">
                    <div class="btn-group">
                    <?php
                    echo $this->getPublishedIcon($row, $row->params, $this->access);
                    echo $this->getFeaturedIcon($row, $row->params, $this->access);
                    ?>                   
                        <a class="btn dropdown-toggle btn-micro" data-toggle="dropdown" href="#">
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                        <?php
                        // Edit Item     
                        if ($this->params->get('edit_column')) :
                            echo $this->getEdit($row, $row->params, $this->access);
                        endif;
                        // Copy Item
                        if ($this->params->get('copy_column')) :
                            echo $this->getCopy($row, $row->params, $this->access);
                        endif;
                        // Edit alias Item
                        if ($this->params->get('edit_alias_column')) :
                            echo $this->getEditAlias($row, $row->params, $this->access);
                        endif;          
                        // Public Item
                        if ($this->params->get('published_column')) :
                            echo $this->getPublished($row, $row->params, $this->access);
                        endif;
                        // Featured Item
                        if ($this->params->get('featured_column')) :
          					echo $this->getFeatured($row, $row->params, $this->access);
                        endif;          
                        ?>
                            <li class="divider"></li>
                        <?php
                        // Trash / Restore Item
                        if ($this->params->get('trash_column')) :
                            echo $this->getTrash($row, $row->params, $this->access);
                        endif;
                        ?>
                        </ul>
                    </div>        
                </td>
				<?php
				// Title column
                if ($this->params->get('title_column')) :
                ?>
                <td>
                <?php
                    echo $this->getTitle($row, $row->params, $this->access);
                    echo "<input type='hidden' id='fual_{$row->id}_title' value='{$row->title}' />";
                    echo "<input type='hidden' id='fual_{$row->id}_alias' value='{$row->alias}' />";
                ?>
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
                    if ((strlen(trim($row->created_by_alias))) && ($this->params->get('show_alias'))) {
                        echo $row->created_by_alias;
                        echo "<br />({$row->author})";
                    }
                    else {
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
                    if ($row->language=='*') {
                        echo JText::alt('JALL','language');
                    } 
                    else {
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
                    <?php echo JHTML::_('date', $row->publish_up, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <?php
                endif;
                // Finish pnblishing column
				if ($this->params->get('finish_publishing_column')) :
                ?>
                <td class="small">
                <?php
                    if ($row->publish_down == '0000-00-00 00:00:00') {
                        echo JText::_('COM_UAM_NEVER');
                    }
                    else {
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
                    <span class="badge badge-info"><?php echo $row->hits; ?></span>
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

    <input type="hidden" name="option" value="com_uam" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="view" value="uam" />
    <input type="hidden" name="controller" value="" />
    <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

    <?php echo JHTML::_('form.token'); ?>
    
</form>

<!-- Edit alias form code -->

<div id="fual_edit_alias_form" class="modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="javascript:fualCloseAliasForm();">&times;</button>
        <h3><?php echo JText::_('COM_UAM_EDIT_ALIAS'); ?></h3>
    </div>
    <div class="modal-body">
        <input type="hidden" id="feaf_txt_saving" value="<?php echo JText::_('COM_UAM_SAVING'); ?>" />
        <input type="hidden" id="feaf_txt_save" value="<?php echo JText::_('COM_UAM_SAVE'); ?>" />
        <input type="hidden" id="feaf_txt_error" value="<?php echo JText::_('COM_UAM_INVALID_ALIAS', true); ?>" />
        <input type="hidden" id="feaf_txt_error_save" value="<?php echo JText::_('COM_UAM_ERROR_SAVING_ALIAS', true); ?>" />
        <input type="hidden" id="feaf_txt_ok_save" value="<?php echo JText::_('COM_UAM_ALIAS_SAVED', true); ?>" />
        <input type="hidden" id="feaf_txt_edit_alias" value="<?php echo JText::_('COM_UAM_EDIT_ALIAS'); ?>" />
        <input type="hidden" id="feaf_txt_close" value="Close" />
        <dl class="dl-horizontal">
            <dt>ID:</dt>
                <dd id="feaf_id_article"></dd>
            <dt><?php echo JText::_('COM_UAM_TITLE'); ?>:</dt>
                <dd id="feaf_title"></dd>
            <dt><?php echo JText::_('COM_UAM_ALIAS'); ?>:</dt>
                <dd><input type="text" id="feaf_alias" class="input-large" maxlength="255" /></dd>
        </dl>
        <div id="alert-block" class="alert" style="display: none"></div>
    </div>
    <div class="modal-footer">
        <button class="btn" type="button" id="feaf_bt_cancel" data-dismiss="modal" aria-hidden="true" onclick="javascript:fualCloseAliasForm();"><?php echo JText::_('COM_UAM_CANCEL'); ?></button>
        <button class="btn" type="button" id="feaf_bt_save" onclick="javascript:fualSaveAlias();"><?php echo JText::_('COM_UAM_SAVE'); ?></button>
    </div>
</div>
