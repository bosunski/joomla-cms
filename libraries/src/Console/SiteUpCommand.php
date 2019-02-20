<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Console;

defined('JPATH_PLATFORM') or die;

use Joomla\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command wrapper for getting the site into offline mode
 *
 * @since  4.0.0
 */
class SiteUpCommand extends AbstractCommand
{
	/**
	 * SymfonyStyle Object
	 * @var SymfonyStyle
	 * @since 4.0
	 */
	private $ioStyle;

	/**
	 * Return code if site:up failed
	 * @since 4.0
	 */
	const SITE_UP_FAILED = 1;

	/**
	 * Return code if site:up was successful
	 * @since 4.0
	 */
	const SITE_UP_SUCCESSFUL = 0;

	/**
	 * Configures the IO
	 *
	 * @return void
	 *
	 * @since 4.0
	 */
	private function configureIO()
	{
		$this->ioStyle = new SymfonyStyle($this->getApplication()->getConsoleInput(), $this->getApplication()->getConsoleOutput());
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
		$this->setName('site:up');
		$this->setDescription('Puts the site into online mode');

		$help = "The <info>%command.name%</info> Puts the site into online mode
				\nUsage: <info>php %command.full_name%</info>";

		$this->setHelp($help);
	}

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface  $input  The input to inject into the command.
	 * @param   OutputInterface $output The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$this->configureIO();

		$command = $this->getApplication()->getCommand('config:set');

		$command->setOptions('offline=false');

		$returnCode = $command->doExecute($input, $output);

		if ($returnCode === 0)
		{
			$this->ioStyle->success("Website is now online");

			return self::SITE_UP_SUCCESSFUL;
		}

		return self::SITE_UP_FAILED;
	}
}
