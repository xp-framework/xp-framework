<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * OpenSSL utility functions
   *
   * @ext      openssl
   * @purpose  Utiltiy functions
   */
  class OpenSslUtil extends Object {
  
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
