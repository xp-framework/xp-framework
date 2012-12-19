<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents the REST payload
   *
   */
  class Payload extends Object {
    public $value, $properties;
  
    /**
     * Creates a new payload instance
     *
     * @param   var value
     * @param   [:string] properties
     */
    public function __construct($value= NULL, $properties= array()) {
      $this->value= $value;
      $this->properties= $properties;
    }

    /**
     * Returns whether a given value is equal to this payload
     * 
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        ($this->value instanceof Generic ? $this->value->equals($cmp->value) : $this->value === $cmp->value) &&
        $this->properties === $cmp->properties
      );
    }
  }
?>
