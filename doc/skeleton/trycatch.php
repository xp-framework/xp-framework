<?php
  /* 
   * Dokumentiert die Funktion try/catch
   * 
   * $Id$
   */
   
  require('lang.base.php');
  uses('io.File');
  
  // Nichtexistente Datei öffnen
  $testFile= '/tmp/file_does_not_exist.txt';
  echo 'Trying to read '.$testFile.":\n";
  try(); {
    $file= new File($testFile);
    $file->open(FILE_MODE_READ);
    $str= $file->readLine();
    $file->close();
  } if (catch('FileNotFoundException', $e)) {
    die($e->printStackTrace());
  }

  echo 'success "'.$str.'"';
  echo "\n";
?>
