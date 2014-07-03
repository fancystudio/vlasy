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
	function _restore_backup_file($path, &$warnings, &$errors) {
	  if (file_exists($path.".opbak")){
	  	_remove_file($path, $warnings, $errors);
	  	if (!rename($path.".opbak", $path))
	    	$errors[] = "Failed to rename file '$path.opbak' to '$path'";
	  }
	}

	# remove unique file (installed with OPC mod)
	function _remove_file($path, &$warnings, &$errors) {
	  
	  if (file_exists($path) && !unlink($path))
	    $errors[] = "Failed to remove file '$path'";
	}
	
	function RemoveDir($sDir) {

		if (is_dir($sDir)) {
			$sDir = rtrim($sDir, '/');
			$oDir = dir($sDir);
			while (($sFile = $oDir->read()) !== false) {
				if ($sFile != '.' && $sFile != '..') {
					(!is_link("$sDir/$sFile") && is_dir("$sDir/$sFile")) ? RemoveDir("$sDir/$sFile") : unlink("$sDir/$sFile");
				}
			}
			$oDir->close();
			rmdir($sDir);
			return true;
		}
		return false;
	}


	$errors = array();
	$warnings = array();


	# check base dir permissions
	if (!is_writable(_PS_ROOT_DIR_)) {
	  $errors[] = "Prestashop base dir is not writable. Please set permissions using: \"sudo chmod g+rw "._PS_ROOT_DIR_." -R\", if it does not help, use: \"sudo chmod o+rw "._PS_ROOT_DIR_." -R\". You can revoke these permissions by replacig \"+\" sign with \"-\""; 
	}
	else
	{
		$theme_list = Theme::getThemes();
		foreach ($theme_list as $theme)
		{
			$files_theme = _PS_ROOT_DIR_.'/themes/'.$theme->directory;
		
			RemoveDir($files_theme.'/pdf');
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