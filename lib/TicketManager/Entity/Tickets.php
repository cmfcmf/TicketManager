<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * Tickets entity class.
 *
 * Annotations define the entity mappings to database.
 *
 * @ORM\Entity
 * @ORM\Table(name="TicketManager_Tickets")
 */
class TicketManager_Entity_Tickets extends Zikula_EntityAccess
{

	/**
	 * The following are annotations which define the id field.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $tid;

	/**
	 * The following are annotations which define the status field.
	 *
	 * @ORM\Column(type="integer")
	 */
	private $status;

	/**
	 * The following are annotations which define the module field.
	 *
	 * @ORM\Column(type="integer")
	 */
	private $module;

	/**
	 * The following are annotations which define the startdate field.
	 *
	 * @ORM\Column(type="datetime")
	 */
	private $startdate;
	
	/**
	 * The following are annotations which define the enddate field.
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $enddate;
	
	/**
	 * The following are annotations which define the information field.
	 *
	 * @ORM\Column(type="array")
	 */
	private $information;
	
	/**
	 * The following are annotations which define the qrcode field.
	 *
	 * @ORM\Column(type="string", length=30)
	 */
	private $qrCode;

	/**
	 * The following are annotations which define the allowedDepreciatings field.
	 *
	 * @ORM\Column(type="integer")
	 */	
	private $allowedDepreciatings;
	
	public function getTid()
	{
		return $this->tid;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function getStartdate()
	{
		return $this->startdate;
	}
	
	public function getEnddate()
	{
		return $this->enddate;
	}
	
	public function getInformation()
	{
		return $this->information;
	}
	
	public function getModule()
	{
		return $this->module;
	}
	
	public function getQRCode()
	{
		return $this->qrCode;
	}
	
	public function getAllowedDepreciatings()
	{
		return $this->allowedDepreciatings;
	}
	
	/**
	 * @return bool True if depreciating is ok, False if not.
	 */
	public function isDepreciatingAllowed()
	{
		//-1 means endless
		return ($this->allowedDepreciatings > 0 || $this->allowedDepreciatings == -1) ? true : false;
	}

	public function setStartdate($date)
	{
		$this->startdate = ($date);
	}
	
	public function setEnddate($date)
	{
		$this->enddate = ($date);
	}
	
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function setInformation($information)
	{
		$this->information = $information;
	}
	
	public function setModule($module)
	{
		$this->module = $module;
	}
	
	public function setQRCode($qrCode)
	{
		$this->qrCode = $qrCode;
	}
	
	public function setAllowedDepreciatings($allowedDepreciatings)
	{
		$this->allowedDepreciatings = $allowedDepreciatings;
	}
}
