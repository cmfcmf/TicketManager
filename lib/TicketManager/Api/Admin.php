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
		
		$links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'test'),
			'text' => $this->__('Generate test-tickets'));
		
		$links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'depreciate'),
			'text' => $this->__('Depreciate'));
		/*
		if (SecurityUtil::checkPermission('Profile::', '::', ACCESS_ADD)) {
			$links[] = array('url' => ModUtil::url('Profile', 'admin', 'newdud'),
				'text' => $this->__('Create new personal info item'),
				'class' => 'z-icon-es-new');
		}
		if (SecurityUtil::checkPermission('Profile::', '::', ACCESS_ADMIN)) {
			$links[] = array('url' => ModUtil::url('Profile', 'admin', 'modifyconfig'),
				'text' => $this->__('User account panel settings'),
				'class' => 'z-icon-es-config');
		}
		*/
		if (SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
			$links[] = array('url' => ModUtil::url('TicketManager', 'admin', 'help'),
				'text' => $this->__('Help'),
				'class' => 'z-icon-es-help');
		}

		return $links;
	}

}
