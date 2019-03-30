<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

class HiModelHi extends JModelItem
{
	protected $msg;

	public function getMsg()
	{
		if (!isset($this->msg))
		{
			$this->msg = "Oga ooo, this component sef";
		}

		return $this->msg;
	}

	/**
	 * Method to get an item.
	 *
	 * @param   integer $pk The id of the item
	 *
	 * @return  object
	 *
	 * @since 4.0.0
	 * @throws \Exception
	 */
	public function getItem($pk = null)
	{
		// TODO: Implement getItem() method.
	}
}
