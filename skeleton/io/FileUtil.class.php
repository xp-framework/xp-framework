<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * File utility functions
   *
   * @see      xp://io.File
   * @purpose  Simplify often used file operations
   */
  class FileUtil extends Object {
  
    /**
     * Retreive file contents as a string
     *
     * <code>
     *   $str= FileUtil::getContents(new File('/etc/passwd'));
     * </code>
     *
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  string file contents
     * @throws  io.IOException
     * @throws  io.FileNotFoundException
     */
    function getContents(&$file) {
      $file->open(FILE_MODE_READ);
      $data= $file->read($file->size());
      $file->close();
      return $data;
    }
    
    /**
     * Set file contents
     *
     * <code>
     *   $bytes_written= FileUtil::setContents(new File('myfile'), 'Hello world');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  int filesize
     * @throws  io.IOException
     */
    function setContents(&$file, $data) {
      $file->open(FILE_MODE_READ);
      $file->write($data);
      $file->close();
      return $file->size();
    }
  }
?>
