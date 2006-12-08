<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Container manager
   *
   * @purpose  Manage beancontainer
   */
  class ContainerManager extends Object {
    var
      $containers= array();
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() { }
    
    /**
     * Register
     *
     * @access  public
     * @param   &remote.server.BeanContainer container
     * @return  int
     */
    function register(&$container) {
      $this->containers[]= &$container;
      $container->setContainerID(sizeof($this->containers)- 1);
      return sizeof($this->containers)- 1;
    }
    
    /**
     * Get a  beancontainer
     *
     * @access  public
     * @param   int oid
     * @return mixed
     */
    function getContainerByOID($oid) {

      // TDB
    }
  }
?>
