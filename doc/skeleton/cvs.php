<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  require('lang.base.php');
  uses('org.cvshome.CVSInterface', 'util.cmd.ParamString');

  $p= &new ParamString();
  if (!$p->exists(1)) {
    printf("Usage: %s <filename>\n", $p->value(0));
    exit();
  }
  
  $cvs= &new CVSInterface($p->value(1));
  try(); {
    $status= &$cvs->getStatus();
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  var_dump($status);
?>
