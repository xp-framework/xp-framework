<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');

  $annotation= isset($argv[1]) ? $argv[1] : '[@webmethod, @restricted(role= \'admin\')]';
  var_dump($annotation);
  
  if (preg_match_all(
    '/@([a-z_]+)(\(([a-z_]+)( *= *'.
      '(\'([^\']+)\')|'.
      '(\"([^\"]+)\")|'.
      '([^\)]+)'.
    ')?\))?/i', 
    trim($annotation, '[]'),
    $matches,
    PREG_SET_ORDER
  )) {
    var_dump($matches);
  } else {
    var_dump(xp::registry('errors'));
  }
?>
