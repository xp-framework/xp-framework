<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base class for all enumerations
   *
   * @purpose  Enumeration
   */
  class Enum extends Object {
    var 
      $ordinal  = 0,
      $value    = NULL,
      $name     = '';

    /**
     * Constructor 
     * 
     */
    public function __construct($name, $value) {
      $this->ordinal= constant($name);
      $this->value= $value;
      $this->name= $name;
    }
  }
?>
