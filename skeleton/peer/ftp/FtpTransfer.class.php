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
    protected
      $r          = -1,
      $s          = -1,
      $h          = NULL,
      $f          = NULL,
      $aborted    = FALSE,
      $remote     = NULL,
      $listener   = NULL;

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
      $this->aborted= TRUE;
      $this->r= -2;
    }
 
    /**
     * Returns whether this transfer has been aborted
     *
     * @return  bool
     */
    public function aborted() {
      return $this->aborted;
    }


    /**
     * Retrieves this transfer's total size
     *
     * @param   int size
     */
    public function size() {
      return $this->s['size'];
    }

    /**
     * Retrieves how many bytes have already been transferred
     *
     * @param   int size
     */
    public function transferred() {
      return ftell($this->f);
    }

    /**
     * Initiate a transfer
     *
     * @param   int mode
     */    
    protected abstract function initiate($mode);
    
    /**
     * Starts this transfer
     *
     * @param   int mode
     * @return  peer.ftp.FtpTransfer this
     */
    public function start($mode) {
      $this->initiate($mode);

      // Notify listener
      $this->listener && $this->listener->started($this);
      return $this;
    }

    /**
     * Returns whether this transfer is complete
     *
     * @return  bool TRUE if this transfer is complete, FALSE otherwiese
     */
    public function complete() {
      return FTP_MOREDATA !== $this->r;
    }

    /**
     * Continues this transfer
     *
     * @throws  peer.SocketException in case this transfer fails
     * @throws  lang.IllegalStateException in case start() has not been called before
     */
    public function perform() {
      $e= NULL;
      switch ($this->r= ftp_nb_continue($this->h)) {
        case FTP_MOREDATA: {
          $this->listener && $this->listener->transferred($this);
          return;
        }
        
        case FTP_FINISHED: {
          $this->listener && $this->listener->completed($this);
          break;
        }
    
        case -1: {
          $e= new IllegalStateException('Transfer has not been started yet');
          break;
        }
        
        case -2: {
          $this->listener && $this->listener->aborted($this);
          break;
        }
        
        case FTP_FAILED: {
          $e= new SocketException('Failed transferring');
          $this->listener && $this->listener->failed($this, $e);
          break;
        }
      }

      // Close file handle, reset result to initial value -1
      $this->f && fclose($this->f);
      $this->f= NULL;
      $this->r= -1;
      if ($e) throw $e;
    }
  }
?>
