<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hotel
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Hotel Reservations Table class
 *
 * @since  0.0.1
 */
class HotelTableReservations extends JTable
{
    /**
     * Constructor
     *
     * @param   JDatabaseDriver  &$db  A database connector object
     */
    function __construct(&$db)
    {
        parent::__construct('#__hotel_reservation', 'id', $db);
    }

    /**
     * Method to store a row in the database from the Table instance properties.
     *
     * If a primary key value is set the row with that primary key value will be
     * updated with the instance property values.
     * If no primary key value is set a new row will be inserted into the database
     * with the properties from the Table instance.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   11.1
     */
    function store($updateNulls = false)
    {
        // The room id selected in the form is not really an id but a preferrence
        // for a floor, little workaround to get a room id from that floor, if possible
        $this->room_id =JModelList::getInstance('rooms', 'HotelModel')->getAvailableRoom($this->room_id);

        return parent::store($updateNulls);
    }
}
