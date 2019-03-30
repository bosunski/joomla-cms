<?php
/**
 * @package    Joomla.Administrator
 * @subpackage com_hotel
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JFormHelper::loadFieldClass('list');

/**
 * Guest Form Field class for the Hotel component
 *
 * @since 1.0
 */

class JFormFieldFloor extends JFormFieldList
{
    /**
    * The field type.
    *
    * @var         string
    */
    protected $type = 'Floor';

    /**
    * Method to get a list of options for a list input.
    *
    * @return  array  An array of JHtml options.
    */
    protected function getOptions()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('DISTINCT floor');
        $query->from('#__hotel_room');
        $db->setQuery((string) $query);

        $floors = $db->loadObjectList();

        $options = array();

        if ($floors) {
            foreach ($floors as $floor) {
                $options[] = JHtml::_('select.option', $floor->floor, $floor->floor);
            }
        }

        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}