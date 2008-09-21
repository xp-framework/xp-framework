<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.codegen.AbstractOutput');

  /**
   * Output for generation
   *
   * @purpose  Abstract base class
   */
  class FileSystemOutput extends AbstractOutput {
    protected
      $path = NULL;
      
    /**
     * Constructor
     *
     * @param   string path
     * @param   util.Observer observer
     */
    public function __construct($path) {
      $this->path= new Folder($path);
    }

    /**
     * Store data
     *
     * @param   string name
     * @param   string data
     */
    protected function store($name, $data) {
      FileUtil::setContents(new File($this->path, $name), $data);
    }
  }
?>
