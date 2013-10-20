<?php namespace xp\codegen;

use io\File;
use lang\archive\Archive;

/**
 * Output for generation
 */
class ArchiveOutput extends AbstractOutput {
  protected
    $archive = null;
    
  /**
   * Constructor
   *
   * @param   string file
   */
  public function __construct($file) {
    $this->archive= new Archive(new File($file));
    $this->archive->open(ARCHIVE_CREATE);
  }

  /**
   * Store data
   *
   * @param   string name
   * @param   string data
   */
  protected function store($name, $data) {
    $this->archive->addFileBytes(
      $name,
      dirname($name),
      basename($name),
      $data
    );
  }
  
  /**
   * Commit output
   *
   */
  public function commit() {
    $this->archive->create();
  }
}
