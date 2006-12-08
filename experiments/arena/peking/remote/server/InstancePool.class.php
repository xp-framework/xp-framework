<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Hashmap');

  /**
   * Instance pool
   *
   * @purpose  Hold instances of deployed beans
   */
  class InstancePool extends Object {
    var
      $_pool    = NULL,
      $_h2id    = NULL;

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->_pool= &new Hashmap();
    }
      
    /**
     * Register a new instance
     *
     * @access  public
     * @param   &lang.Object object
     * @return  bool
     */
    function registerInstance(&$object) {
      $this->_pool->putref($object->hashCode(), $object);
      return TRUE;
    }
    
    /**
     * Fetch
     *
     * @access  public
     * @param   string hashcode
     * @return  mixed
     */
    function fetch($hashCode) {
      return $this->_pool->get($hashCode);
    }    
  }
?>
