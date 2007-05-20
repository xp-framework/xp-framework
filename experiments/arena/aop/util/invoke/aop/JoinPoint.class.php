<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Describes a joinpoint
   *
   * @purpose  AOP
   */
  class JoinPoint extends Object {
    public
      $instance= NULL,
      $method  = '',
      $args    = array();

    /**
     * Constructor
     *
     * @param   lang.Generic instance
     * @param   string method
     * @param   mixed[] args
     */
    public function __construct($instance, $method, $args) {
      $this->instance= $instance;
      $this->method= $method;
      $this->args= $args;
    }
  
    /**
     * Proceed with invocation
     *
     * @return  mixed
     */
    public function proceed() {
      return call_user_func_array(array($this->instance, '·'.$this->method), $this->args);
    }
    
    /**
     * Creates a string representation of this joinpoint
     *
     * @return  string
     */
    public function toString() {
      return 
        sprintf('%s<%s::%s(%s)>',
        $this->getClassName(),
        $this->instance->getClassName(),
        $this->method,
        implode(', ', array_map(array('xp', 'stringOf'), $this->args))
      );
    }
  }
?>
