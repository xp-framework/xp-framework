<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.xar.instruction.AbstractInstruction');

  /**
   * Show Instruction
   *
   * @purpose  Show
   */
  class ShowInstruction extends AbstractInstruction {

    /**
     * Filters entries
     *
     * @param   string entry
     * @param   string[] list
     * @return  bool
     */
    protected function _filter($entry, $list) {

      // No filters given, no filtering
      if (0 == sizeof($list)) return TRUE;
      
      // The files to output must begin with one of the given strings...
      foreach ($list as $l) {

        // Either a directory is given
        $directory= rtrim($l, '/').'/';
        if (0 == strncmp($entry, $directory, strlen($directory))) return TRUE;
        
        // Or a filename, but the it must match completely
        if (0 == strcmp($entry, $l)) return TRUE;
      }
      
      return FALSE;
    }

    /**
     * Execute action
     *
     * @return  int
     */
    public function perform() {
      $this->archive->open(ARCHIVE_READ);
      
      $args= $this->getArguments();
      while ($entry= $this->archive->getEntry()) {
        if (!$this->_filter($entry, $args)) continue;
      
        $this->out->writeLine('== ', $entry, ' ==');
        $this->out->writeLine($this->archive->extract($entry));
      }
      
      $this->archive->close();
    }
  }
?>
