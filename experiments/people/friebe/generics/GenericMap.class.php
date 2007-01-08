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
    protected
      $elements= array();
      
    /**
     * Constructor
     *
     * @param   array<K, V> initial default array()
     */
    public function __construct($initial= array()) {
      foreach ($initial as $key => $value) {
        $this->put($key, $value);
      }
    }
  
    /**
     * Put an element into this map
     *
     * @access  public
     * @param   K key
     * @param   V value
     */
    public function put(K $key, V $value) {
      $this->elements[$key]= $value;
    }

    /**
     * Retrieve an element from this map
     *
     * @access  public
     * @param   K key
     * @return  V value associated with the key
     */
    public function get(K $key) {
      return $this->elements[$key];
    }
  }
?>
