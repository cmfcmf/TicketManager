<?php
/**
 * @copyright Christian Flach
 */
class TicketManager_Api_Ticket extends Zikula_AbstractApi
{
	/**
	 * @brief Reserves X tickets and returns a pdf file to print.
	 * @param int $args['number'] Number of tickets to generate.
	 * @param int $args['allowedDepreciatings']  How often a ticket can be depreciated. Use -1 for endless times. Default is 1.
	 * @param array $args['information'] Further information to store in database. Will be ignored by TicketManager.
	 * @param DateTime $args['eventdate'] The date to be printed onto the ticket (optional, default null).
	 * @param DateTime $args['startdate'] The startdate of the tickets (optional, default 'Now').
	 * @param DateTime $args['enddate'] The enddate of the tickets. null means endless (optional, default null).
	 * @param int $args['price'] Price for each ticket. -1 won't be printed (optional, default -1).
	 * @param string $args['eventname'] The event name (Printed largely onto the ticket).
	 * @param string $args['shortdescription'] A short event description (optional, default null).
	 * @param string $args['picture'] A path to a big picture to print on each ticket. (optional, default null).
	 * @param string $args['logo'] A path to a small logo to print on each ticket. (optional, default null).
	 *
	 * @return string|array $return['pdf'] A pdf file with all tickets ($args['tickets_per_pdf'] = -1), else an array with pdf files, each including $args['tickets_per_pdf'] tickets.
	 */
	public function reserve($args)
	{
		extract($args);

		////Required
		if(!is_integer($number) || $number < 1)
			throw new Zikula_Exception_Fatal('$number is not valid!');
		if(!is_string($eventname))
			throw new Zikula_Exception_Fatal('$eventname is not valid!');
		if(!is_string($module))
			throw new Zikula_Exception_Fatal('$module is not valid!');

		////Optional
		if(isset($price) && (!is_integer($price) || $price < -1))
			throw new Zikula_Exception_Fatal('$price is not valid!');
		
		if(isset($shortdescription) && !is_string($shortdescription))
			throw new Zikula_Exception_Fatal('$shortdescription is not valid!');
		
		if(isset($picture) && (!is_string($picture) || !is_readable($picture)))
			throw new Zikula_Exception_Fatal('$picture is not valid!');
		
		if(isset($logo) && (!is_string($logo) || !is_readable($logo)))
			throw new Zikula_Exception_Fatal('$logo is not valid!');

		if(!is_integer($allowedDepreciatings) || $allowedDepreciatings < -1 || $allowedDepreciatings == 0)
			throw new Zikula_Exception_Fatal('$allowedDepreciatings is not valid!');
		if(!isset($allowedDepreciatings))
			$allowedDepreciatings = 1;

		if(isset($startdate) && !is_a($startdate, 'DateTime'))
			throw new Zikula_Exception_Fatal('$startdate has to be a DateTime object!');
		//Set to current date if not set.
		if(!isset($startdate))
			$startdate = new \Datetime('now');
		
		if(isset($enddate) && !is_a($enddate, 'DateTime'))
			throw new Zikula_Exception_Fatal('$enddate has to be a DateTime object!');
		
		if(isset($eventdate) && !is_a($eventdate, 'DateTime'))
			throw new Zikula_Exception_Fatal('$eventdate has to be a DateTime object!');
			
		//Create database entry for each ticket.
		for($i = 0; $i < $number; $i++)
		{
			$qrCode = $this->generateQRCode();

			$ticket = new TicketManager_Entity_Tickets();
			$ticket->setInformation($information);
			$ticket->setStartdate($startdate);
			$ticket->setEnddate($enddate);
			$ticket->setModule($module);
			$ticket->setQRCode($qrCode);
			$ticket->setAllowedDepreciatings($allowedDepreciatings);
			$ticket->setStatus(TicketManager_Constant::STATUS_RESERVED);

			$this->entityManager->persist($ticket);
			$this->entityManager->flush();

			$ticket->setQRCode($ticket->getQRCode() . $ticket->getTid());
			$this->entityManager->flush();

			$qrCodes[$i] = $ticket->getQRCode();
		}

		$classfile = DataUtil::formatForOS('modules/TicketManager/lib/vendor/tcpdf/tcpdf.php');
		include_once $classfile;
		$lang = ZLanguage::getInstance();
		$langcode = $lang->getLanguageCodeLegacy();

		//Small hack, see https://github.com/zikula-modules/News/issues/106 and
		//http://github.com/cmfcmf/TicketManager/issues/3
		if($langcode == 'deu')
			$langcode = 'ger';

		$langfile = DataUtil::formatForOS("modules/TicketManager/lib/vendor/tcpdf/config/lang/{$langcode}.php");
		if (file_exists($langfile)) {
			include_once $langfile;
		} else {
			// default to english
			include_once DataUtil::formatForOS('modules/TicketManager/lib/vendor/tcpdf/config/lang/eng.php');
		}

		// create new PDF document
		$pdf = new TCPDF(P, 'mm', 'A4', true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator($this->name);
		$pdf->SetAuthor($this->name);
		$pdf->SetTitle($eventname);
		$pdf->SetSubject($this->name);
		#$pdf->SetKTESTeywords('TCPDF, PDF, example, test, guide');

		// set default header data
		$pdf->SetHeaderData("../../../../images/admin.png", 15, $eventname, "TicketManager (c) Christian Flach");

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//Disable auto page breaks
		$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		$pdf->SetFont('helvetica', '', 10);

		// set style for barcode
		$style = array(
			'border' => 2,
			'vpadding' => '1',
			'hpadding' => '1',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
			'module_width' => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);

		// set color for background to white
		$pdf->SetFillColor(255, 255, 255);

		$offset = 25;
		$qrSize = 60;
		$pagewidth = 210;
		$ticketHeight = 75;
		$ticketWidth = $pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT;
		$titleHeight = 15;
		$logoHeight_px = 30;
		$imgSize = 50;
		$ticketDistance = 8;
		$contentSize = 50;
		$shortdescriptionWidth = $ticketWidth-$imgSize-$qrSize;
		$footerHeight = $ticketHeight - $contentSize - $titleHeight;
		$shortdescriptionHeight = $imgSize;
		

		for($ticketsThisPageCounter = 0, $ticketCounter = 0; $ticketCounter < $number; $ticketCounter++, $ticketsThisPageCounter++)
		{
			//Generate a new page all three tickets.
			if($ticketsThisPageCounter % 3 == 0)
			{
				$pdf->AddPage();
				$ticketsThisPageCounter = 0;
			}
				
			//Startdate
			if(isset($eventdate))
			{
				$pdf->SetFont('helvetica', '', 18);
				$pdf->MultiCell($pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-$qrSize, $footerHeight, $eventdate->format('d.m.Y H:i'), 0, 'L', 1, 1, PDF_MARGIN_LEFT, $offset+($ticketDistance+$ticketHeight)*$ticketsThisPageCounter+$titleHeight+$contentSize, true, 0, true);
			}
			//Qr-string
			$pdf->SetFont('helvetica', '', 18);
			$pdf->MultiCell($pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-$qrSize, $footerHeight, "&lt;$qrCodes[$ticketCounter]&gt;", 0, 'R', 1, 1, PDF_MARGIN_LEFT, $offset+($ticketDistance+$ticketHeight)*$ticketsThisPageCounter+$titleHeight+$contentSize, true, 0, true);

			//Title
			$pdf->SetFont('helvetica', '', 35);
			for($size = 35; $pdf->GetStringWidth($eventname) > $ticketWidth; $size--)
			{
				$pdf->SetFont('helvetica', '', $size);
			}
			$pdf->MultiCell($pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT, $ticketHeight, $eventname, 1, 'L', 1, 1, PDF_MARGIN_LEFT, $offset+($ticketDistance+$ticketHeight)*$ticketsThisPageCounter, true, 0, true);
				
			//Title logo
			#if(isset($logo))
			#	$pdf->Image($logo, PDF_MARGIN_LEFT, $offset+($ticketDistance+$ticketHeight)*$ticketsThisPageCounter, 0, $titleHeight, '', '', 'R', true, 150, 'R', false, false, 1, false, false, false);

			//Big Picture
			if(isset($picture))
				$pdf->Image($picture, PDF_MARGIN_LEFT, $offset+$titleHeight+($ticketDistance+$ticketHeight)*$ticketsThisPageCounter, $imgSize, $imgSize, '', '', '', true, 150, '', false, false, 1, false, false, false);

			//Shortdescription
			if(isset($shortdescription))
			{
				$pdf->SetFont('helvetica', '', 20);
				for($size = 20; $pdf->GetStringHeight($shortdescriptionWidth, $shortdescription) > $shortdescriptionHeight; $size--)
				{
					$pdf->SetFont('helvetica', '', $size);
				}
				$pdf->MultiCell($shortdescriptionWidth, $shortdescriptionHeight, "<i>$shortdescription</i>", 0, 'L', 1, 1, PDF_MARGIN_LEFT+$imgSize, $offset+$titleHeight+($ticketDistance+$ticketHeight)*$ticketsThisPageCounter, true, 0, true);
			}
				
			//Barcode
			$pdf->write2DBarcode($qrCodes[$ticketCounter], 'QRCODE,H', $pagewidth-PDF_MARGIN_RIGHT-$qrSize, $offset+($ticketDistance+$ticketHeight)*$ticketsThisPageCounter+$titleHeight, $qrSize, $qrSize, $style, 'N');
				
			////Price
			// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
			#$pdf->SetFont('helvetica', '', 18);
			#$pdf->MultiCell($pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT, $footerHeight, "<img height=\"$logoHeight_px\" width=\"$logoHeight_px\" src=\"$logo\" /><img height=\"$logoHeight_px\" width=\"$logoHeight_px\" src=\"/modules/TicketManager/images/admin.png\" />{$price}€", 1, 'R', 1, 1, PDF_MARGIN_LEFT, $offset+($ticketDistance+$ticketHeight)*$ticketsThisPageCounter+$titleHeight+$contentSize, true, 0, true, false, $footerHeight);
		}

		//Close and output PDF document
		$pdf->Output('Tickets.pdf', 'I');

		return true;
	}

	public function depreciate($args)
	{
		extract($args);

		if(!isset($qrCode))
			throw new InvalidArgumentException('$qrCode is missing');

		$ticket = $this->entityManager->getRepository('TicketManager_Entity_Tickets')->findOneBy(array('qrCode' => $qrCode));

		if(!isset($ticket))
			return ($mode == 'fullscreen') ? TicketManager_Constant::ERROR_PAGE : TicketManager_Constant::TICKET_QRCODE_NOT_FOUND;

		if($ticket->getEnddate()->getTimestamp() > time() && $ticket->getStartdate()->getTimestamp() <= time())
			return ($mode == 'fullscreen') ? TicketManager_Constant::ERROR_PAGE : TicketManager_Constant::TICKET_FITS_NOT_DATE_RANGE;

		if($ticket->getStatus() != TicketManager_Constant::STATUS_RESERVED || !$ticket->isDepreciatingAllowed())
			return ($mode == 'fullscreen') ? TicketManager_Constant::ERROR_PAGE : TicketManager_Constant::TICKET_ALREADY_DEPRECIATED;

		//All ok. Change status to DEPRECIATED.

		$ticket->setAllowedDepreciatings($ticket->getAllowedDepreciatings() - 1);

		//Ticket cannot be depreciated again
		if(!$ticket->isDepreciatingAllowed())
			$ticket->setStatus(TicketManager_Constant::STATUS_DEPRECIATED);

		$this->entityManager->persist($ticket);
		$this->entityManager->flush();

		//Call an api function of the module which reserved the tickets. If this function does not exist, nothing will happen.
		$modinfo = ModUtil::getInfo($ticket->getModule());

		ModUtil::apiFunc($modinfo['name'], 'TicketManager', 'depreciated', array(
			'information'          => $ticket->getInformation(),
			'startdate'            => $ticket->getStartdate(),
			'enddate'              => $ticket->getEnddate(),
			'qrCode'               => $ticket->getQRCode(),
			'status'               => $ticket->getStatus(),
			'allowedDepreciatings' => $ticket->getAllowedDepreciatings()));

		return ($mode == 'fullscreen') ? TicketManager_Constant::OK_PAGE : TicketManager_Constant::TICKET_DEPRECIATED;
	}

	/**
	 * @brief Generates a random qrcode.
	 * @return string The randomly generated string.
	 */
	private function generateQRCode()
	{
		//return RandomUtil::getString(10, 10, false, true, true, false, true, false, false, null);
		return RandomUtil::getString(10, 10, false, true, true, false, true, false, false, uniqid());
	}









	/////////////////////////////////////////////////////////
	//Not used, it may will be deleted later on.
	/////////////////////////////////////////////////////////

	/**
	 * @param int $startdate The startdate of the tickets. 0 means endless (optional, default 0).
	 * @param int $enddate The enddate of the tickets. 0 means endless (optional, default 0).
	 * @param int $price Price for each ticket. -1 won't be printed (optional, default -1).
	 * @param string $picture An absolute path to a picture to print on each ticket. (optional, default null).
	 * @param string $args['logo'] An absolute path to a logo to print on each ticket. (optional, default null).
	 * @param string $eventname The event name.
	 * @param string $shortdescription A short event description (optional, default null).
	 *
	 * @return string $html Html code for a ticket.
	 *
	 * @deprecated HTML tickets won't be supported
	 */
	private function printTicketHtml($eventname = null, $qrHtml = null, $shortdescription = null, $startdate = 0, $enddate = 0, $price = -1, $picture = null, $logo = null)
	{
		$ticket = Zikula_View::getInstance($this->name);

		return $ticket->assign('eventname', $eventname)
		->assign('shortdescription', $shortdescription)
		->assign('startdate', $startdate)
		->assign('enddate', $enddate)
		->assign('price', $price)
		->assign('picture', $picture)
		->assign('logo', $logo)
		->assign('qrHtml', $qrHtml)
		->fetch('Ticket/Default.tpl');
	}

	/**
	 * @brief Generates an HTML QRCODE using tcpdf.
	 * @param string|int|float $information The information to store in QRCODE.
	 *
	 * @return string Generated HTML QRCODE.
	 *
	 * @deprecated HTML tickets won't be supported.
	 */
	private function generateQr($information = null)
	{
		if(!isset($information))
			throw new Zikula_Exception_Fatal('$information is not valid!');

		include_once DataUtil::formatForOS('modules/TicketManager/lib/vendor/tcpdf/2dbarcodes.php');

		$barcode = new TCPDF2DBarcode($information, 'QRCODE,H');

		return $barcode->getBarcodeHTML(9, 9);
	}
}

