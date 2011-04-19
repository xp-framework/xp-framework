<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a single parameter to a SOAP call.
   *
   * @purpose  Wrapper
   */
  class Parameter extends Object {
    public
      $name     = '',
      $value    = NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   var value default NULL
     */
    public function __construct($name, $value= NULL) {
      $this->name= $name;
      $this->value= $value;
    }

    /**
     * Creates a string representation of this image object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s@(%s) {%s}',
        $this->getClassName(),
        $this->name,
        ($this->value instanceof Generic 
          ? $this->value->toString() 
          : var_export($this->value, 1)
        )
      );
    }
  }
?>
