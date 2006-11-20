<?php
/*
 *
 * $Id:$
 */

  uses(
    'lang.IllegalArgumentException',
    'org.dia.DiaCompound',
    'org.dia.DiaConnection'
  );

  /**
   * Represents a 'dia:connections' node
   */
  class DiaConnections extends DiaCompound {

    var
      $node_name= 'dia:connections';

    /**
     * Constructor: simply calls 'initialize()'
     * 
     * @access  public
     */
    function __construct() {
      $this->initialize();
    }

    /**
     * Initializes the connetions object with default values
     *
     * @access  public
     */
    function initialize() {
      // TODO: Implements only has ONE connection point (begin)
      //$this->set('begin', new DiaConnection(0));
      //$this->set('end', new DiaConnection(1));
    }

    /**
     * Returns the DiaConnection object with the specified handle
     *
     * @access  public
     * @param   int handle
     * @return  &org.dia.DiaConnection
     */
    function &getConnection($handle) {
      return $this->getChild($handle);
    }

    /**
     * Adds a DiaConnection child
     *
     * @access  public
     * @param   &org.dia.DiaConnection Conn
     */
    #[@fromDia(xpath= 'child::dia:connection', class= 'org.dia.DiaConnection')]
    function addConnection($Conn) {
      $this->set($Conn->getHandle(), $Conn);
    }
  }
?>
