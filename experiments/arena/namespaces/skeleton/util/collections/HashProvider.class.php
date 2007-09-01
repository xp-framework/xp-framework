<?php
/* This class is part of the XP framework
 *
 * $Id: HashProvider.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace util::collections;

  ::uses('util.collections.MD5HashImplementation');

  /**
   * Provides hashing functionality for maps.
   * 
   * Basic usage:
   * <code>
   *   $hashCode= HashProvider::hashOf($string);
   * </code>
   *
   * Uses MD5 as default hashing implementation. To change the hashing
   * implementation to be used, use the following:
   * <code>
   *   HashProvider::getInstance()->setImplementation(new MyHashImplementation());
   * </code>
   * 
   * @see      xp://util.collections.DJBX33AHashImplementation
   * @see      xp://util.collections.MD5HashImplementation
   * @see      xp://util.collections.Map
   * @purpose  Hashing
   */
  class HashProvider extends lang::Object {
    protected static
      $instance = NULL;

    public
      $impl     = NULL;

    static function __static() {
      self::$instance= new self();
      self::$instance->setImplementation(new MD5HashImplementation());
    }

    /**
     * Constructor
     *
     */
    protected function __construct() {
    }
    
    /**
     * Retrieve sole instance of this object
     *
     * @return  util.collections.HashProvider
     */
    public static function getInstance() {
      return self::$instance;
    }
    
    /**
     * Returns hash for a given string
     *
     * @param   string str
     * @return  int
     */
    public static function hashOf($str) {
      return self::getInstance()->impl->hashOf($str);
    }

    /**
     * Set hashing implementation
     * 
     * @param   util.collections.HashImplementation impl
     * @throws  lang.IllegalArgumentException when impl is not a HashImplementation
     */
    public function setImplementation($impl) {
      if (!::is('util.collections.HashImplementation', $impl)) {
        throw(new lang::IllegalArgumentException(
          'Implementation is not a HashImplementation, '.::xp::typeOf($impl).' given'
        ));
      }
      $this->impl= $impl;
    }

    /**
     * Get hashing implementation
     * 
     * @return  util.collections.HashImplementation
     */
    public function getImplementation() {
      return $this->impl;
    }
  }
?>
