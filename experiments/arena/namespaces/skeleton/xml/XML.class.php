<?php
/* This class is part of the XP framework
 *
 * $Id: XML.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace xml;

  /**
   * Base class for XML documents
   *
   */
  class XML extends lang::Object {
    public 
      $version   = '1.0',
      $_encoding = 'iso-8859-1';
    
    /**
     * Set encoding
     *
     * @param   string e encoding
     */
    public function setEncoding($e) {
      $this->_encoding= $e;
    }
    
    /**
     * Retrieve encoding
     *
     * @return  string encoding
     */
    public function getEncoding() {
      return $this->_encoding;
    }
    
    /**
     * Returns XML declaration
     *
     * @return  string declaration
     */
    public function getDeclaration() {
      return sprintf(
        '<?xml version="%s" encoding="%s"?>',
        $this->version,
        $this->getEncoding()
      );
    }
  }
?>
