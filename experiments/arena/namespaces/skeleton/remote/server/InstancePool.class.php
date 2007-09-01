<?php
/* This class is part of the XP framework
 *
 * $Id: InstancePool.class.php 9151 2007-01-07 13:52:49Z kiesel $ 
 */

  namespace remote::server;

  uses('util.Hashmap');

  /**
   * Instance pool
   *
   * @purpose  Hold instances of deployed beans
   */
  class InstancePool extends lang::Object {
    public
      $_pool    = NULL,
      $_h2id    = NULL;

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->_pool= new util::Hashmap();
    }
      
    /**
     * Register a new instance
     *
     * @param   lang.Object object
     * @return  bool
     */
    public function registerInstance($object) {
      $this->_pool->putref($object->hashCode(), $object);
      return TRUE;
    }
    
    /**
     * Fetch
     *
     * @param   string hashcode
     * @return  mixed
     */
    public function fetch($hashCode) {
      return $this->_pool->get($hashCode);
    }    
  }
?>
