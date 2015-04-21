<?php

/**
 * Avahi server class.
 *
 * @category   apps
 * @package    airplay
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2015 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/airplay/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\airplay;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('airplay');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\Daemon as Daemon;
use \clearos\apps\base\File as File;
use \clearos\apps\network\Iface as Iface;
use \clearos\apps\network\Iface_Manager as Iface_Manager;

clearos_load_library('base/Daemon');
clearos_load_library('base/File');
clearos_load_library('network/Iface');
clearos_load_library('network/Iface_Manager');

// Exceptions
//-----------

use \clearos\apps\base\Validation_Exception as Validation_Exception;

clearos_load_library('base/Validation_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Avahi server class.
 *
 * @category   apps
 * @package    airplay
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011-2014 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/airplay/
 */

class Avahi extends Daemon
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const FILE_CONFIG = '/etc/avahi/avahi-daemon.conf';

    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    protected $is_loaded = false;
    protected $config = array();

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Avahi constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct('avahi-daemon');
    }

    /**
     * Returns allow nics.
     *
     * @return integer port number
     * @throws Engine_Exception
     */

    public function get_allowed_nics()
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! $this->is_loaded)
            $this->_load_config();

        if (isset($this->config['allow-interfaces']))
            return array_map('trim', explode(',', $this->config['allow-interfaces']));
        else
            return array();
    }

    /**
     * Returns reflector.
     *
     * @return boolean reflector enable/disable
     * @throws Engine_Exception
     */

    public function get_reflector()
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! $this->is_loaded)
            $this->_load_config();

        return ($this->config['enable-reflector'] == 'yes' ? TRUE : FALSE);
    }

    /**
     * Get interface details.
     *
     * @return array
     *
     * @throws Engine_Exception
     */
    public function get_interface_details()
    {

        clearos_profile(__METHOD__, __LINE__);

        if (! $this->is_loaded)
            $this->_load_config();

        $iface_manager = new Iface_Manager();
        $ifaces = $iface_manager->get_interface_details();

        return $ifaces;
    }

    /**
     * Sets allowed NICs.
     *
     * @param array $allow allowed nics
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    public function set_allowed_nics($allow)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_allowed_nics($allow));

        $this->_set_parameter('allow-interfaces', implode(',', $allow));
    }

    /**
     * Sets reflector.
     *
     * @param boolean $reflector enable/disable reflector
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    public function set_reflector($reflector)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_reflector($reflector));

        if ($reflector)
            $reflector = 'yes';
        else
            $reflector = 'no';

        $this->_set_parameter('enable-reflector', $reflector);
    }

    ///////////////////////////////////////////////////////////////////////////////
    // V A L I D A T I O N
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Validates allow nics.
     *
     * @param array $allow allowable nics
     *
     * @return string error message if allowed nics is invalid
     */
    
    public function validate_allowed_nics($allow)
    {
        clearos_profile(__METHOD__, __LINE__);
        // TODO
    }

    /**
     * Validates reflector.
     *
     * @param boolean $reflector reflector $policy
     *
     * @return string error message if reflector is invalid
     */
    
    public function validate_reflector($reflector)
    {
        clearos_profile(__METHOD__, __LINE__);
        // TODO
    }

    ///////////////////////////////////////////////////////////////////////////////
    // P R I V A T E  M E T H O D S 
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Loads configuration files.
     *
     * @access private
     * @return void
     * @throws Engine_Exception
     */

    protected function _load_config()
    {
        clearos_profile(__METHOD__, __LINE__);

        $lines = array();

        try {
            $file = new File(self::FILE_CONFIG, TRUE);

            $lines = $file->get_contents_as_array();

        } catch (Exception $e) {
            throw new Engine_Exception($e->GetMessage(), COMMON_WARNING);
        }

        $matches = array();

        foreach ($lines as $line) {
            if (preg_match("/^#/", $line) || preg_match("/^\s*$/", $line)) {
                continue;
            } else if (preg_match("/(.*)=(.*)/", $line, $matches)) {
                $key = $matches[1];
                $value = $matches[2];
                $this->config[$key] = $value;
            }

        }

        $this->is_loaded = TRUE;
    }

    /**
     * Sets a parameter in the config file.
     *
     * @param string $key   name of the key in the config file
     * @param string $value value for the key
     *
     * @access private
     * @return void
     * @throws Engine_Exception
     */

    protected function _set_parameter($key, $value)
    {
        clearos_profile(__METHOD__, __LINE__);

        $file = new File(self::FILE_CONFIG);

        $match = $file->replace_lines("/^$key\s*=/", "$key=$value\n");

        if ($match === 0) {
            $match = $file->replace_lines("/^#\s*$key\s*=/", "$key=$value\n");
            if ($match === 0)
                $file->add_lines("$key=$value\n");
        }

        $this->is_loaded = FALSE;
    }
}
