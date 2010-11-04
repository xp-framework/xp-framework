<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Primitive', 'util.collections.Map');

  /**
   * Hash table consisting of non-null objects as keys and values
   *
   * @test     xp://net.xp_framework.unittest.util.collections.HashTableTest
   * @test     xp://net.xp_framework.unittest.util.collections.GenericsTest
   * @test     xp://net.xp_framework.unittest.util.collections.ArrayAccessTest
   * @test     xp://net.xp_framework.unittest.util.collections.BoxingTest
   * @see      xp://util.collections.Map
   * @purpose  Map interface implementation
   */
  #[@generic(self= 'K, V', Map= 'K, V')]
  class HashTable extends Object implements Map {
    protected
      $_buckets  = array(),
      $_hash     = 0;
    
    /**
     * = list[] overloading
     *
     * @param   K offset
     * @return  V
     */
    #[@generic(params= 'K', return= 'V')]
    public function offsetGet($offset) {
      return $this->get($offset);
    }

    /**
     * list[]= overloading
     *
     * @param   K offset
     * @param   V value
     */
    #[@generic(params= 'K, V')]
    public function offsetSet($offset, $value) {
      $this->put($offset, $value);
    }

    /**
     * isset() overloading
     *
     * @param   K offset
     * @return  bool
     */
    #[@generic(params= 'K')]
    public function offsetExists($offset) {
      return $this->containsKey($offset);
    }

    /**
     * unset() overloading
     *
     * @param   K offset
     */
    #[@generic(params= 'K')]
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
     * @param   K key
     * @param   V value
     * @return  V the previous value associated with the key
     */
    #[@generic(params= 'K, V', return= 'V')]
    public function put($key, $value) {
      $h= $key instanceof Generic ? $key->hashCode() : $key;
      if (!isset($this->_buckets[$h])) {
        $previous= NULL;
      } else {
        $previous= $this->_buckets[$h][1];
      }

      $this->_buckets[$h]= array($key, $value);
      $this->_hash+= HashProvider::hashOf($h.($value instanceof Generic ? $value->hashCode() : $value));
      return $previous;
    }

    /**
     * Returns the value to which this map maps the specified key. 
     * Returns NULL if the map contains no mapping for this key.
     *
     * @param   K key
     * @return  V the value associated with the key
     */
    #[@generic(params= 'K', return= 'V')]
    public function get($key) {
      $h= $key instanceof Generic ? $key->hashCode() : $key;
      return isset($this->_buckets[$h]) ? $this->_buckets[$h][1] : NULL; 
    }
    
    /**
     * Removes the mapping for this key from this map if it is present.
     * Returns the value to which the map previously associated the key, 
     * or null if the map contained no mapping for this key.
     *
     * @param   K key
     * @return  V the previous value associated with the key
     */
    #[@generic(params= 'K', return= 'V')]
    public function remove($key) {
      $h= $key instanceof Generic ? $key->hashCode() : $key;
      if (!isset($this->_buckets[$h])) {
        $prev= NULL;
      } else {
        $prev= $this->_buckets[$h][1];
        $this->_hash-= HashProvider::hashOf($h.($prev instanceof Generic ? $prev->hashCode() : $prev));
        unset($this->_buckets[$h]);
      }

      return $prev;
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
     * @param   K key
     * @return  bool
     */
    #[@generic(params= 'K')]
    public function containsKey($key) {
      $h= $key instanceof Generic ? $key->hashCode() : $key;
      return isset($this->_buckets[$h]);
    }

    /**
     * Returns true if this map maps one or more keys to the specified value. 
     *
     * @param   V value
     * @return  bool
     */
    #[@generic(params= 'V')]
    public function containsValue($value) {
      if ($value instanceof Generic) {
        foreach (array_keys($this->_buckets) as $key) {
          if ($value->equals($this->_buckets[$key][1])) return TRUE;
        }
      } else {
        foreach (array_keys($this->_buckets) as $key) {
          if ($value === $this->_buckets[$key][1]) return TRUE;
        }
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
        $cmp instanceof self && 
        $this->__generic === $cmp->__generic &&
        $this->_hash === $cmp->_hash
      );
    }
    
    /**
     * Returns an array of keys
     *
     * @return  K[]
     */
    #[@generic(return= 'K[]')]
    public function keys() {
      $keys= array();
      foreach (array_keys($this->_buckets) as $key) {
        $keys[]= $this->_buckets[$key][0];
      }
      return $keys;
    }

    /**
     * Returns an array of values
     *
     * @return  V[]
     */
    #[@generic(return= 'V[]')]
    public function values() {
      $values= array();
      foreach (array_keys($this->_buckets) as $key) {
        $values[]= $this->_buckets[$key][1];
      }
      return $values;
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
        $s.= '  '.xp::stringOf($this->_buckets[$key][0]).' => '.xp::stringOf($this->_buckets[$key][1]).",\n";
      }
      return substr($s, 0, -2)."\n}";
    }

  } 
?>
