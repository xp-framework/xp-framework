<?php
  require('../../../skeleton/lang.base.php');
  import('de.schlund.domain.GetDescription');
  
  $descr= new GetDescription();
  $descr->setDomain(isset($argv[1]) ? $argv[1] : 'thekid.de');
  try(); {
    $return= $descr->query();
  } if ($e= catch(E_ANY_EXCEPTION)) {
    var_dump($e);
    exit;
  }
  var_dump($return);
  
  echo "\n\n";
?>
