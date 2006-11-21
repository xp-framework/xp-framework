<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.File',
    'io.FileUtil',
    'io.Folder'
  );
  
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AbstractCommand extends Object {
    var
      $options    = 0,
      $filename   = '',
      $args       = array();
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($options, $filename, $args) {
      $this->options= $options;
      $this->filename= $filename;
      $this->args= $args;
    }
  }
?>
