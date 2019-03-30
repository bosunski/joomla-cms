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

/**
 * Reservation View
 *
 * @since 1.0
 */
class HotelViewReservation extends JViewLegacy
{
    /**
    * View form
    *
    * @var         form
    */
    protected $form = null;

    /**
    * Display the reservation view
    *
    * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
    *
    * @throws Exception
    *
    * @return void
    *
    * @since 1.0
    */
    public function display($tpl = null)
    {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors), 500);

            return false;
        }


        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);
    }

    /**
    * Add the page title and toolbar.
    *
    * @return  void
    *
    * @since   1.0
    */
    protected function addToolBar()
    {
        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);

        $is_new = empty($this->item->id);

        if ($is_new) {
            $title = JText::_('COM_HOTEL_VIEW_RESERVATIONS_NEW');
        } else {
            $title = JText::_('COM_HOTEL_VIEW_RESERVATIONS_EDIT');
        }

        JToolbarHelper::title($title);
        JToolBarHelper::apply('reservation.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('reservation.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('reservation.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        if (!$is_new) {
            // Show save as copy button if editing existing item
            JToolBarHelper::custom('reservation.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }
        JToolbarHelper::cancel('reservation.cancel', ($is_new ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'));
    }

}
