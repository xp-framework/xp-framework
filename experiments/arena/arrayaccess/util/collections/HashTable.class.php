<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Primitive', 'util.collections.Map');

  /**
   * Hash table consisting of non-null objects as keys and values
   *
   * @see      xp://util.collections.Map
   * @purpose  Map interface implementation
   */
  class HashTable extends Object implements Map, ArrayAccess {
    public
      $_buckets = array(),
      $_hash    = 0;

    /**
     * = list[] overloading
     *
     * @param   lang.Generic offset
     * @return  lang.Generic
     */
    public function offsetGet($offset) {
      return $this->get($offset);
    }

    /**
     * list[]= overloading
     *
     * @param   lang.Generic offset
     * @param   lang.Generic value
     */
    public function offsetSet($offset, $value) {
      $this->put($offset, $value);
    }

    /**
     * isset() overloading
     *
     * @param   lang.Generic offset
     * @return  bool
     */
    public function offsetExists($offset) {
      return $this->containsKey($offset);
    }

    /**
     * unset() overloading
     *
     * @param   lang.Generic offset
     */
    public function offsetUnset($offset) {
      $this->remove($offset);
    }

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
    public function put($key, Generic $value) {
      $k= Primitive::boxed($key);
      $h= $k->hashCode();
      if (!isset($this->_buckets[$h])) {
        $previous= NULL;
      } else {
        $previous= $this->_buckets[$h][1];
      }

      $this->_buckets[$h]= array($k, $value);
      $this->_hash+= HashProvider::hashOf($h.$value->hashCode());
      return $previous;
    }

    /**
     * Returns the value to which this map maps the specified key. 
     * Returns NULL if the map contains no mapping for this key.
     *
     * @param   lang.Generic key
     * @return  lang.Generic the value associated with the key
     */
    public function get($key) {
      $h= Primitive::boxed($key)->hashCode();
      if (!isset($this->_buckets[$h])) return NULL; 

      return $this->_buckets[$h][1];
    }
    
    /**
     * Removes the mapping for this key from this map if it is present.
     * Returns the value to which the map previously associated the key, 
     * or null if the map contained no mapping for this key.
     *
     * @param   lang.Generic key
     * @return  lang.Generic the previous value associated with the key
     */
    public function remove($key) {
      $h= Primitive::boxed($key)->hashCode();
      if (!isset($this->_buckets[$h])) {
        $previous= NULL;
      } else {
        $previous= $this->_buckets[$h][1];
        $this->_hash-= HashProvider::hashOf($h.$previous->hashCode());
        unset($this->_buckets[$h]);
      }

      return $previous;
    }
    
    /**
     * Removes all mappings from this map.
     *
     */
    public function clear() {
      $this->_buckets= array();
      $this->_hash= 0;
    }

    /**
     * Returns the number of key-value mappings in this map
     *
     */
    public function size() {
      return sizeof($this->_buckets);
    }

    /**
     * Returns true if this map contains no key-value mappings. 
     *
     */
    public function isEmpty() {
      return empty($this->_buckets);
    }
    
    /**
     * Returns true if this map contains a mapping for the specified key.
     *
     * @param   lang.Generic key
     * @return  bool
     */
    public function containsKey($key) {
      return isset($this->_buckets[Primitive::boxed($key)->hashCode()]);
    }

    /**
     * Returns true if this map maps one or more keys to the specified value. 
     *
     * @param   lang.Generic value
     * @return  bool
     */
    public function containsValue(Generic $value) {
      foreach (array_keys($this->_buckets) as $key) {
        if ($this->_buckets[$key][1]->equals($value)) return TRUE;
      }
      return FALSE;
    }

    /**
     * Returns a hashcode for this map
     *
     * @return  string
     */
    public function hashCode() {
      return $this->_hash;
    }
    
    /**
     * Returns true if this map equals another map.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        ($cmp instanceof Map) && 
        ($this->hashCode() === $cmp->hashCode())
      );
    }
    
    /**
     * Returns an array of keys
     *
     * @return  lang.Generic[]
     */
    public function keys() {
      $keys= array();
      foreach (array_keys($this->_buckets) as $key) {
        $keys[]= $this->_buckets[$key][0];
      }
      return $keys;
    }
    
    /**
     * Returns a string representation of this map
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'['.sizeof($this->_buckets).'] {';
      if (0 == sizeof($this->_buckets)) return $s.' }';

      $s.= "\n";
      foreach (array_keys($this->_buckets) as $key) {
        $s.= '  '.$this->_buckets[$key][0]->toString().' => '.$this->_buckets[$key][1]->toString().",\n";
      }
      return substr($s, 0, -2)."\n}";
    }

  } 
?>
