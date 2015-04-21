<?php

/**
 * Airplay settings view.
 *
 * @category   apps
 * @package    airplay
 * @subpackage views
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
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('base');
$this->lang->load('network');
$this->lang->load('airplay');

///////////////////////////////////////////////////////////////////////////////
// Form type handling
///////////////////////////////////////////////////////////////////////////////

if ($form_type === 'edit') {
    $read_only = FALSE;
    $buttons = array(
        form_submit_update('submit'),
        anchor_cancel('/app/airplay/settings')
    );
} else {
    $read_only = TRUE;
    $buttons = array(
        anchor_edit('/app/airplay/settings/edit')
    );
}


///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('airplay/settings/edit');
echo form_header(lang('base_settings'));

echo field_multiselect_dropdown('allowed_nics[]', $interfaces, $allowed_nics, lang('airplay_interfaces'), $read_only);
echo field_toggle_enable_disable('reflector', $reflector, lang('airplay_reflector'), $read_only);

echo field_button_set($buttons);

echo form_footer();
echo form_close();
