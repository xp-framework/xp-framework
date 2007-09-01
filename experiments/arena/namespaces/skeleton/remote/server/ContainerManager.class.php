<?php
/* This class is part of the XP framework
 *
 * $Id: ContainerManager.class.php 9151 2007-01-07 13:52:49Z kiesel $ 
 */

  namespace remote::server;

  /**
   * Container manager
   *
   * @purpose  Manage beancontainer
   */
  class ContainerManager extends lang::Object {
    public
      $containers= array();
    
    /**
     * Constructor
     *
     */
    public function __construct() { }
    
    /**
     * Register
     *
     * @param   remote.server.BeanContainer container
     * @return  int
     */
    public function register($container) {
      $this->containers[]= $container;
      $container->setContainerID(sizeof($this->containers)- 1);
      return sizeof($this->containers)- 1;
    }
    
    /**
     * Get a  beancontainer
     *
     * @param   int oid
     * @return mixed
     */
    public function getContainerByOID($oid) {

      // TDB
    }
  }
?>
