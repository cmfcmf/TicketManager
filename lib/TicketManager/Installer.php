<?php
/**
 * @copyright Christian Flach
 */
class TicketManager_Installer extends Zikula_AbstractInstaller
{
	protected function getDefaultModVars()
	{
		return array();
	}

	/**
	 * Installer.
	 * @todo Create Databases
	 */
	public function install()
	{
		$this->setVars($this->getDefaultModVars());
		
		try {
			DoctrineHelper::createSchema($this->entityManager, array(
				'TicketManager_Entity_Tickets'
			));
		} catch (Exception $e) {
			return LogUtil::registerError($e);
		}
		
		return true;
	}

	public function upgrade($oldversion)
	{
		switch($oldversion)
		{
			default:
				return LogUtil::RegisterError($this->__('Upgrade of this version is not supported!'));
		}
		return true;
	}

	public function uninstall()
	{
		$this->delVars();
		
		DoctrineHelper::dropSchema($this->entityManager, array(
			'TicketManager_Entity_Tickets'
		));
		
		return true;
	}
}
