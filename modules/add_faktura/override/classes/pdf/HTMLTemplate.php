<?php
abstract class HTMLTemplate extends HTMLTemplateCore
{
	public function getHeader()
	{
		$ic = '';
		$dic = '';
		$icdph = '';
		$path_logo = '';
		$razitko_path = '';
		$fa_ord_inv = '';
		$invoice_address = '';
		$fa_poznamka = '';

		// verification keys: MK##1
		$doprava = new Carrier($this->order->id_carrier, $this->order->id_lang);
		$delivery_address = new Address($this->order->id_address_delivery);
		$customer = new Customer($this->order->id_customer);
		$currency = new Currency($this->order->id_currency);
		$country = new Country($delivery_address->id_country);
		$invoice_date = $this->order->invoice_date;

		$path_logo = $this->getLogo();
		if (file_exists(_PS_MODULE_DIR_.'/add_faktura/img/razitko.png'))
			$razitko_path	 = _PS_MODULE_DIR_.'/add_faktura/img/razitko.png';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice){
			$invoice_address = new Address($this->order->id_address_invoice);
			$ic = $invoice_address->dni;
			$dic = $invoice_address->vat_number;
		} else {
			$ic = $delivery_address->dni;
			$dic = $delivery_address->vat_number;
		}
		
		if ((int)Configuration::get('SK'))
		{
			if (mb_strtoupper(substr($dic,0,2), "utf-8") == 'SK')
			{
				$icdph = $dic;
				$dic = '';
			}else
				$dic = $dic;
		}
		
		$messages = CustomerMessage::getMessagesByOrderId((int)($this->order->id), false);
		if (Configuration::get('FA_POZNAMKA'))
		foreach ($messages as $message)
			$fa_poznamka = nl2br2($message['message']);
		
		if (Configuration::get('FA_ORD_INV') == 'order')
			$fa_ord_inv = sprintf('%06d', $this->order->id);
		elseif (Configuration::get('FA_ORD_INV') == 'invoice')
			$fa_ord_inv = sprintf('%06d', $this->order->invoice_number);
		elseif (Configuration::get('FA_ORD_INV') == 'reference')
			$fa_ord_inv = $this->order->reference;

		$this->smarty->assign(array(
			'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
			'date_due'			 => strtotime($invoice_date.'+'.Configuration::get('DUE_DATE_DATES').' days'),
			'invoice_date'		 => $invoice_date,
			'fa_name_shop'		 => Configuration::get('FA_NAME_SHOP'),
            'fa_address'		 => Configuration::get('FA_ADDRESS'),
            'fa_zipcode'		 => Configuration::get('FA_ZIPCODE'),
            'fa_city'			 => Configuration::get('FA_CITY'),
			'fa_country'		 => Configuration::get('FA_COUNTRY'),
            'fa_web'			 => Configuration::get('FA_WEB'),
            'fa_ico'			 => Configuration::get('FA_ICO'),
            'fa_dic'			 => Configuration::get('FA_DIC'),
            'fa_icdph'			 => Configuration::get('FA_ICDPH'),
			'fa_tel'			 => Configuration::get('FA_TEL'),
            'fa_email'			 => Configuration::get('FA_EMAIL'),
            'fa_bank_name'		 => Configuration::get('FA_BANK_NAME'),
            'fa_bank_number'	 => Configuration::get('FA_BANK_NUMBER'),
			'fa_swift'			 => Configuration::get('FA_SWIFT'),
			'fa_iban'			 => Configuration::get('FA_IBAN'),
			'fa_zapis'			 => Configuration::get('FA_ZAPIS'),
			'fa_k_symbol'		 => Configuration::get('FA_K_SYMBOL'),
			'width_logo'		 => Configuration::get('FA_WIDTH'),
			'height_logo'		 => Configuration::get('FA_HEIGHT'),
			'fa_prefix_vs'		 => Configuration::get('FA_PREFIX_VS'),
			'fa_ord_inv'		 => $fa_ord_inv,
			'img_ps_dir'		 => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time'	 => Configuration::get('PS_IMG_UPDATE_TIME'),
			'delivery_prefix'	 => Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id),
			'invoice_prefix'	 => Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id),
			'sk'				 => Configuration::get('SK'),
			'round_'			 => Configuration::get('ROUND_'),
			'ic'				 => $ic,
			'dic'				 => $dic,
			'icdph'				 => $icdph,
			'razitko_path'		 => $razitko_path,
			'logo_path'			 => $path_logo,
			'title'				 => $this->title,
            'doprava'			 => $doprava->name,
			'dlv_adr'			 => $delivery_address,
			'inv_adr'			 => $invoice_address,
			'telefon'			 => preg_replace('~^(?=[^+])~', '+'.$country->call_prefix, preg_replace('~\s+~', '', $delivery_address->phone_mobile=='' ? $delivery_address->phone : $delivery_address->phone_mobile)),
            'order'				 => $this->order,
			'customer'			 => $customer,
            'currency'			 => $currency,
            'poznamka'			 => $fa_poznamka
		));
	}
}