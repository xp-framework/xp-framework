<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpTransfer', 'io.streams.OutputStream');

  /**
   * Represents an download
   *
   * @see      xp://peer.ftp.FtpFile#downloadTo
   * @purpose  FtpTransfer implementation
   */
  class FtpDownload extends FtpTransfer {
    protected
      $out        = NULL;

    /**
     * Constructor
     *
     * @param   peer.ftp.FtpFile remote
     * @param   io.streams.OutputStream in
     */
    public function __construct(FtpFile $remote, OutputStream $out) {
      $this->remote= $remote;
      $this->out= $out;
    }
    
    /**
     * Returns the target of this transfer
     *
     * @return  io.streams.OutputStream
     */
    public function outputStream() {
      return $this->out;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@('.$this->remote->getName().' -> '.$this->out->toString().')';
    }
  }
?>
