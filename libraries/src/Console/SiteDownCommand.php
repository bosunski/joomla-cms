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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console command wrapper for getting the site into offline mode
 *
 * @since  4.0.0
 */
class SiteDownCommand extends AbstractCommand
{
	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected static $defaultName = 'site:down';

	/**
	 * SymfonyStyle Object
	 * @var SymfonyStyle
	 * @since 4.0
	 */
	private $ioStyle;

	/**
	 * Return code if site:down failed
	 * @since 4.0
	 */
	const SITE_DOWN_FAILED = 1;

	/**
	 * Return code if site:down was successful
	 * @since 4.0
	 */
	const SITE_DOWN_SUCCESSFUL = 0;

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
		$this->ioStyle = new SymfonyStyle($input, $output);
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
		$this->setDescription('Puts the site into offline mode');

		$this->setHelp(<<<'EOF'
The <info>%command.name%</info> is used to place the site offline.

  <info>php %command.full_name%</info>
EOF
		);
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
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$this->configureIO($input, $output);

		$returnCode = $this->getApplication()->getCommand(SetConfigurationCommand::getDefaultName())->execute(
			new ArrayInput(['options' => ['offline=true']]), $output
		);

		if ($returnCode === 0)
		{
			$this->ioStyle->success("Successfully set site to offline");

			return self::SITE_DOWN_SUCCESSFUL;
		}

		return self::SITE_DOWN_FAILED;
	}
}
