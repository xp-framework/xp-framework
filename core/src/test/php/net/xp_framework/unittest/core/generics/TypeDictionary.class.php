<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  uses('net.xp_framework.unittest.core.generics.AbstractTypeDictionary');

  /**
   * Lookup map
   *
   */
  #[@generic(self= 'V', parent= 'V')]
  class net·xp_framework·unittest·core·generics·TypeDictionary extends net·xp_framework·unittest·core·generics·AbstractTypeDictionary {
    protected $elements= array();
    
    /**
     * Put a key/value pair
     *
     * @param   lang.Type key
     * @param   V value
     */
    #[@generic(params= 'lang.Type, V')]
    public function put($key, $value) {
      $offset= $key->literal();
      $this->elements[$offset]= $value;
    } 

    /**
     * Returns a value associated with a given key
     *
     * @param   lang.Type key
     * @return  V value
     * @throws  util.NoSuchElementException
     */
    #[@generic(params= 'lang.Type', return= 'V')]
    public function get($key) {
      $offset= $key->literal();
      if (!isset($this->elements[$offset])) {
        throw new NoSuchElementException('No such key '.xp::stringOf($key));
      }
      return $this->elements[$offset];
    }

    /**
     * Returns all values
     *
     * @return  V[] values
     */
    #[@generic(return= 'V[]')]
    public function values() {
      return array_values($this->elements);
    }
  }
?>
