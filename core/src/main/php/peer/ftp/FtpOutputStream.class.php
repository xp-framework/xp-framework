<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.OutputStream', 'peer.ftp.FtpTransferStream');

  /**
   * OuputStream that writes to FTP files
   *
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
     * @param   var arg
     */
    public function write($arg) {
      $this->socket->write($arg);
    }
    
    /**
     * Close this stream
     *
     */
    public function close() {
      parent::close();
      $this->file->refresh('streaming');
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
