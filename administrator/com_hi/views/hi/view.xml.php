<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * XML View class for the HelloWorld Component
 */
class HiViewHi extends JViewLegacy
{
	// Overwriting JView display method
	public function display($tpl = null)
	{
		echo "<?xml version='1.0' encoding='UTF-8'?>
			<article>
			  <title>How to create a Joomla Component</title>
			  <alias>create-component</alias>
			</article>";
	}
}
