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
     * @throws  IOException
     * @throws  FileNotFoundException
     */
    function getContents(&$file) {
      $file->open(FILE_MODE_READ);
      $data= $file->read($file->size());
      $file->close();
      return $data;
    }
  }
?>
