<?php

namespace Joomla\Component\Hi\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
	protected $default_view = 'articles';

	public function display($cachable = false, $urlparams = array())
	{
		echo "Hello World";
		parent::display($cachable, $urlparams);
	}
}
