<?php

class TicketManager_Version extends Zikula_AbstractVersion
{
	public function getMetaData()
	{
		$modversion['name']           = 'TicketManager';
		$modversion['displayname']    = 'TicketManager';
		$modversion['description']    = $this->__('Manage your TICKETS easy!');
		$modversion['version']        = '0.0.4';
		$modversion['official']       = false;
		$modversion['author']         = 'Christian Flach';
		$modversion['contact']        = 'cmfcmf.flach@gmail.com';
		$modversion['admin']          = true;
		$modversion['user']           = true;
		$modversion['core_min'] = '1.3.0';
		$modversion['core_max'] = '1.3.99';

		return $modversion;
	}

}