<?php namespace xp\codegen;

use io\File;
use io\Folder;
use io\FileUtil;

/**
 * Output for generation
 */
class FileSystemOutput extends AbstractOutput {
  protected
    $path = null;
    
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
   * Commit output
   *
   */
  public function commit() {
    // NOOP
  }
}
