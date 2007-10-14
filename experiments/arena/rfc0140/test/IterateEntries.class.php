<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('test.AbstractFtpTestCommand');

  /**
   * Iterates on directory entries
   *
   * @see      xp://peer.ftp.FtpDir#entries
   * @purpose  Command
   */
  class IterateEntries extends AbstractFtpTestCommand {
    
    /**
     * Main runner method
     *
     */
    public function run() {
      foreach ($this->conn->rootDir()->entries() as $entry) {
        $this->out->writeLine('* ', $entry);
      }
    }
  }
?>
