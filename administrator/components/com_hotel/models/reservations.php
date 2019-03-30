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
 * Reservations Model
 *
 * @since  0.0.1
 */
class HotelModelReservations extends JModelList
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
        $query->select('r.id, r.guest_id, g.name, room.floor, ' .
                       'room.cost, r.checkin_date, r.checkout_date, r.room_id, r.obs')
                ->from($db->quoteName('#__hotel_reservation') . ' AS r')
                ->join('LEFT', '#__hotel_guest AS g ON r.guest_id = g.id')
                ->join('LEFT', '#__hotel_room AS room ON r.room_id = room.id');

        // Add the list ordering clause.
        $orderCol   = $this->state->get('list.ordering', 'id');
        $orderDirn  = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
