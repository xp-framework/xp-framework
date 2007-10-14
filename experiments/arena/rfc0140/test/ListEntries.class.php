<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('test.AbstractFtpTestCommand');

  /**
   * Lists directory entries
   *
   * @see      xp://peer.ftp.FtpDir#entries
   * @purpose  Command
   */
  class ListEntries extends AbstractFtpTestCommand {
    
    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine($this->conn->rootDir()->entries());
    }
  }
?>
