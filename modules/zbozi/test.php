<?php
 error_reporting(E_ALL);
 ini_set('display_errors', 1);
$filename='zbozi_heureka.xml';
 toZip($filename); 

   function toZip($filename) {
    $zip = new ZipArchive();
    if ($zip->open($filename.'.zip', ZIPARCHIVE::CREATE)!==TRUE) {
        exit("cannot open <$filename>\n");
    }
    $zip->addFile($filename);
    $zip->close();                 
 }  
?>
