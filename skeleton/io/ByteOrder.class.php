<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('BIG_ENDIAN',      0x0000);
  define('LITTLE_ENDIAN',   0x0001);

  /**
   * Byte order
   *
   * <quote>
   * Intel's 80x86 processors and their clones are little endian. Sun's 
   * SPARC, Motorola's 68K, and the PowerPC families are all big endian. 
   * </quote>
   *
   * @see      http://www.netrino.com/Publications/Glossary/Endianness.html
   * @purpose  Utility class
   */
  class ByteOrder extends Object {
  
    /**
     * Retrieves the name of a byteorder
     *
     * Example:
     * <code>
     *   uses('io.ByteOrder'); 
     *
     *   var_dump(ByteOrder::nameOf(ByteOrder::nativeOrder()));
     * </code>
     *
     * @param   int order
     * @return  string name
     */
    public static function nameOf($order) {
      switch ($order) {
        case BIG_ENDIAN: return 'BIG_ENDIAN';
        case LITTLE_ENDIAN: return 'LITTLE_ENDIAN';
      }
      return '(unknown)';
    }

    /**
     * Retrieves this system's native byte order
     *
     * @return  int either BIG_ENDIAN or LITTLE_ENDIAN
     * @throws  lang.FormatException in case the byte order cannot be determined
     */
    public static function nativeOrder() {
      switch (pack('d', 1)) {
        case "\0\0\0\0\0\0\360\77": return LITTLE_ENDIAN;
        case "\77\360\0\0\0\0\0\0": return BIG_ENDIAN;
      }

      throw new FormatException('Unexpected result: '.addcslashes(pack('d', 1), "\0..\17"));
    }
    
    /**
     * Returns the network byte order.
     *
     * @return  int network byte order
     * @see     http://www.hyperdictionary.com/computing/network+byte+order
     */
    public static function networkOrder() {
      return BIG_ENDIAN;
    }
  }
?>
