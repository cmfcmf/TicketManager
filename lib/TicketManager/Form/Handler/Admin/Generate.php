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
class TicketManager_Form_Handler_Admin_Generate extends Zikula_Form_AbstractHandler
{
    /**
     * @brief Setup form.
     */
    public function initialize(Zikula_Form_View $view)
    {
        
    }

    /**
     * @brief Handle form submission.
     */
    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'cancel') {
            LogUtil::RegisterStatus($view->__('Generating canceled'));
            return $view->redirect(ModUtil::url($this->name, 'admin', 'main'));
        }

        // check for valid form
        if (!$view->isValid())
            return false;

        $data = $view->getValues();

        $data['eventdate'] = (isset($data['eventdate'])) ? new DateTime($data['eventdate']) : null;
        $data['startdate'] = (isset($data['startdate'])) ? new DateTime($data['startdate']) : null;
        $data['enddate'] = (isset($data['enddate'])) ? new DateTime($data['enddate']) : null;

        if($data['picture']['error'] == 0)
        {
            CacheUtil::createLocalDir('TicketManager', null, false);
			$pathToFile = CacheUtil::getLocalDir($this->name, true) . '/' . $data['picture']['name'];

            if(!copy($data['picture']['tmp_name'], $pathToFile))
				return LogUtil::registerError($this->__('There was a problem with your picture. Please try again.'));
			if(!unlink($data['picture']['tmp_name']))
				return LogUtil::registerError($this->__('There was a problem with your picture. Please try again.'));
			$data['picture'] = $pathToFile;
        }
        else
            $data['picture'] = null;
        
        if($data['logo']['error'] == 0)
        {
            CacheUtil::createLocalDir('TicketManager', null, false);
			$pathToFile = CacheUtil::getLocalDir($this->name, true) . '/' . $data['logo']['name'];

            if(!copy($data['logo']['tmp_name'], $pathToFile))
				return LogUtil::registerError($this->__('There was a problem with your logo. Please try again.'));
			if(!unlink($data['logo']['tmp_name']))
				return LogUtil::registerError($this->__('There was a problem with your logo. Please try again.'));
			$data['logo'] = $pathToFile;
        }
        else
            $data['logo'] = null;

        $data['module'] = ModUtil::getIdFromName($this->name);

        $return = ModUtil::apiFunc($this->name, 'Ticket', 'reserve', $data);

        if($data['pdfOutput'] == 'direct')
            return true;
        else
            return LogUtil::registerStatus($this->__f('File saved at %s', $return), ModUtil::url($this->name, 'admin', 'main'));
    }
}
