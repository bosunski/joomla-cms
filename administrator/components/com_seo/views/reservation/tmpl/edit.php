<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hotel
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined("_JEXEC") or die("Restricted access");

// necessary libraries
JHtml::_('formbehavior.chosen', 'select', null, ['disable_search_threshold' => 1]);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        if (task == 'reservation.cancel' || document.formvalidator.isValid(document.id('reservation-form'))) {
            Joomla.submitform(task, document.getElementById('reservation-form'));
        }
    }
</script>

<form action="<?php JRoute::_('index.php?option=com_hotel&id=' . (int)$this->item->id); ?>" method="post" name="adminForm" id="reservation-form" class="form-validate">

    <div class="form-inline form-inline-header">
        <?php echo $this->form->renderField('id'); ?>
    </div>

    <div class="form-inline form-inline-header">
        <?php echo $this->form->renderField('guest_id'); ?>
    </div>

    <div class="form-inline form-inline-header">
        <?php echo $this->form->renderField('checkin_date'); ?>

        <?php echo $this->form->renderField('checkout_date'); ?>
    </div>

    <?php echo $this->form->renderField('myspacer'); ?>

    <div class="form-inline form-inline-header">
        <?php echo $this->form->renderField('room_id'); ?>
    </div>

    <div class="form-inline form-inline-header">
        <?php echo $this->form->renderField('obs'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
