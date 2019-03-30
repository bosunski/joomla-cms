<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hotel
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.multiselect');

$listOrder = $this->escape($this->filter_order);
$listDirn = $this->escape($this->filter_order_Dir);
?>

<form action="<?php echo JRoute::_('index.php?option=com_hotel&view=rooms'); ?>" method="post" id="adminForm" name="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>

        <div id="j-main-container" class="spawn10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif; ?>

    <?php echo JLayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

    <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULSTS') ?>
        </div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="1%" class="nowrap">
                        <?php echo JHtml::_(
                            'searchtools.sort',
                            'COM_HOTEL_ROOMS_ID_FIELD_LABEL',
                            'r.id',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>

                    <th class="nowrap center">
                        <?php echo JHtml::_(
                            'searchtools.sort',
                            'COM_HOTEL_ROOMS_NAME_FIELD_LABEL',
                            'r.floor',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>

                    <th class="nowrap center">
                        <?php echo JHtml::_(
                            'searchtools.sort',
                            'COM_HOTEL_ROOMS_EMPTY_FIELD_LABEL',
                            'r.empty',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>

                    <th class="nowrap center">
                        <?php echo JHtml::_(
                            'searchtools.sort',
                            'JDATE',
                            'r.created',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="nowrap">
                            <?php echo $this->escape($item->id); ?>
                        </td>

                        <td class="nowrap center">
                            <?php echo $this->escape($item->floor); ?>
                        </td>

                        <td class="nowrap center">
                            <?php echo JHtml::_('grid.boolean', $i, $item->empty); ?>
                        </td>

                        <td class="nowrap center">
                            <?php echo $this->escape($item->created); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php echo $this->pagination->getListFooter(); ?>

    <div>
		<input type="hidden" name="task" value=" " />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>