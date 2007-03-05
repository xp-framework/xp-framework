<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents an invocation
   *
   * @purpose  Value object
   */
  class Invocation extends Object {
    protected
      $method    = NULL,
      $class     = NULL,
      $instance  = NULL,
      $arguments = array();
      
    /**
     * Constructor
     *
     * @param   lang.Generic instance
     * @param   lang.XPClass class
     * @param   string method
     * @param   mixed[] arguments
     */
    public function __construct($instance, XPClass $class, $method, $arguments) {
      $this->instance= $instance;
      $this->class= $class;
      $this->method= $method;
      $this->arguments= $arguments;
    }
    
    /**
     * Get this call's instance
     *
     * @return  lang.Generic
     */
    public function getCallingInstance() {
      return $this->instance;
    }

    /**
     * Get this call's method
     *
     * @return  lang.reflect.Routine
     */
    public function getCallingMethod() {
      return ('__construct' == $this->method
        ? $this->getCallingClass()->getConstructor()
        : $this->getCallingClass()->getMethod($this->method)
      );
    }

    /**
     * Get this call's class or NULL if invoked from global scope
     *
     * @return  lang.XPClass
     */
    public function getCallingClass() {
      return $this->class;
    }

    /**
     * Get the number of arguments
     *
     * @return  int
     */
    public function numArguments() {
      return sizeof($this->arguments);
    }
    
    /**
     * Returns a string representation of this call
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s@{\n".
        "  [instance  ] %s\n".
        "  [class     ] %s\n".
        "  [method    ] %s\n".
        "  [arguments ] %s\n".
        "}",
        $this->getClassName(),
        xp::stringOf($this->getCallingInstance()),
        $this->getCallingClass()->toString(),
        xp::stringOf($this->getCallingMethod()),
        xp::stringOf($this->arguments)
      );
    }
  }
?>
