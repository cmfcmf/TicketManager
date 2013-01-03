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
 * Constant class.
 */
class TicketManager_Constant
{
    /**
     * @name Constants for database storage
     * @{
     */
    const STATUS_RESERVED = 1;
    
    const STATUS_DEPRECIATED = 2;
    /**@}*/
    
    /**
     * @name Constants returned by TicketManager_Api_Tickets::depreciate() if $mode != 'fullscreen'
     * @{
     */
    const TICKET_DEPRECIATED = 1;
    
    const TICKET_ALREADY_DEPRECIATED = 2;
    
    const TICKET_QRCODE_NOT_FOUND = 3;
    
    const TICKET_FITS_NOT_DATE_RANGE = 4;
    /**@}*/
    
    /**
     * @name Constants returned by TicketManager_Api_Tickets::depreciate() if $mode == 'fullscreen'
     * @{
     */
    const ERROR_PAGE = "<html><head></head><body style=\"background-color: red\"></body></html>";
    
    const OK_PAGE = "<html><head></head><body style=\"background-color: green\"></body></html>";
    /**@}*/
}
