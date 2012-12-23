<?php
/**
 * @copyright Christian Flach
 */
class TicketManager_Controller_Admin extends Zikula_AbstractController
{
	protected function postInitialize()
	{
		$this->view->setCaching(Zikula_View::CACHE_DISABLED);
	}
	
	/**
	 * The default entrypoint.
	 *
	 * @return void
	 */
	public function main()
	{
		if (!SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
			return LogUtil::registerPermissionError();
		}
		
		return $this->view->fetch('Admin/Main.tpl');
	}
	
	/**
	 * The Profile help page.
	 *
	 * @return string The rendered template output.
	 */
	public function help()
	{
		if (!SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
			return LogUtil::registerPermissionError();
		}

		return $this->view->fetch('Admin/Help.tpl');
	}
	
	public function test()
	{
		$return = ModUtil::apiFunc($this->name, 'Ticket', 'reserve', 
			array('number' => 2,
				'eventname' =>'Testevent', 
				'picture' => null, //Path to big picture
				'module' => ModUtil::getIdFromName($this->name),
				'logo' => null, //Path to little logo
				'price' => 4,
				'information' => array('uid' => 373),
				'startdate' => '15.3.2013 12:00',
				'shortdescription' => 'Lorem ipsum latinum dolorem sint sant sunt.'
				)
		);
		
		echo $return;
		return true;
	}
}
