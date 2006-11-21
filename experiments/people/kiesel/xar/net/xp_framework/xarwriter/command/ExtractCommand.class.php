<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.archive.Archive',
    'io.File',
    'io.FileUtil',
    'io.Folder'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class ExtractCommand extends Object {
    var
      $options    = 0,
      $filename   = '',
      $args       = array();
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($options, $filename, $args) {
      $this->options= $options;
      $this->filename= $filename;
      $this->args= $args;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function perform() {
      $archive= &new Archive(new File($this->filename));
      $archive->open(ARCHIVE_READ);
      
      $cwd= getcwd();
      if ($this->args->exists('C', 'C', FALSE)) {
        chdir($this->args->value('C', 'C'));
      }
      
      while ($entry= $archive->getEntry()) {
        $f= &new File($entry);
        $data= $archive->extract($entry);
        
        // TBD: Overwrite file, if already exists?
        if (
          !($this->options & OPTION_SIMULATE) &&
          !$f->exists()
        ) {
        
          // Create folder on demand
          $dir= &new Folder(dirname($entry));
          if (!$dir->exists()) {
            $dir->create();
          }
          
          FileUtil::setContents($f, $archive->extract($entry));
        }
        
        if (($this->options & OPTION_VERBOSE)) {
          Console::writeLinef('%10s %s', number_format(strlen($data), 0, FALSE, '.'), $entry);
        }
      }
      
      $archive->close();
      chdir($cwd);
      
      return 0;
    }
  }
?>
