<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.Date');

  // {{{ main
  $p= &new ParamString();  

  Console::writeLinef(
    '- Current date: %s',
    Date::now()->toString()
  );
  Console::writeLinef(
    '- Parsed date from "%s": %s',
    $p->value(1),
    Date::fromString($p->value(1))->toString()
  );
  // }}}
?>
