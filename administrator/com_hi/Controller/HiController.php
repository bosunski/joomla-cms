<?php

namespace Joomla\Component\Hi\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

class HiController extends BaseController
{
	protected $default_view = 'hi';

	public function display($cachable = false, $urlparams = array())
	{
		echo "Hello World";
		parent::display();
	}
}
