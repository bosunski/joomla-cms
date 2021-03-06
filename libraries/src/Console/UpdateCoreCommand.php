<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Console;

defined('JPATH_PLATFORM') or die;

use Joomla\Application\Cli\CliInput;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


/**
 * Console command for updating Joomla! core
 *
 * @since  4.0.0
 */
class UpdateCoreCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected static $defaultName = 'core:update';

	/**
	 * Stores the Input Object
	 * @var CliInput
	 * @since 4.0
	 */
	private $cliInput;

	/**
	 * SymfonyStyle Object
	 * @var SymfonyStyle
	 * @since 4.0
	 */
	private $ioStyle;

	/**
	 * Update Information
	 * @var array
	 * @since 4.0
	 */
	public $updateInfo;

	/**
	 * Update Model
	 * @var array
	 * @since 4.0
	 */
	public $updateModel;

	/**
	 * Progress Bar object
	 * @var ProgressBar
	 * @since 4.0
	 */
	public $progressBar;

	/**
	 * Return code for successful update
	 * @since 4.0
	 */
	const UPDATE_SUCCESSFUL = 0;

	/**
	 * Return code for failed update
	 * @since 4.0
	 */
	const ERR_UPDATE_FAILED = 2;

	/**
	 * Return code for failed checks
	 * @since 4.0
	 */
	const ERR_CHECKS_FAILED = 1;

	/**
	 * @var DatabaseInterface
	 * @since 4.0
	 */
	private $db;

	/**
	 * UpdateCoreCommand constructor.
	 *
	 * @param   DatabaseInterface  $db  Database Instance
	 *
	 * @since 4.0
	 */
	public function __construct(DatabaseInterface $db)
	{
		$this->db = $db;
		parent::__construct();
	}

	/**
	 * Configures the IO
	 *
	 * @param   InputInterface   $input   Console Input
	 * @param   OutputInterface  $output  Console Output
	 *
	 * @return void
	 *
	 * @since 4.0
	 *
	 */
	private function configureIO(InputInterface $input, OutputInterface $output)
	{
		ProgressBar::setFormatDefinition('custom', ' %current%/%max% -- %message%');
		$this->progressBar = new ProgressBar($output, 8);
		$this->progressBar->setFormat('custom');

		$this->cliInput = $input;
		$this->ioStyle = new SymfonyStyle($input, $output);
	}

	/**
	 * Run Checks after Update
	 *
	 * @param   boolean  $purge  To remove or not remove all updates from table
	 *
	 * @return boolean
	 *
	 * @since 4.0
	 *
	 * @throws \Exception
	 */
	public function runChecks($purge = false)
	{
		$model = $this->getUpdateModel();

		if ($purge)
		{
			$model->purge();
		}

		$query = $this->db->getQuery(true);
		$query->select('version');
		$query->from('#__updates');
		$query->where(['element = "joomla"']);
		$this->db->setQuery((string) $query);
		$update = $this->db->loadObjectList();
		$update = count($update) > 0 ? $update : null;

		if ($update)
		{
			return false;
		}

		return true;
	}

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws \Exception
	 */
	public function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$this->configureIO($input, $output);

		$this->progressBar->setMessage("Starting up ...");
		$this->progressBar->start();

		$model = $this->getUpdateModel();

		$this->setUpdateInfo($model->getUpdateInformation());

		$this->progressBar->advance();
		$this->progressBar->setMessage('Running checks ...');

		if ($this->runChecks())
		{
			$this->progressBar->finish();
			$this->ioStyle->note('You already have the latest Joomla! version.');

			return self::ERR_CHECKS_FAILED;
		}

		$this->progressBar->advance();
		$this->progressBar->setMessage('Starting Joomla! update ...');

		$this->progressBar->finish();

		if ($this->updateJoomlaCore($model) && $this->runChecks(true))
		{
			$this->ioStyle->success('Joomla core updated successfully!');

			return self::UPDATE_SUCCESSFUL;
		}

		$this->ioStyle->note('Update cannot be performed.');

		return self::ERR_UPDATE_FAILED;
	}

	/**
	 * Initialise the command.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	protected function configure()
	{
		$this->setDescription('Updates joomla core');

		$this->setHelp(<<<'EOF'
The <info>%command.name%</info> is used to update Joomla.

  <info>php %command.full_name%</info>
EOF
		);
	}

	/**
	 * Update Core Joomla
	 *
	 * @param   mixed  $updatemodel  Update Model
	 *
	 * @return  boolean  success
	 *
	 * @since 4.0
	 */
	private function updateJoomlaCore($updatemodel)
	{
		$updateInformation = $this->updateInfo;

		if (!empty($updateInformation['hasUpdate']))
		{
			$this->progressBar->advance();
			$this->progressBar->setMessage("Processing update package ...");
			$package = $this->processUpdatePackage($updateInformation);

			$this->progressBar->advance();
			$this->progressBar->setMessage("Finalizing update ...");
			$result = $updatemodel->finaliseUpgrade();

			if ($result)
			{
				$this->progressBar->advance();
				$this->progressBar->setMessage("Cleaning up ...");

				// Remove the xml
				if (file_exists(JPATH_BASE . '/joomla.xml'))
				{
					File::delete(JPATH_BASE . '/joomla.xml');
				}

				InstallerHelper::cleanupInstall($package['file'], $package['extractdir']);

				return true;
			}
		}

		return false;
	}

	/**
	 * Sets the update Information
	 *
	 * @param   array  $data  Stores the update information
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function setUpdateInfo($data)
	{
		$this->updateInfo = $data;
	}

	/**
	 * Retrieves the Update model from com_joomlaupdate
	 *
	 * @return mixed
	 *
	 * @since 4.0
	 *
	 * @throws \Exception
	 */
	public function getUpdateModel()
	{
		if (!isset($this->updateModel))
		{
			$this->setUpdateModel();
		}

		return $this->updateModel;
	}

	/**
	 * Sets the Update Model
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function setUpdateModel()
	{
		$app = $this->getApplication();
		$updatemodel = $app->bootComponent('com_joomlaupdate')->getMVCFactory($app)->createModel('Update', 'Administrator');

		if (is_bool($updatemodel))
		{
			$this->updateModel = $updatemodel;

			return;
		}

		$updatemodel->purge();
		$updatemodel->refreshUpdates(true);

		$this->updateModel = $updatemodel;
	}

	/**
	 * Downloads and extracts the update Package
	 *
	 * @param   array  $updateInformation  Stores the update information
	 *
	 * @return array | boolean
	 *
	 * @since 4.0
	 */
	public function processUpdatePackage($updateInformation)
	{
		if (!$updateInformation['object'])
		{
			return false;
		}

		$this->progressBar->advance();
		$this->progressBar->setMessage("Downloading update package ...");
		$file = $this->downloadFile($updateInformation['object']->downloadurl->_data);

		$tmpPath    = $this->getApplication()->get('tmp_path');
		$updatePackage = $tmpPath . '/' . $file;

		$this->progressBar->advance();
		$this->progressBar->setMessage("Extracting update package ...");
		$package = $this->extractFile($updatePackage);

		$this->progressBar->advance();
		$this->progressBar->setMessage("Copying files ...");
		$this->copyFileTo($package['extractdir'], JPATH_BASE);

		return ['file' => $updatePackage, 'extractdir' => $package['extractdir']];
	}

	/**
	 * Downloads the Update file
	 *
	 * @param   string  $url  URL to update file
	 *
	 * @return boolean | string
	 *
	 * @since 4.0
	 */
	public function downloadFile($url)
	{
		$file = InstallerHelper::downloadPackage($url);

		if (!$file)
		{
			return false;
		}

		return $file;
	}

	/**
	 * Extracts Update file
	 *
	 * @param   string  $file  Full path to file location
	 *
	 * @return array | boolean
	 *
	 * @since 4.0
	 */
	public function extractFile($file)
	{
		$package = InstallerHelper::unpack($file, true);

		return $package;
	}

	/**
	 * Copy a file to a destination directory
	 *
	 * @param   string  $file  Full path to file
	 * @param   string  $dir   Destination directory
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	public function copyFileTo($file, $dir)
	{
		Folder::copy($file, $dir, '', true);
	}
}
