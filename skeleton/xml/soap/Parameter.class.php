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
    var
      $name     = '',
      $value    = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   mixed value default NULL
     */
    function __construct($name, $value= NULL) {
      $this->name= $name;
      $this->value= &$value;
    }
  }
?>
