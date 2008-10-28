<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaUMLConnection');

  class DiaUMLImplements extends DiaUMLConnection {
    
    /**
     * Constructor of an UML realization
     *
     */
    public function __construct() {
      parent::__construct('UML - Implements', 0);
    }

    /**
     * Initialize this UMLImplementation with default values
     *
     */
    public function initialize() {
      // default values
      $this->setText('');
      $this->setTextPosition(array(0, 0));
      $this->setDiameter(0.7);
      
      // add essencial nodes
      $this->set('conn_endpoints', new DiaAttribute('conn_endpoints'));
      $this->set('connections', new DiaConnections());

      // positioning information defaults
      $this->setPosition(array(0, 0));
      $this->setBoundingBox(array(array(0, 0), array(1, 1)));

      // default colors
      $this->setLineColor('#000000');
      $this->setTextColor('#000000');
    }

    /**
     * Returns the text of the connection (interface being implemented)
     * 
     * @return  string
     */
    public function getText() {
      return $this->getChildValue('text');
    }

    /** 
     * Sets the text for the Implements connection
     *
     * @param   string text
     */
    #[@fromDia(xpath= 'dia:attribute[@name="text"]/dia:string', value= 'string')]
    public function setText($text) {
      $this->setString('text', $text);
    }

    /**
     * Returns the point of the text position
     *
     * @return  array
     */
    public function getTextPosition() {
      return $this->getChildValue('text_pos');
    }

    /**
     * Sets the text position point
     *
     * @param   array point
     */
    #[@fromDia(xpath= 'dia:attribute[@name="text_pos"]/dia:point/@val', value= 'array')]
    public function setTextPosition($point) {
      $this->setPoint('text_pos', $point);
    }

    /**
     * Returns the diameter of the circle at the end of the connection
     *
     * @return  float
     */
    public function getDiameter() {
      return $this->getChildValue('diameter');
    }

    /**
     * Sets the diameter of the Implements circle
     *
     * @param   float diameter
     */
    #[@fromDia(xpath= 'dia:attribute[@name="diameter"]/dia:real/@val', value= 'real')]
    public function setDiameter($diameter) {
      $this->setReal('diameter', $diameter);
    }

    /**
     * Returns the two endpoints of the connection
     *
     * @return  array[]
     */
    public function getEndPoints() {
      $Conns= $this->getChild('conn_endpoints');
      return $Conns->getChildren();
    }

    /**
     * Adds an endpoint to the Implements connection
     *
     * @param   array point
     */
    #[@fromDia(xpath= 'dia:attribute[@name="conn_endpoints"]/dia:point/@val', value= 'array')]
    public function addEndPoint($point) {
      $Conns= $this->getChild('conn_endpoints');
      $Conns->addChild(new DiaPoint($point));
    }
    
    /**
     * Returns the connection to the object
     *
     * @return  &org.dia.DiaConnection
     */
    public function getConnection() {
      $Conns= $this->getChild('connections');
      return $Conns->getChild('connection');
    }

    /**
     * Sets the connection of the Implements line
     *
     * @param   &org.dia.DiaConnection Conn
     */
    #[@fromDia(xpath= 'dia:attribute[@name="connetions"]/dia:connection', class= 'org.dia.DiaConnection')]
    public function setConnection($Conn) {
      $Conns= $this->getChild('connections');
      $Conns->set('connection', $Conn);
    }

    /**
     * Set the ID and connection point of the object where the line begins
     *
     * @param   string id The diagram object ID
     * @param   int connpoint default 0 The connection point of the object
     */
    public function beginAt($id, $connpoint= 0) {
      $Conns= $this->getChild('connections');
      $Conns->set('connection', new DiaConnection("0, $id, $connpoint"));
    }

    /**
     * Overwrite parent method and disable it!
     *
     * @param   string id The diagram object ID
     * @param   int connpoint default 5 The connection point of the object
     */
    public function endAt($id, $connpoint= 5) { }
  }
?>
