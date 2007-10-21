<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('test.AbstractFtpTestCommand');

  /**
   * Sends the "FEAT" command to the FTP server
   *
   * @see      xp://peer.ftp.FtpDir#entries
   * @purpose  Command
   */
  class FeatureCommand extends AbstractFtpTestCommand {
    
    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine($this->conn->sendCommand('FEAT'));
    }
  }
?>
