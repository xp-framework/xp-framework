<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.Date', 'text.parser.DateParser');

  // {{{ main
  $p= &new ParamString();  

  Console::writeLinef(
    '- Current date: %s',
    Date::now()->toString()
  );
  Console::writeLinef(
    '- Parsed date from "%s": %s',
    $p->value(1),
    DateParser::parse($p->value(1))->toString()
  );
  // }}}
?>
