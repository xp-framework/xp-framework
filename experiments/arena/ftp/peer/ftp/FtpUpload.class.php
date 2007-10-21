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
    public function __construct(FtpFile $remote= NULL, InputStream $in) {
      $this->remote= $remote;
      $this->in= $in;
    }

    /**
     * Creates a new FtpDownload instance without a remote file
     *
     * @see     xp://peer.ftp.FtpFile#start
     * @param   io.streams.OutputStream in
     */
    public static function from(InputStream $out) {
      return new self(NULL, $out);
    }

    /**
     * Starts this transfer
     *
     * @param   int mode
     * @return  peer.ftp.FtpTransfer this
     */
    public function start($mode) {
      $this->h= $this->remote->getConnection()->handle;
      $this->f= Streams::readableFd($this->in);
      $this->s= fstat($this->f);
      $this->r= ftp_nb_fput($this->h, $this->remote->getName(), $this->f, $mode);

      // Notify listener
      $this->listener && $this->listener->started($this);
      return $this;
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
