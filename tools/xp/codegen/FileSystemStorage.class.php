<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.codegen.AbstractStorage',
    'io.Folder',
    'io.File',
    'io.FileUtil'
  );

  /**
   * File system storage
   *
   * @see      xp://xp.codegen.AbstractStorage
   * @purpose  Storage implementation
   */
  class FileSystemStorage extends AbstractStorage {
    protected
      $path   = NULL;

    /**
     * Constructor
     *
     * @param   string path
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

    /**
     * Fetch data
     *
     * @param   string name
     * @return  string data
     */
    protected function fetch($name) {
      return FileUtil::getContents(new File($this->path, $name));
    }
  }
?>
