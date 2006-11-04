<?php
  require('lang.base.php');
  uses('MsXslProcessor');
  
  $proc= &new MsXslProcessor();
  $proc->setXSLFile('test.xsl');
  $proc->setXMLFile('test.xml');
  
  try(); {
    $proc->run();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  echo $proc->output();
?>
