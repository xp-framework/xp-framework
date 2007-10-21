<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'test.TransferFile',
    'io.File',
    'io.streams.FileOutputStream'
  );

  /**
   * Downloads a file to the FTP server to the current working 
   * directory.
   *
   * @see      xp://peer.ftp.FtpFile#downloadTo
   * @purpose  Command
   */
  class DownloadFile extends TransferFile {
    
    /**
     * Set file to download
     *
     * @param   string remote remote filename
     */
    #[@arg(position= 1)]
    public function setFile($remote) {
      $this->remote= $remote;
      $this->local= new FileOutputStream(new File(basename($remote)));
    }
    
    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine('Download ', $this->remote, ' -> ', $this->local);
      $this->out->writeLine($this->conn
        ->rootDir()
        ->file($this->remote)
        ->downloadTo($this->local, FTP_BINARY, $this->listener)
      );
    }
  }
?>
