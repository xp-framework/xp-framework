<?php
/* Beispiel zur Verwendung der GetDescription-Klasse
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('de.schlund.domain.GetDescription');
  
  $descr= new GetDescription();
  $descr->setDomain(isset($argv[1]) ? $argv[1] : 'thekid.de');
  try(); {
    $return= $descr->query();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit;
  }
  var_dump($return);
  
  echo "\n\n";
?>
