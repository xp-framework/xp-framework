<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('security.checksum.MessageDigestImpl');

  /**
   * Default digest implementation. Uses <tt>hash</tt> extension.
   *
   * @ext      hash
   * @test     xp://net.xp_framework.unittest.security.checksum.MD5DigestTest
   * @test     xp://net.xp_framework.unittest.security.checksum.SHA1DigestTest
   * @test     xp://net.xp_framework.unittest.security.checksum.CRC32DigestTest
   * @see      xp://security.checksum.MessageDigestImpl
   * @see      xp://security.checksum.MD5
   * @see      xp://security.checksum.SHA1
   * @see      xp://security.checksum.CRC32
   * @see      php://hash_algos
   */
  class DefaultDigestImpl extends MessageDigestImpl {
    protected $handle= NULL;
    
    static function __static() {
      $self= new XPClass(__CLASS__);
      foreach (hash_algos() as $algo) {
        MessageDigest::register($algo, $self);
      }
      
      // Overwrite crc32b implementation if a buggy implementation is detected. 
      // Workaround for http://bugs.php.net/bug.php?id=45028
      if ('0a1cb779' === hash('crc32b', 'AAAAAAAA')) {
        MessageDigest::register('crc32b', ClassLoader::defineClass(__CLASS__.'·CRC32bDigestImpl', $self->getName(), array(), '{
          public function doFinal() {
            $n= hexdec(hash_final($this->handle));
            return sprintf("%08x", (($n & 0xFF) << 24) + (($n & 0xFF00) << 8) + (($n & 0xFF0000) >> 8) + (($n >> 24) & 0xFF));
          }
        }'));
      }
    }

    /**
     * Initialize this implementation
     *
     * @param   string algo
     * @throws  lang.IllegalStateException
     */
    public function __construct($algo) {
      if (!($this->handle= hash_init($algo))) {
        throw new IllegalStateException('Could not initialize algorithm "'.$algo.'"');
      }
    }

    /**
     * Update hash with data
     *
     * @param   string data
     */
    public function doUpdate($data) {
      hash_update($this->handle, $data);
    }
    
    /**
     * Finalizes digest and returns a checksum object
     *
     * @return  string
     */
    public function doFinal() {
      $final= hash_final($this->handle);
      $this->handle= NULL;
      return $final;
    }
  }
?>
