<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.types.Bytes');

  /**
   * Encapsulates UUIDs (Universally Unique IDentifiers), also known as
   * GUIDs (Globally Unique IDentifiers).
   *
   * <quote>
   * A UUID is an identifier that is unique across both space and time,
   * with respect to the space of all UUIDs.  To be precise, the UUID
   * consists of a finite bit space.  Thus the time value used for
   * constructing a UUID is limited and will roll over in the future
   * (approximately at A.D.  3400, based on the specified algorithm).
   * </quote>
   *
   * Creating UUIDs
   * --------------
   * <code>
   *   UUID::timeUUID();     // Creates a new v1, time based, UUID
   *   UUID::randomUUID();   // Creates a new v4, pseudo randomly generated, UUID
   * </code>
   *
   * Creating name-based UUIDs
   * -------------------------
   * <code>
   *   UUID::md5UUID(UUID::$NS_DNS, 'example.com');
   *   UUID::sha1UUID(UUID::$NS_DNS, 'example.com');
   * </code>
   *
   * Instanciation
   * -------------
   * UUIDs can be created from various input sources. The following are
   * all equivalent:
   *
   * <code>
   *   new UUID('6ba7b811-9dad-11d1-80b4-00c04fd430c8');
   *   new UUID('{6ba7b811-9dad-11d1-80b4-00c04fd430c8}');
   *   new UUID('urn:uuid:6ba7b811-9dad-11d1-80b4-00c04fd430c8');
   *   new UUID(new Bytes("k\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0O\xd40\xc8"));
   * </code>
   *
   * Output
   * -----
   * <code>
   *   $uuid->hashCode(); // '6ba7b811-9dad-11d1-80b4-00c04fd430c8'
   *   $uuid->toString(); // '{6ba7b811-9dad-11d1-80b4-00c04fd430c8}'
   *   $uuid->getUrn();   // 'urn:uuid:6ba7b811-9dad-11d1-80b4-00c04fd430c8'
   *   $uuid->getBytes(); // new Bytes("k\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0O\xd40\xc8")
   * </code>
   *
   * @see   rfc://4122
   * @see   http://www.ietf.org/internet-drafts/draft-mealling-uuid-urn-00.txt
   */
  class UUID extends Object {
    const FORMAT = '%04x%04x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x';

    public static
      $NS_DNS                       = NULL,
      $NS_URL                       = NULL,
      $NS_OID                       = NULL,
      $NS_X500                      = NULL;

    public
      $time_low                     = 0,
      $time_mid                     = 0,
      $time_hi_and_version          = 0,
      $clock_seq_low                = 0,
      $clock_seq_hi_and_reserved    = 0,
      $node                         = array();

    protected
      $version                      = NULL;

    static function __static() {
      self::$NS_DNS= new self('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
      self::$NS_URL= new self('6ba7b811-9dad-11d1-80b4-00c04fd430c8');
      self::$NS_OID= new self('6ba7b812-9dad-11d1-80b4-00c04fd430c8');
      self::$NS_X500= new self('6ba7b814-9dad-11d1-80b4-00c04fd430c8');
    }

    /**
     * Create a UUID
     *
     * @param   var arg
     * @throws  lang.FormatException in case str is not a valid UUID string
     */
    public function __construct($arg) {
      if ($arg instanceof Bytes) {
        $this->populate(implode('-', unpack('H8a/H4b/H4c/H4d/H12e', $arg)));
      } else if (is_array($arg)) {
        $this->version= $arg[0];
        $this->time_low= $arg[1];
        $this->time_mid= $arg[2];
        $this->time_hi_and_version= $arg[3] | ($arg[0] << 12);
        $this->clock_seq_low= $arg[4] & 0xFF;
        $this->clock_seq_hi_and_reserved= (($arg[4] & 0x3F00) >> 8) | 0x80;
        $this->node= $arg[5];
      } else if (0 === strncasecmp($arg, 'urn:uuid', 8)) {
        $this->populate(substr($arg, 9));
      } else {
        $this->populate(trim($arg, '{}'));
      }
    }

    /**
     * Populate instance members from a given string
     *
     * @param   string
     */
    private function populate($str) {

      // Parse. Use %04x%04x for "time_low" instead of "%08x" to overcome
      // sscanf()'s 32 bit limitation and do the multiplication manually.
      if (12 !== sscanf(
        $str, 
        self::FORMAT,
        $l[0], $l[1],
        $this->time_mid,
        $this->time_hi_and_version,
        $this->clock_seq_hi_and_reserved,
        $this->clock_seq_low,
        $this->node[0],
        $this->node[1],
        $this->node[2],
        $this->node[3],
        $this->node[4],
        $this->node[5]
      )) {
        throw new FormatException($str.' is not a valid UUID string');
      }
      $this->time_low= $l[0] * 0x10000 + $l[1];

      // Detect version
      $this->version= ($this->time_hi_and_version >> 12) & 0xF;
    }

    /**
     * Create a version 1 UUID based upon time stamp and node identifier
     *
     * @return  util.UUID
     * @see     http://www.ietf.org/internet-drafts/draft-mealling-uuid-urn-00.txt section 4.1.4
     */
    public static function timeUUID() {

      // Get timestamp and convert it to UTC (based Oct 15, 1582).
      list($usec, $sec) = explode(' ', microtime());
      $t= ($sec * 10000000) + ($usec * 10) + 122192928000000000;
      $clock_seq= mt_rand();
      $h= md5(php_uname());

      return new self(array(
        1,
        ($t & 0xFFFFFFFF),
        (($t >> 32) & 0xFFFF),
        (($t >> 48) & 0x0FFF),
        $clock_seq,
        array(
          hexdec(substr($h, 0x0, 2)),
          hexdec(substr($h, 0x2, 2)),
          hexdec(substr($h, 0x4, 2)),
          hexdec(substr($h, 0x6, 2)),
          hexdec(substr($h, 0x8, 2)),
          hexdec(substr($h, 0xB, 2))
        )
      ));
    }

    /**
     * Create a version 3 UUID based upon a name and a given namespace
     *
     * @param   util.UUID namespace
     * @param   string name
     * @return  util.UUID
     */
    public static function md5UUID(self $namespace, $name) {
      $bytes= md5($namespace->getBytes().iconv(xp::ENCODING, 'utf-8', $name));
      
      return new self(array(
        3,
        hexdec(substr($bytes, 0, 8)),
        hexdec(substr($bytes, 8, 4)),
        hexdec(substr($bytes, 12, 4)) & 0x0fff,
        hexdec(substr($bytes, 16, 4)) & 0x3fff | 0x8000,
        array_map('hexdec', str_split(substr($bytes, 20, 12), 2))
      ));
    }

    /**
     * Create a version 5 UUID based upon a name and a given namespace
     *
     * @param   util.UUID namespace
     * @param   string name
     * @return  util.UUID
     */
    public static function sha1UUID(self $namespace, $name) {
      $bytes= sha1($namespace->getBytes().iconv(xp::ENCODING, 'utf-8', $name));

      return new self(array(
        5,
        hexdec(substr($bytes, 0, 8)),
        hexdec(substr($bytes, 8, 4)),
        hexdec(substr($bytes, 12, 4)) & 0x0fff,
        hexdec(substr($bytes, 16, 4)) & 0x3fff | 0x8000,
        array_map('hexdec', str_split(substr($bytes, 20, 12), 2))
      ));
    }

    /**
     * Create a version 4 UUID based upon random bits
     *
     * @return  util.UUID
     */
    public static function randomUUID() {
      return new self(array(
        4,
        mt_rand(0, 0xffff) * 0x10000 + mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff),
        mt_rand(0, 0x3fff) | 0x8000,
        sscanf(
          sprintf('%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
          '%02x%02x%02x%02x%02x%02x'
        )
      ));
    }

    /**
     * Returns version
     *
     * @return  int
     */
    public function version() {
      return $this->version;
    }

    /**
     * Get bytes
     *
     * @return  lang.types.Bytes
     */
    public function getBytes() {
      return new Bytes(pack('H32', str_replace('-', '', $this->hashCode())));
    }

    /**
     * Creates a urn representation
     *
     * @return  string
     */
    public function getUrn() {
      return 'urn:uuid:'.$this->hashCode();
    }
    
    /**
     * Creates a string representation. 
     *
     * Example: <tt>{f81d4fae-7dec-11d0-a765-00a0c91e6bf6}</tt>
     *
     * @return  string
     */
    public function toString() {
      return '{'.$this->hashCode().'}';
    }

    /**
     * Returns a hashcode
     *
     * Example: <tt>f81d4fae-7dec-11d0-a765-00a0c91e6bf6</tt>
     *
     * @return  string
     */
    public function hashCode() {
      $r= (int)($this->time_low / 0x10000);
      return sprintf(
        self::FORMAT,
        $r, $this->time_low - $r * 0x10000,
        $this->time_mid, 
        $this->time_hi_and_version,
        $this->clock_seq_hi_and_reserved, 
        $this->clock_seq_low,
        $this->node[0], 
        $this->node[1], 
        $this->node[2],
        $this->node[3], 
        $this->node[4], 
        $this->node[5]
      );
    }

    /**
     * Returns whether another instance is equal to this
     *
     * @param   var cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->hashCode() === $this->hashCode();
    }

    /**
     * Serialization callback
     *
     * @return    string[]
     */
    public function __sleep() {
      $this->value= $this->hashCode();    // Invent "value" member
      return array('value');
    }

    /**
     * Deserialization callback
     *
     */
    public function __wakeup() {
      $this->populate($this->value);
      unset($this->value);
    }
  }
?>
