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
   * @see      xp://webservices.soap.SOAPClient
   * @purpose  Mapping for QNames
   */
  class SOAPMapping extends Object {
    public
      $_classes     = array(),
      $_qnames      = array(),
      $_q2c         = array(),
      $_c2q         = array();
      
    /**
     * Register a new mapping.
     *
     * @access  public
     * @param   &xml.QName qname
     * @param   &lang.XPClass class
     * @throws  lang.IllegalArgumentException
     */
    public function registerMapping(&$qname, &$class) {
      if (!is('xml.QName', $qname)) {
        throw(new IllegalArgumentException(
          'Argument class is not an xml.QName (given: '.xp::typeOf($qname).')'
        ));
      }
      
      if (!is('lang.XPClass', $class)) {
        throw(new IllegalArgumentException(
          'Argument class is not an XPClass (given: '.xp::typeOf($class).')'
        ));
      }
      
      $this->_classes[$class->getName()]= &$class;
      $this->_qnames[$qname->toString()]= &$qname;
      $this->_q2c[$qname->toString()]= $class->getName();
      $this->_c2q[$class->getName()]= $qname->toString();
    }

    /**
     * Fetch a qname for a class.
     *
     * @access  public
     * @param   &lang.XPClass class
     * @return  &mixed xml.QName or NULL if no mapping exists
     */
    public function &qnameFor(&$class) {
      if (!is('lang.XPClass', $class) || !isset($this->_c2q[$class->getName()])) return NULL;
      return $this->_qnames[$this->_c2q[$class->getName()]];
    }
    
    /**
     * Fetch a class for a qname
     *
     * @access  public
     * @param   &xml.QName qname
     * @return  &mixed lang.XPClass or NULL if no mapping exists
     */
    public function &classFor(&$qname) {
      if (!is('xml.QName', $qname) || !isset($this->_q2c[$qname->toString()])) return NULL;
      return $this->_classes[$this->_q2c[$qname->toString()]];
    }
  }
?>
