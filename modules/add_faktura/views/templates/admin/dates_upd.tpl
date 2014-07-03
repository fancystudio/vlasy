{* ########################################################################### */
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
/* ########################################################################### *}
<script type="text/javascript" src="../js/jquery/ui/i18n/jquery.ui.datepicker-cs.js"></script>
<script type="text/javascript" src="../js/jquery/ui/jquery.ui.datepicker.min.js"></script>
<link href="../js/jquery/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" media="all"/>

<script type="text/javascript">
	$(document).ready(function() {
		if ($("form#calendar_form .datepicker").length > 0)
			$("form#calendar_form .datepicker").datepicker({
								prevText: "",
								nextText: "",
								dateFormat: "yy-mm-dd"
		});
	});

	$(function() {				
    	$("#submit_date").click(function() {
			$("#calendar_form").submit(function(e) {
       			return false;
			});
					
		$.ajax({
			url: "{$link->getModuleLink($module_name, 'ajax', [], false)}",
			type:"POST",
			dataType: "json",
			data : {
					submit_date		: $("#submit_date").val(),
					datum			: $(".add").val(),
					datum_inv		: $(".inv").val(),
					datum_dlv		: $(".dlv").val(),
					id_order		: '{$order->id}'
			},
			success: function(data){
				if(data.ok == 1)
				{
					if (data.add)
						$(".add").val(data.add);
					if (data.inv)
						$(".inv").val(data.inv);
					if (data.dlv)
						$(".dlv").val(data.dlv);
					$(".conf_mk").fadeIn(200).show();
  					$('.conf_mk').delay(3000).slideUp();
				}
				if(data.ok == 2)
				{
					$(".warn_mk").fadeIn(200).show();
  					$('.warn_mk').delay(3000).slideUp();
				}
				if(data.ok == 3)
				{
					$(".error_mk").fadeIn(200).show();
  					$('.error_mk').delay(3000).slideUp();
				}
			}
			});
    	});
	});
</script>

<form action="{$link->getModuleLink($module_name, 'ajax', [], false)}" method="post" id="calendar_form" style="width:100%">
	<fieldset>
		<legend><img src="../img/admin/date.png" alt="" title="" />{l s='Úprava datumů objednávky'}</legend>
		<div class="conf conf_mk" style="display:none">{l s='Datumy byly aktualizovány'}</div>
		<div class="warn warn_mk" style="display:none">{l s='Nebyl vybrán nový datum'}</div>
		<div class="error error_mk" style="display:none">{l s='Chyba při ukládání'}</div>
		<p><label>{l s='Datum vytvoření objednávky: '}</label>
			<input type="text" name="datum" value="{$order->date_add}" class="datepicker add" style="width:140px">
		</p>
		<p><label>{l s='Datum vystavení faktury: '}</label>
			<input type="text" name="datum_inv" value="{$order->invoice_date}" class="datepicker inv" style="width:140px">
		</p>
		<p><label>{l s='Datum vystavení dodacího listu: '}</label>
			<input type="text" name="datum_dlv" value="{$order->delivery_date}" class="datepicker dlv" style="width:140px">
		</p>
		<br /><br />
		<center><input id="submit_date" type="submit" class="button" name="submit_date" value="{l s='Uložit datumy'}" /></center>
	</fieldset>
</form>