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
			case '0.0.1':
			case '0.0.2':
				try {
					DoctrineHelper::dropSchema($this->entityManager, array(
						'TicketManager_Entity_Tickets'
					));
				} catch (Exception $e) {
					return LogUtil::registerError($e);
				}
				try {
					DoctrineHelper::createSchema($this->entityManager, array(
						'TicketManager_Entity_Tickets'
					));
				} catch (Exception $e) {
					return LogUtil::registerError($e);
				}
				return true;
			default:
				return LogUtil::RegisterError($this->__('Upgrade of this version is not supported!'));
		}
	}

	public function uninstall()
	{
		$this->delVars();

		try {
			DoctrineHelper::dropSchema($this->entityManager, array(
				'TicketManager_Entity_Tickets'
			));
		} catch (Exception $e) {
			return LogUtil::registerError($e);
		}
		
		return true;
	}
}
