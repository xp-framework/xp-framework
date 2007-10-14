<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.ftp.FtpEntry');

  /**
   * FTP file
   *
   * @see      xp://peer.ftp.FtpDir#getFile
   * @purpose  FtpEntry implementation
   */
  class FtpFile extends FtpEntry {

    /**
     * Delete this entry
     *
     * @throws  peer.SocketException in case of an I/O error
     */
    public function delete() {
      return ftp_delete($this->connection->handle, $this->name);
    }
  }
?>
