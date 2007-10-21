<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'test.AbstractTransferCommand',
    'io.File',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream'
  );

  /**
   * Downloads a file to the FTP server to the current working 
   * directory.
   *
   * @see      xp://peer.ftp.FtpFile#start
   * @purpose  Command
   */
  class TransferFile extends AbstractTransferCommand {
    
    /**
     * Main runner method
     *
     */
    public function run() {
      with ($file= $this->conn->rootDir()->file($this->getClassName().'.tmp')); {
      
        // First upload from a MemoryInputStream
        $upload= $file->start(FtpUpload::from(new MemoryInputStream('Test file')));
        $this->out->writeLine($upload);
        while (!$upload->complete()) {
          $upload->perform();
        }
        $this->out->writeLine('Done');

        // Then download and print to console
        $download= $file->start(FtpDownload::to(new MemoryOutputStream()));      
        $this->out->writeLine($download);
        while (!$download->complete()) {
          $download->perform();
        }
        $this->out->writeLine('Done, contents= "', $download->outputStream()->getBytes(), '"');
        
        // Remove temporary file
        $file->delete();
      }
    }
  }
?>
