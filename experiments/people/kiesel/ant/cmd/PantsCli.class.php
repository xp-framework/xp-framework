<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.File',
    'io.FileUtil',
    'util.cmd.Command',
    'ant.AntProject'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PantsCli extends Command {
    public
      $project= NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@arg(short= 'f')]
    public function setBuildfile($file= NULL) {
      if (NULL === $file) $file= 'build.xml';
      $this->project= AntProject::fromString(
        FileUtil::getContents(new File($file)),
        realpath($file)
      );
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   string args
     */
    #[@args(select= '[0..]')]
    public function setArgs($args) {
      $this->args= $args;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run() {
      return $this->project->run(
        $this->out,
        $this->err,
        $this->args
      );
    }
  }
?>
