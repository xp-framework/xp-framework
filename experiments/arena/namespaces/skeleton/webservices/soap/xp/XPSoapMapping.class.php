<?php
/* This class is part of the XP framework
 *
 * $Id: XPSoapMapping.class.php 10015 2007-04-16 16:36:48Z kiesel $ 
 */

  namespace webservices::soap::xp;

  uses('xml.QName');

  /**
   * Provide a mapping between qnames and XP
   * classes for SOAPClients.
   *
   * @see      xp://webservices.soap.xp.XPSoapClient
   * @purpose  Mapping for QNames
   */
  class XPSoapMapping extends lang::Object {
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
    public function registerMapping(xml::QName $qname, lang::XPClass $class) {
      $this->_classes[$class->getName()]= $class;
      $this->_qnames[$qname->toString()]= $qname;
      $this->_q2c[$qname->toString()]= $class->getName();
      $this->_c2q[$class->getName()]= $qname->toString();
    }

    /**
     * Fetch a qname for a class.
     *
     * @param   lang.XPClass class
     * @return  mixed xml.QName or NULL if no mapping exists
     */
    public function qnameFor(lang::XPClass $class) {
      if (!isset($this->_c2q[$class->getName()])) return NULL;
      return $this->_qnames[$this->_c2q[$class->getName()]];
    }
    
    /**
     * Fetch a class for a qname
     *
     * @param   xml.QName qname
     * @return  mixed lang.XPClass or NULL if no mapping exists
     */
    public function classFor(xml::QName $qname) {
      if (!isset($this->_q2c[$qname->toString()])) return NULL;
      return $this->_classes[$this->_q2c[$qname->toString()]];
    }
  }
?>
