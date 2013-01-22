<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.xar.Options',
    'lang.archive.Archive',
    'io.File',
    'io.FileUtil',
    'io.Folder'
  );
  
  /**
   * Base Instruction class
   *
   * @purpose  Base Instruction
   */
  abstract class AbstractInstruction extends Object {
    protected
      $out        = NULL,
      $err        = NULL,
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
    public function __construct($out, $err, $options, $archive, $args) {
      $this->out= $out;
      $this->err= $err;
      $this->options= $options;
      $this->archive= $archive;
      $this->args= $args;
    }
    
    /**
     * Retrieve file arguments from Instructionline
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
