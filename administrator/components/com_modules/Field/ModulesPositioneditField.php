<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Modules\Administrator\Field;

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Edit Modules Position field.
 *
 * @since  4.0.0
 */
class ModulesPositioneditField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $type = 'ModulesPositionedit';

	/**
	 * Name of the layout being used to render the field
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $layout = 'joomla.form.field.modulespositionedit';

	/**
	 * Client name.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $client;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to get the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'client':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to set the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'client':
				$this->$name = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value. This acts as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     FormField::setup()
	 * @since   __DEPLOY_VERSION__
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result === true)
		{
			$this->client = $this->element['client'] ? (string) $this->element['client'] : 'site';
		}

		return $result;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.0.0
	 */
	protected function getInput()
	{
		$data = $this->getLayoutData();

		$clientId  = $this->client === 'administrator' ? 1 : 0;
		$positions = HTMLHelper::_('modules.positions', $clientId, 1, $this->value);

		$data['client']    = $clientId;
		$data['positions'] = $positions;

		$renderer = $this->getRenderer($this->layout);
		$renderer->setComponent('com_modules');
		$renderer->setClient(1);

		return $renderer->render($data);
	}
}
