<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.OutputStream', 'peer.ftp.FtpTransferStream');

  /**
   * OuputStream that writes to FTP files
   *
   * @ext      ftp
   * @see      xp://peer.ftp.FtpFile#getOutputStream
   * @purpose  OutputStream implementation
   */
  class FtpOutputStream extends FtpTransferStream implements OutputStream {

    /**
     * Returns command to send ("STOR")
     *
     * @return  string
     */
    protected function getCommand() {
      return 'STOR';
    }

    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) {
      $this->socket->write($arg);
    }

    /**
     * Flush this buffer. A NOOP for this implementation - data is written
     * directly to the transfer
     *
     */
    public function flush() { 
    }
  }
?>
