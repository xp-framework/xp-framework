<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.crypto.CryptoException');

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
   *   // Use extended DES-based encryption with a nine character salt
   *   $extdes= UnixCrypt::crypt('plain', '_012345678');
   * </code>
   *
   * Usage: Verifying an entered password
   * <code>
   *   $verified= UnixCrypt::matches($crypted, $entered);
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.security.UnixCryptTest
   * @see      php://crypt
   * @purpose  One-way string encryption (hashing)
   */
  class UnixCrypt extends Object {
    protected static $md5impl= NULL;

    static function __static() {

      // In PHP Bug #55439, crypt() returns just the salt for MD5. This bug 
      // first occurred in PHP 5.3.7 RC6 and was shipped with PHP 5.3.7, and
      // fixed in the release thereafter.
      if (0 === strpos(PHP_VERSION, '5.3.7')) {
        if ('$1$' === crypt('', '$1$')) {
          self::$md5impl= XPClass::forName('security.crypto.MD5CryptImpl')->newInstance();
        }
      }
    }
  
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
    public static function crypt($original, $salt= NULL) {
      if (self::$md5impl && 0 === strpos($salt, '$1$')) {
        return self::$md5impl->crypt($original, $salt);
      } else {
        $crypted= crypt($original, $salt);
        if (strlen($crypted) < 13) {
          throw new CryptoException('Failed to crypt: '.$crypted);
        }
        return $crypted;
      }
    }
    
    /**
     * Check if an entered string matches the crypt
     *
     * @param   string encrypted
     * @param   string entered
     * @return  bool
     */
    public static function matches($encrypted, $entered) {
      return ($encrypted === self::crypt($entered, $encrypted));
    }
  }
?>
