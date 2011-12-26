<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base class for up- and downloads
   *
   * @see      xp://peer.ftp.FtpUpload
   * @see      xp://peer.ftp.FtpDownload
   * @purpose  Abstract base class
   */
  abstract class FtpTransfer extends Object {
    const ASCII  = 1;
    const BINARY = 2;

    protected static $modes= array(
      self::ASCII  => 'A',
      self::BINARY => 'I'
    );
    
    protected
      $remote      = NULL,
      $listener    = NULL,
      $socket      = NULL,
      $state       = 0,
      $transferred = 0;

    /**
     * Sets the remote file
     *
     * @param   peer.ftp.FtpFile remote
     */
    public function setRemote(FtpFile $remote) {
      $this->remote= $remote;
    }

    /**
     * Returns the remote file
     *
     * @return  peer.ftp.FtpFile
     */
    public function getRemote() {
      return $this->remote;
    }

    /**
     * Returns the remote file
     *
     * @return  peer.ftp.FtpTransferListener l
     * @return  peer.ftp.FtpTransfer this transfer object
     */
    public function withListener(FtpTransferListener $l= NULL) {
      $this->listener= $l;
      return $this;
    }

    /**
     * Aborts this transfer
     *
     */
    public function abort() {
      $this->state= 3;
    }

    /**
     * Returns whether this transfer is complete
     *
     * @return  bool TRUE if this transfer is complete, FALSE otherwise
     */
    public function complete() {
      return 2 == $this->state;
    }
 
    /**
     * Returns whether this transfer has been aborted
     *
     * @return  bool
     */
    public function aborted() {
      return 3 == $this->state;
    }

    /**
     * Retrieves how many bytes have already been transferred
     *
     * @param   int size
     */
    public function transferred() {
      return $this->transferred;
    }

    /**
     * Starts this transfer
     *
     * @param   int mode
     * @return  peer.ftp.FtpTransfer this
     */
    public function start($mode) {      
      with ($conn= $this->remote->getConnection()); {

        // Set mode
        $conn->expect($conn->sendCommand('TYPE %s', self::$modes[$mode]), array(200));
      
        // Issue the transfer command
        $this->socket= $conn->transferSocket();
        $r= $conn->sendCommand('%s %s', $this->getCommand(), $this->remote->getName());
        sscanf($r[0], "%d %[^\r\n]", $code, $message);
        if (150 !== $code) {
          throw new ProtocolException(sprintf(
            '%s: Cannot transfer %s (%d: %s)',
            $this->getCommand(),
            $this->remote->getName(),
            $code,
            $message
          ));
        }
      
        $this->transferred= 0;
        $this->state= 1;
      }
      $this->listener && $this->listener->started($this);
      return $this;
    }

    /**
     * Close down communication
     *
     */
    protected function close() {
      $this->socket->close();
      with ($conn= $this->remote->getConnection()); {
        $conn->expect($conn->getResponse(), array(226));
      }
    }

    /**
     * Continues this transfer
     *
     * @throws  peer.SocketException in case this transfer fails
     * @throws  lang.IllegalStateException in case start() has not been called before
     */
    public function perform() {
      if (1 === $this->state) {
        $this->doTransfer();
      } else if (2 === $this->state) {
        throw new IllegalStateException('Transfer finished');
      } else if (3 === $this->state) {
        $this->close();
        $this->listener && $this->listener->aborted($this);
        return;
      } else {
        $this->close();
        $e= new IllegalStateException('Transfer has not been started yet');
        $this->listener && $this->listener->failed($this, $e);
        throw $e;
      }
    }

    /**
     * Returns command to send
     *
     * @return  string
     */
    protected abstract function getCommand();

    /**
     * Continues this transfer
     *
     * @throws  peer.SocketException in case this transfer fails
     * @throws  lang.IllegalStateException in case start() has not been called before
     */
    protected abstract function doTransfer();

    /**
     * Retrieves this transfer's total size
     *
     * @param   int size
     */
    public abstract function size();
  }
?>