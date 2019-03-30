<?php
/**
 * @package    Joomla.Administrator
 * @subpackage com_hotel
 *
 * @copyright  Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die();

/**
 * Reservation Controller
 *
 * @since 1.0
 */
class HotelControllerReservation extends JControllerForm
{
    /**
     * Method to run batch operations.
     *
     * @param   object  $model  The model.
     *
     * @return  boolean   True if successful, false otherwise and internal error is set.
     *
     * @since   1.0
     */
    public function batch($model = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model = $this->getModel('Reservation', 'HotelModel', array());

        // Preset the redirect
        $this->setRedirect(
            JRoute::_(
                'index.php?option=com_hotel&view=reservations' . $this->getRedirectToListAppend(),
                false
            )
        );
        return parent::batch($model);
    }
}
