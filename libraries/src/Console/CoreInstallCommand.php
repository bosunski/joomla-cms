<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Console;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installation\Model\ConfigurationModel;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Console\AbstractCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command for installing the Joomla CMS
 *
 * @since  4.0.0
 */
class CoreInstallCommand extends AbstractCommand
{
	/**
	 * Stores the Input Object
	 * @var
	 * @since 4.0
	 */
	private $cliInput;

	/**
	 * SymfonyStyle Object
	 * @var
	 * @since 4.0
	 */
	private $ioStyle;

	/**
	 * Configures the IO
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	private function configureIO()
	{
		$this->cliInput = $this->getApplication()->getConsoleInput();
		$this->ioStyle = new SymfonyStyle($this->getApplication()->getConsoleInput(), $this->getApplication()->getConsoleOutput());
//		$site_name = $this->ioStyle->ask('What is the name of your website?');
	}

	/**
	 * Execute the command.
	 *
	 * @return  integer  The exit code for the command.
	 *
	 * @since   4.0.0
	 */
	public function execute(): int
	{
		$this->configureIO();
		$options = $this->collectOptions();
		exit;
		$model = new ConfigurationModel;

		$options['site_name'] = $this->ioStyle->ask('What is the name of your website?');
		$options['admin_user'] = $this->ioStyle->ask('Username?');
		$options['admin_password'] = $this->ioStyle->ask('Password?');
		$options['admin_email'] = $this->ioStyle->ask('Email?');
		$options['db_type'] = 'mysql';
		$options['db_host'] =  $this->ioStyle->ask('Database Host?');
		$options['db_user'] =  $this->ioStyle->ask('Database Username?');
		$options['db_pass'] =  $this->ioStyle->ask('Database Password?');
		$options['db_name'] =  $this->ioStyle->ask('Database Name?');
		$options['db_prefix'] = 'lmao_';
		$options['helpurl'] = 'http://joomla.org';
		$options['db_old'] = 'remove';
		$options['language'] = 'en-GB';
		$model->setup($options);

		return 0;
	}

	/**
	 * Initialise the command.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function initialise()
	{
		$this->setName('core:install');

		$this->setDescription('Sets up the joomla CMS.');

		$help = "The <info>%command.name%</info> is used for setting up the Joomla CMS \n 
					<info>php %command.full_name%</info>";

		$this->setHelp($help);
	}


	public function getOptions()
	{
		return $options = [
			'site_name' => [
				'question'  => "What's the name of your website?",
				'type'      => 'question',
				'rules'     => 'isInteger',
			],
			'admin_user' => [
				'question'  => "Enter Admin username.",
				'type'      => 'question',
			],
			'admin_password' => [
				'question'  => "Enter admin password.",
				'type'      => 'question',
			],
			'admin_email' => [
				'question'  => "Enter admin email.",
				'type'      => 'question',
			],
			'db_type' => [
				'question'  => "What's your database type?",
				'type'      => 'select',
			],
			'db_host' => [
				'question'  => "Enter database host.",
				'type'      => 'question',
			],
			'db_user' => [
				'question'  => "Enter database user",
				'type'      => 'question',
			],
			'db_pass' => [
				'question'  => "Enter database password.",
				'type'      => 'question',
			],
			'db_name' => [
				'question'  => "Enter database name.",
				'type'      => 'question',
			],
			'db_prefix' => [
				'question'  => "Database prefix?",
				'type'      => 'question',
			],
			'helpurl' => [
				'question'  => "Help URL",
				'type'      => 'question',
			],
			'db_old' => [
				'question'  => "What do you want to do about old DB?",
				'type'      => 'select',
			],
			'language' => [
				'question'  => "Site Language",
				'type'      => 'select',
			],
		];
	}

	public function collectOptions()
	{
		$data = $this->getOptions();

		$options = [];

		foreach ($data as $key => $value)
		{
			$valid = false;


			while(!$valid) {
				$val = $this->processType($value);
				$rules = isset($value['rules']) ?? null;

				if ($rules)
				{
					$validator = $this->validateInput($val, $value['rules']);

					if ($validator === true)
					{
						$valid = true;
						$options[$key] = $val;
						break;
					}

					$this->ioStyle->error($validator['message']);
				}

				$options[$key] = $val;
			}
		}

		return $options;
	}

	public function processType($data, $message = null)
	{
		switch ($data['type'])
		{
			case 'question':
				return $this->ioStyle->ask($data['question']);
				break;
		}
	}

	public function validateInput($input, $validator)
	{
		$rules = explode('|', $validator);

		foreach ($rules as $key => $rule)
		{
			if (strpos($rule, ':') === false)
			{
				$valid = $this->{$rule}($input);

				if (is_array($valid))
				{
					return $valid;
				}
			}
			else
			{
				list($function, $arg) = explode(':', $rule);
				$valid = $this->{$function}($input, $arg);

				if (is_array($valid))
				{
					return $valid;
				}
			}
		}

		return true;
	}

	public function isInteger($input, $length = null)
	{
		if (!is_numeric($input))
		{
			return ['message' => 'The input must be an integer.'];
		}

		return true;
	}
}
