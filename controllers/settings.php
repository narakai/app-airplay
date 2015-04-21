<?php

/**
 * Airplay settings controller.
 *
 * @category   apps
 * @package    airplay
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2015 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/airplay/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \Exception as Exception;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Airplay settings controller.
 *
 * @category   apps
 * @package    airplay
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2015 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/airplay/
 */

class Settings extends ClearOS_Controller
{
    /**
     * SSH server settings controller.
     *
     * @return view
     */

    function index()
    {
        $this->_view_edit('view');
    }

    /**
     * SSH server settings controller.
     *
     * @return view
     */

    function edit()
    {
        $this->_view_edit('edit');
    }

    /**
     * Common edit/view controller.
     *
     * @param string $form_type form type
     *
     * @return view View
     */

    function _view_edit($form_type)
    {
        // Load dependencies
        //------------------

        $this->lang->load('base');
        $this->load->library('airplay/Avahi');

        // Set validation rules
        //---------------------

        $this->form_validation->set_policy('allowed_nics', 'airplay/Avahi', 'validate_allowed_nics', FALSE);
        $this->form_validation->set_policy('reflector', 'airplay/Avahi', 'validate_reflector', FALSE);

        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if ($this->input->post('submit') && $form_ok) {
            try {
                $this->avahi->set_allowed_nics($this->input->post('allowed_nics'));
                $this->avahi->set_reflector($this->input->post('reflector'));

                $this->avahi->reset(TRUE);
                $this->page->set_status_updated();
                redirect('/airplay');
            } catch (Engine_Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load view data
        //---------------

        try {
            $data['form_type'] = $form_type;
            $network_options = $this->avahi->get_interface_details();
            foreach ($network_options as $key => $value) {
                if (is_array($value))
                    $data['interfaces'][$key] = $key;
                else
                    $data['interfaces'][$key] = $value;
            }
            $data['reflector'] = $this->avahi->get_reflector();
            $data['allowed_nics'] = $this->avahi->get_allowed_nics();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('airplay/settings', $data, lang('base_settings'));
    }
}
