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
    var $_encoding= 'iso-8859-1';
    
    /**
     * Returns XML declaration
     *
     * @access  public
     * @return  string declaration
     */
    function getDeclaration() {
      return '<?xml version="1.0" encoding="'.$this->_encoding.'" ?>';
    }
  }
?>
