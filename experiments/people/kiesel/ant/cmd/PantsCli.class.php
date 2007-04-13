<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.File',
    'io.FileUtil',
    'util.cmd.Command',
    'xml.meta.Unmarshaller'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PantsCli extends Command {

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@arg(short= 'f')]
    public function setBuildfile($file= NULL) {
      if (NULL === $file) $file= 'build.xml';
      $this->buildXml= FileUtil::getContents(new File($file));
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   string args
     */
    #[@args]
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
      $project= Unmarshaller::unmarshal($this->buildXml, 'ant.AntProject');
      // $this->out->writeLine('===> file structure: '.$project->toString());
      
      return $project->run(
        $this->out,
        $this->err,
        $this->args
      );
    }
  }
?>
