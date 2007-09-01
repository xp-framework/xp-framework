<?php
/* This class is part of the XP framework
 *
 * $Id: Parameter.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace webservices::soap;

  /**
   * Represents a single parameter to a SOAP call.
   *
   * @purpose  Wrapper
   */
  class Parameter extends lang::Object {
    public
      $name     = '',
      $value    = NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   mixed value default NULL
     */
    public function __construct($name, $value= ) {
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
        (is('Generic', $this->value) 
          ? $this->value->toString() 
          : var_export($this->value, 1)
        )
      );
    }
  }
?>
