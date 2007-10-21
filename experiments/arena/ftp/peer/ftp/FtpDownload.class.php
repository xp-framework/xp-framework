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
     * @param   io.streams.OutputStream out
     */
    public function __construct(FtpFile $remote= NULL, OutputStream $out) {
      $this->remote= $remote;
      $this->out= $out;
    }
    
    /**
     * Creates a new FtpDownload instance without a remote file
     *
     * @see     xp://peer.ftp.FtpFile#start
     * @param   io.streams.OutputStream out
     */
    public static function to(OutputStream $out) {
      return new self(NULL, $out);
    }

    /**
     * Starts this transfer
     *
     */
    public function start($mode) {
      $this->h= $this->remote->getConnection()->handle;
      $this->r= ftp_nb_fget(
        $this->h, 
        $this->f= Streams::writeableFd($this->out),
        $this->remote->getName(),
        $mode
      );
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
      return sprintf(
        '%s@(%s -> %s)',
        $this->getClassName(),
        $this->remote ? $this->remote->getName() : '(null)',
        $this->out->toString()
      );
    }
  }
?>
