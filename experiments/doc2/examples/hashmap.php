<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.Hashmap');

  // {{{ main
  $h= new Hashmap();
  $h->put('color', 'red');
  $h->put('count', 5);
  
  Console::writeLinef('Hashmap %s', $h->getClassName());
  
  // Check if a key "color" exists
  if ($h->containsKey('color')) {
    Console::writeLinef('- Contains key "color" with value (string)"%s"', $h->get('color'));
  }
  
  // Check if the value (int)5 exists
  if ($h->containsValue($c= 5)) {
    Console::writeLinef('- Contains the value (int)5');
  }

  // Show iterator functionality
  Console::writeLine('- Using iterator to enumerate keys');
  for ($i= $h->iterator(); $i->hasNext(); ) {
    $key= $i->next();
    Console::writeLinef('  * %-20s => %s', var_export($key, 1), var_export($h->get($key), 1));
  }
  // }}}
?>
