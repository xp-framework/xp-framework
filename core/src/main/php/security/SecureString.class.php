<?php
/*
 * This class is part of the XP Framework
 *
 */

  uses('lang.Runtime');

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
   */
  final class SecureString extends Object {
    private static $store   = array();
    private static $encrypt = NULL;
    private static $decrypt = NULL;

    static function __static() {
      if (Runtime::getInstance()->extensionAvailable('mcrypt')) {
        $engine= mcrypt_module_open(MCRYPT_DES, '', 'ecb', '');
        $engineiv= mcrypt_create_iv(mcrypt_enc_get_iv_size($engine), MCRYPT_RAND);
        $key= substr(md5(uniqid()), 0, mcrypt_enc_get_key_size($engine));
        mcrypt_generic_init($engine, $key, $engineiv);

        self::$encrypt= function($value) use($engine) {
          return mcrypt_generic($engine, $value);
        };

        self::$decrypt= function($value) use ($engine) {
          return rtrim(mdecrypt_generic($engine, $value), "\0");
        };
      } else if (Runtime::getInstance()->extensionAvailable('openssl')) {
        $key= substr(md5(uniqid()));
        $iv= substr(md5(uniqid()), 0, openssl_cipher_iv_length("des"));

        self::$encrypt= function($value) use ($key, $iv) {
          return openssl_encrypt($value, "DES", $key,  0, $iv);
        };

        self::$decrypt= function($value) use ($key, $iv) {
          return openssl_decrypt($value, "DES", $key,  0, $iv);
        };
      } else {
        throw new RuntimeException('Cannot instanciate, neither extension "mcrypt" nor "openssl" available - at least one required.');
      }
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
        self::$store[$this->hashCode()]= $m($c);
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
        throw new IllegalStateException('An error occurred during storing the encrypted password.');
      }
      $m= self::$decrypt;
      return $m(self::$store[$this->hashCode()]);
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

    public function __destruct() {
      unset(self::$store[$this->hashCode()]);
    }
  }
?>