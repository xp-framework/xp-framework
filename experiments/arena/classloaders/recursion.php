<?php
  require('lang.base.php');
  $s= microtime(TRUE);
  uses(
    'webservices.soap.types.SOAPHashMap'
  );
  
  printf(
    "- %d classes, %.3f seconds\n",
    sizeof(get_declared_classes()),
    microtime(TRUE)- $s
  );

  echo '! ';
  var_dump(xp::$registry['errors']);
?>
