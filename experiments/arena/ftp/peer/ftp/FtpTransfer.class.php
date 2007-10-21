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
      $r          = NULL,
      $h          = NULL,
      $f          = NULL,
      $aborted    = FALSE,
      $remote     = NULL;

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
     * Aborts this transfer
     *
     */
    public function abort() {
      $this->aborted= TRUE;
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
     * Starts this transfer
     *
     * @param   int mode
     */
    public abstract function start($mode);
    
    /**
     * Returns whether this transfer is complete
     *
     * @return  bool
     */
    public function complete() {
      return $this->r !== FTP_MOREDATA;
    }

    /**
     * Continues this transfer
     *
     */
    public function perform() {
      if (FTP_MOREDATA !== ($this->r= ftp_nb_continue($this->h))) {
        fclose($this->f);
      } 
    }
  }
?>
