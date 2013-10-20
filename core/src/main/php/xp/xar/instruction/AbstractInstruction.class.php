<?php namespace xp\xar\instruction;

/**
 * Base Instruction class
 */
abstract class AbstractInstruction extends \lang\Object {
  protected
    $out        = null,
    $err        = null,
    $options    = 0,
    $archive    = null,
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
   */
  public abstract function perform();
}
