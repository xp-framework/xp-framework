<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */
 
  /**
   * Debugger
   *
   * @purpose   Debugger 
   */
  class Debugger extends Object {
  
    /**
     * Start the debugger
     *
     * @access  public
     */
    function start() {
      register_tick_function(array(&$this, 'trace'));
    }

    /**
     * Stop the debugger
     *
     * @access  public
     */
    function stop() {
      unregister_tick_function(array(&$this, 'trace'));
    }
    
    /**
     * Export an array
     *
     * @access  protected
     * @param   array a
     * @return  string[]
     */
    function export($a) {
      $args= array();
      foreach (array_keys($a) as $j) {
        $key= is_string($j) ? "'".$j."' => " : '';
        switch (gettype($a[$j])) {
          case 'integer':
          case 'float':
            $args[]= $key.$a[$j]; 
            break;

          case 'string':
            $args[]= $key."'".$a[$j]."'"; 
            break;

          case 'NULL':
            $args[]= $key.'(null)';
            break;

          case 'object':
            $args[]= $key.'{'.xp::typeof($a[$j]).'}';
            break;

          case 'array':
            $args[]= $key.'['.implode(', ', $this->export($a[$j])).']';
            break;

          default:
            $args[]= $key.gettype($a[$j]);
        }
      }
      return $args;
    }

    /**
     * Tick callback
     *
     * @access  magic
     */
    function trace() {
      $t= debug_backtrace();
      array_shift($t);
      for ($i= sizeof($t)- 1; $i >= 0; $i--) {
        fputs(STDERR, (1 == $i ? "\x1b[01;44;36m" : '').str_pad(sprintf(
          '>>> [%d] %s%s(%s) at %s:%d', 
          $i,
          isset($t[$i]['class']) ? xp::nameOf($t[$i]['class']).$t[$i]['type'] : '<main>::',
          $t[$i]['function'],
          isset($t[$i]['args']) ? implode(', ', $this->export($t[$i]['args'])) : '',
          isset($t[$i]['file']) ? basename($t[$i]['file']) : '<main>', 
          isset($t[$i]['line']) ? $t[$i]['line'] : 0
        ), 140)."\x1b[0m\n");
        
        if ('throw' == $t[$i]['function']) {
          fputs(STDERR, '[Press any key to continue]'); fread(STDIN, 1);
        }
      }
    }
  }
?>
