<?php
/**
 * TicketManager
 *
 * @copyright  (c) TicketManager Team
 * @link       http://github.com/cmfcmf/TicketManager/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package    TicketManager
 * @subpackage Version
 */

/**
 * TicketManager Version Info.
 */
class TicketManager_Version extends Zikula_AbstractVersion
{
    public function getMetaData()
    {
        $modversion['name']           = 'TicketManager';
        $modversion['displayname']    = 'TicketManager';
        $modversion['description']    = $this->__('Manage your TICKETS easy!');
        $modversion['version']        = '0.0.7';
        $modversion['official']       = false;
        $modversion['author']         = 'Christian Flach';
        $modversion['contact']        = 'cmfcmf.flach@gmail.com';
        $modversion['admin']          = true;
        $modversion['user']           = false;
        $modversion['core_min'] = '1.3.0';
        $modversion['core_max'] = '1.3.99';

        return $modversion;
    }

}
