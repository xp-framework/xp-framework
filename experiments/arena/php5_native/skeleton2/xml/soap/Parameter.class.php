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
     * @access  public
     * @param   string name
     * @param   mixed value default NULL
     */
    public function __construct($name, $value= NULL) {
      $this->name= $name;
      $this->value= &$value;
    }

    /**
     * Creates a string representation of this image object
     *
     * @access  public
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
