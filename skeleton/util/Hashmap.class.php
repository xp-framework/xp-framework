<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.HashmapIterator');
   
  /**
   * Hashmap class
   *
   * Example usage:
   * <code>
   *   uses('util.Hashmap');
   *
   *   $h= new Hashmap();
   *   $h->put('color', 'red');
   *   $h->put('count', 5);
   *   if ($h->containsKey('color')) {
   *     echo 'color => '; var_dump($h->get('color'));
   *   }
   *   var_dump($h->containsValue($c= 5));
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.util.HashmapTest
   * @see      php://array
   * @purpose  Associative array wrapper class
   */
  class Hashmap extends Object {
    public 
      $_hash= array();

    /**
     * Constructor
     *
     * @param   array<var, var> default NULL an array
     */
    public function __construct($map= NULL) {
      if (is_array($map)) $this->_hash= $map;
      
    }
    
    /**
     * Destructor. Frees all elements
     *
     */
    public function __destruct() {
      unset($this->_hash);
    }

    /**
     * Returns an iterator over the values of this hashmap
     *
     * @return  util.HashmapIterator
     * @see     xp://util.HashmapIterator
     */
    public function iterator() {
      return new HashmapIterator($this->_hash);
    }
        
    /**
     * Returns an iterator over the keys of this hashmap
     *
     * @return  util.HashmapIterator
     * @see     xp://util.HashmapIterator
     */
    public function keyIterator() {
      return new HashmapIterator(array_keys($this->_hash));
    }
        
    /**
     * Sort this hashmap
     *
     * The flags parameter may be used to influence the sorting
     * behaviour and may be one of the following constans
     * SORT_REGULAR - compare items normally
     * SORT_NUMERIC - compare items numerically
     * SORT_STRING  - compare items as strings
     *
     * @param   int flags default SORT_REGULAR sort flags
     */
    public function sort($flags= SORT_REGULAR) {
      asort($this->_hash, $flags);
    }

    /**
     * Sort this hashmap by key
     *
     * @param   int flags default SORT_REGULAR sort flags
     * @see     xp://util.Hashmap#sort
     */
    public function ksort($flags= SORT_REGULAR) {
      ksort($this->_hash, $flags);
    }
    
    /**
     * Sort this hashmap in reverse order
     *
     * @param   int flags default SORT_REGULAR sort flags
     * @see     xp://util.Hashmap#sort
     */
    public function rsort($flags= SORT_REGULAR) {
      arsort($this->_hash, $flags);
    }

    /**
     * Sort this hashmap using a comparator.
     *
     * @param  util.Comparator comparator an existing function or method
     */
    public function usort($comparator) {
      uasort($this->_hash, array($comparator, 'compare'));
    }
    
    /**
     * Filters elements from this hashmap using a callback
     * function
     *
     * @param   var function an existing function or method
     */
    public function filter($function) {
      $this->_hash= array_filter($this->_hash, $function);
    }
    
    /**
     * Swaps the values with the specified keys. If one of the
     * keys does not exist, the function returns FALSE
     *
     * @param   var k
     * @param   var j
     * @return  bool success
     */
    public function swap($k, $j) {
      if (!isset($this->_hash[$k]) || !isset($this->_hash[$j])) {
        return FALSE;
      }
      $t= $this->_hash[$k];
      $this->_hash[$k]= $this->_hash[$j];
      $this->_hash[$j]= $t;
      return TRUE;
    }
    
    /**
     * Flip keys and values. Note that the values may only consists
     * of scalar values, else the operation will fail (and no key/value
     * pair in question will *not* be flipped. If a value has several
     * occurrences, the latest key will be used as its value
     * 
     * Example:
     * <pre>
     *   before   after
     *   ------   -----
     *   a => b   b => b
     *   b => b   1 => c
     *   c => 1   
     * </pre>
     *
     * @return  bool all keys/values have been flipped
     * @throws  lang.FormatException in case this hash contains non-scalar values
     */
    public function flip() {
      $h= array_flip($this->_hash);
      if (xp::errorAt(__FILE__, __LINE__ - 1)) {
        throw new FormatException('hash contains values which are not scalar');
      }
      $this->_hash= $h;
      return TRUE;
    }
    
    /**
     * Merge this hashmap. There are two ways of doing this:
     *
     * 1. Non-recursive merge:
     * In case the given map contains identical keys the values from
     * the given hashmap for these keys will be discarded.
     * 
     * 2. Recursive merge:
     * In case the given map contains identical keys the values for
     * these keys are merged together into an array (recursively)
     *
     * @param  var map an array or another Hashmap
     * @param   bool recursive default FALSE Merge hashmaps recursively
     * @throws  lang.IllegalArgumentException in case the parameter is neither an array nor a Hashmap
     */
    public function merge($map, $recursive= FALSE) {
      if ($map instanceof Hashmap) {
        $h= $map->_hash;
      } else if (is_array($map)) {
        $h= $map;
      } else {
        throw new IllegalArgumentException('map is neither an array nor a Hashmap');
      }
      
      if ($recursive) {
        $this->_hash= array_merge_recursive($h, $this->_hash);
      } else {
        $this->_hash= array_merge($h, $this->_hash);
      }
    }
    
    /**
     * Clear the hashmap
     *
     */
    public function clear() {
      $this->_hash= array();
    }

    /**
     * Check whether the given key exists in this hashmap
     *
     * @param   var key key to check for
     * @return  bool key exists
     */
    public function containsKey($key) {
      return isset($this->_hash[$key]);
    }

    /**
     * Check whether the given value exists in this hashmap. With strict
     * checking off (which is the default), the type won't be checked,
     * i.e. a value of 0 is also found when searching for FALSE.
     *
     * @param  var val
     * @param  bool strict default FALSE use strict checking.
     * @return bool TRUE if value exists, FALSE otherwise
     */     
    public function containsValue($val, $strict= FALSE) {
      return in_array($val, $this->_hash, $strict);
    }
    
    /**
     * Put a value into this hashmap by reference.
     *
     * @param   var key
     * @param  var value
     */
    public function putref($key, $value) {
      $this->_hash[$key]= $value;
    }

    /**
     * Put a value into this hashmap
     *
     * @param   var key
     * @param   var value
     */
    public function put($key, $value) {
      $this->_hash[$key]= $value;
    }

    /**
     * Retrieve a value by its key. Returns NULL if there is no such key
     *
     * @param   var key
     * @return  var value
     */
    public function get($key) {
      if (isset($this->_hash[$key])) return $this->_hash[$key]; else return NULL;
    }

    /**
     * Remove a value from the hashmap
     *
     * @param   var key
     */
    public function remove($key) {
      unset($this->_hash[$key]);
    }

    /**
     * Returns size of the hashmap
     *
     * @return  int size
     */
    public function size() {
      return sizeof(array_keys($this->_hash));
    }

    /**
     * Checks whether the hashmap is empty (in other words: contains
     * no elements)
     *
     * @return  bool empty TRUE if the hashmap is empty, FALSE otherwise
     */
    public function isEmpty() {
      return (0 == $this->size());
    }

    /**
     * Returns all keys in this hashmap.
     *
     * @return  var[] keys
     */
    public function keys() {
      return array_keys($this->_hash);
    }

    /**
     * Returns all values in this hashmap
     *
     * @return  var[] values
     */
    public function values() {
      return array_values($this->_hash);
    }
    
    /**
     * Compares two hashmaps and returns TRUE when equal.
     *
     * @param   var object to compare with
     * @return  boolean
     */
    public function equals($cmp) {
      return is('util.Hashmap', $cmp) && ($this->_hash === $cmp->_hash);
    }
    
    /**
     * Returns an array representation of this hashmap
     *
     * @return  array<var, var>
     */
    public function toArray() {
      return $this->_hash;
    }
    
    /**
     * Create string representation
     * 
     * Example:
     * <pre>
     *   util.Hashmap@[2]{
     *     'key'  => 'value',
     *     'key2' => 'value2',
     *   } 
     * </pre>
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->size().')@'.xp::stringOf($this->_hash);
    }
  }
?>
