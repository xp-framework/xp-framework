<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('text.String', 'lang.System');

  // {{{ main
  $s= new String('Hello');
  Console::writeLine('String: ', $s->toString());
  
  // Appending it
  Console::writeLine(
    '- Appending " " and "World" using dereferencing: ',
    $s->append(' ')->append('World')->getBuffer()
  );

  // Substrings
  Console::writeLinef(
    '- Substring before " ": "%s", after: "%s"',
    $s->substringBefore(' ')->getBuffer(),
    $s->substringAfter(' ')->getBuffer()
  );
  Console::writeLinef(
    '- Substring 0 .. {indexOf(" ")}: "%s"',
    $s->substring(0, $s->indexOf(' '))->getBuffer()
  );
  
  // Regular expressions
  foreach (array('/^Hello/', '/world$/i') as $pattern) {
    Console::writeLine(
      '- Matches regular expression ', $pattern, ': ',
      var_export($s->matches($pattern), 1)
    );
  }
  $s->matches('/([^ ]+) ([a-z]+)/i', $matches);
  Console::writeLine(
    '- Matches from pattern /([^ ]+) ([a-z]+)/i : ', 
    var_export($matches, 1)
  );
  
  // Replacing
  $s->replace('World', String::valueOf(System::getProperty('user.name'))->toLowerCase()->getBuffer());
  Console::writeLine(
    '- Replaced "World" with system username, result: ',
    $s->getBuffer()
  );
  // }}}
?>
