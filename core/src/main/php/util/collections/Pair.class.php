<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Provides key and value for iteration
   *
   * @see  xp://util.collections.HashTable
   */
  #[@generic(self= 'K, V')]
  class Pair extends Object {
    #[@type('K')]
    public $key;
    #[@type('V')]
    public $value;

    /**
     * Constructor
     *
     * @param  K key
     * @param  V value
     */
    #[@generic(params= 'K, V')]
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
