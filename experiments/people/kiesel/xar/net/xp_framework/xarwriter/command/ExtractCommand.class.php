<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.xarwriter.command.AbstractCommand');

  /**
   * Extract command
   *
   * @purpose  Extract
   */
  class ExtractCommand extends AbstractCommand {

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
      
        $f= new File($entry);
        $data= $this->archive->extract($entry);
        
        // TBD: Overwrite file, if already exists?
        if (
          !($this->options & Xar::OPTION_SIMULATE) &&
          !$f->exists()
        ) {
        
          // Create folder on demand
          $dir= new Folder(dirname($entry));
          if (!$dir->exists()) { $dir->create(); }
          
          FileUtil::setContents($f, $data);
        }
        
        $this->options & Xar::OPTION_VERBOSE && Console::writeLinef('%10s %s', number_format(strlen($data), 0, FALSE, '.'), $entry);
      }
      
      $this->archive->close();
    }
  }
?>
