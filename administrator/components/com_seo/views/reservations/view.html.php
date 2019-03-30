<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hotel
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Hotel View
 *
 * @since  0.0.1
 */
class HotelViewReservations extends JViewLegacy
{
    /**
     * Display the Hotel Reservations view
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $context = "hotel.list.admin.reservation";

        // Get data from the model
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->filter_order = $app->getUserStateFromRequest(
            $context . 'filter_order',
            'filter_order',
            'id',
            'cmd'
        );
        $this->filter_order_Dir = $app->getUserStateFromRequest(
            $context . 'filter_order_Dir',
            'filter_order_Dir',
            'asc',
            'cmd'
        );
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        HotelHelper::addSubmenu('reservations');
        JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

        // Set the toolbar
        $this->sidebar = JHtmlSidebar::render();
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    protected function addToolBar()
    {
        JToolbarHelper::title(JText::_('COM_HOTEL_VIEW_RESERVATIONS_TITLE'));

        JToolbarHelper::addNew('reservation.add');
        JToolbarHelper::editList('reservation.edit');
        JToolBarHelper::publish('reservations.publish');
        JToolBarHelper::unpublish('reservations.unpublish');
        JToolbarHelper::archiveList('reservations.archive');
        JToolbarHelper::trash('reservations.trash');
        JToolBarHelper::deleteList('', 'reservations.delete');
        JToolBarHelper::preferences('com_hotel');
    }

    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_HOTEL_ADMINISTRATION_RESERVATIONS_WINDOW_TITLE'));
    }
}