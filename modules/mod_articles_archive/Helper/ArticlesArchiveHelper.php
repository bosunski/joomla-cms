<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_archive
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\ArticlesArchive\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;

/**
 * Helper for mod_articles_archive
 *
 * @since  1.5
 */
class ArticlesArchiveHelper
{
	/**
	 * Retrieve list of archived articles
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public static function getList(&$params)
	{
		// Get application
		$app = Factory::getApplication();

		// Get database
		$db    = Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select($query->month($db->quoteName('created')) . ' AS created_month')
			->select('MIN(' . $db->quoteName('created') . ') AS created')
			->select($query->year($db->quoteName('created')) . ' AS created_year')
			->from($db->quoteName('#__content', 'c'))
			->innerJoin($db->quoteName('#__workflow_associations', 'wa') . ' ON wa.item_id = c.id')
			->innerJoin($db->quoteName('#__workflow_stages', 'ws') . ' ON wa.stage_id = ws.id')
			->where($db->quoteName('ws.condition') . ' = ' . (int) ContentComponent::CONDITION_ARCHIVED)
			->group($query->year($db->quoteName('c.created')) . ', ' . $query->month($db->quoteName('c.created')))
			->order($query->year($db->quoteName('c.created')) . ' DESC, ' . $query->month($db->quoteName('c.created')) . ' DESC');

		// Filter by language
		if ($app->getLanguageFilter())
		{
			$query->where('language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		$query->setLimit((int) $params->get('count'), 0);
		$db->setQuery($query);

		try
		{
			$rows = (array) $db->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
			$app->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');

			return [];
		}

		$menu   = $app->getMenu();
		$item   = $menu->getItems('link', 'index.php?option=com_content&view=archive', true);
		$itemid = (isset($item) && !empty($item->id)) ? '&Itemid=' . $item->id : '';

		$i     = 0;
		$lists = array();

		foreach ($rows as $row)
		{
			$date = Factory::getDate($row->created);

			$createdMonth = $date->format('n');
			$createdYear  = $date->format('Y');

			$createdYearCal = HTMLHelper::_('date', $row->created, 'Y');
			$monthNameCal   = HTMLHelper::_('date', $row->created, 'F');

			$lists[$i] = new \stdClass;

			$lists[$i]->link = Route::_('index.php?option=com_content&view=archive&year=' . $createdYear . '&month=' . $createdMonth . $itemid);
			$lists[$i]->text = Text::sprintf('MOD_ARTICLES_ARCHIVE_DATE', $monthNameCal, $createdYearCal);

			$i++;
		}

		return $lists;
	}
}
