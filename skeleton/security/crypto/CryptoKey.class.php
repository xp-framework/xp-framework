<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.crypto.CryptoException');

  /**
   * Cryptographic Key base class.
   *
   * @ext      openssl
   * @see      http://openssl.org
   * @purpose  Crypto key base
   */
  class CryptoKey extends Object {
    var
      $_hdl = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   resource handle
     */
    function __construct($handle) {
      $this->_hdl= $handle;
    }
    
    /**
     * Retrieves the handle for the key.
     *
     * @access  public
     * @return  resource
     */
    function getHandle() {
      return $this->_hdl;
    }    
    
    /**
     * Create a key from its string representation
     *
     * @model   abstract
     * @access  public
     * @param   string string
     * @return  &security.crypto.CryptoKey
     */
    function &fromString($string) { }
    
    /**
     * Encrypt data using this key
     *
     * @model   abstract
     * @access  public
     * @param   string data
     * @return  string
     */
    function encrypt($data) { }
    
    /**
     * Decrypt data using this key
     *
     * @model   abstract
     * @access  public
     * @param   string data
     * @return  string 
     */
    function decrypt($data) { }    
      
  }
?>
