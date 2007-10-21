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
      $aborted    = FALSE,
      $remote     = NULL;

    /**
     * Returns the remote file
     *
     * @return  peer.ftp.FtpFile
     */
    public function remote() {
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
  }
?>
