<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Unix crypt algorithm implementation. Note:  There is no decrypt 
   * function, since crypt() uses a one-way algorithm.
   *
   * Usage: Generating a crypted password
   * <code>
   *   $password= UnixCrypt::crypt('plain');
   * </code>
   *
   * Usage: Verifying an entered password
   * <code>
   *   $verified= UnixCrypt::matches($crypted, $entered);
   * </code>
   *
   * @model    static
   * @see      php://crypt
   * @purpose  One-way string encryption (hashing)
   */
  class UnixCrypt extends Object {
  
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
     * @model   static
     * @access  public
     * @param   string original
     * @param   string salt default NULL
     * @return  string crypted
     */
    function crypt($original, $salt= NULL) {
      return crypt($original, $salt);
    }
    
    /**
     * Check if an entered string matches the crypt
     *
     * @model   static
     * @access  public
     * @param   string encrypted
     * @param   string entered
     * @return  bool
     */
    function matches($encrypted, $entered) {
      return ($encrypted == crypt($entered, $encrypted));
    }
  }
?>
