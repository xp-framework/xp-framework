<?php
/* Demo der Folder-Klasse
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('io.Folder');

  try(); {
    $d= new Folder(isset($argv[1]) ? $argv[1] : '.');
    while ($entry= $d->getEntry()) {
      printf("%s/%s\n", $d->uri, $entry);
    }
    $d->close();
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
  }
?>
