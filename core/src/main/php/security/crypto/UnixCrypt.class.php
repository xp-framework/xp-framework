<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'security.crypto.CryptoException',
    'security.crypto.NativeCryptImpl',
    'security.crypto.CryptNotImplemented'
  );

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
    public static $DEFAULT;
    public static $STANDARD;
    public static $EXTENDED;
    public static $BLOWFISH;
    public static $MD5;

    static function __static() {
      $builtin= version_compare(PHP_VERSION, '5.3.0', 'ge');

      if (!CRYPT_STD_DES) {
        self::$STANDARD= new CryptNotImplemented('STD_DES');
      } else {
        self::$STANDARD= new NativeCryptImpl();
        
        // Before 5.3.2, PHP's crypt() function returned incorrect values 
        // when given salt characters outside of the alphabet "./0-9A-Za-z".
        // No real workaround, so throw an exception - this is inconsistent
        // with XP on newer PHP versions which yields the correct results.
        // On systems >= 5.3.2, check for usage of libc crypt() which also  
        // allows salts which are too short and unsafe characters \n and : 
        if (version_compare(PHP_VERSION, '5.3.2', 'lt')) {
          self::$STANDARD= newinstance('security.crypto.NativeCryptImpl', array(), '{
            public function crypt($plain, $salt) {
              if (!preg_match("#^[./0-9A-Za-z]{2}#", $salt)) {
                throw new CryptoException("Malformed salt");
              }
              return parent::crypt($plain, $salt);
            }

            public function toString() {
              return "security.crypto.NativeCryptImpl+std:salt-alphabet-constraint";
            }
          }');
        } else if (':' === substr(crypt('', ':'), 0, 1)) {
          self::$STANDARD= newinstance('security.crypto.NativeCryptImpl', array(), '{
            public function crypt($plain, $salt) {
              if (strlen($salt) < 1 || strcspn($salt, "\n:") < 2) {
                throw new CryptoException("Malformed salt");
              }
              return parent::crypt($plain, $salt);
            }

            public function toString() {
              return "security.crypto.NativeCryptImpl+std:salt-unsafe-check";
            }
          }');
        }
      }

      if (!CRYPT_BLOWFISH) {
        self::$BLOWFISH= new CryptNotImplemented('BLOWFISH');
      } else {
        self::$BLOWFISH= new NativeCryptImpl();
        
        // The blowfish method has a bug between PHP 5.3.0 and 5.3.2 which
        // returns a bogus result instead of failing when the cost parameter
        // is incorrect. For *any* builtin implementation, recognition is 
        // broken as the "__" in "$2a$__$" for example makes PHP not jump 
        // into the blowfish branch but fall back to the else branch, and thus 
        // to standard DES. See line 247 and following in ext/standard/crypt.c
        if ($builtin) {
          self::$BLOWFISH= newinstance('security.crypto.NativeCryptImpl', array(), '{
            public function crypt($plain, $salt) {
              if (1 !== sscanf($salt, "$2a$%02d$", $cost)) {
                throw new CryptoException("Malformed cost parameter");
              }
              if ($cost < 4 || $cost > 31) {
                throw new CryptoException("Cost parameter must be between 04 and 31");
              }
              return parent::crypt($plain, $salt);
            }

            public function toString() {
              return "security.crypto.NativeCryptImpl+blowfish:cost-param-check";
            }
          }');
        }
      }
      
      if (!CRYPT_EXT_DES) {
        self::$EXTENDED= new CryptNotImplemented('EXT_DES');
      } else {

        // PHP's crypt() function crashes if the salt is too short due to PHP 
        // using its own DES implementations as 5.3 - these don't check the 
        // return value correctly. See Bug #51059, which was fixed in PHP 5.3.2
        // Debian only recognizes EXT_DES if the salt is 9 characters long, see
        // notes in PHP Bug #51254. As there is no reliable way to detect whether
        // the patch (referenced in this bug) is applied, always use the check
        // and strip off rest of crypted when matching. 
        self::$EXTENDED= newinstance('security.crypto.NativeCryptImpl', array(), '{
          public function crypt($plain, $salt) {
            if (strlen($salt) < 9) {
              throw new CryptoException("Extended DES: Salt too short");
            }
            return parent::crypt($plain, $salt); 
          }

          public function matches($encrypted, $entered) {
            return ($encrypted === $this->crypt($entered, substr($encrypted, 0, 9))); 
          }

          public function toString() {
            return "security.crypto.NativeCryptImpl+ext:recognition-fix";
          }
        }');
      }

      if (!CRYPT_MD5) {
        self::$MD5= XPClass::forName('security.crypto.MD5CryptImpl')->newInstance();
      } else {
        self::$MD5= new NativeCryptImpl();
        
        // In PHP version between 5.3.0 and 5.3.5, this fails for situations when
        // the salt is too short, too long or does not end with "$". 5.3.6 only
        // breaks when the salt is too short. 5.3.7 is the first version to get it
        // right, except: In PHP Bug #55439, crypt() returns just the salt for MD5
        // on Un*x systems. This bug first occurred in PHP 5.3.7 RC6 and was shipped 
        // with PHP 5.3.7, and fixed in the release thereafter.
        if ($builtin && version_compare(PHP_VERSION, '5.3.7', 'lt')) {
          self::$MD5= XPClass::forName('security.crypto.MD5CryptImpl')->newInstance();
        } else if (0 === strpos(PHP_VERSION, '5.3.7')) {
          if ('$1$' === crypt('', '$1$')) {
            self::$MD5= XPClass::forName('security.crypto.MD5CryptImpl')->newInstance();
          }
        }
      }
      
      self::$DEFAULT= self::$MD5;
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
      if (NULL === $salt) {
        $impl= self::$DEFAULT;
      } else if ('_' === $salt{0}) {
        $impl= self::$EXTENDED;
      } else if (0 === strpos($salt, '$1$')) {
        $impl= self::$MD5;
      } else if (0 === strpos($salt, '$2a$')) {
        $impl= self::$BLOWFISH;
      } else {
        $impl= self::$STANDARD;
      }

      return $impl->crypt($original, $salt);
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

    /**
     * Returns crypt implementations
     *
     * @return  [:security.crypto.CryptImpl]
     */
    public static function implementations() {
      return array(
        'std_des'  => self::$STANDARD, 
        'ext_des'  => self::$EXTENDED, 
        'blowfish' => self::$BLOWFISH, 
        'md5'      => self::$MD5
      );
    }
  }
?>
