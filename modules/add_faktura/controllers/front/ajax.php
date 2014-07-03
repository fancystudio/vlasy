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
class add_fakturaAjaxModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		if (Tools::isSubmit('submit_date'))
		{
			$output = 1;
			$order = new Order(Tools::getValue('id_order'));

			if (Tools::getValue('datum') && $this->ToTime(Tools::getValue('datum')) != $this->ToTime($order->date_add))
				$order->date_add = $this->ToTime(Tools::getValue('datum')).' '.$this->ThisTime();

			elseif (Tools::getValue('datum_inv') && $this->ToTime(Tools::getValue('datum_inv')) != $this->ToTime($order->invoice_date))
				$order->invoice_date = $this->ToTime(Tools::getValue('datum_inv')).' '.$this->ThisTime();

			elseif (Tools::getValue('datum_dlv') && $this->ToTime(Tools::getValue('datum_dlv')) != $this->ToTime($order->delivery_date))
				$order->delivery_date = $this->ToTime(Tools::getValue('datum_dlv')).' '.$this->ThisTime();
			else
				$output = 2;

			if (!$order->update())
				$output = 3;

			$array = array(
					'ok' => $output,
					'add' => $order->date_add,
					'inv' => $order->invoice_date,
					'dlv' => $order->delivery_date
			);
			die(Tools::jsonEncode($array));
		}
	}

	private function ToTime($time)
	{
		return StrFTime("%Y-%m-%d",StrToTime($time));
	}

	private function ThisTime()
	{
		return StrFTime("%H:%M:%S", Time());
	}
}