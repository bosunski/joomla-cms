<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.joomla
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 *
 * <!-- Open Graph -->
<meta property="og:type" content="article"/>
<meta property="og:description" content="{{ $page->getExcerpt() }}"/>
<meta property="og:title" content="{{ $page->title ?  $page->title . ' | ' : '' }}{{ $page->siteName }}"/>
<meta property="og:site_name" content="{{ $page->siteName }}"/>
<meta property="og:image" content="{{ $page->cover_image ? $page->cover_image : $page->defaultImage }}" />
<meta property="og:image:type" content="{{ $page->meta->facebook->image->type }}" />
<meta property="og:image:width" content="{{ $page->meta->facebook->image->width }}" />
<meta property="og:image:height" content="{{ $page->meta->facebook->image->height }}" />
<meta property="og:url" content="{{ $page->getUrl() }}">

<meta property="og:locale" content="{{ $page->meta->facebook->locale ? $page->meta->facebook->locale : 'en_US' }}">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="{{ $page->meta->twitter->site }}">
<meta name="twitter:title" content="{{ $page->title ?  $page->title . ' | ' : '' }}{{ $page->siteName }}">
<meta name="twitter:creator" content="{{ $page->meta->twitter->creator }}">
<meta name="twitter:description" content="{{ $page->getExcerpt() }}">
<meta name="twitter:image:src" content="{{ $page->cover_image ? $page->cover_image : $page->defaultImage }}">
<meta name="twitter:domain" content="{{ $page->meta->twitter->domain }}">

 */

defined('_JEXEC') or die;

use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Table\CoreContent;
use Joomla\CMS\User\User;
use Joomla\CMS\Workflow\Workflow;
use Joomla\CMS\Workflow\WorkflowServiceInterface;
use Joomla\Component\Content\Administrator\Table\ArticleTable;
use Joomla\Component\Messages\Administrator\Model\MessageModel;
use Joomla\Component\Workflow\Administrator\Model\StagesModel;
use Joomla\Component\Workflow\Administrator\Table\StageTable;
use Joomla\Component\Workflow\Administrator\Table\WorkflowTable;
use Joomla\Utilities\ArrayHelper;
use Joomla\Component\Config\Administrator\Helper\ConfigHelper;
/**
 * Example Content Plugin
 *
 * @since  1.6
 */
class PlgContentSeo extends CMSPlugin
{
	/**
	 * Database Driver Instance
	 *
	 * @var    \Joomla\Database\DatabaseDriver
	 * @since  4.0.0
	 */
	protected $db;

	protected $config;

	public function onAfterLegacyMetaGeneration($context, $document, $item): HtmlDocument
	{
		$this->config = $this->getContentConfig();

		$this->setTwitterCards($document, $item)
			->setFacebookOgpg($document, $item)
			->addSchemaContext($document);

		return $document;
	}

	/**
	 * @param $document
	 * @param $item
	 *
	 * @return PlgContentSeo
	 *
	 * @since version
	 */
	protected function setTwitterCards(&$document, $item): self
	{
		if (!empty($item->metadata['twitter_title']))
			$document->setMetaData('twitter:title', $item->metadata['twitter_title']);
		if (!empty($item->metadata['twitter_description']))
			$document->setMetaData('twitter:description', $item->metadata['twitter_description']);

		return $this;
	}

	/**
	 * @param $document
	 * @param $item
	 *
	 * @return PlgContentSeo
	 *
	 * @since version
	 */
	protected function setFacebookOgpg(&$document, $item): self
	{
		return $this;
	}

	/**
	 *
	 * @return stdClass
	 *
	 * @since version
	 */
	protected function getContentConfig(): stdClass
	{
		$query = $this->db->getQuery(true);

		$query->select('params')
			->from('#__extensions')
			->where('name=' . $query->quote('com_content'));
		$this->db->setQuery($query);

		$results = $this->db->loadColumn();

		return json_decode($results[0]);
	}

	/**
	 * @param HtmlDocument $document
	 *
	 * @return PlgContentSeo
	 *
	 * @since version
	 */
	protected function addSchemaContext(HtmlDocument &$document): self
	{
		$document->addScriptDeclaration($this->getSchemaScript(), 'application/ld+json');

		return $this;
	}

	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
	protected function getSchemaScript(): string
	{
		$template = <<< EOO
		{"@context":"https://schema.org","@type":"@site_type","url":"@url","sameAs":\["@facebook_link","https://twitter.com/@twitter_handle"],"@id":"#person","name":"@owner_name"}
EOO;
		$template = str_replace('@twitter_handle', $this->getTwitterHandle(), $template);
		$template = str_replace('@site_type', $this->config->site_type, $template);
		$template = str_replace('@facebook_link', $this->config->facebook, $template);
		$template = str_replace('@owner_name', $this->config->owner_name, $template);

		return $template;
	}

	/**
	 *
	 * @return string
	 *
	 * @since version
	 */
	private function getTwitterHandle(): string
	{
		return $this->config->twitter_handle;
	}
}
