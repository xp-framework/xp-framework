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
   * Example [creating a new UUID]:
   * <code>
   *   $uuid= UUID::create();
   *   var_dump($uuid->toString());
   * </code>
   *
   * Creating UUIDs 
   * --------------
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
      if (NULL === $arg) return;

      // Detect input format
      if ($arg instanceof Bytes) {
        $str= implode('-', unpack('H8a/H4b/H4c/H4d/H12e', $arg));
      } else if (0 === strncasecmp($arg, 'urn:uuid', 8)) {
        $str= substr($arg, 9);
      } else {
        $str= trim($arg, '{}');
      }

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

      $uuid= new self(NULL);
      $uuid->version= 1;
      $uuid->time_low= ($t & 0xFFFFFFFF);
      $uuid->time_mid= (($t >> 32) & 0xFFFF);
      $uuid->time_hi_and_version= (($t >> 48) & 0x0FFF);
      $uuid->time_hi_and_version |= (1 << 12);
      $uuid->clock_seq_low= $clock_seq & 0xFF;
      $uuid->clock_seq_hi_and_reserved= ($clock_seq & 0x3F00) >> 8;
      $uuid->clock_seq_hi_and_reserved |= 0x80;

      $h= md5(php_uname());
      $uuid->node= array(
        hexdec(substr($h, 0x0, 2)),
        hexdec(substr($h, 0x2, 2)),
        hexdec(substr($h, 0x4, 2)),
        hexdec(substr($h, 0x6, 2)),
        hexdec(substr($h, 0x8, 2)),
        hexdec(substr($h, 0xB, 2))
      );

      return $uuid;
    }


    /**
     * Create a version 3 UUID based upon a name and a given namespace
     *
     * @param   util.UUID namespace
     * @param   string name
     * @return  util.UUID
     */
    public static function md5UUID(self $namespace, $name, $charset= 'utf-8') {
      $bytes= md5($namespace->getBytes().iconv('iso-8859-1', $charset, $name));
      $str= sprintf('%08s-%04s-%04x-%04x-%12s',

        // 32 bits for "time_low"
        substr($bytes, 0, 8),

        // 16 bits for "time_mid"
        substr($bytes, 8, 4),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 5
        hexdec(substr($bytes, 12, 4)) & 0x0fff | 0x3000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        hexdec(substr($bytes, 16, 4)) & 0x3fff | 0x8000,

        // 48 bits for "node"
        substr($bytes, 20, 12)
      );
      return new self($str);
    }

    /**
     * Create a version 3 UUID based upon a name and a given namespace
     *
     * @param   util.UUID namespace
     * @param   string name
     * @return  util.UUID
     */
    public static function sha1UUID(self $namespace, $name, $charset= 'utf-8') {
      $bytes= sha1($namespace->getBytes().iconv('iso-8859-1', $charset, $name));
      $str= sprintf('%08s-%04s-%04x-%04x-%12s',

        // 32 bits for "time_low"
        substr($bytes, 0, 8),

        // 16 bits for "time_mid"
        substr($bytes, 8, 4),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 5
        hexdec(substr($bytes, 12, 4)) & 0x0fff | 0x5000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        hexdec(substr($bytes, 16, 4)) & 0x3fff | 0x8000,

        // 48 bits for "node"
        substr($bytes, 20, 12)
      );
      return new self($str);
    }

    /**
     * Create a version 4 UUID based upon random bits
     *
     * @return  util.UUID
     */
    public static function randomUUID() {
      $str= sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
      );

      return new self($str);
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
  }
?>
