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
<form action="<?php echo JRoute::_('index.php?option=com_hotel&view=reservations'); ?>" method="post" id="adminForm" name="adminForm">
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
        <table class="table table-striped" id="reservations-list">
            <thead>
                <tr>
                    <th width="1%" class='hidden-phone'>
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>

                    <?php if (isset($this->items[0]->published)) : ?>
                        <th width="1%" class="nowrap center">
                            <?php echo JHtml::_(
                                'searchtools.sort',
                                'JSTATUS',
                                'r.state',
                                $listDirn,
                                $listOrder
                            ); ?>
                        </th>
                    <?php endif; ?>

                    <th class="nowrap left">
                        <?php echo JHtml::_(
                            'grid.sort',
                            JText::_('COM_HOTEL_RESERVATIONS_FIELD_GUEST_LABEL'),
                            'g.name',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>

                    <th class="center">
                        <?php echo JHtml::_(
                            'grid.sort',
                            JText::_('COM_HOTEL_RESERVATIONS_FIELD_OBS_LABEL'),
                            'room.floor',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>

                    <th class="nowrap left">
                        <?php echo JHtml::_(
                            'searchtools.sort',
                            'JDATE',
                            'r.checkin_date',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>

                    <th class="nowrap left">
                        <?php echo JHtml::_(
                            'searchtools.sort',
                            'JDATE',
                            'r.checkout_date',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>

                    <th class="center">
                        <?php echo JHtml::_(
                            'grid.sort',
                            JText::_('COM_HOTEL_RESERVATIONS_FIELD_FLOOR_LABEL'),
                            'room.floor',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>

                    <th class="center">
                        <?php echo JHtml::_(
                            'grid.sort',
                            JText::_('COM_HOTEL_RESERVATIONS_FIELD_COST_LABEL'),
                            'room.cost',
                            $listDirn,
                            $listOrder
                        ); ?>
                    </th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="center">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>

                        <?php if (isset($this->items[0]->published)) : ?>
                            <td class="center">
                                <?php echo JHtml::_(
                                    'jgrid.published',
                                    $item->published,
                                    $id,
                                    'reservations.',
                                    true,
                                    'cb'
                                ); ?>
                            </td>
                        <?php endif; ?>

                        <td class="nowrap has-context">
                            <div class="pull-left">
                                <?php echo $this->escape($item->name); ?>
                            </div>
                        </td>

                        <td class="nowrap has-context">
                            <div class="pull-left">
                                <?php echo $this->escape($item->obs); ?>
                            </div>
                        </td>

                        <td class="nowrap has-context">
                            <div class="pull-left">
                                <?php echo $this->escape($item->checkin_date); ?>
                            </div>
                        </td>

                        <td class="nowrap has-context">
                            <div class="pull-left">
                                <?php echo $this->escape($item->checkout_date); ?>
                            </div>
                        </td>

                        <td class="nowrap">
                            <div class="center">
                                <?php echo $this->escape($item->floor); ?>
                            </div>
                        </td>

                        <td class="nowrap">
                            <div class="center">
                                <?php echo $this->escape($item->cost); ?>
                            </div>
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
