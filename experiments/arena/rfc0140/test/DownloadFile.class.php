<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'test.AbstractFtpTestCommand',
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
  class DownloadFile extends AbstractFtpTestCommand {
    protected
      $local  = NULL,
      $remote = '';
    
    /**
     * Set file to upload
     *
     * @param   string local local filename
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
        ->downloadTo($this->local, FTP_BINARY)
      );
    }
  }
?>
