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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'guest.cancel' || document.formvalidator.isValid(document.id('guest-form'))) {
			Joomla.submitform(task, document.getElementById('guest-form'));
		}
	}
</script>

<form action="<?php JRoute::_('index.php?option=com_hotel&id=' . (int)$this->item->id); ?>" method="post" name="adminForm" id="guest-form" class="form-validate">

	<div class="form-inline form-inline-header">
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
		</div>
    </div>

    <div class="form-inline form-inline-header">
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
		</div>
    </div>

    <div class="form-inline form-inline-header">
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('vip'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('vip'); ?></div>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>