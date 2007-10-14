<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'test.AbstractFtpTestCommand',
    'io.File',
    'io.streams.FileInputStream'
  );

  /**
   * Uploads a file to the FTP server's root directory
   *
   * @see      xp://peer.ftp.FtpFile#uploadFrom
   * @purpose  Command
   */
  class UploadFile extends AbstractFtpTestCommand {
    protected
      $local  = NULL,
      $remote = '';
    
    /**
     * Set file to upload
     *
     * @param   string local local filename
     */
    #[@arg(position= 1)]
    public function setFile($local) {
      $f= new File($local);
      $this->remote= $f->getFileName();
      $this->local= new FileInputStream($f);
    }
    
    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine('Uploading ', $this->local, ' -> ', $this->remote);
      $this->out->writeLine($this->conn
        ->rootDir()
        ->file($this->remote)
        ->uploadFrom($this->local, FTP_BINARY)
      );
    }
  }
?>
