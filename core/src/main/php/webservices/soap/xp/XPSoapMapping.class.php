<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.QName');

  /**
   * Provide a mapping between qnames and XP
   * classes for SOAPClients.
   *
   * @see      xp://webservices.soap.xp.XPSoapClient
   * @purpose  Mapping for QNames
   */
  class XPSoapMapping extends Object {
    public
      $_classes     = array(),
      $_qnames      = array(),
      $_q2c         = array(),
      $_c2q         = array();
      
    /**
     * Register a new mapping.
     *
     * @param   xml.QName qname
     * @param   lang.XPClass class
     * @throws  lang.IllegalArgumentException
     */
    public function registerMapping(QName $qname, XPClass $class) {
      $this->_classes[$class->getName()]= $class;
      $this->_qnames[$qname->toString()]= $qname;
      $this->_q2c[$qname->toString()]= $class->getName();
      $this->_c2q[$class->getName()]= $qname->toString();
    }

    /**
     * Fetch a qname for a class.
     *
     * @param   lang.XPClass class
     * @return  var xml.QName or NULL if no mapping exists
     */
    public function qnameFor(XPClass $class) {
      if (!isset($this->_c2q[$class->getName()])) return NULL;
      return $this->_qnames[$this->_c2q[$class->getName()]];
    }
    
    /**
     * Fetch a class for a qname
     *
     * @param   xml.QName qname
     * @return  var lang.XPClass or NULL if no mapping exists
     */
    public function classFor(QName $qname) {
      if (!isset($this->_q2c[$qname->toString()])) return NULL;
      return $this->_classes[$this->_q2c[$qname->toString()]];
    }
  }
?>
