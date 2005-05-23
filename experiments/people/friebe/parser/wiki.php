<?php
/* This file is part of the XP framework's people's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses('text.StringTokenizer');
  
  // {{{ main
  $class= &XPClass::forName('WikiSyntaxParser');
  $patterns= array();
  foreach ($class->getMethods() as $method) {
    if (!$method->hasAnnotation('parser')) continue;
    
    $state= $method->getAnnotation('parser', 'state');
    $pattern= $method->getAnnotation('parser', 'pattern');
    $patterns[$state][$pattern]= $method->getName();
  }
  
  $parser= &$class->newInstance();

  $text= <<<__
== This is a heading ==

This is regular text

== This is another heading ==
=== This is a subheading ===
Here comes a list:
* Hello
* World (http://example.com/)

Nice going!
__;
  
  $st= &new StringTokenizer($text."\n", "\n");
  $i= 0; $text= '';
  while ($st->hasMoreTokens()) {
    $token= $st->nextToken();
    $state= $parser->currentState();
    printf("[%-10s] %d %s\n", $state, ++$i, $token);
    
    foreach (array_merge($patterns[$state], $patterns['*']) as $key => $val) {
      $token= preg_replace_callback($key, array(&$parser, $val), $token);
    }
    $text.= $token."\n";
  }
  
  echo str_repeat('=', 72), "\n", $text;
?>
