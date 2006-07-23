<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  
  require('__xp__.php');
  
  // {{{ Original
  // $s= NULL;
  // try {
  //   $s->invoke();
  // } catch (xp~lang~NullPointerException) {
  //   echo '*** Caught: ', $e->toString(), "\n";
  // }
  // }}}

  // {{{ Generated version
  $s= xp::$null;
  try {
    $s->invoke();
  } catch (XPException $__e) {
    if ($__e->cause instanceof xp·lang·NullPointerException) {
      $e= $__e->cause;
      echo '*** Caught: ', $e->toString(), "\n";
    } else {
      throw $__e;
    }
  }
  // }}}
?>
