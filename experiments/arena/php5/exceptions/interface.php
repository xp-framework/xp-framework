<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  // {{{ class Error
  class Error implements IException {
    protected 
      $message  = '',
      $code     = 0,
      $file     = '',
      $line     = 0,
      $trace    = array();

    public function __construct($message, $code= 0) {
      $this->trace= debug_backtrace();
      $this->message= $message;
      $this->code= $code;
      for ($i= 0, $s= sizeof($this->trace); $i < $s; $i++) {
        if (!isset($this->trace[$i]['line'])) continue;
        $this->file= $this->trace[$i]['file'];
        $this->line= $this->trace[$i]['line'];
        break;
      }
    }
    
    public function getMessage() {
      return $this->message;
    }

    public function getCode() {
      return $this->code;
    }

    public function getFile() {
      return $this->file;
    }

    public function getLine() {
      return $this->line;
    }
    
    public function getTrace() {
      return $this->trace;
    }
    
    public function __toString() {
      $return= 'Exception '.get_class($this).' ('.$this->getMessage().")\n";

      for ($i= 0, $s= sizeof($this->trace); $i < $s; $i++) {
        $t= &$this->trace[$i];
        $args= array();
        for ($j= 0, $a= sizeof($t['args']); $j < $a; $j++) {
          if (is_object($t['args'][$j])) {
            $args[]= get_class($t['args'][$j]).' { ... }';
          } elseif (is_array($t['args'][$j])) {
            $args[]= 'array['.sizeof($t['args'][$j]).'] ( ... )';
          } elseif (is_string($t['args'][$j])) {
            $args[]= "'".substr($t['args'][$j], 0, 0xF).(strlen($t['args'][$j]) > 0xF ? '...' : '')."'";
          } else {
            $args[]= var_export($t['args'][$j], 1);
          }
        }
        $return.= sprintf(
          "  at %s(%d) %s%s%s(%s)\n",
          isset($t['file']) ? basename($t['file']) : '(null)',
          isset($t['line']) ? $t['line'] : '0',
          isset($t['class']) ? $t['class'] : '<main>',
          isset($t['type']) ? $t['type'] : '::',
          $t['function'],
          implode(', ', $args)
        );
      }
      return $return;

    }
  }
  // }}} 

  // {{{ void raise(string str)
  //     Raises an error 
  function raise($str) {
    throw new Error($str);
  }
  // }}}
  
  // {{{ main
  try {
    raise('An error occured');
  } catch (IException $e) {
    echo '*** ', $e, "\n";
  }
  // }}}
?>
