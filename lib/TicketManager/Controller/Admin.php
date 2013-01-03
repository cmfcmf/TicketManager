<?php
/**
 * TicketManager
 *
 * @copyright  (c) TicketManager Team
 * @link       http://github.com/cmfcmf/TicketManager/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package    TicketManager
 * @subpackage Controller
 */

/**
 * Admin Controller.
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
     * The TicketManager help page.
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

    /**
     * Function for depreciating a ticket via QR-Code.
     * @param GET $qrCode The QRCode.
     * @param GET $mode The display mode. Default is null.
     * @return string The rendered template output.
     */
    public function depreciate()
    {
        if (!SecurityUtil::checkPermission('TicketManager::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        $qrCode = FormUtil::getPassedValue("qrCode", null, "GET");
        $mode = FormUtil::getPassedValue("mode", null, "GET");

        if(!isset($qrCode))
            return LogUtil::RegisterError('No $qrCode given! Use "&qrCode=abc" in the url.', null, ModUtil::url($this->name, 'admin', 'main'));

        $return = ModUtil::apiFunc($this->name, 'Ticket', 'depreciate', array('qrCode' => $qrCode, 'mode' => $mode));

        switch($return)
        {
            case TicketManager_Constant::TICKET_DEPRECIATED:
                LogUtil::RegisterStatus("Ticket successfully depreciated!");
                break;
            case TicketManager_Constant::TICKET_FITS_NOT_DATE_RANGE:
                LogUtil::RegisterError("Date range of ticket does not fit!");
                break;
            case TicketManager_Constant::TICKET_ALREADY_DEPRECIATED:
                LogUtil::RegisterError("Ticket is already depreciated!");
                break;
            case TicketManager_Constant::TICKET_QRCODE_NOT_FOUND:
                LogUtil::RegisterError("QRCode could not be found!");
                break;
        }

        if($mode == 'fullscreen')
        {
            echo $return;
            return true;
        }
        else
            return $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
    }

    public function clearCache()
    {
        if(CacheUtil::removeLocalDir('TicketManager', false))
            return LogUtil::registerStatus('Cache cleared!', ModUtil::url($this->name, 'admin', 'main'));
        else
            return LogUtil::registerError('Cache clearing failed!', null, ModUtil::url($this->name, 'admin', 'main'));
    }

    public function test()
    {
        $return = ModUtil::apiFunc($this->name, 'Ticket', 'reserve',
            array(
                'number' => 4,
                'allowedDepreciatings' => 1,
                'eventname' =>'Testevent',
                'picture' => 'images/logo.gif', //Path to big picture
                'module' => ModUtil::getIdFromName($this->name),
                'logo' => 'images/logo.gif', //Path to little logo
                'price' => 4,
                'information' => array('uid' => 373, 'foo' => 'bar'),
                'startdate' => new \DateTime('15.3.2013 12:00'),
                'eventdate' => new \DateTime('1.2.1234 12:34'),
                'shortdescription' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam',
                'pdfOutput' => 'file'
            )
        );
        return LogUtil::registerStatus('File saved at ' . $return, ModUtil::url($this->name, 'admin', 'main'));
    }
}
