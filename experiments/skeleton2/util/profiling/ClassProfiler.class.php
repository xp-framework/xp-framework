<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.profiling.Timer');

  /**
   * Class that implements a simple class profiler. Every call
   * is being measured in milliseconds.
   *
   * @ext overload
   */
  class ClassProfiler extends Object {
    public
      $timer= array(),
      $calls= array();
      
    public
      $_profilee;

    /**
     * Construct a profiler, overload this class
     *
     * @access  public
     */
    public function __construct() {
      $this->_profilee= NULL;
      overload ('ClassProfiler');
    }

    /**
     * Attach a profilee
     *
     * @access  public
     * @param   &Object obj
     */    
    public function attachProfilee(&$obj) {
      $this->_profilee= $obj;
    }

    /**
     * The "proxy" function
     *
     * @access  public
     * @param   string method
     * @param   mixed params
     * @param   mixed &return
     * @return  boolean success
     */    
    public function __call($method, $params, &$return) {
      $t= new Timer();
      $t->start();
      if (!isset ($this->timer[$method]))
        $this->timer[$method]= 0;
      
      if (!isset ($this->calls[$method]))
        $this->calls[$method]= 0;
      
      // Execute the function
      $return= call_user_func_array (array (&$this->_profilee, $method), $params);

      $t->stop();
      $this->timer[$method]+= $t->elapsedTime();
      $this->calls[$method]++;

      return TRUE;
    }

    /**
     * Retrieve the profiling results for a given function, or for all
     * if no function is given.
     *
     * @access  public
     * @param   string method default NULL
     * @return  mixed results
     */    
    public function getProfile($method= NULL) {
      if (NULL === $method)
        return array (
          'timer' => $this->timer,
          'calls' => $this->calls
        );
      
      return array (
        'timer' => $this->timer[$method],
        'calls' => $this->calls[$method]
      );
    }
  }
?>
