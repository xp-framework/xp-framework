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

    public
      $node_name= 'dia:connections';

    /**
     * Constructor: simply calls 'initialize()'
     * 
     * @access  public
     */
    public function __construct() {
      $this->initialize();
    }

    /**
     * Initializes the connetions object with default values
     *
     * @access  public
     */
    public function initialize() {
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
    public function &getConnection($handle) {
      return $this->getChild($handle);
    }

    /**
     * Adds a DiaConnection child
     *
     * @access  public
     * @param   &org.dia.DiaConnection Conn
     */
    #[@fromDia(xpath= 'child::dia:connection', class= 'org.dia.DiaConnection')]
    public function addConnection($Conn) {
      $this->set($Conn->getHandle(), $Conn);
    }
  }
?>
