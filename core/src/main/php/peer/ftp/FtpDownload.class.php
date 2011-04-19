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
    protected $out= NULL;

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
     * Returns command to send
     *
     * @return  string
     */
    protected function getCommand() {
      return 'RETR';
    }

    /**
     * Retrieves this transfer's total size
     *
     * @param   int size
     */
    public function size() {
      return $this->remote->getSize();
    }

    /**
     * Continues this transfer
     *
     * @throws  peer.SocketException in case this transfer fails
     * @throws  lang.IllegalStateException in case start() has not been called before
     */
    protected function doTransfer() {
      try {
        $chunk= $this->socket->readBinary();
      } catch (IOException $e) {
        $this->listener && $this->listener->failed($this, $e);
        $this->close();
        throw $e;
      }

      if (0 == ($len= strlen($chunk))) {
        $this->state= 2;
        $this->close();
        $this->listener && $this->listener->completed($this);
        return;
      }

      try {
        $this->out->write($chunk);
      } catch (IOException $e) {
        $this->listener && $this->listener->failed($this, $e);
        $this->close();
        throw $e;
      }

      $this->transferred+= $len;
      $this->listener && $this->listener->transferred($this);
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
