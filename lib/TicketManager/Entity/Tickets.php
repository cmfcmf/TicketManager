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
	 * @ORM\Column(type="datetime")
	 */
	private $enddate;
	
	/**
	 * The following are annotations which define the information field.
	 *
	 * @ORM\Column(type="array")
	 */
	private $information;

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
	/*
	public function getDateFormatted()
	{
		return $this->date->format('d.m.Y G:i');
	}
	*/

	public function setStartdate($date)
	{
		$this->startdate = new \DateTime($date);
	}
	
	public function setEnddate($date)
	{
		$this->enddate = new \DateTime($date);
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
}
