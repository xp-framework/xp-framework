<?php
  /* 
   * Dokumentiert die Funktion try/catch
   * 
   * $Id$
   */
   
  require_once('../../skeleton/lang.base.php');
  
  // Nichtexistente Datei öffnen
  $testFile= '/tmp/file_does_not_exist.txt';
  echo 'Trying to read '.$testFile.': ';
  try(); {
    $fd= fopen($testFile, 'r');
    $str= fgets($fd, 1024);
    fclose($fd);
  } if ($e= catch(E_ANY_EXCEPTION)) {
    echo 'failure "'.$e->message.'"';
  } else {
    echo 'success "'.$str.'"';
  }
  echo "\n";
?>
