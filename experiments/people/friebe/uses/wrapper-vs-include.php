<?php
/* This file is part of the XP framework's people's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses('util.profiling.Timer', 'lang.RuntimeError');
  
  // {{{ Include wrapper
  class uwrp·include {
    var $fd;

    function stream_open($path, $mode, $options, &$opened) {
      $this->fd= fopen(substr($path, 5), $mode, TRUE);
      return (bool)$this->fd;
    }

    function stream_read($count) {
      return fread($this->fd, $count);
    }

    function stream_eof() {
      return feof($this->fd);
    }
    
    function stream_close() {
      return fclose($this->fd);
    }
  }
  // }}}
  
  // {{{ Assertion callback
  function assertion($file, $line, $code) {
    xp::error(xp::stringOf(new RuntimeError('Assertion failed at line '.$line)));
    // Bails
  }
  // }}}
  
  // {{{ main
  stream_wrapper_register('xp', 'uwrp·include');
  switch (@$argv[1]) {
    case 'include': $str= 'filename.inc'; break;
    case 'wrap': $str= 'xp://filename.inc'; break;
    default: die('Unknow argument');
  }
  
  // Loop
  $t= &new Timer();
  $t->start();
  for ($i= 0; $i < 10000; $i++) {
    include_once($str);
  }
  $t->stop();
  printf("%.3f seconds for %d runs\n", $t->elapsedTime(), $i);
  
  assert_options(ASSERT_ACTIVE, TRUE);
  assert_options(ASSERT_CALLBACK, 'assertion');

  assert($included);
  assert(class_exists('DeclarationTest'));
  assert(0 == sizeof(xp::registry('errors')));
  // }}}
?>
