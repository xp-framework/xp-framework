<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  define('UUID_FMTSTRING',  '%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x');

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
   * Example [get a UUID from a string]:
   * <code>
   *   try {
   *     $uuid= UUID::fromString($uuidstr);
   *   } catch (FormatException $e) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   var_dump($uuid->toString());
   * </code>
   *
   * @purpose  Generate UUIDs
   * @see      http://www.ietf.org/internet-drafts/draft-mealling-uuid-urn-00.txt
   */
  class UUID extends Object {
    public
      $time_low                     = 0,
      $time_mid                     = 0,
      $time_hi_and_version          = 0,
      $clock_seq_low                = 0,
      $clock_seq_hi_and_reserved    = 0,
      $node                         = array();
        
    /**
     * Create a new UUID
     *
     * @return  org.ietf.UUID
     * @see     http://www.ietf.org/internet-drafts/draft-mealling-uuid-urn-00.txt section 4.1.4
     */
    public static function create() {
    
      // Get timestamp and convert it to UTC (based Oct 15, 1582).
      list($usec, $sec) = explode(' ', microtime());
      $t= ($sec * 10000000) + ($usec * 10) + 122192928000000000;
      $clock_seq= mt_rand();
      
      $uuid= new UUID();
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
     * Create a UUID from a string
     *
     * @param   string str
     * @return  org.ietf.UUID
     * @throws  lang.FormatException in case str is not a valid UUID string
     */
    public static function fromString($str) {
      $uuid= new UUID();
      $a= array();
      if (11 != sscanf(
        $str, 
        UUID_FMTSTRING,
        $uuid->time_low,
        $uuid->time_mid,
        $uuid->time_hi_and_version,
        $uuid->clock_seq_low,
        $uuid->clock_seq_hi_and_reserved,
        $uuid->node[0],
        $uuid->node[1],
        $uuid->node[2],
        $uuid->node[3],
        $uuid->node[4],
        $uuid->node[5]
      )) {
        throw new FormatException($str.' is not a valid UUID string');
      }
      return $uuid;
    }
    
    /**
     * Creates a string representation. 
     *
     * Examples: 
     * <pre>
     *   f81d4fae-7dec-11d0-a765-00a0c91e6bf6
     *   c71a4a80-4a80-171a-8fb7-000401000800
     * </pre>
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        UUID_FMTSTRING,
        $this->time_low, 
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
  }
?>
