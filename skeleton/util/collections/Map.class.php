<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.collections.HashProvider');

  /**
   * An object that maps keys to values. A map cannot contain duplicate 
   * keys; each key can map to at most one value. 
   *
   * @see      xp://util.collections.HashProvider
   * @purpose  Interface
   */
  interface Map extends ArrayAccess {
    
    /**
     * Associates the specified value with the specified key in this map.
     * If the map previously contained a mapping for this key, the old 
     * value is replaced by the specified value.
     * Returns previous value associated with specified key, or NULL if 
     * there was no mapping for the specified key.
     *
     * @param   lang.Generic key
     * @param   lang.Generic value
     * @return  lang.Generic the previous value associated with the key
     */
    public function put($key, Generic $value);

    /**
     * Returns the value to which this map maps the specified key. 
     * Returns NULL if the map contains no mapping for this key.
     *
     * @param   lang.Generic key
     * @return  lang.Generic the value associated with the key
     */
    public function get($key);
    
    /**
     * Removes the mapping for this key from this map if it is present.
     * Returns the value to which the map previously associated the key, 
     * or null if the map contained no mapping for this key.
     *
     * @param   lang.Generic key
     * @return  lang.Generic the previous value associated with the key
     */
    public function remove($key);
    
    /**
     * Removes all mappings from this map.
     *
     */
    public function clear();

    /**
     * Returns the number of key-value mappings in this map
     *
     */
    public function size();

    /**
     * Returns true if this map contains no key-value mappings. 
     *
     */
    public function isEmpty();
    
    /**
     * Returns true if this map contains a mapping for the specified key.
     *
     * @param   lang.Generic key
     * @return  bool
     */
    public function containsKey($key);

    /**
     * Returns true if this map maps one or more keys to the specified value. 
     *
     * @param   lang.Generic value
     * @return  bool
     */
    public function containsValue(Generic $value);

    /**
     * Returns a hashcode for this map
     *
     * @return  string
     */
    public function hashCode();
    
    /**
     * Returns true if this map equals another map.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp);

  }
?>
