<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$
 */

  /**
   * Generic map class
   *
   * @purpose  Generics demonstration
   */
  class GenericMap<K, V> extends Object {
    var
      $elements= array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   array<K, V> initial default array()
     */
    function __construct($initial= array()) {
      $this->elements= $initial;
    }
  
    /**
     * Put an element into this map
     *
     * @access  public
     * @param   K key
     * @param   &V value
     */
    function &put($key, &$value) {
      $this->elements[$key]= &$value;
    }

    /**
     * Retrieve an element from this map
     *
     * @access  public
     * @param   K key
     * @return  &V value associated with the key
     */
    function &get($key) {
      return $this->elements[$key];
    }
  }
?>
