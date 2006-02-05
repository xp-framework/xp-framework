<?php
/* This file is part of the XP framework's people's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  include('chain-function.php');
  
  // {{{ main
  // What we would like to be able to do:
  // echo XPClass::forName('util.Binford')->newInstance()->toString();

  // What makes it work  
  echo '#1] Chain invokation results in ';
  var_dump(chain(XPClass::forName('util.Binford'), 'newInstance()', 'toString()'));
  
  // Test exceptions break the chain
  $caught= FALSE;
  try(); {
    chain(XPClass::forName('@@NOTEXISTANTCLASS@@'), 'newInstance()', 'toString()');
  } if (catch('lang.Throwable', $e)) {
    echo '#2] Chain broken with ', $e->getClassName(), ': ', $e->getMessage(), "\n";
    $caught= TRUE;
  }
  if (!$caught) xp::error(xp::stringOf(new Error('#2] Chain continued!')));
  
  // Test NULL doesn't cause FATAL errors
  $caught= FALSE;
  try(); {
    chain($instance= NULL, 'newInstance()', 'toString()');
  } if (catch('lang.NullPointerException', $e)) {
    echo '#3] Chain broken with ', $e->getClassName(), ': ', $e->getMessage(), "\n";
    $caught= TRUE;
  }
  if (!$caught) xp::error(xp::stringOf(new Error('#3] Chain continued!')));
  // }}}
?>
