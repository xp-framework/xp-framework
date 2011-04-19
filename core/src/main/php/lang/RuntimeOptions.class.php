<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents runtime options
   *
   * @test     xp://net.xp_framework.unittest.core.RuntimeOptionsTest
   * @see      xp://lang.Runtime#startupOptions
   */
  class RuntimeOptions extends Object {
    protected $backing= array();

    /**
     * Constructor
     *
     * @param   [:var] backing default array()
     */
    public function __construct($backing= array()) {
      $this->backing= $backing;
    }
    
    /**
     * Set switch (e.g. "-q")
     *
     * @param   string name switch name without leading dash
     * @return  lang.RuntimeOptions this object
     */
    public function withSwitch($name) {
      $this->backing["\1".$name]= TRUE; 
      return $this;
    }

    /**
     * Get switch (e.g. "-q")
     *
     * @param   string name switch name without leading dash
     * @param   bool default default FALSE
     * @return  bool
     */
    public function getSwitch($name, $default= FALSE) {
      $key= "\1".$name;
      return isset($this->backing[$key])
        ? $this->backing[$key]
        : $default
      ;
    }
    
    /**
     * Get setting (e.g. "memory_limit")
     *
     * @param   string name
     * @param   string[] default default NULL
     * @return  string[] values
     */
    public function getSetting($name, $default= NULL) {
      $key= 'd'.$name;
      return isset($this->backing[$key])
        ? $this->backing[$key]
        : $default
      ;
    }

    /**
     * Set setting (e.g. "memory_limit")
     *
     * @param   string setting
     * @param   var value either a number, a string or an array of either
     * @param   bool add default FALSE
     * @return  lang.RuntimeOptions this object
     */
    public function withSetting($setting, $value, $add= FALSE) {
      $key= 'd'.$setting;
      if ($add && isset($this->backing[$key])) {
        $this->backing[$key]= array_merge($this->backing[$key], (array)$value); 
      } else {
        $this->backing[$key]= (array)$value; 
      }
      return $this;
    }
    
    /**
     * Sets classpath
     *
     * @param   var element either an array of paths or a single path
     * @return  lang.RuntimeOptions this object
     */
    public function withClassPath($element) {
      $this->backing["\0cp"]= array();
      foreach (is_array($element) ? $element : array($element) as $path) {
        $this->backing["\0cp"][]= rtrim($path, DIRECTORY_SEPARATOR);
      }
      return $this;
    }
    
    /**
     * Gets classpath
     *
     * @return  string[]
     */
    public function getClassPath() {
      return isset($this->backing["\0cp"]) ? $this->backing["\0cp"] : array();
    }

    /**
     * Return an array suitable for passing to lang.Process' constructor
     *
     * @return  string[]
     */
    public function asArguments() {
      $s= array();
      foreach ($this->backing as $key => $value) {
        if ("\1" === $key{0}) {
          $s[]= '-'.substr($key, 1);
        } else if ("\0" !== $key{0}) {
          foreach ($value as $v) {
            $s[]= '-'.$key.'='.$v;
          }
        }
      }
      return $s;
    }
    
    /**
     * Creates a string representation of these options
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@'.xp::stringOf($this->asArguments());
    }

    /**
     * Returns whether another object is equal to these options
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->backing === $cmp->backing;
    }
  }
?>
