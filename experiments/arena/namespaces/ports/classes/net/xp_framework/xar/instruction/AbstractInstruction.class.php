<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractInstruction.class.php 9886 2007-04-05 13:51:20Z kiesel $ 
 */

  namespace net::xp_framework::xar::instruction;

  ::uses(
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
  abstract class AbstractInstruction extends lang::Object {
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
