<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('SignalHandler');

  /**
   * Signal handler
   *
   * @ext      pcntl
   * @see      http://www.javaspecialists.co.za/archive/Issue043.html
   * @see      php://pcntl_signal
   * @purpose  Handle signals
   */
  class Signal extends Object {
    var
      $number = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   int number
     */
    function __construct($number) {
      $this->number= $number;
    }
  
    /**
     * Set up a signal handler
     *
     * @access  public
     * @param   &Signal sig
     * @param   &SignalHandler handler
     * @return  bool
     * @throws  lang.IllegalArgumentException
     */
    function handle(&$sig, &$handler) {
      if (!pcntl_signal($sig->number, array(&$handler, 'handle'))) {
        return throw(new IllegalArgumentException('Cannot set signal handler'));
      }
      return TRUE;
    }
  }
?>
