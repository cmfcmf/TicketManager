<?php
/**
 * TicketManager
 *
 * @copyright  (c) TicketManager Team
 * @link       http://github.com/cmfcmf/TicketManager/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package    TicketManager
 * @subpackage Api
 */

/**
 * Admin Api.
 */
class TicketManager_Api_Admin extends Zikula_AbstractApi
{
    /**
     * Get available admin panel links.
     * 
     * @return array An array of admin links.
     */
    public function getlinks()
    {
        $links = array();

        if (SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'main'),
                            'text' => $this->__('Configuration'),
                            'class' => 'z-icon-es-config');
        }
        if (SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'generate'),
                            'text' => $this->__('Generate tickets'),
                            'class' => 'z-icon-es-gears');
        }
        if (SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'depreciate'),
                            'text' => $this->__('Depreciate'),
                            'class' => 'z-icon-es-cut');
        }
        if (SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'clearCache'),
                            'text' => $this->__('Clear cache'),
                            'class' => 'z-icon-es-cancel');
        }
        if (SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'help'),
                            'text' => $this->__('Help'),
                            'class' => 'z-icon-es-help');
        }
        if (SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'test'),
                            'text' => $this->__('Generate test-tickets'),
                            'class' => 'z-icon-es-cubes');
        }

        return $links;
    }

}
