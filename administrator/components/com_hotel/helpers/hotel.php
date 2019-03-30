<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hotel
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted Access');

/**
 * Hotel component helper.
 *
 * @since  1.6
 */
class HotelHelper extends JHelperContent
{
    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     *
     * @since   1.6
     */
    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_HOTEL_SUBMENU_GUESTS'),
            'index.php?option=com_hotel&view=guests',
            $vName == 'guests'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_HOTEL_SUBMENU_RESERVATIONS'),
            'index.php?option=com_hotel&view=reservations',
            $vName == 'reservations'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_HOTEL_SUBMENU_ROOMS'),
            'index.php?option=com_hotel&view=rooms',
            $vName == 'rooms'
        );
    }
}
