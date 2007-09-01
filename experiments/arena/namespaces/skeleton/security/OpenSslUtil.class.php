<?php
/* This class is part of the XP framework
 *
 * $Id: OpenSslUtil.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace security;

  /**
   * OpenSSL utility functions
   *
   * @ext      openssl
   * @purpose  Utiltiy functions
   */
  class OpenSslUtil extends lang::Object {
  
    /**
     * Retrieve errors
     *
     * @return  string[] error
     */
    public static function getErrors() {
      $e= array();
      while ($msg= openssl_error_string()) {
        $e[]= $msg;
      }
      return $e;
    }
    
    /**
     * Get OpenSSL configuration file environment value
     *
     * @return  string
     */
    public function getConfiguration() {
      return getenv('OPENSSL_CONF');
    }
  }
?>
