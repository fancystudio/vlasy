<?php
class PDFGenerator extends PDFGeneratorCore
{
	public function render($filename, $display = true)
	{
        // verification keys: MK##1
		if (empty($filename))
			throw new PrestaShopException('Missing filename.');
		$this->lastPage();
		if (Configuration::get('PDF') == 1){
			$output = 'I';
			$this->IncludeJS("print(true);");
		} else {
			if ($display === true)
				$output = 'D';
			elseif ($display === false)
				$output = 'S';
			elseif ($display == 'D')
				$output = 'D';
			elseif ($display == 'S')
				$output = 'S';
			else 	
				$output = 'I';
		}
		ob_clean();
		return $this->output($filename, $output);
	}

	public function writePage()
	{
		$this->SetHeaderMargin(5);
		$this->SetFooterMargin(18);
		$this->setMargins(10, 10, 10);
		$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
    	$this->AddPage('P', 'A4');
		$this->writeHTML($this->content, true, false, true, false, '');
	}

	public function Footer()
	{
		$this->context = Context::getContext();
		$customer = $this->context->employee ? ($this->context->employee->firstname.' '.$this->context->employee->lastname) : 'E-shop system';
		$this->writeHTML($this->footer);
		$this->SetY(-28);
		$this->SetX(20);

		if (Configuration::get('PDF_FONT'))
			$this->SetFont(Configuration::get('PDF_FONT'), 'I', 6);
		if (empty($this->pagegroups))
		{
			$this->Cell(0, 25, 'Dokument vystavil(a): '.$customer, 0, false, 'L', 0, '', 0, false, 'T', 'M');
			$this->Cell(0, 25, 'Strana '.$this->getAliasNumPage().' / '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
		} else {
			$this->Cell(0, 25, 'Dokument vystavil(a): '.$customer, 0, false, 'L', 0, '', 0, false, 'T', 'M');
			$this->Cell(0, 25, 'Strana '.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
		}
	}

	public function setFontForLang($iso_lang)
	{
		$this->font = PDFGenerator::DEFAULT_FONT;
		if (array_key_exists($iso_lang, $this->font_by_lang))
			$this->font = $this->font_by_lang[$iso_lang];

		$this->font = Configuration::get('PDF_FONT') ? Configuration::get('PDF_FONT') : $this->font;
		$this->setHeaderFont(array($this->font, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(array($this->font, '', PDF_FONT_SIZE_MAIN));
		$this->setFont($this->font);
	}
}