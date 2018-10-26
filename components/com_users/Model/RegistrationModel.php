<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Users\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;

/**
 * Registration model class for Users.
 *
 * @since  1.6
 */
class RegistrationModel extends FormModel
{
	/**
	 * @var    object  The user registration data.
	 * @since  1.6
	 */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @param   array                 $config       An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   MVCFactoryInterface   $factory      The factory.
	 * @param   FormFactoryInterface  $formFactory  The form factory.
	 *
	 * @see     \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 * @since   3.2
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null)
	{
		$config = array_merge(
			array(
				'events_map' => array('validate' => 'user')
			), $config
		);

		parent::__construct($config, $factory, $formFactory);
	}

	/**
	 * Method to activate a user account.
	 *
	 * @param   string  $token  The activation token.
	 *
	 * @return  mixed    False on failure, user object on success.
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function activate($token)
	{
		$app = Factory::getApplication();
		$userParams = ComponentHelper::getParams('com_users');
		$db = $this->getDbo();

		// Get the user id based on the token.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('activation') . ' = ' . $db->quote($token))
			->where($db->quoteName('block') . ' = ' . 1)
			->where($db->quoteName('lastvisitDate') . ' = ' . $db->quote($db->getNullDate()));
		$db->setQuery($query);

		try
		{
			$userId = (int) $db->loadResult();
		}
		catch (\RuntimeException $e)
		{
			$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

			return false;
		}

		// Check for a valid user id.
		if (!$userId)
		{
			$this->setError(Text::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'));

			return false;
		}

		// Load the users plugin group.
		PluginHelper::importPlugin('user');

		// Activate the user.
		$user = Factory::getUser($userId);

		// Admin activation is on and user is verifying their email
		if (($userParams->get('useractivation') == 2) && !$user->getParam('activate', 0))
		{
			$uri = Uri::getInstance();

			// Compile the admin notification mail values.
			$data = $user->getProperties();
			$data['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
			$user->set('activation', $data['activation']);
			$data['siteurl'] = Uri::base();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			// Remove administrator/ from activate URL in case this method is called from admin
			if (Factory::getApplication()->isClient('administrator'))
			{
				$adminPos         = strrpos($data['activate'], 'administrator/');
				$data['activate'] = substr_replace($data['activate'], '', $adminPos, 14);
			}

			$data['fromname'] = $app->get('fromname');
			$data['mailfrom'] = $app->get('mailfrom');
			$data['sitename'] = $app->get('sitename');
			$user->setParam('activate', 1);
			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_SUBJECT',
				$data['name'],
				$data['sitename']
			);

			$emailBody = Text::sprintf(
				'COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_BODY',
				$data['sitename'],
				$data['name'],
				$data['email'],
				$data['username'],
				$data['activate']
			);

			// Get all admin users
			$query->clear()
				->select($db->quoteName(array('name', 'email', 'sendEmail', 'id')))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('sendEmail') . ' = 1')
				->where($db->quoteName('block') . ' = 0');

			$db->setQuery($query);

			try
			{
				$rows = $db->loadObjectList();
			}
			catch (\RuntimeException $e)
			{
				$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

				return false;
			}

			// Send mail to all users with users creating permissions and receiving system emails
			foreach ($rows as $row)
			{
				$usercreator = Factory::getUser($row->id);

				if ($usercreator->authorise('core.create', 'com_users'))
				{
					try
					{
						$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBody);
					}
					catch (\Exception $exception)
					{
						try
						{
							Log::add(Text::_($exception->getMessage()), Log::WARNING, 'jerror');

							$return = false;
						}
						catch (\RuntimeException $exception)
						{
							Factory::getApplication()->enqueueMessage(Text::_($exception->errorMessage()), 'warning');

							$return = false;
						}
					}

					// Check for an error.
					if ($return !== true)
					{
						$this->setError(Text::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));

						return false;
					}
				}
			}
		}
		// Admin activation is on and admin is activating the account
		elseif (($userParams->get('useractivation') == 2) && $user->getParam('activate', 0))
		{
			$user->set('activation', '');
			$user->set('block', '0');

			// Compile the user activated notification mail values.
			$data = $user->getProperties();
			$user->setParam('activate', 0);
			$data['fromname'] = $app->get('fromname');
			$data['mailfrom'] = $app->get('mailfrom');
			$data['sitename'] = $app->get('sitename');
			$data['siteurl'] = Uri::base();
			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_SUBJECT',
				$data['name'],
				$data['sitename']
			);

			$emailBody = Text::sprintf(
				'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['siteurl'],
				$data['username']
			);

			try
			{
				$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
			}
			catch (\Exception $exception)
			{
				try
				{
					Log::add(Text::_($exception->getMessage()), Log::WARNING, 'jerror');

					$return = false;
				}
				catch (\RuntimeException $exception)
				{
					Factory::getApplication()->enqueueMessage(Text::_($exception->errorMessage()), 'warning');

					$return = false;
				}
			}

			// Check for an error.
			if ($return !== true)
			{
				$this->setError(Text::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));

				return false;
			}
		}
		else
		{
			$user->set('activation', '');
			$user->set('block', '0');
		}

		// Store the user object.
		if (!$user->save())
		{
			$this->setError(Text::sprintf('COM_USERS_REGISTRATION_ACTIVATION_SAVE_FAILED', $user->getError()));

			return false;
		}

		return $user;
	}

	/**
	 * Method to get the registration form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return  mixed  Data object on success, false on failure.
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function getData()
	{
		if ($this->data === null)
		{
			$this->data = new \stdClass;
			$app = Factory::getApplication();
			$params = ComponentHelper::getParams('com_users');

			// Override the base user data with any data in the session.
			$temp = (array) $app->getUserState('com_users.registration.data', array());

			// Don't load the data in this getForm call, or we'll call ourself
			$form = $this->getForm(array(), false);

			foreach ($temp as $k => $v)
			{
				// Here we could have a grouped field, let's check it
				if (is_array($v))
				{
					$this->data->$k = new stdClass;

					foreach ($v as $key => $val)
					{
						if ($form->getField($key, $k) !== false)
						{
							$this->data->$k->$key = $val;
						}
					}
				}
				// Only merge the field if it exists in the form.
				elseif ($form->getField($k) !== false)
				{
					$this->data->$k = $v;
				}
			}

			// Get the groups the user should be added to after registration.
			$this->data->groups = array();

			// Get the default new user group, Registered if not specified.
			$system = $params->get('new_usertype', 2);

			$this->data->groups[] = $system;

			// Unset the passwords.
			unset($this->data->password1, $this->data->password2);

			// Get the dispatcher and load the users plugins.
			PluginHelper::importPlugin('user');

			// Trigger the data preparation event.
			Factory::getApplication()->triggerEvent('onContentPrepareData', array('com_users.registration', $this->data));
		}

		return $this->data;
	}

	/**
	 * Method to get the registration form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form  A Form object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.registration', 'registration', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// When multilanguage is set, a user's default site language should also be a Content Language
		if (Multilanguage::isEnabled())
		{
			$form->setFieldAttribute('language', 'type', 'frontend_language', 'params');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		$data = $this->getData();

		if (Multilanguage::isEnabled() && empty($data->language))
		{
			$data->language = Factory::getLanguage()->getTag();
		}

		$this->preprocessData('com_users.registration', $data);

		return $data;
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param   Form    $form   A Form object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  \Exception if there is an error in the form event.
	 */
	protected function preprocessForm(Form $form, $data, $group = 'user')
	{
		$userParams = ComponentHelper::getParams('com_users');

		// Add the choice for site language at registration time
		if ($userParams->get('site_language') == 1 && $userParams->get('frontend_userparams') == 1)
		{
			$form->loadFile('sitelang', false);
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	protected function populateState()
	{
		// Get the application object.
		$app = Factory::getApplication();
		$params = $app->getParams('com_users');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $temp  The form data.
	 *
	 * @return  mixed  The user id on success, false on failure.
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function register($temp)
	{
		$params = ComponentHelper::getParams('com_users');

		// Initialise the table with Joomla\CMS\User\User.
		$user = new User;
		$data = (array) $this->getData();

		// Merge in the registration data.
		foreach ($temp as $k => $v)
		{
			$data[$k] = $v;
		}

		// Prepare the data for the user object.
		$data['email'] = PunycodeHelper::emailToPunycode($data['email1']);
		$data['password'] = $data['password1'];
		$useractivation = $params->get('useractivation');
		$sendpassword = $params->get('sendpassword', 1);

		// Check if the user needs to activate their account.
		if (($useractivation == 1) || ($useractivation == 2))
		{
			$data['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
			$data['block'] = 1;
		}

		// Bind the data.
		if (!$user->bind($data))
		{
			$this->setError(Text::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));

			return false;
		}

		// Load the users plugin group.
		PluginHelper::importPlugin('user');

		// Store the data.
		if (!$user->save())
		{
			$this->setError(Text::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));

			return false;
		}

		$app = Factory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname'] = $app->get('fromname');
		$data['mailfrom'] = $app->get('mailfrom');
		$data['sitename'] = $app->get('sitename');
		$data['siteurl'] = Uri::root();

		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = Uri::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			// Remove administrator/ from activate URL in case this method is called from admin
			if (Factory::getApplication()->isClient('administrator'))
			{
				$adminPos         = strrpos($data['activate'], 'administrator/');
				$data['activate'] = substr_replace($data['activate'], '', $adminPos, 14);
			}

			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if ($sendpassword)
			{
				$emailBody = Text::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = Text::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username']
				);
			}
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = Uri::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			// Remove administrator/ from activate URL in case this method is called from admin
			if (Factory::getApplication()->isClient('administrator'))
			{
				$adminPos         = strrpos($data['activate'], 'administrator/');
				$data['activate'] = substr_replace($data['activate'], '', $adminPos, 14);
			}

			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if ($sendpassword)
			{
				$emailBody = Text::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = Text::sprintf(
					'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['activate'],
					$data['siteurl'],
					$data['username']
				);
			}
		}
		else
		{
			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			if ($sendpassword)
			{
				$emailBody = Text::sprintf(
					'COM_USERS_EMAIL_REGISTERED_BODY',
					$data['name'],
					$data['sitename'],
					$data['siteurl'],
					$data['username'],
					$data['password_clear']
				);
			}
			else
			{
				$emailBody = Text::sprintf(
					'COM_USERS_EMAIL_REGISTERED_BODY_NOPW',
					$data['name'],
					$data['sitename'],
					$data['siteurl']
				);
			}
		}

		// Try to send the registration email.
		try
		{
			$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
		}
		catch (\Exception $exception)
		{
			try
			{
				Log::add(Text::_($exception->getMessage()), Log::WARNING, 'jerror');

				$return = false;
			}
			catch (\RuntimeException $exception)
			{
				Factory::getApplication()->enqueueMessage(Text::_($exception->errorMessage()), 'warning');

				$this->setError(Text::_('COM_MESSAGES_ERROR_MAIL_FAILED'), 500);

				$return = false;
			}
		}

		// Send Notification mail to administrators
		if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1))
		{
			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBodyAdmin = Text::sprintf(
				'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
				$data['name'],
				$data['username'],
				$data['siteurl']
			);

			// Get all admin users
			$query->clear()
				->select($db->quoteName(array('name', 'email', 'sendEmail')))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('sendEmail') . ' = 1')
				->where($db->quoteName('block') . ' = 0');

			$db->setQuery($query);

			try
			{
				$rows = $db->loadObjectList();
			}
			catch (\RuntimeException $e)
			{
				$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

				return false;
			}

			// Send mail to all superadministrators id
			foreach ($rows as $row)
			{
				try
				{
					$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
				}
				catch (\Exception $exception)
				{
					try
					{
						Log::add(Text::_($exception->getMessage()), Log::WARNING, 'jerror');

						$return = false;
					}
					catch (\RuntimeException $exception)
					{
						Factory::getApplication()->enqueueMessage(Text::_($exception->errorMessage()), 'warning');

						$return = false;
					}
				}

				// Check for an error.
				if ($return !== true)
				{
					$this->setError(Text::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));

					return false;
				}
			}
		}

		// Check for an error.
		if ($return !== true)
		{
			$this->setError(Text::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

			// Send a system message to administrators receiving system mails
			$db = $this->getDbo();
			$query->clear()
				->select($db->quoteName('id'))
				->from($db->quoteName('#__users'))
				->where($db->quoteName('block') . ' = ' . (int) 0)
				->where($db->quoteName('sendEmail') . ' = ' . (int) 1);
			$db->setQuery($query);

			try
			{
				$userids = $db->loadColumn();
			}
			catch (\RuntimeException $e)
			{
				$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

				return false;
			}

			if (count($userids) > 0)
			{
				$jdate = new Date;

				// Build the query to add the messages
				foreach ($userids as $userid)
				{
					$values = array(
						$db->quote($userid),
						$db->quote($userid),
						$db->quote($jdate->toSql()),
						$db->quote(Text::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT')),
						$db->quote(Text::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username']))
					);
					$query->clear()
						->insert($db->quoteName('#__messages'))
						->columns($db->quoteName(array('user_id_from', 'user_id_to', 'date_time', 'subject', 'message')))
						->values(implode(',', $values));
					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (\RuntimeException $e)
					{
						$this->setError(Text::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

						return false;
					}
				}
			}

			return false;
		}

		if ($useractivation == 1)
		{
			return 'useractivate';
		}
		elseif ($useractivation == 2)
		{
			return 'adminactivate';
		}
		else
		{
			return $user->id;
		}
	}
}
