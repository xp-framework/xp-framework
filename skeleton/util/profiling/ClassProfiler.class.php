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
     */
    public function __construct() {
      $this->_profilee= NULL;
    }

    /**
     * Attach a profilee
     *
     * @param   Object obj
     */    
    public function attachProfilee($obj) {
      $this->_profilee= $obj;
    }

    /**
     * The "proxy" function
     *
     * @param   string method
     * @param   mixed params
     * @param   mixed return
     * @return  boolean success
     */    
    public function __call($method, $params) {
      $t= new Timer();
      $t->start();
      if (!isset ($this->timer[$method]))
        $this->timer[$method]= 0;
      
      if (!isset ($this->calls[$method]))
        $this->calls[$method]= 0;
      
      // Execute the function
      $return= call_user_func_array (array ($this->_profilee, $method), $params);

      $t->stop();
      $this->timer[$method]+= $t->elapsedTime();
      $this->calls[$method]++;

      return $return;
    }
    
    /**
     * Get a property
     *
     * @param   string propname
     * @param   mixed propvalue
     * @return  boolean success
     */
    public function __get($propname) {
      if (!isset ($this->_profilee->{$propname}))
        return FALSE;
        
      $propvalue= $this->_profilee->{$propname};
      return $propvalue;        
    }
    
    /**
     * Set a property
     *
     * @param   string propname
     * @param   mixed propvalue
     * @return  bool success
     */
    public function __set($propname, $propvalue) {
      $this->_profilee->{$propname}= $propvalue;
      return TRUE;
    }
    
    /**
     * Gets all method names for called methods
     *
     * @return  array names
     */
    public function getCalledFunction() {
      return array_keys ($this->timer);
    }    

    /**
     * Retrieve the profiling results for a given function
     *
     * @param   string method
     * @return  float time
     */
    public function getTiming($method) {
      return $this->timer[$method];
    }
    
    /**
     * Retrieve the call counter for a given function
     *
     * @param   string method
     * @return  int calls
     */
    public function getCalledCount($method) {
      return $this->calls[$method];
    }
    
    /**
     * Creates a string representation for this class and
     * its profiling information.
     *
     * @return  string representation
     */
    public function toString() {
      $vals= array_unique(array_merge(array_keys ($this->timer), array_keys ($this->calls)));
      $t= sprintf ("Profiling information for class %s\n", 
        ($this->_profilee instanceof Generic 
          ? $this->_profilee->getClassName() 
          : get_class ($this->_profilee)
      ));
      
      $sumCalls= $sumTimer= 0;
      foreach ($vals as $v) {
        $calls= max ($this->calls[$v], 1); 
        $sumCalls+= $this->calls[$v]; $sumTimer+= $this->timer[$v];
        $t.= sprintf ("Method %15s: %2d calls, %1.3fs, avg. %1.3fs\n",
          $v,
          $this->calls[$v],
          $this->timer[$v],
          $this->timer[$v] / $calls
        );
      }
      
      $t.= sprintf ("Total                *: %2d calls, %1.3fs, avg. %1.3fs\n",
        $sumCalls,
        $sumTimer,
        $sumTimer / $sumCalls
      );
      
      return $t;
    }
    
  } overload ('ClassProfiler');

?>
