<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpTransfer', 'io.streams.InputStream');

  /**
   * Represents an upload
   *
   * @see      xp://peer.ftp.FtpFile#uploadFrom
   * @purpose  FtpTransfer implementation
   */
  class FtpUpload extends FtpTransfer {
    protected
      $in         = NULL;

    /**
     * Constructor
     *
     * @param   peer.ftp.FtpFile remote
     * @param   io.streams.InputStream in
     */
    public function __construct(FtpFile $remote, InputStream $in) {
      $this->remote= $remote;
      $this->in= $in;
    }
    
    /**
     * Returns the origin of this transfer
     *
     * @return  io.streams.InputStream
     */
    public function inputStream() {
      return $this->in;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@('.$this->in->toString().' -> '.$this->remote->getName().')';
    }
  }
?>
