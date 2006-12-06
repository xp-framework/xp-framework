<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.File',
    'io.FileUtil',
    'name.kiesel.pxl.storage.IStorage'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FilesystemContainer extends Object implements IStorage {
    public
      $base = '';
    
    public function __construct($base) {
      $this->base= $base;
    }
    
    public function load($abstract) {
      return FileUtil::getContents(new File($this->base.'/'.$abstract.'.xml'));
    }
    
    public function save($abstract, $data) {
      $f= new File($this->base.'/'.$abstract.'.xml');
      $f->open(FILE_MODE_WRITE);
      $f->lockExclusive(TRUE);
      $f->write($data);
      $f->unlock();
      $f->close();
    }
    
    public function getRelative($path) {
      return new FilesystemContainer($base.'/'.$path);
    }
    
    public function getBase() {
      return $this->base;
    }
  } 
?>
