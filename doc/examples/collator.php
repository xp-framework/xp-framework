<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.Hashmap', 'text.Collator');

  // {{{ main
  $p= new ParamString();
  if (!$p->exists(1)) {
    Console::writeLinef('Usage: %s %s <<locale>>', $p->value(-1), $p->value(0));
    exit(1);
  }

  try {
    $locale= new Locale($p->value(1));
  } catch (IllegalArgumentException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  // Create a hashmap
  $h= new Hashmap();
  $h->put('auml', 'ä');
  $h->put('a', 'a');
  $h->put('b', 'b');
  $h->put('A', 'A');
  $h->put('Auml', 'Ä');

  Console::writeLine('Hashmap: ', $h->toString());
  
  // Sort the hashmap using the collator for the specified locale
  $h->usort(Collator::getInstance($locale));

  Console::writeLine('Hashmap: ', $h->toString());
  // }}}
?>
