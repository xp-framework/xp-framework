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
    protected $in = NULL;

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
     * @param   io.streams.InputStream in
     */
    public static function from(InputStream $in) {
      return new self(NULL, $in);
    }

    /**
     * Returns command to send
     *
     * @return  string
     */
    protected function getCommand() {
      return 'STOR';
    }

    /**
     * Retrieves this transfer's total size
     *
     * @param   int size
     */
    public function size() {
      return -1;
    }

    /**
     * Continues this transfer
     *
     * @throws  peer.SocketException in case this transfer fails
     * @throws  lang.IllegalStateException in case start() has not been called before
     */
    protected function doTransfer() {
      if ($this->in->available() <= 0) {
        $this->state= 2;
        $this->close();
        $this->listener && $this->listener->completed($this);
        return;
      }

      try {
        $chunk= $this->in->read(8192);
        $this->socket->write($chunk);
      } catch (IOException $e) {
        $this->listener && $this->listener->failed($this, $e);
        $this->close();
        throw $e;
      }

      $this->transferred+= strlen($chunk);
      $this->listener && $this->listener->transferred($this);
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
