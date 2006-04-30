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
      foreach ($initial as $key => $value) {
        $this->put($key, $value);
      }
    }
  
    /**
     * Put an element into this map
     *
     * @access  public
     * @param   K key
     * @param   &V value
     */
    function &put(K $key, V &$value) {
      $this->elements[$key]= &$value;
    }

    /**
     * Retrieve an element from this map
     *
     * @access  public
     * @param   K key
     * @return  &V value associated with the key
     */
    function &get(K $key) {
      return $this->elements[$key];
    }
  }
?>
