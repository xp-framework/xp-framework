<?php
  require('lang.base.php');
  $s= microtime(TRUE);
  uses(
    'tests.classes.RecursionOne',
    'tests.classes.RecursionTwo'
  );
  
  printf(
    "- %d classes, %.3f seconds\n",
    sizeof(get_declared_classes()),
    microtime(TRUE)- $s
  );

  echo '! ';
  var_dump(xp::$registry['errors']);
?>
