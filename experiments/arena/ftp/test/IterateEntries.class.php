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
    protected
      $dir = '';
    
    /**
     * Set directory to list
     *
     * @param   string dir default "/"
     */
    #[@arg(position= 1)]
    public function setDir($dir= '/') {
      $this->dir= $dir;
    }
    
    /**
     * Main runner method
     *
     */
    public function run() {
      with ($dir= $this->conn->rootDir()->getDir($this->dir)); {
        $this->out->writeLine('== ', $dir->getName(), ' ==');
        foreach ($dir->entries() as $entry) {
          $this->out->writeLine('* ', $entry);
        }
      }
    }
  }
?>
