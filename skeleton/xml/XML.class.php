<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Base class for XML documents
   *
   * @deprecated
   */
  class XML extends Object {
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
