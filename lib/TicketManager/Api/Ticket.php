<?php
/**
 * @copyright Christian Flach
 */
class TicketManager_Api_Ticket extends Zikula_AbstractApi
{
	/**
	 * @brief Reserves X tickets and returns a pdf file to print.
	 * @param int $args['number'] Number of tickets to generate.
	 * @param array $args['information'] Further information to store in database. Will be ignored by TicketManager.
	 * @param int $args['startdate'] The startdate of the tickets. 0 means endless (optional, default 0).
	 * @param int $args['enddate'] The enddate of the tickets. 0 means endless (optional, default 0).
	 * @param int $args['price'] Price for each ticket. -1 won't be printed (optional, default -1).
	 * @param string $args['eventname'] The event name.
	 * @param string $args['shortdescription'] A short event description (optional, default null).
	 * @param string $args['picture'] An absolute path to a picture to print on each ticket. (optional, default null).
	 * @param string $args['logo'] An absolute path to a logo to print on each ticket. (optional, default null).
	 * @param int $args['tickets_per_pdf'] -1 for all tickets (optional, default -1).
	 * 
	 * @return string|array $return['pdf'] A pdf file with all tickets ($args['tickets_per_pdf'] = -1), else an array with pdf files, each including $args['tickets_per_pdf'] tickets.
	 *
	 * @todo Set Enddate to null if not submitted.
	 * @bug TCPDF Error if $number = 1.
	 */
	public function reserve($args)
	{
		extract($args);
	
		if(!is_integer($number) || $number < 1)
			throw new Zikula_Exception_Fatal('$number is not valid!');
		if(!is_integer($id) && !is_array($id))
			throw new Zikula_Exception_Fatal('$id is not valid!');
		if(!is_string($eventname))
			throw new Zikula_Exception_Fatal('$eventname is not valid!');
		if(!is_string($module))
			throw new Zikula_Exception_Fatal('$module is not valid!');

		#if(isset($startdate) && !is_integer($startdate))
		#	throw new Zikula_Exception_Fatal('$startdate is not valid!');
		#if(isset($enddate) && !is_integer($enddate))
		#	throw new Zikula_Exception_Fatal('$enddate is not valid!');
		if(isset($price) && (!is_integer($price) || $price < -1))
			throw new Zikula_Exception_Fatal('$price is not valid!');
		if(isset($shortdescription) && !is_string($shortdescription))
			throw new Zikula_Exception_Fatal('$shortdescription is not valid!');
		if(isset($picture) && !is_string($picture))
			throw new Zikula_Exception_Fatal('$picture is not valid!');
		if(isset($logo) && !is_string($logo))
			throw new Zikula_Exception_Fatal('$logo is not valid!');

		if(!isset($tickets_per_pdf))
			$tickets_per_pdf = -1;
		
		$ids = array();
		
		//Create database entry for each ticket.
		for($i = 0; $i < $number; $i++)
		{
			$ticket = new TicketManager_Entity_Tickets();
			$ticket->setInformation($information);
			$ticket->setStartdate($startdate);
			$ticket->setEnddate($enddate);
			$ticket->setModule($module);
			$ticket->setStatus(TicketManager_Constant::STATUS_RESERVED);

			$this->entityManager->persist($ticket);
			$this->entityManager->flush();
			
			
			//get the last ticket id
			$em = $this->getService('doctrine.entitymanager');
			$qb = $em->createQueryBuilder();
			$qb->select('p')
			   ->from('TicketManager_Entity_Tickets', 'p')
			   /*->where('p.name = :name')*/
			   /*->setParameter('name', name)*/
			   ->orderBy('p.tid', 'DESC')
			   ->setMaxResults(1);
			$tickets = $qb->getQuery()->getArrayResult();

			$ids[] = $tickets[0]['tid'];
		}
		
		
		$classfile = DataUtil::formatForOS('modules/TicketManager/lib/vendor/tcpdf/tcpdf.php');
		include_once $classfile;
		$lang = ZLanguage::getInstance();
		$langcode = $lang->getLanguageCodeLegacy();
		$langfile = DataUtil::formatForOS("modules/TicketManager/lib/vendor/tcpdf/config/lang/{$langcode}.php");
		if (file_exists($langfile)) {
			include_once $langfile;
		} else {
			// default to english
			include_once DataUtil::formatForOS('modules/TicketManager/lib/vendor/tcpdf/config/lang/eng.php');
		}
		// create new PDF document
		///@todo DINA4 only
		$pdf = new TCPDF(P, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator($this->name);
		$pdf->SetAuthor($this->name);
		$pdf->SetTitle($eventname);
		$pdf->SetSubject($this->name);
		#$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// set default header data
		$pdf->SetHeaderData("../../../../images/admin.png", 15, $eventname, "TicketManager (c) Christian Flach");

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
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

		// set color for background
		$pdf->SetFillColor(255, 255, 255);
		// set cell padding
		#$pdf->setCellPaddings(1, 1, 1, 1);

		$offset = 25;
		$qrSize = 60;
		$ticketHeight = 75;
		$pagewidth = 210;
		$titleHeight = 15;
		$logoHeight_px = 30;
		$imgSize = 50;
		$ticketDistance = 8;
		$contentSize = 50;

		$footerHeight = $ticketHeight - $contentSize - $titleHeight;

		for($i = 0, $pageCounter = 0; $pageCounter < $number; $pageCounter++, $i++)
		{
			if($i % 3 == 0 && $i + 1 != $number)
			{
				$pdf->AddPage();
				$i = 0;
			}
			
			////Code Id
			$codeId = self::generateQRCode($ids[$pageCounter]);
			
			////Startdate
			$pdf->SetFont('helvetica', '', 18);
			if(isset($startdate))
				$pdf->MultiCell($pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT, $footerHeight, $this->__('Date').": ".date("j.m.Y H:i", strToTime($startdate))." &lt;$codeId&gt;", 0, 'L', 1, 1, PDF_MARGIN_LEFT, $offset+($ticketDistance+$ticketHeight)*$i+$titleHeight+$contentSize, true, 0, true);
			
			////Title
			$pdf->SetFont('helvetica', '', 18);
			// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
			$pdf->MultiCell($pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT, $ticketHeight, "<h1>$eventname</h1>", 1, 'L', 1, 1, PDF_MARGIN_LEFT, $offset+($ticketDistance+$ticketHeight)*$i, true, 0, true);
	
			////Picture
			if(isset($picture))
				// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
				$pdf->Image($picture, PDF_MARGIN_LEFT, $offset+$titleHeight+($ticketDistance+$ticketHeight)*$i, $imgSize, $imgSize, 'JPG', '', '', true, 150, '', false, false, 1, false, false, false);

			////Shortdescription
			$pdf->SetFont('helvetica', '', 13);
			$pdf->MultiCell($pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-$imgSize-$qrSize, $contentSize, "<i>$shortdescription</i>", 0, 'L', 1, 1, PDF_MARGIN_LEFT+$imgSize, $offset+$titleHeight+($ticketDistance+$ticketHeight)*$i, true, 0, true);
	
			////Barcode
			$pdf->write2DBarcode($codeId, 'QRCODE,H', $pagewidth-PDF_MARGIN_RIGHT-$qrSize, $offset+($ticketDistance+$ticketHeight)*$i+$titleHeight, $qrSize, $qrSize, $style, 'N');
	
			////Price
			// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
			#$pdf->SetFont('helvetica', '', 18);
			#$pdf->MultiCell($pagewidth-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT, $footerHeight, "<img height=\"$logoHeight_px\" width=\"$logoHeight_px\" src=\"$logo\" /><img height=\"$logoHeight_px\" width=\"$logoHeight_px\" src=\"/modules/TicketManager/images/admin.png\" />{$price}€", 1, 'R', 1, 1, PDF_MARGIN_LEFT, $offset+($ticketDistance+$ticketHeight)*$i+$titleHeight+$contentSize, true, 0, true, false, $footerHeight);
		}

		//Close and output PDF document
		$pdf->Output('Tickets.pdf', 'I');

		return true;
/*
		$html = "";
		for($i = 0; $i < $number; $i++)
		{
			$html .= self::printTicketHtml($eventname, self::generateQr($id), $shortdescription, $startdate, $enddate, $price, $picture, $logo);
			#$pdf = self::printTicketPdf($pdf, $eventname, self::generateQr($id), $shortdescription, $startdate, $enddate, $price, $picture, $logo);
			
			// set style for barcode
			$style = array(
				'border' => 2,
				'vpadding' => 'auto',
				'hpadding' => 'auto',
				'fgcolor' => array(0,0,0),
				'bgcolor' => false, //array(255,255,255)
				'module_width' => 1, // width of a single module in points
				'module_height' => 1 // height of a single module in points
			);

			$pdf->write2DBarcode($id, 'QRCODE,H', 20, 210, 50, 50, $style, 'N');
			$pdf->Text(20, 205, 'QRCODE H');

			#$pdf->MultiCell(55, 5, '[LEFT] abc123', 1, 'L', 0, 0, '', '', true);
		}
		
		$pdf->lastPage();

		//Close and output PDF document
		ob_end_clean();
		$pdf->Output($title , 'I');
		return true;
		*/
		#return self::printPdf(self::printTicket($eventname, ""/*self::generateQr($id)*/, $shortdescription, $startdate, $enddate, $price, $picture, $logo));
		#return $html;
		
	}
	
	private function generateQRCode($id)
	{
		return hash('crc32', $id);// * mt_rand(5, 20);
	}
	
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
	 * @todo Paramter prüfen
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
	 * @todo Paramter prüfen
	 */
	private function printTicketPdf($pdf, $eventname = null, $qrHtml = null, $shortdescription = null, $startdate = 0, $enddate = 0, $price = -1, $picture = null, $logo = null)
	{
		return $pdf;
	}

	/**
	 * @param string $html The html content to print
	 * @todo Paramter prüfen
	 */
	private function printPdf($html = null)
	{
		$classfile = DataUtil::formatForOS('modules/TicketManager/lib/vendor/tcpdf/tcpdf.php');
		include_once $classfile;
		$lang = ZLanguage::getInstance();
		$langcode = $lang->getLanguageCodeLegacy();
		$langfile = DataUtil::formatForOS("modules/TicketManager/lib/vendor/tcpdf/config/lang/{$langcode}.php");
		if (file_exists($langfile)) {
			include_once $langfile;
		} else {
			// default to english
			include_once DataUtil::formatForOS('modules/TicketManager/lib/vendor/tcpdf/config/lang/eng.php');
		}


		// create new PDF document
		$pdf = new TCPDF(P, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator($this->name);
		$pdf->SetAuthor($this->name);
		#$pdf->SetTitle($title);
		#$pdf->SetSubject($title);

		// set default header data
		#$pdf->SetHeaderData('', '0px', $title, $this->__('Generated by:').' EventManager '.$this->__('and').' TCPDF'); ///@TODO %s!

		// set header and footer fonts
		#if (FormUtil::getPassedValue('mode', '', 'GET') != 'elternsprechtag')
		#$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		#else
		#	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 20));
		#$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		#$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		#$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		#$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		#$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('dejavusans', '', 10);

		// add a page
		$pdf->AddPage();

		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->lastPage();

		//Close and output PDF document
		ob_end_clean();
		$pdf->Output($title , 'I');
		return true;
	}
	
	private function generateQr($information = null)
	{
		if(!isset($information))
			throw new Zikula_Exception_Fatal('$information is not valid!');

		include_once DataUtil::formatForOS('modules/TicketManager/lib/vendor/tcpdf/2dbarcodes.php');
		
		$barcode = new TCPDF2DBarcode($information, 'QRCODE,H');
		
		return $barcode->getBarcodeHTML(9, 9);
	}
}

