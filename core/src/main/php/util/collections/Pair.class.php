<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Provides
   */
  class Pair extends Object {
    public $key;
    public $value;

    /**
     * Constructor
     *
     */
    public function __construct($key, $value) {
      $this->key= $key;
      $this->value= $value;
    }

    /**
     * Get hashing implementation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<key= '.xp::stringOf($this->key).', value= '.xp::stringOf($this->value).'>';
    }
  }
?>
