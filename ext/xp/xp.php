<?php
/* This file is part of the XP framework extension
 *
 * $Id$ 
 */

  // {{{ main  
  var_dump(get_declared_classes('lang'));
  $o= new lang::Object();
  var_dump($o, $o->getClassName());
  $e= new lang::Exception();
  var_dump($e, $e->getClassName());
  // }}}
?>
