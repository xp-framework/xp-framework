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
   * Base command class
   *
   * @purpose  Base command
   */
  abstract class AbstractCommand extends Object {
    protected
      $options    = 0,
      $archive    = NULL,
      $args       = array();
    
    /**
     * Constructor
     *
     * @param   int options
     * @param   lang.archive.Archive archive
     * @param   string[] args
     */
    public function __construct($options, $archive, $args) {
      $this->options= $options;
      $this->archive= $archive;
      $this->args= $args;
    }
    
    /**
     * Retrieve file arguments from commandline
     *
     * @return  string[]
     */
    protected function getArguments() {
      return $this->args;
    }
    
    /**
     * Perform
     *
     */
    public abstract function perform();
  }
?>
