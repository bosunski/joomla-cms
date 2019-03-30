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
 * Guests Controller
 *
 * @since  0.0.1
 */
class HotelControllerGuests extends JControllerAdmin
{
    /**
    * Proxy for getModel.
    *
    * @param string $name   The model name. Optional.
    * @param string $prefix The class prefix. Optional.
    * @param array  $config Configuration array for model. Optional.
    *
    * @return  object  The model.
    *
    * @since   1.0
    */
    public function getModel($name = 'Guest', $prefix = 'HotelModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
