<?php
/* This class is part of the XP framework
 *
 * $Id: UnixCrypt.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace security::crypto;

  /**
   * Unix crypt algorithm implementation. Note:  There is no decrypt 
   * function, since crypt() uses a one-way algorithm.
   *
   * Usage: Generating a crypted password
   * <code>
   *   // Use system default, generate a salt
   *   $default= UnixCrypt::crypt('plain');
   *
   *   // Use traditional
   *   $traditional= UnixCrypt::crypt('plain', 'ab');
   *
   *   // Use MD5 encryption with 12 character salt
   *   $md5= UnixCrypt::crypt('plain', '$1$0123456789AB');
   *
   *   // Use blowfish encryption with 16 character salt
   *   $blowfish= UnixCrypt::crypt('plain', '$2$0123456789ABCDEF');
   *
   *   // Use standard DES-based encryption with a two character salt
   *   $des= UnixCrypt::crypt('plain', '_01');
   *
   *   // Use extended DES-based encryption with a nine character salt
   *   $extdes= UnixCrypt::crypt('plain', '_012345678');
   * </code>
   *
   * Usage: Verifying an entered password
   * <code>
   *   $verified= UnixCrypt::matches($crypted, $entered);
   * </code>
   *
   * @see      php://crypt
   * @purpose  One-way string encryption (hashing)
   */
  class UnixCrypt extends lang::Object {
  
    /**
     * Encrypt a string
     *
     * The salt may be in one of three forms (from man 3 crypt):
     *
     * <pre>
     * Extended
     * --------
     * If it begins with an underscore (``_'') then the DES Extended 
     * Format is used in interpreting both the key and the salt, as 
     * outlined below.
     *
     * Modular 
     * -------     
     * If it begins with the string ``$digit$'' then the Modular Crypt 
     * Format is used, as outlined below.
     *
     * Traditional
     * -----------
     * If neither of the above is true, it assumes the Traditional 
     * Format, using the entire string as the salt (or the first portion).
     * </pre>
     *
     * If ommitted, the salt is generated and the system default is used.
     *
     * @param   string original
     * @param   string salt default NULL
     * @return  string crypted
     */
    public static function crypt($original, $salt= ) {
      return crypt($original, $salt);
    }
    
    /**
     * Check if an entered string matches the crypt
     *
     * @param   string encrypted
     * @param   string entered
     * @return  bool
     */
    public static function matches($encrypted, $entered) {
      return ($encrypted == crypt($entered, $encrypted));
    }
  }
?>
