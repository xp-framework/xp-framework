<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  if (!defined('XML_ENCODING_DEFAULT')) define('XML_ENCODING_DEFAULT',        'iso-8859-1');
  if (!defined('XML_DECLARATION'))      define('XML_DECLARATION',             '<?xml version="1.0" encoding="'.XML_ENCODING_DEFAULT.'" ?>');

  /**
   * Base class for
   *
   * @access public
   */
  class XML extends Object {
    protected
      $_encoding = 'iso-8859-1';
    public
      $version   = '1.0';
    
    /**
     * Set encoding
     *
     * @access  public
     * @param   string e encoding
     */
    public function setEncoding($e) {
      $this->_encoding= $e;
    }
    
    /**
     * Retrieve encoding
     *
     * @access  public
     * @return  string encoding
     */
    public function getEncoding() {
      return $this->_encoding;
    }
    
    /**
     * Returns XML declaration
     *
     * @access  public
     * @return  string declaration
     */
    public function getDeclaration() {
      return sprintf(
        '<?xml version="%s" encoding="%s"?>',
        $this->version,
        self::getEncoding()
      );
    }
  }
?>
