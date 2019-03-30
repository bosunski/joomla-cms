<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class HiViewHowdy extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->msg = "Howdy People!";

		parent::display($tpl);
	}
}
