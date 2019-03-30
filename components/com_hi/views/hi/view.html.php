<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class HiViewHi extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->msg = $this->get('Msg');

		parent::display($tpl);
	}
}
