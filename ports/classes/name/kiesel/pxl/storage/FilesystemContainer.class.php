<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File', 'io.FileUtil');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FilesystemContainer extends Object {
    var
      $base = '';
    
    function __construct($base) {
      $this->base= $base;
    }
    
    function load($abstract) {
      return FileUtil::getContents(new File($this->base.'/'.$abstract.'.xml'));
    }
    
    function save($abstract, $data) {
      $f= &new File($this->base.'/'.$abstract.'.xml');
      $f->open(FILE_MODE_WRITE);
      $f->lockExclusive(TRUE);
      $f->write($data);
      $f->unlock();
      $f->close();
    }
    
    function getRelative($path) {
      return new FilesystemContainer($base.'/'.$path);
    }
  } implements(__FILE__, 'name.kiesel.pxl.storage.IStorage');
?>
