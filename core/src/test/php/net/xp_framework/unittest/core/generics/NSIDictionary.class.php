<?php namespace net\xp_framework\unittest\core\generics;

/**
 * Lookup map
 *
 */
#[@generic(self= 'K, V')]
interface NSIDictionary {
 
  /**
   * Put a key/value pair
   *
   * @param   K key
   * @param   V value
   */
  #[@generic(params= 'K, V')]
  public function put($key, $value);

  /**
   * Returns a value associated with a given key
   *
   * @param   K key
   * @return  V value
   * @throws  util.NoSuchElementException
   */
  #[@generic(params= 'K', return= 'V')]
  public function get($key);

  /**
   * Returns all values
   *
   * @return  V[] values
   */
  #[@generic(return= 'V[]')]
  public function values();
}
