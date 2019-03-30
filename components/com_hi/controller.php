<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Hello World Component Controller
 */
class HiController extends JControllerLegacy
{
	public function shout()
	{
		echo "<b>SHOUT!!!</b>";
	}
}
