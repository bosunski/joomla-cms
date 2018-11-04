<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_languages
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Languages\Administrator\View\Languages;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Languages\Administrator\Helper\LanguagesHelper;

/**
 * HTML Languages View class for the Languages component.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * An array of items
	 *
	 * @var  array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var  \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * The model state
	 *
	 * @var    \JObject
	 * @since  4.0.0
	 */
	protected $state;

	/**
	 * Form object for search filters
	 *
	 * @var    \JForm
	 * @since  4.0.0
	 */
	public $filterForm;

	/**
	 * The active search filters
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	public $activeFilters;

	/**
	 * The sidebar markup
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $sidebar;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		LanguagesHelper::addSubmenu('languages');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);
		}

		$this->addToolbar();
		$this->sidebar = \JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$canDo = ContentHelper::getActions('com_languages');

		ToolbarHelper::title(Text::_('COM_LANGUAGES_VIEW_LANGUAGES_TITLE'), 'comments-2 langmanager');

		if ($canDo->get('core.create'))
		{
			ToolbarHelper::addNew('language.add');
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.published') != 2)
			{
				ToolbarHelper::publishList('languages.publish');
				ToolbarHelper::unpublishList('languages.unpublish');
			}
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'languages.delete', 'JTOOLBAR_EMPTY_TRASH');
			ToolbarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			ToolbarHelper::trash('languages.trash');
			ToolbarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			// Add install languages link to the lang installer component.
			$bar = Toolbar::getInstance('toolbar');
			$bar->appendButton('Link', 'upload', 'COM_LANGUAGES_INSTALL', 'index.php?option=com_installer&view=languages');
			ToolbarHelper::divider();

			ToolbarHelper::preferences('com_languages');
			ToolbarHelper::divider();
		}

		ToolbarHelper::help('JHELP_EXTENSIONS_LANGUAGE_MANAGER_CONTENT');

		\JHtmlSidebar::setAction('index.php?option=com_languages&view=languages');

	}

	/**
	 * Returns an array of fields the table can be sorted by.
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value.
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering'     => Text::_('JGRID_HEADING_ORDERING'),
			'a.published'    => Text::_('JSTATUS'),
			'a.title'        => Text::_('JGLOBAL_TITLE'),
			'a.title_native' => Text::_('COM_LANGUAGES_HEADING_TITLE_NATIVE'),
			'a.lang_code'    => Text::_('COM_LANGUAGES_FIELD_LANG_TAG_LABEL'),
			'a.sef'          => Text::_('COM_LANGUAGES_FIELD_LANG_CODE_LABEL'),
			'a.image'        => Text::_('COM_LANGUAGES_HEADING_LANG_IMAGE'),
			'a.access'       => Text::_('JGRID_HEADING_ACCESS'),
			'a.lang_id'      => Text::_('JGRID_HEADING_ID')
		);
	}
}
