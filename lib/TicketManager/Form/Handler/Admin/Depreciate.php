<?php
/**
 * TicketManager
 *
 * @copyright  (c) TicketManager Team
 * @link       http://github.com/cmfcmf/TicketManager/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package    TicketManager
 * @subpackage FormHandler
 */

/**
 * Depreciate FormHandler.
 */
class TicketManager_Form_Handler_Admin_Depreciate extends Zikula_Form_AbstractHandler
{
    /**
     * @brief Setup form.
     
     */
    function initialize(Zikula_Form_View $view)
    {
        $view->assign('modes', array(array('text' => 'Normal', 'value' => 'normal'), array('text' => 'Fullscreen', 'value' => 'fullscreen')));
    }

    /**
     * @brief Handle form submission.
     */
    function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'cancel') {
            LogUtil::RegisterStatus($view->__('Depreciating canceled'));
            return $view->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }

        // check for valid form
        if (!$view->isValid())
            return false;

        $data = $view->getValues();

        return System::redirect(ModUtil::url($this->name, 'admin', 'depreciate', array('qrCode' => $data['qrCode'], 'mode' => $data['mode'], 'dryRun' => $data['dryRun'])));
    }
}
