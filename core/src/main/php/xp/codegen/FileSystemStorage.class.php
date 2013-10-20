<?php namespace xp\codegen;

use io\Folder;
use io\File;
use io\FileUtil;

/**
 * File system storage
 *
 * @see   xp://xp.codegen.AbstractStorage
 */
class FileSystemStorage extends AbstractStorage {
  protected
    $path   = null;

  /**
   * Constructor
   *
   * @param   string path
   */
  public function __construct($path) {
    $this->path= new Folder($path);
  }

  /**
   * get URI
   *
   * @return  string
   */
  public function getUri() {
    return 'file://'.urlencode($this->path->uri);
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
