<?php
/* This class is part of the XP framework
 *
 * $Id: CRC16Digest.class.php 14066 2010-01-10 12:42:48Z friebe $ 
 */

  uses('security.checksum.MessageDigestImpl');

  /**
   * CRC16 digest. 
   *
   * @test     xp://net.xp_framework.unittest.security.checksum.CRC16DigestTest
   * @see      xp://security.checksum.MessageDigestImpl
   * @see      xp://security.checksum.CRC16
   */
  class CRC16DigestImpl extends MessageDigestImpl {
    protected $sum= 0;

    static function __static() {
      MessageDigest::register('crc16', new XPClass(__CLASS__));
    }

    /**
     * Constructor
     *
     * @param   string algo
     * @throws  lang.IllegalStateException
     */
    public function __construct($algo) {
      $this->sum= 0xFFFF;
    }

    /**
     * Update hash with data
     *
     * @param   string data
     */
    public function doUpdate($data) {
      for ($x= 0, $s= strlen($data); $x < $s; $x++) {
        $this->sum= $this->sum ^ ord($data{$x});
        for ($i= 0; $i < 8; $i++) {
          $this->sum= (1 === ($this->sum & 1) ? ($this->sum >> 1) ^ 0xA001 : $this->sum >> 1);
        }
      }
    }
    
    /**
     * Finalizes digest and returns a checksum
     *
     * @return  string
     */
    public function doFinal() {
      return sprintf('%04x', $this->sum);
    }
  }
?>
