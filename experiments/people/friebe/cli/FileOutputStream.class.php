<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$
 */

  /**
   * OuputStream that writes to files
   *
   * @purpose  OuputStream implementation
   */
  class FileOutputStream extends Object {
    var
      $file= NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &io.File file
     */
    function __construct(&$file) {
      $this->file= deref($file);
      $this->file->open(FILE_MODE_WRITE);
    }

    /**
     * Write a string
     *
     * @access  public
     * @param   mixed arg
     */
    function write($arg) { 
      $this->file->write($arg);
    }

    /**
     * Destructor. Ensures file is closed.
     *
     * @access  public
     */
    function __destruct() {
      $this->file->close();
    }

  } implements(__FILE__, 'OutputStream');
?>
