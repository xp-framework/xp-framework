<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.archive.Archive',
    'io.File'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class CreateCommand extends Object {
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
      $archive->open(ARCHIVE_MODE_CREATE);
      
      foreach ($this->retrieveFilelist() as $file) {
        if (($this->options & OPTIONS_VERBOSE)) {
          Console::writeLine('A '.$file);
        }
        
        $archive->add(new File($file), $file);
      }
      
      $archive->close();
      return 0;
    }
  }
?>
