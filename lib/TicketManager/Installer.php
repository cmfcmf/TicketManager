<?php
/**
 * TicketManager
 *
 * @copyright  (c) TicketManager Team
 * @link       http://github.com/cmfcmf/TicketManager/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package    TicketManager
 * @subpackage Installer
 */

/**
 * TicketManager Installer.
 */
class TicketManager_Installer extends Zikula_AbstractInstaller
{
    protected function getDefaultModVars()
    {
        return array();
    }

    protected function checkTcpdf()
    {
        if(!PluginUtil::isAvailable(PluginUtil::getServiceId('SystemPlugin_Tcpdf_Plugin'))) {
            $url = 'https://www.github.com/cmfcmf/Tcpdf';
            return LogUtil::registerError($this->__f('You must install the TCPDF SystemPlugin! You can download it here: %s.', "<a href=\"$url\">$url</a>"));
        }
        return true;
    }
    /**
     * Installer.
     * @todo Create Databases
     */
    public function install()
    {
        if(!$this->checkTcpdf()) {
            return;
        }

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
            case '0.0.3':
                try {
                    DoctrineHelper::updateSchema($this->entityManager, array(
                        'TicketManager_Entity_Tickets'
                    ));
                } catch (Exception $e) {
                    return LogUtil::registerError($e);
                }
            case '0.0.4':
            case '0.0.5':
                try {
                    DoctrineHelper::updateSchema($this->entityManager, array(
                        'TicketManager_Entity_Tickets'
                    ));
                } catch (Exception $e) {
                    return LogUtil::registerError($e);
                }
            case '0.0.6':
            case '0.0.7':
                if(!$this->checkTcpdf()) {
                    return;
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
