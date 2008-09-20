<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.cmd.Command', 'lang.archive.Archive');

  /**
   * XAR command
   *
   * @purpose  purpose
   */
  class Xar extends Command {
    const OPTION_SIMULATE = 0x0001;
    const OPTION_VERBOSE  = 0x0002;

    protected
      $operation = NULL,
      $options   = 0,
      $archive   = NULL,
      $args      = array();
  
    /**
     * Set command
     *
     * @param   string command
     */
    #[@arg(position= 0)]
    public function parseInstructions($op) {
      for ($i= 0, $operation= NULL; $i < strlen($op); $i++) {
        switch ($op{$i}) {
          case 'c': $this->setOperation('create'); break;
          case 'x': $this->setOperation('extract'); break;
          case 's': $this->setOperation('show'); break;
          case 'd': $this->setOperation('diff'); break;
          case 't':
            $this->setOperation('extract');
            $this->options|= self::OPTION_SIMULATE;
            break;
          case 'v': $this->options|= self::OPTION_VERBOSE; break;
          case 'f': $this->filename= ''; break;
          
          default: throw new IllegalArgumentException('Unsupported commandline option "'.$op{$i}.'"');
        }
      }
    }
    
    /**
     * Set operation
     *
     * @param   string name
     */
    protected function setOperation($name) {
      if (NULL !== $this->operation)
        throw new IllegalArgumentException('Cannot execute more than one instruction at a time.');
      
      $this->operation= XPClass::forName('net.xp_framework.xar.instruction.'.ucfirst($name).'Instruction');
    }

    /**
     * Set archive
     *
     * @param   string filename
     */
    #[@arg(position= 1)]
    public function setArchive($filename) {
      $this->archive= new Archive(new File($filename));
    }

    /**
     * Set working directory
     *
     * @param   string dir
     */
    #[@arg(short= 'R')]
    public function setWorkDirectory($dir= '.') {
      chdir($dir);
    }

    /**
     * Set args
     *
     * @param   string[] args
     */
    #[@args(select= '[2..]')]
    public function setArgs($args) {
      $this->args= $args;
    }
    
    /**
     * Runs this command
     *
     */
    public function run() {
      $this->operation->newInstance($this->out, $this->err, $this->options, $this->archive, $this->args)->perform();
    }
  }
?>
