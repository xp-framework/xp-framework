<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.cmd.Command');

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
    public function setOperation($op) {
      for ($i= 0, $operation= NULL; $i < strlen($op); $i++) {
        switch ($op{$i}) {
          case 'c': $operation= 'create'; break;
          case 'x': $operation= 'extract'; break;
          case 't':
            $operation= 'extract';
            $this->options|= self::OPTION_SIMULATE;
            break;
          case 'v': $this->options|= self::OPTION_VERBOSE; break;
          case 'f': $thhis->filename= ''; break;
          default: throw new IllegalArgumentException('Unsupported commandline option "'.$op{$i}.'"');
        }
      }
      $this->operation= XPClass::forName('net.xp_framework.xarwriter.command.'.ucfirst($operation).'Command');
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
     * Set args
     *
     * @param   string args
     */
    #[@arg]
    public function setArgs($args) {
      $this->args= '' == trim($args) ? array() : explode(' ', trim($args));
    }
    
    /**
     * Runs this command
     *
     */
    public function run() {
      $this->operation->newInstance($this->options, $this->archive, $this->args)->perform();
    }
  }
?>
