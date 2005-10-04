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
      
      if (!isset($t[2]['function'])) {
        fputs(STDERR, sprintf(
          "\x1b[01;41;37m- <main> in %s:%d\x1b[0m\n",
          basename($t[1]['file']),
          $t[1]['line']
        ));
        return;
      }

      fputs(STDERR, sprintf(
        "  %s %d, %s%s(%s) in %s:%d\n",
        str_repeat(' ', sizeof($t)),
        sizeof($t),
        isset($t[2]['class']) ? xp::nameOf($t[2]['class']).$t[2]['type'] : '<main>::',
        $t[2]['function'],
        implode(', ', $this->export($t[2]['args'])),
        basename($t[1]['file']),
        $t[1]['line']
      ));
    }
  }
?>
