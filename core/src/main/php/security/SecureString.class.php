<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  uses('lang.Runtime', 'security.SecurityException');

  /**
   * SecureString provides a reasonable secure storage for security-sensistive
   * lists of characters, such as passwords.
   *
   * It prevents accidentially revealing them in output, by var_dump()ing,
   * echo()ing, or casting the object to array. All these cases will not
   * show the password, nor the crypt of it.
   *
   * However, it is not safe to consider this implementation secure in a crypto-
   * graphically sense, because it does not care for a very strong encryption,
   * and it does share the encryption key with all instances of it in a single
   * PHP instance.
   *
   * Hint: when using this class, you must make sure not to extract the secured string
   * and pass it to a place where an exception might occur, as it might be exposed as
   * method argument.
   *
   * As a rule of thumb: extract it from the container at the last possible location.
   *
   * @test  xp://net.xp_framework.unittest.security.SecureStringTest
   * @test  xp://net.xp_framework.unittest.security.McryptSecureStringTest
   * @test  xp://net.xp_framework.unittest.security.OpenSSLSecureStringTest
   * @test  xp://net.xp_framework.unittest.security.PlainTextSecureStringTest
   */
  final class SecureString extends Object {
    const BACKING_MCRYPT    = 0x01;
    const BACKING_OPENSSL   = 0x02;
    const BACKING_PLAINTEXT = 0x03;

    private static $store   = array();
    private static $encrypt = NULL;
    private static $decrypt = NULL;
    private static $engine  = NULL;

    static function __static() {
      if (Runtime::getInstance()->extensionAvailable('mcrypt')) {
        self::useBacking(self::BACKING_MCRYPT);
      } else if (Runtime::getInstance()->extensionAvailable('openssl')) {
        self::useBacking(self::BACKING_OPENSSL);
      } else {
        self::useBacking(self::BACKING_PLAINTEXT);
      }
    }

    /**
     * Switch storage algorithm backing
     *
     * @param  int $type one of BACKING_MCRYPT, BACKING_OPENSSL, BACKING_PLAINTEXT
     * @throws lang.IllegalArgumentException If illegal backing type was given
     * @throws lang.IllegalStateException If chosen backing missed a extension dependency
     */
    public static function useBacking($type) {
      switch ($type) {
        case self::BACKING_MCRYPT: {
          if (!Runtime::getInstance()->extensionAvailable('mcrypt')) {
            throw new IllegalStateException('Backing "mcrypt" required but extension not available.');
          }
          self::$engine= mcrypt_module_open(MCRYPT_DES, '', 'ecb', '');
          $engineiv= mcrypt_create_iv(mcrypt_enc_get_iv_size(self::$engine), MCRYPT_RAND);
          $key= substr(md5(uniqid()), 0, mcrypt_enc_get_key_size(self::$engine));
          mcrypt_generic_init(self::$engine, $key, $engineiv);

          return self::setBacking(
            create_function('$value, $engine', 'return mcrypt_generic($engine, $value);'),
            create_function('$value, $engine', 'return rtrim(mdecrypt_generic($engine, $value), "\0");')
          );
        }

        case self::BACKING_OPENSSL: {
          if (!Runtime::getInstance()->extensionAvailable('openssl')) {
            throw new IllegalStateException('Backing "openssl" required but extension not available.');
          }
          self::$engine= array(
            'key' => md5(uniqid()),
            'iv'  => substr(md5(uniqid()), 0, 8) // openssl_cipher_iv_length('des')
          );

          return self::setBacking(
            create_function('$value, $engine', 'return openssl_encrypt($value, "DES", $engine["key"],  0, $engine["iv"]);'),
            create_function('$value, $engine', 'return openssl_decrypt($value, "DES", $engine["key"],  0, $engine["iv"]);')
          );
        }

        case self::BACKING_PLAINTEXT: {
          return self::setBacking(
            create_function('$value, $engine', 'return base64_encode($value);'),
            create_function('$value, $engine', 'return base64_decode($value);')
          );
        }

        default: {
          throw new IllegalArgumentException('Invalid backing given: '.xp::stringOf($type));
        }
      }
    }

    /**
     * Store encryption and decryption routines (unittest method only)
     *
     * @param callable $encrypt
     * @param callable $decrypt
     */
    public static function setBacking($encrypt, $decrypt) {
      self::$encrypt= $encrypt;
      self::$decrypt= $decrypt;
    }

    /**
     * Constructor
     *
     * @param string $c Characters to secure
     */
    public function __construct($c) {
      $this->setCharacters($c);
    }

    /**
     * Prevent serialization of object
     *
     * @return array
     */
    public function __sleep() {
      throw new IllegalStateException('Cannot serialize SecureString instances.');
    }

    /**
     * Set characters to secure
     *
     * @param string $c
     */
    public function setCharacters(&$c) {
      try {
        $m= self::$encrypt;
        self::$store[$this->hashCode()]= $m($c, self::$engine);
      } catch (Exception $e) {
        // This intentionally catches *ALL* exceptions, in order not to fail
        // and produce a stacktrace (containing arguments on the stack that were)
        // supposed to be protected.
        // Also, cleanup XP error stack
        unset(self::$store[$this->hashCode()]);
        xp::gc();
      }

      $c= str_repeat('*', strlen($c));
      $c= NULL;
    }

    /**
     * Retrieve secured characters
     *
     * @return string
     */
    public function getCharacters() {
      if (!isset(self::$store[$this->hashCode()])) {
        throw new SecurityException('An error occurred during storing the encrypted password.');
      }
      $m= self::$decrypt;
      return $m(self::$store[$this->hashCode()], self::$engine);
    }

    /**
     * Override regular __toString() output
     *
     * @return string
     */
    public function __toString() {
      return $this->toString();
    }

    /**
     * Provide string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'('.$this->hashCode().') {}';
    }

    /**
     * Destructor; removes references from crypted storage for this instance.
     */
    public function __destruct() {
      unset(self::$store[$this->hashCode()]);
    }
  }
?>