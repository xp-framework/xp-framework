<?php
  set_error_handler('__capture');
  
  function __capture($severity, $message, $file, $line, $context) {
    $c= array();
    foreach ($context as $k => $v) {
      '_' == $k{0} || 'HTTP_' == substr($k, 0, 5) || $c[$k]= $v;
    }
    // DEBUG Console::writeLine($severity, ', ', $message, ', ', $file, ', ', $line, ', ', xp::stringOf($c));
    if (1 == sscanf($message, 'Undefined variable:  %s', $var)) {
      xp::registry('using', array(
        $var,
        $c
      ));
      return;
    }
    __error($severity, $message, $file, $line);
  }
  
  function using(&$expr, $as, $block) {

    // Border case #1: Exception already thrown at entry
    if (xp::registry('exceptions')) {
      xp::registry('using', NULL);
      return xp::null();
    }

    if (NULL === ($u= xp::registry('using'))) {
      $e= &new NullPointerException('Shadowing of as ('.xp::stringOf($as).')');
      $expr && $expr->__exit($e);
      return throw($e);
    }

    extract($u[1]);
    xp::registry('using', NULL);

    if (!$expr) {
      return throw(new NullPointerException('using('.$u[0].')'));
    }

    try(); {
      eval('$'.$u[0].'= &$expr; '.$block);
    } if (catch('Throwable', $e)) {
      // Intentionally empty
    } finally(); {
      $expr->__exit($e);
      if ($e) return throw($e);
    }
  }
?>
