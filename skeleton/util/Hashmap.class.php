<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
   
  /**
   * Hashmap class
   *
   * Example usage:
   * <code>
   *   $h= &new Hashmap();
   *   $h->put('color', 'red');
   *   $h->put('count', 5);
   *   if ($h->containsKey('color')) {
   *     printf(
   *       "Hashmap contains key 'color' with value '%s'", 
   *       $h->get('color')
   *     );
   *   }
   *   if ($h->containsValue(5)) {
   *     printf("Hashmap contains the value 5");
   *   }
   * </code>
   *
   * @see php-doc://array
   */
  class Hashmap extends Object {
    var $_hash= array();

    /**
     * Constructor
     *
     * @access  public
     * @param   map default NULL an array
     */
    function __construct($map= NULL) {
      if (is_array($map)) $this->_hash= $map;
      parent::__construct();
    }
    
    /**
     * Destructor. Frees all elements
     *
     * @access  public
     */
    function __destruct() {
      unset($this->_hash);
      parent::__destruct();
    }

    /**
     * Returns a shallow copy of this hashmap. Values are *not*
     * cloned
     *
     * @access  public
     * @return  util.Hashmap copy 
     */
    function &clone() {
      return new Hashmap($this->_hash);
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
     * @access  public
     * @param   int flags default SORT_REGULAR sort flags
     */
    function sort($flags= SORT_REGULAR) {
      asort($this->_hash, $flags);
    }
    
    /**
     * Sort this hashmap in reverse order
     *
     * @access  public
     * @param   int flags default SORT_REGULAR sort flags
     * @see     util.Hashmap#sort
     */
    function rsort($flags= SORT_REGULAR) {
      arsort($this->_hash, $flags);
    }

    /**
     * Sort this hashmap using a user-defined callback function
     * for comparing these values.
     *
     * The parameter function may contain either a globally defined
     * function, a dynamically created one or the special 
     * array(&$obj, 'method') notation
     *
     * @access  public
     * @param   mixed comparator an existing function or method
     * @see     php-doc://create_function
     */
    function usort($comparator) {
      ursort($this->_hash, $comparator);
    }
    
    /**
     * Filters elements from this hashmap using a callback
     * function
     *
     * @access  public 
     * @param   mixed function an existing function or method
     */
    function filter($function) {
      $this->_hash= array_filter($this->_hash, $function);
    }
    
    /**
     * Swaps the values with the specified keys. If one of the
     * keys does not exist, the function returns FALSE
     *
     * @access  public
     * @param   scalar k
     * @param   scalar j
     * @return  bool success
     */
    function swap($k, $j) {
      if (!isset($this->_hash[$k]) or !isset($this->_hash[$j])) {
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
     * @access  public
     * @return  bool all keys/values have been flipped
     */
    function flip() {
      try(); {
        $this->_hash= array_flip($this->_hash);
        if (is_error()) throw(new FormatException('hash contains values which are not scalar'));
      } if (catch('FormatException', $e)) {
        return FALSE;
      }
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
     * @access  public
     * @param   &mixed map an array or another Hashmap
     * @param   bool recursive default FALSE Merge hashmaps recursively
     * @throws  IllegalArgumentException in case the parameter is neither an array nor a Hashmap
     */
    function merge(&$map, $recursive= FALSE) {
      if (is_a($map, 'Hashmap')) {
        $h= &$map->_hash;
      } elseif (is_array($map)) {
        $h= &$map;
      } else {
        return throw(new IllegalArgumentException('map is neither an array nor a Hashmap'));
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
     * @access  public
     */
    function clear() {
      $this->_hash= array();
    }

    /**
     * Check whether the given key exists in this hashmap
     *
     * @access  public
     * @param   scalar key key to check for
     * @return  bool key exists
     */
    function containsKey($key) {
      return isset($this->_hash[$key]);
    }

    /**
     * Check whether the given value exists in this hashmap. With strict
     * checking off (which is the default), the type won't be checked,
     * i.e. a value of 0 is also found when searching for FALSE.
     *
     * @access public
     * @param  &mixed val
     * @param  bool strict default FALSE use strict checking.
     * @return mixed value TRUE if value exists, its key otherwise
     */     
    function containsValue(&$val, $strict= FALSE) {
      return array_search($val, $this->_hash, $strict);
    }
    
    /**
     * Put a value into this hashmap by reference.
     *
     * @access  public
     * @param   scalar key
     * @param   &mixed value
     */
    function putref($key, &$value) {
      $this->_hash[$key]= &$value;
    }

    /**
     * Put a value into this hashmap
     *
     * @access  public
     * @param   scalar key
     * @param   mixed value
     */
    function put($key, $value) {
      $this->_hash[$key]= $value;
    }

    /**
     * Retreive a value by its key. Returns NULL if there is no such
     * key
     *
     * @access  public
     * @param   scalar key
     * @return  &mixed value
     */
    function &get($key) {
      return isset($this->_hash[$key]) ? $this->_hash[$key] : NULL;
    }

    /**
     * Remove a value from the hashmap
     *
     * @access  public
     * @param   scalar key
     */
    function remove($key) {
      unset($this->_hash[$key]);
    }

    /**
     * Returns size of the hashmap
     *
     * @access  public
     * @param   int size
     */
    function size() {
      return sizeof(array_keys($this->_hash));
    }

    /**
     * Checks whether the hashmap is empty (in other words: contains
     * no elements)
     *
     * @access  public
     * @return  bool empty TRUE if the hashmap is empty, FALSE otherwise
     */
    function isEmpty() {
      return (0 == $this->size());
    }

    /**
     * Returns all keys in this hashmap.
     *
     * @access  public
     * @return  &scalar[] keys
     */
    function &keys() {
      return array_keys($this->_hash);
    }

    /**
     * Returns all values in this hashmap
     *
     * @access  public
     * @return  &mixed[] values
     */
    function &values() {
      return array_values($this->_hash);
    }
    
    /**
     * Create string representation
     * 
     * Example:
     * <pre>
     *   util.Hashmap {
     *     'key'  => 'value',
     *     'key2' => 'value2',
     *   } 
     * </pre>
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getName()." {\n".substr(var_export($this->_hash, 1), 8, -2)."\n}";
    }
  }
?>
