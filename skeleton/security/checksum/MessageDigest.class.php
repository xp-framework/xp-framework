<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.NoSuchAlgorithmException');

  /**
   * Base class for message digests
   *
   * Creating a message digest incrementally
   * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
   * <code>
   *   $digest= mew MD5Digest();
   *   while ($in->available() > 0) {
   *     $digest->update($in->read());
   *   }
   *   $md5= $digest->final();
   * </code>
   *
   * Verifying
   * ~~~~~~~~~
   * <code>
   *   if ($md5->verify(new MD5('...'))) {
   *     // Checksums match
   *   }
   * </code>
   *
   * @ext      hash
   * @test     xp://net.xp_framework.unittest.security.checksum.MessageDigestTest
   * @see      xp://security.checksum.MD5Digest
   * @see      xp://security.checksum.SHA1Digest
   * @see      xp://security.checksum.CRC32Digest
   */
  abstract class MessageDigest extends Object {
    protected $handle= NULL;
    
    /**
     * Constructor
     *
     * @throws  security.NoSuchAlgorithmException if algorithm is not supported
     */
    public function __construct() {
      if (!($this->handle= hash_init($algo= $this->algorithm()))) {
        throw new NoSuchAlgorithmException('Could not initialize algorithm "'.$algo.'"');
      }
    }
    
    /**
     * Returns algorithm
     *
     * @return  string
     */
    protected abstract function algorithm();

    /**
     * Returns checksum instance
     *
     * @param   string final
     * @return  security.checksum.Checksum
     */
    protected abstract function instance($final);
    
    /**
     * Update hash with data
     *
     * @param   string data
     * @throws  lang.IllegalStateException if digest already finalized
     */
    public function update($data) {
      if (NULL === hash_update($this->handle, $data)) {
        throw new IllegalStateException('Digest already finalized');
      }
    }
    
    /**
     * Finalizes digest and returns a checksum object
     *
     * @param   string data default NULL
     * @return  security.checksum.Checksum
     * @throws  lang.IllegalStateException if digest already finalized
     */
    public function digest($data= NULL) {
      if (NULL !== $data) $this->update($data);
      if (NULL === ($final= hash_final($this->handle))) {
        throw new IllegalStateException('Digest already finalized');
      }
      $this->handle= NULL;
      return $this->instance($final);
    }
  }
?>
