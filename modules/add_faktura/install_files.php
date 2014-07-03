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
	function _backup_file($source, $destination, &$warnings) {
		
		if(file_exists($destination) ){
            $dH=filesize($destination);
				if($dH > 100) {
					$warnings[] = "Tento soubor '.$destination.' již existuje, ale liší se od toho, který dodán s modulem. Zkontrolujte prosím kompatibilitu v záložním souboru .opbak";
				}
			if(!rename($destination, $destination.".opbak"))
				$warnings[] = "Failed to rename file '$path' to '$path.opbak'";
		}
	}


	# copy altered files to proper location, calling backup beforehand
	function _copy_file($source, $destination, &$warnings, &$errors)
	{
		$dH = '';
		$dB = '';

		if(file_exists($destination))
            $dH=filesize($destination);
		if(file_exists($source))
            $dB=filesize($source);
			
		if ($dH != $dB){
	  		_backup_file($source, $destination, $warnings);
			if (!copy($source, $destination))
				$errors[] = "Failed to copy file '$source' to '$destination'";
		}
	}


	$errors = array();
	$warnings = array();


	# check base dir permissions
	if (!is_writable(_PS_ROOT_DIR_)) {
	  $errors[] = "Prestashop base dir is not writable. Please set permissions using: \"sudo chmod g+rw "._PS_ROOT_DIR_." -R\", if it does not help, use: \"sudo chmod o+rw "._PS_ROOT_DIR_." -R\". You can revoke these permissions by replacig \"+\" sign with \"-\""; 
	}
	else
	{
		
		$install_dir = _PS_MODULE_DIR_."add_faktura/install/";
		$files_owerride = _PS_OVERRIDE_DIR_.'classes/';
		$files_tools = _PS_ROOT_DIR_.'/tools/tcpdf';
	  
		_copy_file($install_dir."fonts/dejavuserifcondensed.ctg.z", $files_tools.'/fonts/dejavuserifcondensed.ctg.z', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensed.php", $files_tools.'/fonts/dejavuserifcondensed.php', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensed.z", $files_tools.'/fonts/dejavuserifcondensed.z', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedb.ctg.z", $files_tools.'/fonts/dejavuserifcondensedb.ctg.z', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedb.php", $files_tools.'/fonts/dejavuserifcondensedb.php', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedb.z", $files_tools.'/fonts/dejavuserifcondensedb.z', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedbi.ctg.z", $files_tools.'/fonts/dejavuserifcondensedbi.ctg.z', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedbi.php", $files_tools.'/fonts/dejavuserifcondensedbi.php', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedbi.z", $files_tools.'/fonts/dejavuserifcondensedbi.z', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedi.ctg.z", $files_tools.'/fonts/dejavuserifcondensedi.ctg.z', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedi.php", $files_tools.'/fonts/dejavuserifcondensedi.php', $warnings, $errors);
		_copy_file($install_dir."fonts/dejavuserifcondensedi.z", $files_tools.'/fonts/dejavuserifcondensedi.z', $warnings, $errors);
		
		$theme_list = Theme::getThemes();
		foreach ($theme_list as $theme)
		{
			$files_theme = _PS_ROOT_DIR_.'/themes/'.$theme->directory;
		
			if(!is_dir($files_theme.'/pdf'))
				mkdir($files_theme.'/pdf', 0750);
			if(!is_dir($files_theme.'/pdf/lang'))
				mkdir($files_theme.'/pdf/lang', 0750);	  
	  
	  		_copy_file($install_dir."pdf/lang/cs.php", $files_theme.'/pdf/lang/cs.php', $warnings, $errors);
	  		_copy_file($install_dir."pdf/lang/sk.php", $files_theme.'/pdf/lang/sk.php', $warnings, $errors);
	  		_copy_file($install_dir."pdf/footer.tpl.php", $files_theme.'/pdf/footer.tpl', $warnings, $errors);
	  		_copy_file($install_dir."pdf/header.tpl.php", $files_theme.'/pdf/header.tpl', $warnings, $errors);
	  		_copy_file($install_dir."pdf/invoice.tpl.php", $files_theme.'/pdf/invoice.tpl', $warnings, $errors);
	  		_copy_file($install_dir."pdf/invoice.tax-tab.tpl.php", $files_theme.'/pdf/invoice.tax-tab.tpl', $warnings, $errors);
	  		_copy_file($install_dir."pdf/delivery-slip.tpl.php", $files_theme.'/pdf/delivery-slip.tpl', $warnings, $errors);
	  		_copy_file($install_dir."pdf/order-slip.tpl.php", $files_theme.'/pdf/order-slip.tpl', $warnings, $errors);
	  		_copy_file($install_dir."pdf/invoice-b2b.tpl.php", $files_theme.'/pdf/invoice-b2b.tpl', $warnings, $errors);
		}
	  
	}


	$ret = true;
	if (!empty($warnings))
	  foreach ($warnings as $warning_msg)
	    $this->_errors[] = "[WARNING] ".$warning_msg;

	if (!empty($errors)) {
	  foreach ($errors as $error_msg)
	  $this->_errors[] = "[ERROR] ".$error_msg;
	  $ret = false;
	}