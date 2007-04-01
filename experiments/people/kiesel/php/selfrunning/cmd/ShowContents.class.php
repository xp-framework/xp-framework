<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.cmd.Command');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ShowContents extends Command {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run() {
      $this->out->writeLine('Showing contents of ', $this->getClass()->getClassLoader());
      
      $archive= $this->getClass()->getClassLoader()->archive;
      while ($id= $archive->getEntry()) {
        $this->out->writeLinef('%20s %s',
          sprintf('%-10d', strlen($archive->extract($id))),
          $id
        );
      }
    }
  }
?>
