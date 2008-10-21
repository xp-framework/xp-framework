<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.InputStream', 'peer.ftp.FtpTransferStream');

  /**
   * InputStream that reads from FTP files
   *
   * @ext      ftp
   * @see      xp://peer.ftp.FtpFile#getInputStream
   * @purpose  InputStream implementation
   */
  class FtpInputStream extends FtpTransferStream implements InputStream {

    /**
     * Returns command to send ("RETR")
     *
     * @return  string
     */
    protected function getCommand() {
      return 'RETR';
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192) {
      return $this->socket->readBinary($limit);
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return $this->socket->eof() ? 0 : 1;
    }
  }
?>
