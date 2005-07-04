<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Base class for XML documents
   *
   * @access public
   */
  class XML extends Object {
    var 
      $version   = '1.0',
      $_encoding = 'iso-8859-1';
    
    /**
     * Set encoding
     *
     * @access  public
     * @param   string e encoding
     */
    function setEncoding($e) {
      $this->_encoding= $e;
    }
    
    /**
     * Retrieve encoding
     *
     * @access  public
     * @return  string encoding
     */
    function getEncoding() {
      return $this->_encoding;
    }
    
    /**
     * Returns XML declaration
     *
     * @access  public
     * @return  string declaration
     */
    function getDeclaration() {
      return sprintf(
        '<?xml version="%s" encoding="%s"?>',
        $this->version,
        $this->getEncoding()
      );
    }
  }
?>
