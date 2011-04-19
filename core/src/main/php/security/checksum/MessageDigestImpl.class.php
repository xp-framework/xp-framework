<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Message digest implementation
   *
   * @see      xp://security.checksum.MessageDigest
   */
  abstract class MessageDigestImpl extends Object {
    protected $finalized= FALSE;

    /**
     * Initialize this implementation
     *
     * @param   string algo
     * @throws  lang.IllegalStateException
     */
    public abstract function __construct($algo);

    /**
     * Update hash with data
     *
     * @param   string data
     */
    public abstract function doUpdate($data);
    
    /**
     * Finalizes digest and returns checksum
     *
     * @return  string
     */
    public abstract function doFinal();
    
    /**
     * Update hash with data
     *
     * @param   string data
     * @throws  lang.IllegalStateException if digest already finalized
     */
    public function update($data) {
      if ($this->finalized) {
        throw new IllegalStateException('Digest already finalized');
      }
      $this->doUpdate($data);
    }
    
    /**
     * Finalizes digest and returns a checksum object
     *
     * @param   string data default NULL
     * @return  string final
     * @throws  lang.IllegalStateException if digest already finalized
     */
    public function digest($data= NULL) {
      if ($this->finalized) {
        throw new IllegalStateException('Digest already finalized');
      }
      if (NULL !== $data) $this->doUpdate($data);
      $final= $this->doFinal();
      $this->finalized= TRUE;
      return $final;
    }
  }
?>
