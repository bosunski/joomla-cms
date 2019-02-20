<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\CMS\Tree;

defined('JPATH_PLATFORM') or die;

/**
 * Interface for a node class
 *
 * @since  __DEPLOY_VERSION__
 */
interface NodeInterface extends ImmutableNodeInterface
{
	/**
	 * Set the parent of this node
	 *
	 * If the node already has a parent, the link is unset
	 *
	 * @param   NodeInterface|null  $parent  NodeInterface for the parent to be set or null
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setParent(NodeInterface $parent);

	/**
	 * Add child to this node
	 *
	 * If the child already has a parent, the link is unset
	 *
	 * @param   NodeInterface  $child  The child to be added.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addChild(NodeInterface $child);

	/**
	 * Remove a specific child
	 *
	 * @param   NodeInterface  $child  Child to remove
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function removeChild(NodeInterface $child);

	/**
	 * Function to set the left or right sibling of a node
	 *
	 * @param   NodeInterface  $sibling  NodeInterface object for the sibling
	 * @param   boolean        $right    If set to false, the sibling is the left one
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setSibling(NodeInterface $sibling, $right = true);
}
