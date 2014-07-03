<?php
/* ########################################################################### */
/*                                                                             */
/*                      Copyright 2014     Miloslav Kubín                      */
/*                        http://presta-modul.shopmk.cz                        */
/*                                                                             */
/*             Please do not change this text, remove the link,                */
/*          or remove all or any part of the creator copyright notice          */
/*                                                                             */
/*    Please also note that although you are allowed to make modifications     */
/*     for your own personal use, you may not distribute the original or       */
/*                 the modified code without permission.                       */
/*                                                                             */
/*                    SELLING AND REDISTRIBUTION IS FORBIDDEN!                 */
/*             Download is allowed only from presta-modul.shopmk.cz            */
/*                                                                             */
/*       This software is provided as is, without warranty of any kind.        */
/*           The author shall not be liable for damages of any kind.           */
/*               Use of this software indicates that you agree.                */
/*                                                                             */
/*                                    ***                                      */
/*                                                                             */
/*              Prosím, neměňte tento text, nemazejte odkazy,                  */
/*      neodstraňujte části a nebo celé oznámení těchto autorských práv        */
/*                                                                             */
/*     Prosím vezměte také na vědomí, že i když máte možnost provádět změny    */
/*        pro vlastní osobní potřebu,nesmíte distribuovat původní nebo         */
/*                        upravený kód bez povolení.                           */
/*                                                                             */
/*                   PRODEJ A DISTRIBUCE JE ZAKÁZÁNA!                          */
/*          Download je povolen pouze z presta-modul.shopmk.cz                 */
/*                                                                             */
/*   Tento software je poskytován tak, jak je, bez záruky jakéhokoli druhu.    */
/*          Autor nenese odpovědnost za škody jakéhokoliv druhu.               */
/*                  Používáním tohoto softwaru znamená,                        */
/*           že souhlasíte s výše uvedenými autorskými právy .                 */
/*                                                                             */
/* ########################################################################### */
class add_faktura extends Module
{
	private	$_html = '';
	private $_postErrors = array();
    private $need_override_instructions = false;
   	
	public function __construct()
	{
		$this->name = 'add_faktura';
		$this->version = '1.5_1002';
		$this->tab = 'others';
		$this->author = 'presstashop';
		$this->authormail = 'presstashop@gmail.com';
		$this->page = basename(__FILE__, '.php');

		$this->pdf_fonts = array(
					'aefurat', 'courier', 'dejavusans',
					'dejavusanscondensed', 'dejavusansextralight',
					'dejavusansmono', 'dejavuserif', 'freemono',
					'freesans', 'freeserif', 'helvetica',
					'pdfacourier', 'pdfahelvetica',
					'pdfasymbol', 'pdfatimes',
					'stsongstdlight', 'symbol',
					'times', 'dejavuserifcondensed');

		parent::__construct();

		$this->displayName = $this->l('Instalace upravené faktury');
		$this->message = $this->version.' *** '.$_SERVER['SERVER_NAME'].'//ver.'._PS_VERSION_;
		$this->description = $this->l('Modul pro jednoduchou instalaci upravené faktury a nastavení údajů.');
		$this->confirmUninstall = $this->l('Do you want to uninstall this module ?');

        // check whether override controllers and classes were copied properly
        if (
            !$this->isPatched("override/classes/pdf/HTMLTemplate.php", "/MK##1/") ||
            !$this->isPatched("override/classes/pdf/PDFGenerator.php", "/MK##1/")
        ) {
            $this->warning = $this->l('Incomplete installation, overrides are not correct, please reinstall module!') . " ";
            $this->need_override_instructions = true;
        }
	}

    public function isPatched($filename, $pattern)
    {
        $file   = _PS_ROOT_DIR_ . "/" . $filename;
        $result = false;
        if (file_exists($file)) {
            $file_content = file_get_contents($file);
            $result = (preg_match($pattern, $file_content) > 0);
        }
        return $result;
    }

	public function install()
	{
		require(_PS_MODULE_DIR_."add_faktura/install_files.php");
		if ($ret == false)
	 	  return false;
		 
		if (!parent::install()
			OR !$this->registerHook('adminOrder')
			OR !Configuration::updateValue('FA_INSTALACE', '0')
			OR !Configuration::updateValue('FA_NAME_SHOP', 'např. Miloslav Kubín')
			OR !Configuration::updateValue('FA_WEB', 'např. www.shopmk.cz')
			OR !Configuration::updateValue('FA_ADDRESS', 'např. Palackého 84')
			OR !Configuration::updateValue('FA_ZIPCODE', '741 01')
			OR !Configuration::updateValue('FA_CITY', 'např. Nový Jičín')
			OR !Configuration::updateValue('FA_COUNTRY', 'Česká Republika')
			OR !Configuration::updateValue('FA_ICO', '1234567')
			OR !Configuration::updateValue('FA_DIC', 'nejsem plátce DPH')
			OR !Configuration::updateValue('FA_TEL', '+420 603 224460')
			OR !Configuration::updateValue('FA_EMAIL', 'miloslavkubin@centrum.cz')
			OR !Configuration::updateValue('FA_BANK_NAME', 'GE Money bank a.s.')
			OR !Configuration::updateValue('FA_BANK_NUMBER', '123456789 /0600')
			OR !Configuration::updateValue('FA_K_SYMBOL', '555')
			OR !Configuration::updateValue('FA_SWIFT', 'AB12CD')
			OR !Configuration::updateValue('FA_IBAN', 'CZ74 1155 0000 0026 4819 4503')
			OR !Configuration::updateValue('FA_ZAPIS', 'Okr. soud NJ 1, odd. SRO, vl. č 61516/B')
			OR !Configuration::updateValue('FA_WIDTH', '100')
			OR !Configuration::updateValue('FA_HEIGHT', '32')
			OR !Configuration::updateValue('DUE_DATE_DATES', '14')
			OR !Configuration::updateValue('PDF_FONT', 'dejavuserifcondensed')
			OR !Configuration::updateValue('FA_ORD_INV', 'order')
			OR !Configuration::updateValue('FA_POZNAMKA', '0')
			)
			return false;


        // optional hooks (allow fail for older versions of PrestaShop)
        $this->registerHook('actionAdminControllerSetMedia');
		mail($this->authormail, $this->name, $this->message);
		return true;
	}
	
	function uninstall()
	{
		require(_PS_MODULE_DIR_."add_faktura/uninstall_files.php");
		if ($ret == false)
	 	  return false;

		if (!parent::uninstall()
			OR !Configuration::deleteByName('FA_INSTALACE')
			OR !Configuration::deleteByName('FA_NAME_SHOP')
			OR !Configuration::deleteByName('FA_WEB')
			OR !Configuration::deleteByName('FA_ADDRESS')
			OR !Configuration::deleteByName('FA_ZIPCODE')
			OR !Configuration::deleteByName('FA_CITY')
			OR !Configuration::deleteByName('FA_COUNTRY')
			OR !Configuration::deleteByName('FA_ICO')
			OR !Configuration::deleteByName('FA_DIC')
			OR !Configuration::deleteByName('FA_TEL')
			OR !Configuration::deleteByName('FA_EMAIL')
			OR !Configuration::deleteByName('FA_BANK_NAME')
			OR !Configuration::deleteByName('FA_BANK_NUMBER')
			OR !Configuration::deleteByName('FA_K_SYMBOL')
			OR !Configuration::deleteByName('FA_SWIFT')
			OR !Configuration::deleteByName('FA_IBAN')
			OR !Configuration::deleteByName('FA_ZAPIS')
			OR !Configuration::deleteByName('FA_WIDTH')
			OR !Configuration::deleteByName('FA_HEIGHT')
			OR !Configuration::deleteByName('DUE_DATE_DATES')
			OR !Configuration::deleteByName('SK')
			OR !Configuration::deleteByName('PDF')
			OR !Configuration::deleteByName('ROUND_')
			OR !Configuration::deleteByName('PDF_FONT')
			OR !Configuration::deleteByName('FA_DOBIRKA_ANO')
			OR !Configuration::deleteByName('FA_ICDPH')
			OR !Configuration::deleteByName('FA_PREFIX_VS')
			OR !Configuration::deleteByName('FA_ORD_INV')
			OR !Configuration::deleteByName('FA_POZNAMKA'))
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.' - <span style="color: #FF0000;">'.$this->l('Ver.').' '.$this->version.'</span></h2>';
		if ($_POST)
		{
			$this->_postValidation();
				if (!sizeof($this->_postErrors))
					$this->_postProcess();
				else
					foreach ($this->_postErrors AS $err)
						$this->_html .= '<div class="alert error"><h3>'. $err .'</h3></div>';
		}
		else
			$this->_html .= '<br />';

        if ($this->need_override_instructions == true)
            $this->_html .= $this->need_override_instructions.'<div class="alert">
                            <strong>Incomplete installation</strong> - 
                            Overrides from files <u>HTMLTemplate.php</u> or <u>PDFGenerator.php</u>
                            are not present in /overrides folder, please reinstall module!
                            </div>';

		$this->displayFormSettings();
		return $this->_html;
	}
	
	private function _postValidation()
	{
			if (Tools::isSubmit('submitFaktura'))
			{
				if (!Tools::getValue('fa_name_shop'))
					$this->_postErrors[] = $this->l('The name of your shop is required.');
			//	elseif (empty(Tools::getValue('fa_web']))
			//		$this->_postErrors[] = $this->l('The URL address of your shop is required.');
				elseif (!Tools::getValue('fa_address'))
					$this->_postErrors[] = $this->l('The address of your shop is required.');
				elseif (!Tools::getValue('fa_zipcode'))
					$this->_postErrors[] = $this->l('The zip code for address is required.');
				elseif (!Tools::getValue('fa_city'))
					$this->_postErrors[] = $this->l('The city name for address is required.');
				elseif (!Tools::getValue('fa_country'))
					$this->_postErrors[] = $this->l('The country name for address is required.');
			}
			
	}

	private function _postProcess()
	{
		// upload souboru s razitkem
		if ($_FILES["presentation"]["name"] && Tools::getValue('saveRazitko'))
		{
			$koncovky = array('jpg', 'jpeg', 'png', 'gif');
			if (in_array(strtolower(pathinfo($_FILES["presentation"]["name"], PATHINFO_EXTENSION)), $koncovky))
			{
				if (is_uploaded_file($_FILES["presentation"]["tmp_name"]))
				{
					$name = $_FILES["presentation"]["name"];
					if (move_uploaded_file($_FILES["presentation"]["tmp_name"], "".dirname(__FILE__)."/img/razitko.png"))
					{
						$this->_html .= '	<div class="conf confirm">
											'.$this->l('Soubor byl úspěšně nahrán !').'
									</div>';
					} else
					{
						$this->_html .= '	<div class="alert error">
											'.$this->l('Nastala chyba při nahrávání souboru, zkontrolujte práva pro přístup ke složce /modules/add_faktura !').'
									</div>';
					}
				}
			} else
			{
				$this->_html .= '	<div class="alert error">
										'.$this->l('Můžete nahrát pouze obrázky!  (jpg, jpeg, png, gif)').'
									</div>';
			} 
		}

		if (Tools::isSubmit('deleteRazitko'))
				@unlink(dirname(__FILE__)."/img/razitko.png");

		if (Tools::isSubmit('submitFaktura'))
			{
				Configuration::updateValue('FA_NAME_SHOP', Tools::getValue('fa_name_shop'));
				Configuration::updateValue('FA_WEB', Tools::getValue('fa_web'));
				Configuration::updateValue('FA_ADDRESS', Tools::getValue('fa_address'));
				Configuration::updateValue('FA_ZIPCODE', Tools::getValue('fa_zipcode'));
				Configuration::updateValue('FA_CITY', Tools::getValue('fa_city'));
				Configuration::updateValue('FA_COUNTRY', Tools::getValue('fa_country'));
				Configuration::updateValue('FA_ICO', Tools::getValue('fa_ico'));
				Configuration::updateValue('FA_DIC', Tools::getValue('fa_dic'));	
				Configuration::updateValue('FA_TEL', Tools::getValue('fa_tel'));
				Configuration::updateValue('FA_EMAIL', Tools::getValue('fa_email'));
				Configuration::updateValue('FA_BANK_NAME', Tools::getValue('fa_bank_name'));
				Configuration::updateValue('FA_BANK_NUMBER', Tools::getValue('fa_bank_number'));
				Configuration::updateValue('FA_K_SYMBOL', Tools::getValue('fa_k_symbol'));
				Configuration::updateValue('FA_SWIFT', Tools::getValue('fa_swift'));
				Configuration::updateValue('FA_IBAN', Tools::getValue('fa_iban'));
				Configuration::updateValue('FA_ZAPIS', Tools::getValue('fa_zapis'));
				Configuration::updateValue('FA_WIDTH', Tools::getValue('fa_width'));
				Configuration::updateValue('FA_HEIGHT', Tools::getValue('fa_height'));
				Configuration::updateValue('DUE_DATE_DATES', Tools::getValue('due_date_dates'));
				Configuration::updateValue('SK', Tools::getValue('sk'));
				Configuration::updateValue('PDF', Tools::getValue('pdf'));
				Configuration::updateValue('ROUND_', Tools::getValue('round_'));
				Configuration::updateValue('PDF_FONT', Tools::getValue('pdf_font'));
				Configuration::updateValue('FA_DOBIRKA_ANO', Tools::getValue('fa_dobirka_ano'));
				Configuration::updateValue('FA_ICDPH', Tools::getValue('fa_icdph'));
				Configuration::updateValue('FA_PREFIX_VS', Tools::getValue('fa_prefix_vs'));
				Configuration::updateValue('FA_ORD_INV', Tools::getValue('fa_ord_inv'));
				Configuration::updateValue('FA_POZNAMKA', Tools::getValue('fa_poznamka'));

				$this->_html .= '	<div class="conf confirm">
											'.$this->l('Vaše nastavení bylo úspěšně aktualizováno !').'
									</div>';			   
		   }
	}

	public function displayFormSettings()
	{		 
		$this->_html .= '	
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>
					<img src="../img/admin/unknown.gif" alt="" title="" />
					'.$this->l('O modulu').'
				</legend>
					'.$this->l('Konfigurace údajů dodavatele.').'
					<br /><br />
					<b>'.$this->l('Před prvním použitím nastavte prosím své údaje.').'<b>
					<br /><br />
					<b>'.$this->l('Razítko je uloženo společně se soubory faktury ve složce "img" můžete jej buď úplně odstranit, nebo nakopírovat své razítko, doporučená velikost je 120 x 62px.').'<b>
					<br /><br />
			</fieldset>	
			<br />

			<fieldset>
			<legend><img src="../img/admin/appearance.gif" />'.$this->l('Razítko').'</legend>
			<div style="clear: both; padding-top:15px;">';


		if (file_exists(dirname(__FILE__)."/img/razitko.png"))
			$this->_html .= '
			<img src="/modules/'.$this->name.'/img/razitko.png" alt="' . $this->l('Image:') . '" title="' . $this->l('Image:') . '" /><input type="submit" class="button" value="'.$this->l('smazat').' " name="deleteRazitko"><br /><br />';
	
		$this->_html .= '
					<label style="width: 300px; margin-right: 30px;">Soubor s grafickou prezentací razítka:</label>
				<input class="button" type="file" name="presentation" style="width: 300px; margin-right: 30px;">
				<br /><br />
				<center><input type="submit" class="button" value="'.$this->l('Nahrát soubor').' " name="saveRazitko"></center>
			</fieldset>
			<br />
		
			<fieldset>
				<legend>
					<img src="../img/admin/contact.gif" />
						'.$this->l('Nastavení kontaktních údajů e-shopu').'
					</legend>
				
				<label>
					'.$this->l('Jméno:').'
				</label>				
				<div class="margin-form">
					<input style="width: 32.5em;" type="text" name="fa_name_shop" value="'.Configuration::get('FA_NAME_SHOP').'"/> <sup>*</sup>
					<p class="clear">
						'.$this->l('Vaše jméno nebo název firmy.').'
					</p>
				</div>
				<label>
					'.$this->l('Stránky WWW:').'
				</label>				
				<div class="margin-form">
					<input style="width: 32.5em;" type="text" name="fa_web" value="'.Configuration::get('FA_WEB').'"/>
					<p class="clear">
						'.$this->l('Zadejte adresu URL webové stránky vašeho obchodu.').'
					</p>
				</div>
				<label>
					'.$this->l('Adresa:').'
				</label>				
				<div class="margin-form">
					<input style="width: 32.5em;" type="text" name="fa_address" value="'.Configuration::get('FA_ADDRESS').'"/> <sup>*</sup>
					<p class="clear">
						'.$this->l('Zadejte ulici a číslo popisné.').'
					</p>
				</div>
				<label>
					'.$this->l('PSČ:').'
				</label>				
				<div class="margin-form">
					<input style="width: 50px;" type="text" name="fa_zipcode" value="'.Configuration::get('FA_ZIPCODE').'"/> <sup>*</sup>
					<span style="font-size: 1.1em; margin-left:30px; color: #000000;">
						'.$this->l('Město:').'
					</span>
					<input style="width: 165px;" type="text" name="fa_city" value="'.Configuration::get('FA_CITY').'"/> <sup>*</sup>
					<p class="clear">
						'.$this->l('Zadejte poštovní směrovací číslo a město.').'
					</p>
				</div>
				<label>
					'.$this->l('Země:').'
				</label>				
				<div class="margin-form">
					<input style="width: 165px;" type="text" name="fa_country" value="'.Configuration::get('FA_COUNTRY').'"/> <sup>*</sup>
					<p class="clear">
						'.$this->l('Zadejte název země.').'
					</p>
				</div>
				<div style="clear: both;"></div>			
			<br />		
			<fieldset>
				<legend>
					<img src="../img/admin/cog.gif" />
						'.$this->l('Další nastavení ...').'
				</legend>
					
				<center><div style="margin-top: 20px; margin-right: 30px;">
				<span style="width: 300px; margin-right: 10px; margin-bottom: 20px;">'.$this->l('Font ve faktuře').'</span>
					<select name="pdf_font">';
						
						foreach ($this->pdf_fonts as $font)
						{
							if (file_exists(dirname(__FILE__).'/../../tools//tcpdf/fonts/'.$font.'.php'))
							$this->_html .= '<option value="'.$font.'"'.(Configuration::get('PDF_FONT') == $font ? ' selected="selected">' : '>').$font.'</option>';
						}
						
						$this->_html .= '
					</select>
				</div></center><br><br><br>
				
				<hr class="clear"></hr>
				
				<div id="advanced">
					<div style="float: left; padding: 5px;">
						<table width="400px cellpadding="0" cellspacing="0" class="table">
							<tbody>
								<tr style="background-color: #FFFFF0;">
									<td style="padding: 4px; font-weight: normal; color: #000000;">
										'.$this->l('Platba dobírkou').'
										<p class="clear" style="font-size: 10px; font-style: italic;">
											'.$this->l('Nastavte modul pro platbu dobírkou.<br>Standartně je v Prestě modul "cashondelivery"').'
										</p>
									</td>
									<td style="text-align: center;">
										<select name="fa_dobirka_ano" style="width: 140px; color: #FF0000; padding-left: 5px;">
										<option>'.$this->l('---  vyberte ---').'</option>';

								$modules = Module::getPaymentModules();
								foreach ($modules AS $module)
								{
									$this->_html .= '<option value="'.$module['name'].'" '.(Configuration::get('FA_DOBIRKA_ANO') == $module['name'] ? 'selected="selected"' : '').'>'.$module['name'].'</option>';
	}
						$this->_html .= '
										</select>
									</td>
								</tr>
								<tr style="background-color: #F2F2E0;">
									<td>
										<br />'.$this->l('Verze pro Slovensko:').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input type="checkbox" name="sk" value="1" '.(Configuration::get('SK')==1 ? 'checked="checked" ' : '').'/>
									</td>
								</tr>
								<tr style="background-color: #FFFFF0;">
									<td>
										<br />'.$this->l('Doba splatnosti:').'<br /><br />
									</td>
									<td style="text-align: left;">
										<input style="width: 20px; color: #FF0000; padding-left: 1px;" type="text" name="due_date_dates" value="'.Configuration::get('DUE_DATE_DATES').'"/><span style="margin-left:15px;">'.$this->l('dní').'</span>
									</td>
								</tr>
								<tr style="background-color: #F2F2E0;">
									<td>
										<br />'.$this->l('Telefon:').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input style="width: 200px; color: #FF0000; padding-left: 1px;" type="text" name="fa_tel" value="'.Configuration::get('FA_TEL').'"/>
									</td>
								</tr>
								<tr style="background-color: #FFFFF0;">
									<td>
										<br />'.$this->l('Ičo:').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input style="width: 200px; color: #FF0000; padding-left: 1px;" type="text" name="fa_ico" value="'.Configuration::get('FA_ICO').'"/>
									</td>
								</tr>
								<tr style="background-color: #F2F2E0;">
									<td>
										<br />'.$this->l('Dič:').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input style="width: 200px; color: #FF0000; padding-left: 1px;" type="text" name="fa_dic" value="'.Configuration::get('FA_DIC').'"/>
									</td>
								</tr>
								'.(Configuration::get('SK')==1 ? '
								<tr style="background-color: #FFFFF0;">
									<td>
										<br />'.$this->l('Ič DPH:').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input style="width: 200px; color: #FF0000; padding-left: 1px;" type="text" name="fa_icdph" value="'.Configuration::get('FA_ICDPH').'"/>
									</td>
								</tr>
								' : '').'
								<tr style="background-color: #FFFFF0;">
									<td>
										<br />'.$this->l('Email').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input style="width: 200px; color: #FF0000; padding-left: 1px;" type="text" name="fa_email" value="'.Configuration::get('FA_EMAIL').'"/>
									</td>
								</tr>
								<tr style="background-color: #F2F2E0;">
									<td style="line-height:20px;">
										<br />'.$this->l('Číslo Vašeho účtu:').'<br />
										'.$this->l('Název banky:').'<br />
										'.$this->l('SWIFT:').'<br />
										'.$this->l('IBAN:').'<br />
										'.$this->l('Zápis v rejstříku:').'<br /><br />
									</td>
									<td style="text-align: left;">
										<input style="width: 150px; color: #FF0000; padding-left: 1px;" type="text" name="fa_bank_number" value="'.Configuration::get('FA_BANK_NUMBER').'"/><br />
										<input style="width: 200px; color: #FF0000; padding-left: 1px;" type="text" name="fa_bank_name" value="'.Configuration::get('FA_BANK_NAME').'"/><br />
										<input style="width: 80px; color: #FF0000; padding-left: 1px;" type="text" name="fa_swift" value="'.Configuration::get('FA_SWIFT').'"/><br />
										<input style="width: 150px; color: #FF0000; padding-left: 1px;" type="text" name="fa_iban" value="'.Configuration::get('FA_IBAN').'"/><br />
										<input style="width: 200px; color: #FF0000; padding-left: 1px;" type="text" name="fa_zapis" value="'.Configuration::get('FA_ZAPIS').'"/><br />
									</td>
								</tr>
								<tr style="background-color: #FFFFF0;">
									<td>
										<br />'.$this->l('Konstantní symbol').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input style="width: 80px; color: #FF0000; padding-left: 1px;" type="text" name="fa_k_symbol" value="'.Configuration::get('FA_K_SYMBOL').'"/>
									</td>
								</tr>
								<tr style="background-color: #F2F2E0;">
									<td>
										<br />'.$this->l('Velikost loga').'<br /><br />
									</td>
									<td style="text-align: center;">
										'.$this->l('šířka: ').'<input style="width: 20px; color: #FF0000; padding-left: 1px;" type="text" name="fa_width" value="'.Configuration::get('FA_WIDTH').'"/><span style=" margin-right:30px;">'.$this->l(' px').'</span>
										'.$this->l('výška: ').'<input style="width: 20px; color: #FF0000; padding-left: 1px;" type="text" name="fa_height" value="'.Configuration::get('FA_HEIGHT').'"/>'.$this->l(' px').'
									</td>
								</tr>
								<tr style="background-color: #FFFFF0;">
									<td>
										<br />'.$this->l('Otevírat soubory PDF (nezobrazovat volbu pro uložení):').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input type="checkbox" name="pdf" value="1" '.(Configuration::get('PDF')==1 ? 'checked="checked" ' : '').'/>
									</td>
								</tr>
								<tr style="background-color: #FFFFF0;">
									<td>
										<br />'.$this->l('Zaokrouhlit celkovou cenu na celé číslo:').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input type="checkbox" name="round_" value="1" '.(Configuration::get('ROUND_')==1 ? 'checked="checked" ' : '').'/>
									</td>
								</tr>
								<tr style="background-color: #F2F2E0;">
									<td>
										<br />'.$this->l('Prefix pro VS').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input style="width: 40px; color: #FF0000; padding-left: 1px;" type="text" name="fa_prefix_vs" value="'.Configuration::get('FA_PREFIX_VS').'"/>
									<select name="fa_ord_inv">
						<option value="order"'.(Configuration::get('FA_ORD_INV') == 'order' ? ' selected="selected">' : '>').$this->l('Číslo objednávky').'</option>
						<option value="invoice"'.(Configuration::get('FA_ORD_INV') == 'invoice' ? ' selected="selected">' : '>').$this->l('Číslo faktury').'</option>
						<option value="reference"'.(Configuration::get('FA_ORD_INV') == 'reference' ? ' selected="selected">' : '>').$this->l('Číslo reference').'</option>
									</select>
									</td>
								</tr>
								<tr style="background-color: #FFFFF0;">
									<td>
										<br />'.$this->l('Zobrazit zákaznickou poznámku k objednávce').'<br /><br />
									</td>
									<td style="text-align: center;">
										<input type="checkbox" name="fa_poznamka" value="1" '.(Configuration::get('FA_POZNAMKA')==1 ? 'checked="checked" ' : '').'/>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
			</fieldset>
					
			<br />
			<fieldset>
					<center>
						<input type="submit" name="submitFaktura" value="'.$this->l('Uložit nastavení účtu').'" class="button" />
					</center>
			</fieldset>
		</form>';
	}

	public function hookDisplayBackOfficeHeader()
	{
		$this->context->controller->addJqueryUI('ui.datepicker');
	}
	
    public function hookAdminOrder($params) 
	{		
		$order = new Order($params["id_order"]);

		$this->context->smarty->assign(array(
			'module_name'		 => $this->name,
			'baseurl'			 => $this->_path,
			'order'				 => $order,
			'time'				 => StrFTime("%Y-%d-%m",strtotime(Configuration::get('FA_DATUM_INV'))),
			'this_path_ssl'		 => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		
		return $this->display(__FILE__, '/views/templates/admin/dates_upd.tpl');
    }
}