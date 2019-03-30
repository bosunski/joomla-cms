<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Rooms Model
 *
 * @since  0.0.1
 */
class HotelModelRooms extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id',
                'floor',
                'empty',
                'ordering',
                'state'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        // Initialize variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Create the base select statement.
        $query->select('*')
            ->from($db->quoteName('#__hotel_room'));

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            $query->where('published = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('published IN (0, 1)');
        }

        // Add the list ordering clause.
        $orderCol   = $this->state->get('list.ordering', 'id');
        $orderDirn  = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     * Returns the id of an available room in a given floor if there is one,
     * otherwise in a room from any floor
     *
     * @param int $pref_floor preferred floor
     *
     * @return int $room_id the id of the room available
     */
    public function getAvailableRoom($pref_floor)
    {
        // Initialize variables.
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Create the base select statement.
        $query->select('id, floor')
            ->from($db->quoteName('#__hotel_room'))
            ->where("empty = true");

        $db->setQuery($query);
        $rooms = $db->loadObjectList();
        // Shuffles the result, so the hotel doesn't get filled
        // from bottom to top, all guests side by side
        shuffle($rooms);

        foreach ($rooms as $room) {
            $room = get_object_vars($room);
            if ($pref_floor === $room['floor']) {
                // room found on preferred floor
                return $room['id'];
            }
        }
        // no available room on preferred floor, returning any other available room
        return get_object_vars($rooms{0})['id'];
    }
}
